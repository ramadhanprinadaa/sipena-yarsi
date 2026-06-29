<?php

namespace App\Services;

use App\Models\User;
use App\Models\Pegawai;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class LdapAuthService
{
    protected string $host = 'pdc.yarsi.ac.id';
    protected string $baseDn = 'dc=yarsi,dc=ac,dc=id';
    protected int $port = 389;

    public function authenticate(string $username, string $password): bool
    {
        // 1. Coba login sebagai Local User terlebih dahulu (Support Email & Username)
        $field = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // 1. Coba login sebagai Local User terlebih dahulu
        $localUser = User::where($field, $username)->where('auth_type', 'local')->first();
        if ($localUser && Auth::attempt([$field => $username, 'password' => $password])) {
            return true;
        }

        // 2. Jika bukan Local User, proses via LDAP
        $ldapConnection = @ldap_connect($this->host, $this->port);
        if (!$ldapConnection) return false;

        ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapConnection, LDAP_OPT_REFERRALS, 0);

        // Format user DN untuk bind, menyesuaikan struktur YARSI
        $userDn = "cn={$username},{$this->baseDn}";

        // Proses verifikasi username dan password ke LDAP
        try {
            $bind = @ldap_bind($ldapConnection, $userDn, $password);
        } catch (\Exception $e) {
            return false;
        }

        if ($bind) {
            // Ambil atribut pengguna LDAP
            $filter = "(cn=$username)";
            $search = ldap_search($ldapConnection, $this->baseDn, $filter);
            $entries = ldap_get_entries($ldapConnection, $search);

            if ($entries['count'] > 0) {
                $ldapData = $entries[0];
                return $this->handleLdapProvisioning($ldapData);
            }
        }

        return false;
    }

    protected function handleLdapProvisioning(array $ldapData): bool
    {
        // Ekstraksi data berdasarkan struktur atribut LDAP YARSI
        $username = $ldapData['cn'][0] ?? null;
        $nipNpm = $ldapData['description'][0] ?? null;
        $namaLengkap = $ldapData['displayname'][0] ?? null;
        $telp = $ldapData['telephonenumber'][0] ?? null;
        $title = $ldapData['title'][0] ?? null; // D, S, M

        if (!$nipNpm) return false; // NIP/NPM wajib ada

        // Cari pegawai berdasarkan NIP dari LDAP
        $pegawai = Pegawai::where('nip', $nipNpm)->first();

        // REJECT jika pegawai tidak terdaftar (Keamanan)
        if (!$pegawai) return false;

        // Cari atau buat User berdasarkan pegawai_id
        $user = User::where('pegawai_id', $pegawai->id)->first();

        if (!$user) {
            // Pertama kali login (Provisioning)
            $user = new User();
            $user->pegawai_id = $pegawai->id;
            $user->auth_type = 'ldap';
            // Mapping Role saat pertama kali dibuat
            $user->role_id = $this->mapLdapTitleToRole($title);
        }

        // Sinkronisasi data yang diizinkan (berlaku untuk insert baru & update)
        $user->username = $username;
        // Hanya sinkron jika nilai dari LDAP tidak kosong
        if ($namaLengkap) $pegawai->nama = $namaLengkap;
        if ($telp) $pegawai->no_telpon = $telp;

        $pegawai->save();
        $user->save();

        // Login user ke sesi Laravel
        Auth::login($user);

        return true;
    }

    protected function mapLdapTitleToRole(?string $title): int
    {
        // Role Mapping berdasarkan $title LDAP: D, S, M
        $roleName = match(strtoupper($title)) {
            'D' => 'Dosen',
            'S', 'M' => 'Staff',
            default => 'Staff'
        };

        // Asumsi nama role di tabel roles sesuai dengan string di atas
        $role = Role::where('name', $roleName)->first();

        // Return ID, fallback ke default role ID (misal 7) jika tidak ketemu
        return $role ? $role->id : 7;
    }
}