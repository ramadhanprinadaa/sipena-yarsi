<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StatusPegawai;

class StatusPegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StatusPegawai::create([
            'id' => 1,
            'status' => 'Tetap',
        ]);
        StatusPegawai::create([
            'id' => 2,
            'status' => 'Kontrak',
        ]);
    }
}