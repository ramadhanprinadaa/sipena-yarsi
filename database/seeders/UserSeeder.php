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
        $user = [
            [
                'role_id' => 1,
                'username' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password')
            ],
            [
                'role_id' => 2,
                'username' => 'sdm.yayasan',
                'email' => 'sdmyayasan@gmail.com',
                'password' => Hash::make('password')
            ],
            [
                'role_id' => 3,
                'username' => 'sdm.universitas',
                'email' => 'sdmuniversitas@gmail.com',
                'password' => Hash::make('password')
            ],
            [
                'role_id' => 5,
                'pegawai_id' => 51,
                'username' => 'ramadhan.prinada',
                'email' => 'ramadhanprinada@gmail.com',
                'password' => Hash::make('password')
            ],
            [
                'role_id' => 7,
                'pegawai_id' => 52,
                'username' => 'annisa.putri',
                'email' => 'annisaputri@gmail.com',
                'password' => Hash::make('password')
            ],
            [
                'role_id' => 6,
                'pegawai_id' => 53,
                'username' => 'rafly.eryan',
                'email' => 'raflyeryan@gmail.com',
                'password' => Hash::make('password')
            ],
        ];

        foreach ($user as $key => $value) {
            User::create($value);
        }
    }
}