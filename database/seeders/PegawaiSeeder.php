<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pegawai;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pegawai::create([
            'nip' => '123456789',
            'ktp' => '987654321',
            'npwp' => '1234567890',
            'nama' => 'Muhammad Ramadhan Prinada',
            'gelar_depan' => 'Ir.',
            'gelar_belakang' => 'S.Kom, M.Kom',
            'tanggal_lahir' => '2003-10-30',
            'tempat_lahir' => 'Jakarta',
            'tanggal_pensiun' => '2073-10-30',
            'alamat_ktp' => 'Jl. Merdeka No. 1, Jakarta',
            'alamat_domisili' => 'Jl. Merdeka No. 1, Jakarta',
            'no_telepon' => '085710705935',
            'status' => 'active'
        ]);

        Pegawai::create([
            'nip' => '987654321',
            'ktp' => '123456789',
            'npwp' => '0987654321',
            'nama' => 'Rafly Eryan Azis',
            'gelar_depan' => 'Prof.',
            'gelar_belakang' => 'S.Kom',
            'tanggal_lahir' => '2004-05-15',
            'tempat_lahir' => 'Bandung',
            'tanggal_pensiun' => '2064-05-15',
            'alamat_ktp' => 'Jl. Sudirman No. 2, Karawang',
            'alamat_domisili' => 'Jl. Sudirman No. 2, Karawang',
            'no_telepon' => '081234567890',
            'status' => 'active'
        ]);
    }
}