<?php

namespace Database\Seeders;

use App\Models\Lembur;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LemburSeeder extends Seeder
{
    public function run(): void
    {
        // Hari Kerja Normal (Hilal Akbar)
        Lembur::create([
            'pegawai_id'                => 6,
            'surat_perintah_lembur_id'  => null,
            'tanggal_lembur'            => '2026-06-24',
            'jenis_hari'                => 'Hari Kerja Normal',
            'jam_mulai'                 => '16:00:00',
            'jam_selesai'               => '18:00:00',
            'alasan_lembur'             => 'Akreditasi Fakultas',
            'status'                    => 'Selesai',
        ]);

        // Hari Libur Mingguan (Hilal Akbar)
        Lembur::create([
            'pegawai_id'                => 6,
            'surat_perintah_lembur_id'  => null,
            'tanggal_lembur'            => '2026-06-20',
            'jenis_hari'                => 'Hari Libur Mingguan',
            'jam_mulai'                 => '08:00:00',
            'jam_selesai'               => '14:00:00',
            'alasan_lembur'             => 'Rapat FTI',
            'status'                    => 'Selesai',
        ]);

        // Hari Libur Nasional (Hilal Akbar)
        Lembur::create([
            'pegawai_id'                => 6,
            'surat_perintah_lembur_id'  => null,
            'tanggal_lembur'            => '2026-05-27',
            'jenis_hari'                => 'Hari Libur Nasional',
            'jam_mulai'                 => '08:00:00',
            'jam_selesai'               => '14:00:00',
            'alasan_lembur'             => 'Ujian Basis Data',
            'status'                    => 'Selesai',
        ]);
    }
}