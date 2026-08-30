<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StatusKehadiran;

class StatusKehadiranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StatusKehadiran::create([
            'id'       => 1,
            'kondisi'  => 'Absen 2x & ≥ 8 Jam',
            'status'   => 'Hadir Normal',
            'warna'    => '#22C55E',
        ]);
        StatusKehadiran::create([
            'id'      => 2,
            'kondisi' => 'Absen 2x & 6 - 7,59 Jam',
            'status'  => 'Hadir (Kurang Jam)',
            'warna'   => '#3B82F6',
        ]);
        StatusKehadiran::create([
            'id'      => 3,
            'kondisi' => 'Absen 2x & < 6 Jam',
            'status'  => 'Tidak Hadir (Kurang Jam)',
            'warna'   => '#EAB308',
        ]);
        StatusKehadiran::create([
            'id'      => 4,
            'kondisi' => 'Absen 1x',
            'status'  => 'Tidak Hadir (Absen 1x)',
            'warna'   => '#EF4444',
        ]);
        StatusKehadiran::create([
            'id'      => 5,
            'kondisi' => 'Tidak Absen & Tanpa Ket.',
            'status'  => 'Tidak Hadir (Tidak Absen)',
            'warna'   => '#B91C1C',
        ]);
        StatusKehadiran::create([
            'id'      => 6,
            'kondisi' => 'Izin',
            'status'  => 'Izin',
            'warna'   => '#9333EA',
        ]);
        StatusKehadiran::create([
            'id'      => 7,
            'kondisi' => 'Sakit',
            'status'  => 'Sakit',
            'warna'   => '#7C3AED',
        ]);
        StatusKehadiran::create([
            'id'      => 8,
            'kondisi' => 'Cuti',
            'status'  => 'Cuti',
            'warna'   => '#8B5CF6',
        ]);
        StatusKehadiran::create([
            'id'      => 9,
            'kondisi' => 'Hari Libur Tapi Masuk & Absen 2x & >= 5 Jam',
            'status'  => 'Lembur',
            'warna'   => '#CF11B6',
        ]);
    }
}