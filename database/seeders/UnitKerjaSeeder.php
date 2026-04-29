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
        UnitKerja::create(['name' => 'Yayasan', 'unit_sdm_id' => 1]); // unit kerja khusus pimpinan diluar universitas dengan atasan langsung Sekretariat Yayasan
        UnitKerja::create(['name' => 'Sekretariat Yayasan', 'unit_sdm_id' => 1]);
        UnitKerja::create(['name' => 'Preschool', 'unit_sdm_id' => 1]);
        UnitKerja::create(['name' => 'Klinik', 'unit_sdm_id' => 1]);
        UnitKerja::create(['name' => 'Masjid', 'unit_sdm_id' => 1]);

        UnitKerja::create(['name' => 'Universitas', 'unit_sdm_id' => 2]); // unit kerja khusus pimpinan dengan atasan rektor
        UnitKerja::create(['name' => 'Sekretariat Universitas', 'unit_sdm_id' => 2]);
        UnitKerja::create(['name' => 'Fakultas Kedokteran', 'unit_sdm_id' => 2]);
        UnitKerja::create(['name' => 'Fakultas Kedokteran Gigi', 'unit_sdm_id' => 2]);
        UnitKerja::create(['name' => 'Fakultas Hukum', 'unit_sdm_id' => 2]);
        UnitKerja::create(['name' => 'Fakultas Ekonomi', 'unit_sdm_id' => 2]);
        UnitKerja::create(['name' => 'Fakultas Teknologi Informasi', 'unit_sdm_id' => 2]);
        UnitKerja::create(['name' => 'Fakultas Psikologi', 'unit_sdm_id' => 2]);
    }
}