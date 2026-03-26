<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'SDM Universitas']);
        Role::create(['name' => 'Pegawai Tendik']);
        Role::create(['name' => 'Pegawai Dosen']);
        Role::create(['name' => 'Tamu']);
    }
}
