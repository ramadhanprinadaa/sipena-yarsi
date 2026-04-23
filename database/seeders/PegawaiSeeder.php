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
        Pegawai::factory()->create([
            'nama' => 'Muhammad Ramadhan Prinada',
            'email_yarsi' => 'muhammadprinada@yarsi.ac.id',
            'jenis_kelamin' => 'laki-laki',
        ]);
        Pegawai::factory()->create([
            'nama' => 'Rafly Eryan Azis',
            'email_yarsi' => 'raflyeryan@yarsi.ac.id',
            'jenis_kelamin' => 'laki-laki',
        ]);
        Pegawai::factory()->create([
            'nama' => 'Hilal Rizqi Akbar',
            'email_yarsi' => 'lalbar@yarsi.ac.id',
            'jenis_kelamin' => 'laki-laki',
        ]);
        Pegawai::factory()->create([
            'nama' => 'Wily Ahmad Fauzan',
            'email_yarsi' => 'wilyfauzan@yarsi.ac.id',
            'jenis_kelamin' => 'laki-laki',
        ]);
        Pegawai::factory()->create([
            'nama' => 'Hafizh Vito Pratomo',
            'email_yarsi' => 'vitoo@yarsi.ac.id',
            'jenis_kelamin' => 'laki-laki',
        ]);
        Pegawai::factory()->create([
            'nama' => 'Alvin Dimas Lunardi',
            'email_yarsi' => 'alvin@yarsi.ac.id',
            'jenis_kelamin' => 'laki-laki',
        ]);
        Pegawai::factory()->create([
            'nama' => 'Muhammad Arya Kusuma',
            'email_yarsi' => 'arya@yarsi.ac.id',
            'jenis_kelamin' => 'laki-laki',
        ]);
        Pegawai::factory()->create([
            'nama' => 'Ahmad Bahaudin Alghozi',
            'email_yarsi' => 'ghozi@yarsi.ac.id',
            'jenis_kelamin' => 'laki-laki',
        ]);
        Pegawai::factory()->create([
            'nama' => 'Afjar Maulana',
            'email_yarsi' => 'afjar@yarsi.ac.id',
            'jenis_kelamin' => 'laki-laki',
        ]);
        Pegawai::factory()->create([
            'nama' => 'Kiki Aimar Wicaksana',
            'email_yarsi' => 'kiki@yarsi.ac.id',
            'jenis_kelamin' => 'laki-laki',
        ]);
        Pegawai::factory()->create([
            'nama' => 'Muhammad Syafiul Umam',
            'email_yarsi' => 'umam@yarsi.ac.id',
            'jenis_kelamin' => 'laki-laki',
        ]);
    }
}