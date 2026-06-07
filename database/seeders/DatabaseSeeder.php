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
            PegawaiSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            UnitSDMSeeder::class,
            UnitKerjaSeeder::class,
            StatusPegawaiSeeder::class,
            JenisPegawaiSeeder::class,
            KeluargaSeeder::class,
            // RekeningSeeder::class,
            HariLiburSeeder::class,
            StatusKehadiranSeeder::class,
            PresensiSeeder::class,
            JenisCutiSeeder::class,
        ]);
    }
}