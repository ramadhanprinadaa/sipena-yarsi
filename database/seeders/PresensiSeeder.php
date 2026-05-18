<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Presensi;

class PresensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Presensi::create([
            'pegawai_nip' => '1402022043',
            'import_presensi_id' => null,
            'tanggal' => '2023-05-11',
            'jam_masuk' => '09:00:00',
            'jam_keluar' => '16:00:00',
            'status_kehadiran_id' => 1,
            'keterangan' => null,
            'updated_by' => 1,
        ]);
    }
}