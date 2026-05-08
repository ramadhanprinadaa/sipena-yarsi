<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UnitSdm;

class UnitSDMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UnitSdm::create(['name' => 'SDM Yayasan']);
        UnitSdm::create(['name' => 'SDM Universitas']);
    }
}