<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Keluarga;

class KeluargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Keluarga::create([
            'pegawai_id'    => 51,
            'nama'          => 'Freyanashifa Jayawardana',
            'hubungan'      => 'Istri',
            'tempat_lahir'  => 'Yogyakarta',
            'tanggal_lahir' => '2006-02-13',
            'pekerjaan'     => 'Ibu Rumah Tangga',
            'no_telpon'     => '085710705934',
            'alamat'        => 'Jl. Legenda Wisata No. 57',
            'updated_by'    => 1,
        ]);
        Keluarga::create([
            'pegawai_id'    => 52,
            'nama'          => 'Yusuf',
            'hubungan'      => 'Suami',
            'tempat_lahir'  => 'Bandung',
            'tanggal_lahir' => '2003-09-30',
            'pekerjaan'     => 'Guru',
            'no_telpon'     => '0985710705933',
            'alamat'        => 'Jl. I Gusti Ngurah Rai No. 20',
            'updated_by'    => 1,
        ]);
        Keluarga::create([
            'pegawai_id'    => 52,
            'nama'          => 'Fathurrahman',
            'hubungan'      => 'Anak',
            'tempat_lahir'  => 'Bogor',
            'tanggal_lahir' => '2025-05-12',
            'pekerjaan'     => null,
            'no_telpon'     => null,
            'alamat'        => null,
            'updated_by'    => 1,
        ]);
    }
}