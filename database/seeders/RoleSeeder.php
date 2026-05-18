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
        Role::create(['id' => 1, 'name' => 'Admin']);
        Role::create(['id' => 2, 'name' => 'SDM Yayasan']);
        Role::create(['id' => 3, 'name' => 'SDM Universitas']);
        Role::create(['id' => 4, 'name' => 'Rektor']);
        Role::create(['id' => 5, 'name' => 'Pimpinan']);
        Role::create(['id' => 6, 'name' => 'Staff']);
        Role::create(['id' => 7, 'name' => 'Dosen']);
    }
}