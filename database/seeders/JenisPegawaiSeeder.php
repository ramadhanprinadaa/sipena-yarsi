<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JenisPegawai;

class JenisPegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JenisPegawai::create([
            'id' => 1,
            'jenis' => 'Tenaga Non Kependidikan',
        ]);
        JenisPegawai::create([
            'id' => 2,
            'jenis' => 'Tenaga Kependidikan',
        ]);
        JenisPegawai::create([
            'id' => 3,
            'jenis' => 'Tenaga Pendidik',
        ]);
        JenisPegawai::create([
            'id' => 4,
            'jenis' => 'Guru',
        ]);
        JenisPegawai::create([
            'id' => 5,
            'jenis' => 'Dokter',
        ]);
        JenisPegawai::create([
            'id' => 6,
            'jenis' => 'Perawat',
        ]);
        JenisPegawai::create([
            'id' => 7,
            'jenis' => 'Helper',
        ]);
    }
}