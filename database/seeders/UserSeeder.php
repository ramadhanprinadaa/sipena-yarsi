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
                'role_id' => 4,
                'pegawai_id' => 1,
                'username' => 'ramadhanprinada',
                'email' => 'ramadhanprinada@gmail.com',
                'password' => Hash::make('password')
            ],
        ];

        foreach ($user as $key => $value) {
            User::create($value);
        }
    }
}
