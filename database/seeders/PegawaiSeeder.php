<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pegawai = [
            [
                'id' => 1,
                'unit_kerja_id' => 10,
                'jenis_pegawai_id' => 3,
                'status_pegawai_id' => 1,
                'nip' => '199001012020011001',
                'ktp' => '3171010101900001',
                'npwp' => '091234567812345',
                'nama' => 'Ramadhan Prinada',
                'gelar_depan' => null,
                'gelar_belakang' => 'M.Kom',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1990-01-01',
                'tanggal_bergabung' => '2020-01-01',
                'tanggal_habis_kontrak' => null,
                'tanggal_pensiun' => '2055-01-01',
                'jenis_kelamin' => 'L',
                'alamat_ktp' => 'Jl. Letjen Suprapto, Cempaka Putih, Jakarta Pusat',
                'alamat_domisili' => 'Jl. Letjen Suprapto, Cempaka Putih, Jakarta Pusat',
                'no_telpon' => '081234567890',
                'email_yarsi' => 'ramadhanprinada@yarsi.ac.id',
                'status' => 'active',
            ],
            [
                'id' => 2,
                'unit_kerja_id' => 10,
                'jenis_pegawai_id' => 2,
                'status_pegawai_id' => 2,
                'nip' => '200101012024011001',
                'ktp' => '3171010101010002',
                'npwp' => '092345678912345',
                'nama' => 'Rafly Eryan',
                'gelar_depan' => null,
                'gelar_belakang' => 'S.Kom',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '2001-01-01',
                'tanggal_bergabung' => '2024-01-01',
                'tanggal_habis_kontrak' => '2027-01-01',
                'tanggal_pensiun' => null,
                'jenis_kelamin' => 'L',
                'alamat_ktp' => 'Jl. Salemba Raya, Jakarta Pusat',
                'alamat_domisili' => 'Jl. Salemba Raya, Jakarta Pusat',
                'no_telpon' => '081234567891',
                'email_yarsi' => 'raflyeryan@yarsi.ac.id',
                'status' => 'active',
            ],
        ];

        Pegawai::unguarded(function () use ($pegawai) {
            foreach ($pegawai as $data) {
                Pegawai::updateOrCreate(
                    ['id' => $data['id']],
                    $data
                );
            }
        });
    }
}
