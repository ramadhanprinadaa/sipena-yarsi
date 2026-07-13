<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin 1
        User::create([
            'id' => 1,
            'username' => 'admin1',
            'role_id' => 1,
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('sipena123'),
            'status' => 'active',
        ]);
        // Admin 2
        User::create([
            'id' => 2,
            'username' => 'admin2',
            'role_id' => 1,
            'email' => 'admin2@gmail.com',
            'password' => Hash::make('sipena123'),
            'status' => 'active',
        ]);

        // SDM Yayasan
        User::create([
            'id' => 3,
            'username' => 'sdm.yayasan',
            'pegawai_id' => 4,
            'role_id' => 2,
            'email' => 'sdmyayasan@gmail.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        // SDM Universitas
        User::create([
            'id' => 4,
            'username' => 'sdm.universitas',
            'pegawai_id' => 5,
            'role_id' => 3,
            'email' => 'sdmuniversitas@gmail.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        // Rektor
        User::create([
            'id' => 5,
            'username' => 'rektor',
            'role_id' => 4,
            'email' => 'rektor@gmail.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        // Staff / Annisa Putri
        User::create([
            'id' => 6,
            'username' => 'annisa.putri',
            'pegawai_id' => 2,
            'role_id' => 6,
            'email' => 'annisa@gmail.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        // Pimpinan / hilal.akbar
        User::create([
            'id' => 7,
            'username' => 'hilal.akbar',
            'pegawai_id' => 6,
            'role_id' => 5,
            'email' => 'akbar@gmail.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        // Staff / Agus Widayat
        User::create([
            'id' => 8,
            'username' => 'agus.widayat',
            'pegawai_id' => 3,
            'role_id' => 6,
            'email' => 'agus@gmail.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
    }
}