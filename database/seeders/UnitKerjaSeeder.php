<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UnitKerja;

class UnitKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UnitKerja::create(['id' => 1, 'name' => 'Sekretariat Yayasan', 'unit_sdm_id' => 1]);
        UnitKerja::create(['id' => 2, 'name' => 'Preschool', 'unit_sdm_id' => 1]);
        UnitKerja::create(['id' => 3, 'name' => 'Klinik', 'unit_sdm_id' => 1]);
        UnitKerja::create(['id' => 4, 'name' => 'Masjid', 'unit_sdm_id' => 1]);

        UnitKerja::create(['id' => 5, 'name' => 'Sekretariat Universitas', 'unit_sdm_id' => 2]);
        UnitKerja::create(['id' => 6, 'name' => 'Fakultas Kedokteran', 'unit_sdm_id' => 2]);
        UnitKerja::create(['id' => 7, 'name' => 'Fakultas Kedokteran Gigi', 'unit_sdm_id' => 2]);
        UnitKerja::create(['id' => 8, 'name' => 'Fakultas Hukum', 'unit_sdm_id' => 2]);
        UnitKerja::create(['id' => 9, 'name' => 'Fakultas Ekonomi', 'unit_sdm_id' => 2]);
        UnitKerja::create(['id' => 10, 'name' => 'Fakultas Teknologi Informasi', 'unit_sdm_id' => 2]);
        UnitKerja::create(['id' => 11, 'name' => 'Fakultas Psikologi', 'unit_sdm_id' => 2]);
    }
}