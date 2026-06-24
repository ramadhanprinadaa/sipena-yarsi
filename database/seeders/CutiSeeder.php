<?php

namespace Database\Seeders;

use App\Models\Cuti;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CutiSeeder extends Seeder
{
    public function run(): void
    {
        // Hilal Akbar Cuti Tahunan
        Cuti::create([
            'pegawai_id'            => 6,
            'jenis_cuti_id'         => 1,
            'tanggal_pengajuan'     => '2026-05-01',
            'metode_potongan'       => null,
            'tanggal_mulai'         => '2026-05-05',
            'tanggal_selesai'       => '2026-05-06',
            'jam_mulai'             => null,
            'jam_selesai'           => null,
            'jumlah_hari_cuti'      => 2,
            'jumlah_jam'            => null,
            'saldo_cuti_sebelum'    => 12,
            'saldo_cuti_sesudah'    => 10,
            'dokumen_pendukung'     => null,
            'keterangan'            => null,
            'status'                => 'disetujui',
        ]);

        // Hilal Akbar Izin Sakit
        Cuti::create([
            'pegawai_id'            => 6,
            'jenis_cuti_id'         => 4,
            'tanggal_pengajuan'     => '2026-06-01',
            'metode_potongan'       => null,
            'tanggal_mulai'         => '2026-06-02',
            'tanggal_selesai'       => '2026-06-04',
            'jam_mulai'             => null,
            'jam_selesai'           => null,
            'jumlah_hari_cuti'      => 3,
            'jumlah_jam'            => null,
            'saldo_cuti_sebelum'    => null,
            'saldo_cuti_sesudah'    => null,
            'dokumen_pendukung'     => null,
            'keterangan'            => null,
            'status'                => 'disetujui',
        ]);

        // Hilal Akbar Izin Insidental
        Cuti::create([
            'pegawai_id'            => 6,
            'jenis_cuti_id'         => 5,
            'tanggal_pengajuan'     => '2026-06-23',
            'metode_potongan'       => null,
            'tanggal_mulai'         => '2026-06-23',
            'tanggal_selesai'       => '2026-06-23',
            'jam_mulai'             => null,
            'jam_selesai'           => null,
            'jumlah_hari_cuti'      => 1,
            'jumlah_jam'            => null,
            'saldo_cuti_sebelum'    => null,
            'saldo_cuti_sesudah'    => null,
            'dokumen_pendukung'     => null,
            'keterangan'            => null,
            'status'                => 'disetujui',
        ]);
    }
}