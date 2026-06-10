<?php

namespace Database\Seeders;

use App\Models\JenjangPendidikan;
use Illuminate\Database\Seeder;

class JenjangPendidikanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JenjangPendidikan::create([
            'id'     => 1,
            'kode'   => 'SD',
            'nama'   => 'Sekolah Dasar',
            'urutan' => 1,
        ]);
        JenjangPendidikan::create([
            'id'     => 2,
            'kode'   => 'SMP',
            'nama'   => 'Sekolah Menengah Pertama',
            'urutan' => 2,
        ]);
        JenjangPendidikan::create([
            'id'     => 3,
            'kode'   => 'SMA',
            'nama'   => 'Sekolah Menengah Atas',
            'urutan' => 3,
        ]);
        JenjangPendidikan::create([
            'id'     => 4,
            'kode'   => 'SMK',
            'nama'   => 'Sekolah Menengah Kejuruan',
            'urutan' => 3,
        ]);
        JenjangPendidikan::create([
            'id'     => 5,
            'kode'   => 'D1',
            'nama'   => 'Diploma I',
            'urutan' => 4,
        ]);
        JenjangPendidikan::create([
            'id'     => 6,
            'kode'   => 'D2',
            'nama'   => 'Diploma II',
            'urutan' => 5,
        ]);
        JenjangPendidikan::create([
            'id'     => 7,
            'kode'   => 'D3',
            'nama'   => 'Diploma III',
            'urutan' => 6,
        ]);
        JenjangPendidikan::create([
            'id'     => 8,
            'kode'   => 'D4',
            'nama'   => 'Diploma IV',
            'urutan' => 7,
        ]);
        JenjangPendidikan::create([
            'id'     => 9,
            'kode'   => 'S1',
            'nama'   => 'Sarjana',
            'urutan' => 8,
        ]);
        JenjangPendidikan::create([
            'id'     => 10,
            'kode'   => 'S2',
            'nama'   => 'Magister',
            'urutan' => 9,
        ]);
        JenjangPendidikan::create([
            'id'     => 11,
            'kode'   => 'S3',
            'nama'   => 'Doktor',
            'urutan' => 10,
        ]);
    }
}