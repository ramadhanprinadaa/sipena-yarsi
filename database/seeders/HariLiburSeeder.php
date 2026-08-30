<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\HariLibur;

class HariLiburSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HariLibur::create([
            'tanggal'           => '2026-05-12',
            'nama_hari_libur'   => 'Idul Adha',
            'jenis_hari_libur'  => 'Hari Libur Nasional',
            'keterangan'        => ''
        ]);
        HariLibur::create([
            'tanggal'           => '2026-05-15',
            'nama_hari_libur'   => 'Hari Tasyrik',
            'jenis_hari_libur'  => 'Hari Libur Nasional',
            'keterangan'        => ''
        ]);
        HariLibur::create([
            'tanggal'           => '2026-05-27',
            'nama_hari_libur'   => 'Hari Lahir Pancasila',
            'jenis_hari_libur'  => 'Hari Libur Nasional',
            'keterangan'        => ''
        ]);
    }
}