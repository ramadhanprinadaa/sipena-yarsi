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
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'SDM Yayasan']);
        Role::create(['name' => 'SDM Universitas']);
        Role::create(['name' => 'Rektor']);
        Role::create(['name' => 'Pimpinan']);
        Role::create(['name' => 'Staff']);
        Role::create(['name' => 'Tendik']);
        Role::create(['name' => 'Dosen']);
    }
}