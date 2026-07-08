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
        // Pegawai::factory()->count(50)->create();

        // Muhammad Ramadhan Prinada 1
        Pegawai::create([
            'unit_kerja_id'           => 5,
            'jenis_pegawai_id'        => 3,
            'status_pegawai_id'       => 1,
            'nip'                     => '1402022043',
            'ktp'                     => '3172033010030002',
            'npwp'                    => '3172033010030002',
            'nama'                    => 'Muhammad Ramadhan Prinada',
            'gelar_depan'             => 'Ir.',
            'gelar_belakang'          => 'S.Kom, M.Kom',
            'tempat_lahir'            => 'Jakarta',
            'tanggal_lahir'           => '2003-10-30',
            'tanggal_bergabung'       => '2022-09-09',
            'tanggal_habis_kontrak'   => null,
            'tanggal_pensiun'         => '2063-10-30',
            'jenis_kelamin'           => 'L',
            'alamat_ktp'              => 'Griya Alam Sentosa, Blok O 4 No. 8 Cileungsi, Bogor',
            'alamat_domisili'         => 'Griya Alam Sentosa, Blok O 4 No. 8 Cileungsi, Bogor',
            'no_telpon'               => '085710705935',
            'email_yarsi'             => 'ramadhanprinada@yarsi.ac.id',
            'status'                  => 'active',
        ]);

        // Annisa Putri 2
        Pegawai::create([
            'unit_kerja_id'           => 2,
            'jenis_pegawai_id'        => 4,
            'status_pegawai_id'       => 1,
            'nip'                     => '1502022045',
            'ktp'                     => '3172033010030003',
            'npwp'                    => '3172033010030003',
            'nama'                    => 'Annisa Putri Yuniar',
            'gelar_depan'             => null,
            'gelar_belakang'          => 'S.Pd',
            'tempat_lahir'            => 'Purworejo',
            'tanggal_lahir'           => '2004-06-12',
            'tanggal_bergabung'       => '2022-09-09',
            'tanggal_habis_kontrak'   => null,
            'tanggal_pensiun'         => '2064-06-12',
            'jenis_kelamin'           => 'P',
            'alamat_ktp'              => 'Jl. Jokowi 56 No. 34, Purworejo, Jawa Tengah',
            'alamat_domisili'         => 'Jl. I Gusti Ngurah Rai No. 20',
            'no_telpon'               => '085710705936',
            'email_yarsi'             => 'annisaputri@yarsi.ac.id',
            'status'                  => 'active',
        ]);

        // Agus Widayat 3
        Pegawai::create([
            'unit_kerja_id'             => 5,
            'jenis_pegawai_id'          => 2,
            'status_pegawai_id'         => 2,
            'nip'                       => '531142102011',
            'ktp'                       => '3171010101010004',
            'npwp'                      => '3171010101010004',
            'nama'                      => 'Agus Widayat',
            'gelar_depan'               => null,
            'gelar_belakang'            => null,
            'tempat_lahir'              => 'Jakarta',
            'tanggal_lahir'             => '2001-01-01',
            'tanggal_bergabung'         => '2024-01-01',
            'tanggal_habis_kontrak'     => '2027-01-01',
            'tanggal_pensiun'           => null,
            'jenis_kelamin'             => 'L',
            'alamat_ktp'                => 'Jl. Salemba Raya, Jakarta Pusat',
            'alamat_domisili'           => 'Jl. Salemba Raya, Jakarta Pusat',
            'no_telpon'                 => '081234567891',
            'email_yarsi'               => 'aguswidayat@yarsi.ac.id',
            'status'                    => 'active',
        ]);

        // SDM Yayasan 4
        Pegawai::create([
            'unit_kerja_id' => 8,
            'jenis_pegawai_id' => 3,
            'status_pegawai_id' => 1,
            'nip' => '199001012020011999',
            'ktp' => '3171010101900999',
            'npwp' => '091234567812999',
            'nama' => 'SDM Yayasan',
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
            'no_telpon' => '081234567999',
            'email_yarsi' => 'sdmyayasan@yarsi.ac.id',
            'status' => 'active',
        ]);

        // SDM Univesitas 5
        Pegawai::create([
            'unit_kerja_id' => 8,
            'jenis_pegawai_id' => 3,
            'status_pegawai_id' => 1,
            'nip' => '199001012020011888',
            'ktp' => '3171010101900888',
            'npwp' => '091234567812888',
            'nama' => 'SDM Universitas',
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
            'no_telpon' => '081234567888',
            'email_yarsi' => 'sdmuniversitas@yarsi.ac.id',
            'status' => 'active',
        ]);

        // Hilal Akbar 6
        Pegawai::create([
            'unit_kerja_id'             => 5,
            'jenis_pegawai_id'          => 2,
            'status_pegawai_id'         => 2,
            'nip'                       => '1402022023',
            'ktp'                       => '3172031011030002',
            'npwp'                      => null,
            'nama'                      => 'Hilal Rizqi Akbar',
            'gelar_depan'               => null,
            'gelar_belakang'            => null,
            'tempat_lahir'              => 'Jakarta',
            'tanggal_lahir'             => '2003-11-10',
            'tanggal_bergabung'         => '2022-09-12',
            'tanggal_habis_kontrak'     => '2032-09-12',
            'tanggal_pensiun'           => null,
            'jenis_kelamin'             => 'L',
            'alamat_ktp'                => 'Kelapa Gading, Jakarta Utara',
            'alamat_domisili'           => 'Kelapa Gading, Jakarta Utara',
            'no_telpon'                 => '081317425875',
            'email_yarsi'               => 'hilal.rizqi@yarsi.ac.id',
            'status'                    => 'active',
        ]);
    }
}