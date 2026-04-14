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
                'username' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password')
            ],
            [
                'role_id' => 2,
                'username' => 'SDM_Yayasan',
                'email' => 'sdmyayasan@gmail.com',
                'password' => Hash::make('password')
            ],
            [
                'role_id' => 3,
                'username' => 'SDM_Universitas',
                'email' => 'sdmuniversitas@gmail.com',
                'password' => Hash::make('password')
            ],
            [
                'role_id' => 4,
                'pegawai_id' => 1,
                'username' => 'Ramadhan_Prinada',
                'email' => 'ramadhanprinada@gmail.com',
                'password' => Hash::make('password')
            ],
            [
                'role_id' => 5,
                'pegawai_id' => 2,
                'username' => 'Rafly_Eryan',
                'email' => 'raflyeryan@gmail.com',
                'password' => Hash::make('password')
            ],
            [
                'role_id' => 6,
                'username' => 'Hilal_Akbar',
                'email' => 'hilalakbar@gmail.com',
                'password' => Hash::make('password')
            ],
            [
                // 'role_id' => 7,
                'username' => 'Wily_Ahmad',
                'email' => 'wilyahmad@gmail.com',
                'password' => Hash::make('password')
            ],
        ];

        foreach ($user as $key => $value){
            User::create($value);
        }
    }
}