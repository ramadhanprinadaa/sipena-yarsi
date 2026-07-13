<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            UnitSDMSeeder::class,
            UnitKerjaSeeder::class,
            StatusPegawaiSeeder::class,
            JenisPegawaiSeeder::class,
            StatusKehadiranSeeder::class,
            JenisCutiSeeder::class,
            JenisKeluargaSeeder::class,
            JenjangPendidikanSeeder::class,

            // Comment Jika Ingin Ke Production
            PegawaiSeeder::class,
            PresensiSeeder::class,
            HariLiburSeeder::class,
            LemburSeeder::class,
            CutiSeeder::class,
        ]);
    }
}