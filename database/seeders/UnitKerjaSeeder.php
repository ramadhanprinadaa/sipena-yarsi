<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnitKerja;

class UnitKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UnitKerja::create(['id' => 1, 'name' => 'Fakultas Kedokteran', 'unit_sdm_id' => 2]);
        UnitKerja::create(['id' => 2, 'name' => 'Fakultas Kedokteran Gigi', 'unit_sdm_id' => 2]);
        UnitKerja::create(['id' => 3, 'name' => 'Fakultas Ekonomi', 'unit_sdm_id' => 2]);
        UnitKerja::create(['id' => 4, 'name' => 'Fakultas Hukum', 'unit_sdm_id' => 2]);
        UnitKerja::create(['id' => 5, 'name' => 'Fakultas Teknologi Informasi', 'unit_sdm_id' => 2]);
        UnitKerja::create(['id' => 6, 'name' => 'Fakultas Psikologi', 'unit_sdm_id' => 2]);
        UnitKerja::create(['id' => 7, 'name' => 'Fakultas Pascasarjana', 'unit_sdm_id' => 2]);

        UnitKerja::create(['id' => 8, 'name' => 'Sekretariat', 'unit_sdm_id' => 1]);
        UnitKerja::create(['id' => 9, 'name' => 'Klinik', 'unit_sdm_id' => 1]);
        UnitKerja::create(['id' => 10, 'name' => 'Masjid', 'unit_sdm_id' => 1]);
        UnitKerja::create(['id' => 11, 'name' => 'Preschool', 'unit_sdm_id' => 1]);
        UnitKerja::create(['id' => 12, 'name' => 'Stem Cel', 'unit_sdm_id' => 1]);
        UnitKerja::create(['id' => 13, 'name' => 'Proyek', 'unit_sdm_id' => 1]);
        UnitKerja::create(['id' => 14, 'name' => 'Lemlit', 'unit_sdm_id' => 1]);
        UnitKerja::create(['id' => 15, 'name' => 'Perpustakaan', 'unit_sdm_id' => 1]);
        UnitKerja::create(['id' => 16, 'name' => 'Layanan Terpadu', 'unit_sdm_id' => 1]);
        UnitKerja::create(['id' => 17, 'name' => 'PJJ', 'unit_sdm_id' => 1]);
        UnitKerja::create(['id' => 18, 'name' => 'Registrar', 'unit_sdm_id' => 1]);
    }
}