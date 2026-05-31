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
        Pegawai::factory()->count(50)->create();

        // Muhammad Ramadhan Prinada 51
        Pegawai::create([
            'unit_kerja_id'           => 10,
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

        // Annisa Putri 52
        Pegawai::create([
            'unit_kerja_id'           => 2,
            'jenis_pegawai_id'        => 4,
            'status_pegawai_id'       => 1,
            'nip'                     => '1402022044',
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

        // Rafly Eryan Azis 53
        Pegawai::create([
            'unit_kerja_id'             => 10,
            'jenis_pegawai_id'          => 2,
            'status_pegawai_id'         => 2,
            'nip'                       => '200101012024011001',
            'ktp'                       => '3171010101010002',
            'npwp'                      => '092345678912345',
            'nama'                      => 'Rafly Eryan',
            'gelar_depan'               => null,
            'gelar_belakang'            => 'S.Kom',
            'tempat_lahir'              => 'Jakarta',
            'tanggal_lahir'             => '2001-01-01',
            'tanggal_bergabung'         => '2024-01-01',
            'tanggal_habis_kontrak'     => '2027-01-01',
            'tanggal_pensiun'           => null,
            'jenis_kelamin'             => 'L',
            'alamat_ktp'                => 'Jl. Salemba Raya, Jakarta Pusat',
            'alamat_domisili'           => 'Jl. Salemba Raya, Jakarta Pusat',
            'no_telpon'                 => '081234567891',
            'email_yarsi'               => 'raflyeryan@yarsi.ac.id',
            'status'                    => 'active',
        ]);

        // Agus Widayat 54
        Pegawai::create([
            'unit_kerja_id'             => 10,
            'jenis_pegawai_id'          => 2,
            'status_pegawai_id'         => 1,
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

        // Hilal Rizqi Akbar 55
        Pegawai::create([
            'unit_kerja_id'             => 11,
            'jenis_pegawai_id'          => 3,
            'status_pegawai_id'         => 2,
            'nip'                       => '200101012024011007',
            'ktp'                       => '3171010101010007',
            'npwp'                      => '092345678912347',
            'nama'                      => 'Hilal Rizqi Akbar',
            'gelar_depan'               => null,
            'gelar_belakang'            => 'S.Kom',
            'tempat_lahir'              => 'Jakarta',
            'tanggal_lahir'             => '2001-01-01',
            'tanggal_bergabung'         => '2024-01-01',
            'tanggal_habis_kontrak'     => '2027-01-01',
            'tanggal_pensiun'           => null,
            'jenis_kelamin'             => 'L',
            'alamat_ktp'                => 'Jl. Salemba Raya, Jakarta Pusat',
            'alamat_domisili'           => 'Jl. Salemba Raya, Jakarta Pusat',
            'no_telpon'                 => '081234567891',
            'email_yarsi'               => 'hilalrizqi@yarsi.ac.id',
            'status'                    => 'active',
        ]);

        // SDM Yayasan 56
        Pegawai::create([
            'unit_kerja_id' => 1,
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

        // SDM Univesitas 57
        Pegawai::create([
            'unit_kerja_id' => 5,
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

        // Taufiq Hakim 58
        Pegawai::create([
            'unit_kerja_id' => 10,
            'jenis_pegawai_id' => 3,
            'status_pegawai_id' => 1,
            'nip' => '199001012020011333',
            'ktp' => '3171010101900333',
            'npwp' => '091234567812333',
            'nama' => 'Taufiq Hakim',
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
            'no_telpon' => '081234567333',
            'email_yarsi' => 'taufiq@yarsi.ac.id',
            'status' => 'active',
        ]);

        // Wily Ahmad 59
        Pegawai::create([
            'unit_kerja_id' => 11,
            'jenis_pegawai_id' => 3,
            'status_pegawai_id' => 1,
            'nip' => '199001012020011222',
            'ktp' => '3171010101900222',
            'npwp' => '091234567812222',
            'nama' => 'Wily Ahmad',
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
            'no_telpon' => '081234567222',
            'email_yarsi' => 'wilyahmad@yarsi.ac.id',
            'status' => 'active',
        ]);

        // Umam Syafiul 60
        Pegawai::create([
            'unit_kerja_id' => 2,
            'jenis_pegawai_id' => 3,
            'status_pegawai_id' => 2,
            'nip' => '200101012024011555',
            'ktp' => '3171010101010555',
            'npwp' => '092345678912555',
            'nama' => 'Umam Syafiul',
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
            'no_telpon' => '081234567555',
            'email_yarsi' => 'umamsyafiul@yarsi.ac.id',
            'status' => 'active',
        ]);
    }
}