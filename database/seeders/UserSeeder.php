<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = [
            [
                'id_role' => 1,
                'username' => 'super.admin',
                'email' => 'superadmin@gmail.com',
                'password' => bcrypt('12345678')
            ],
            [
                'id_role' => 2,
                'username' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('12345678')
            ],
            [
                'id_role' => 3,
                'username' => 'sdm.universitas',
                'email' => 'yarsisdm@gmail.com',
                'password' => bcrypt('12345678')
            ],
            [
                'id_role' => 4,
                'username' => 'rafly.eryan',
                'email' => 'raflyeryan@gmail.com',
                'password' => bcrypt('12345678')
            ],
            [
                // 'id_role' => 4,
                'username' => 'hilal.akbar',
                'email' => 'hilalakbar@gmail.com',
                'password' => bcrypt('12345678')
            ],
        ];

        foreach ($user as $key => $value){
            User::create($value);
        }
    }
}