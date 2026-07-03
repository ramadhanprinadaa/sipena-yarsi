<?php

namespace App\Services;

use App\Ldap\User as LdapUser;
use App\Models\Pegawai;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use LdapRecord\Container;
use LdapRecord\Auth\BindException;

class LdapAuthService
{
    /**
     * Mapping title LDAP -> nama role SIPENA default.
     * Role ini HANYA dipakai saat user baru pertama kali dibuat.
     */
    protected const ROLE_MAP = [
        'D' => 'Dosen',
        'S' => 'Staff',
        'M' => 'Staff',
    ];

    protected const DEFAULT_ROLE = 'Staff';

    /**
     * Autentikasi user via LDAP, lalu resolve/provision ke User SIPENA.
     *
     * @return User|null  Null jika autentikasi gagal ATAU pegawai/user tidak dapat di-resolve.
     */
    public function authenticate(string $username, string $password): ?User
    {
        $ldapUser = $this->findLdapUser($username);

        if (! $ldapUser) {
            Log::info('LDAP login gagal: username tidak ditemukan di direktori', [
                'username' => $username,
            ]);

            return null;
        }

        if (! $this->verifyPassword($ldapUser, $password)) {
            Log::warning('LDAP login gagal: password salah', [
                'username' => $username,
            ]);

            return null;
        }

        return $this->resolveOrProvisionUser($ldapUser);
    }

    /**
     * Cari entry user di LDAP berdasarkan cn (username).
     */
    protected function findLdapUser(string $username): ?LdapUser
    {
        try {
            return LdapUser::findBy('cn', $username);
        } catch (\Throwable $e) {
            Log::error('LDAP search error', [
                'username' => $username,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Verifikasi password dengan bind langsung ke DN user (bukan akun admin).
     */
    protected function verifyPassword(LdapUser $ldapUser, string $password): bool
    {
        try {
            $connection = Container::getConnection($ldapUser->getConnectionName() ?? 'default');

            return $connection->auth()->attempt($ldapUser->getDn(), $password);
        } catch (BindException $e) {
            return false;
        } catch (\Throwable $e) {
            Log::error('LDAP bind error', ['message' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Resolusi pegawai -> user SIPENA. Auto-provisioning jika pegawai ditemukan
     * tapi user belum ada. Menolak login jika pegawai tidak ditemukan.
     */
    protected function resolveOrProvisionUser(LdapUser $ldapUser): ?User
    {
        $nip = $this->firstAttributeValue($ldapUser, 'description');

        if (! $nip) {
            Log::warning('LDAP login ditolak: atribut description (NIP/NPM) kosong', [
                'dn' => $ldapUser->getDn(),
            ]);

            return null;
        }

        $pegawai = Pegawai::where('nip', $nip)->first();

        if (! $pegawai) {
            // Kebijakan: pegawai tidak ditemukan -> tolak login (tidak auto-provisioning).
            Log::warning('LDAP login ditolak: pegawai dengan NIP tersebut tidak ditemukan di SIPENA', [
                'nip' => $nip,
            ]);

            return null;
        }

        $user = User::where('pegawai_id', $pegawai->id)->first();

        if (! $user) {
            $user = $this->provisionUser($pegawai, $ldapUser);
        } else {
            $this->syncUserFromLdap($user, $pegawai, $ldapUser);
        }

        return $user;
    }

    /**
     * Buat user SIPENA baru untuk pegawai yang baru pertama kali login via LDAP.
     */
    protected function provisionUser(Pegawai $pegawai, LdapUser $ldapUser): User
    {
        $roleId = $this->resolveRoleId($ldapUser);

        $user = new User([
            'username' => $this->firstAttributeValue($ldapUser, 'cn'),
            'pegawai_id' => $pegawai->id,
            'role_id' => $roleId,
            'email' => $pegawai->email_yarsi,
            'status' => 'active',
            'auth_type' => 'ldap',
        ]);

        // Password acak & di-hash: user LDAP tidak pernah login pakai password lokal,
        // tapi kolom password NOT NULL di banyak skema Laravel default.
        $user->password = bcrypt(str()->random(32));
        $user->ldap_synced_at = now();
        $user->save();

        Log::info('User SIPENA baru dibuat dari LDAP', [
            'user_id' => $user->id,
            'pegawai_id' => $pegawai->id,
            'role_id' => $roleId,
        ]);

        return $user;
    }

    /**
     * Sinkronkan HANYA data identitas dari LDAP. Role dan data bisnis lain
     * tidak boleh disentuh setelah user pernah dibuat.
     */
    protected function syncUserFromLdap(User $user, Pegawai $pegawai, LdapUser $ldapUser): void
    {
        $user->fill([
            'username' => $this->firstAttributeValue($ldapUser, 'cn') ?? $user->username,
            'email' => $pegawai->email_yarsi ?? $user->email,
        ]);

        $user->ldap_synced_at = now();
        $user->save();
    }

    /**
     * Tentukan role_id awal berdasarkan title LDAP. Dipanggil HANYA saat provisioning.
     */
    protected function resolveRoleId(LdapUser $ldapUser): int
    {
        $title = $this->firstAttributeValue($ldapUser, 'title');
        $roleName = self::ROLE_MAP[$title] ?? self::DEFAULT_ROLE;

        $role = Role::where('name', $roleName)->first();

        if (! $role) {
            Log::warning('Role default tidak ditemukan di tabel roles, fallback ke role_id bawaan User model', [
                'role_name' => $roleName,
            ]);

            // Fallback ke default attribute pada User model (role_id => 6 / Staff)
            return (new User())->role_id;
        }

        return $role->id;
    }

    /**
     * Helper: ambil nilai pertama dari atribut LDAP multi-value dengan aman.
     */
    protected function firstAttributeValue(LdapUser $ldapUser, string $attribute): ?string
    {
        $value = $ldapUser->getFirstAttribute($attribute);

        return $value !== null ? trim($value) : null;
    }
}