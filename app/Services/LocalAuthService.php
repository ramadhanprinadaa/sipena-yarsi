<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LocalAuthService
{
    /**
     * Autentikasi user lokal (auth_type = 'local') berdasarkan username atau email.
     *
     * @return User|null  Null jika user tidak ditemukan, bukan user lokal, atau password salah.
     */
    public function authenticate(string $identifier, string $password): ?User
    {
        $user = User::where('auth_type', 'local')
            ->where(function ($query) use ($identifier) {
                $query->where('username', $identifier)
                    ->orWhere('email', $identifier);
            })
            ->first();

        if (! $user) {
            return null;
        }

        if (! Hash::check($password, $user->password)) {
            Log::warning('Local login gagal: password salah', [
                'identifier' => $identifier,
            ]);

            return null;
        }

        if ($user->status !== 'active') {
            Log::warning('Local login ditolak: status user tidak aktif', [
                'user_id' => $user->id,
                'status' => $user->status,
            ]);

            return null;
        }

        return $user;
    }

    /**
     * Cek apakah identifier (username/email) terdaftar sebagai user lokal.
     * Dipakai AuthController untuk menentukan jalur autentikasi mana yang dicoba.
     */
    public function isLocalUser(string $identifier): bool
    {
        return User::where('auth_type', 'local')
            ->where(function ($query) use ($identifier) {
                $query->where('username', $identifier)
                    ->orWhere('email', $identifier);
            })
            ->exists();
    }
}