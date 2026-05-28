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
                'pegawai_id' => 1,
                'email' => 'sdmyayasan@gmail.com',
                'password' => Hash::make('password')
            ],
            [
                'role_id' => 3,
                'username' => 'sdm.universitas',
                'pegawai_id' => 2,
                'email' => 'sdmuniversitas@gmail.com',
                'password' => Hash::make('password')
            ],
            [
                'role_id' => 4,
                'pegawai_id' => 3,
                'username' => 'taufiq.hakim',
                'email' => 'taufiq@gmail.com',
                'password' => Hash::make('password')
            ],
            [
                'role_id' => 5,
                'pegawai_id' => 4,
                'username' => 'ramadhan.prinada',
                'email' => 'ramadhanprinada@gmail.com',
                'password' => Hash::make('password')
            ],
            [
                'role_id' => 5,
                'pegawai_id' => 5,
                'username' => 'wily.ahmad',
                'email' => 'wilyahmad@gmail.com',
                'password' => Hash::make('password')
            ],
            [
                'role_id' => 5,
                'pegawai_id' => 6,
                'username' => 'umam.syafiul',
                'email' => 'umamsyafiul@gmail.com',
                'password' => Hash::make('password')
            ],
            [
                'role_id' => 6,
                'pegawai_id' => 7,
                'username' => 'rafly.eryan',
                'email' => 'raflyeryan@gmail.com',
                'password' => Hash::make('password')
            ],
            [
                'role_id' => 6,
                'pegawai_id' => 8,
                'username' => 'hilal.rizqi',
                'email' => 'hilalrizqi@gmail.com',
                'password' => Hash::make('password')
            ],
            [
                'role_id' => 6,
                'pegawai_id' => 9,
                'username' => 'isa.agiya',
                'email' => 'isagiyaa@gmail.com',
                'password' => Hash::make('password')
            ],
        ];

        foreach ($user as $key => $value) {
            User::create($value);
        }
    }
}
