<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rekening;

class RekeningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rekening::create([
            'pegawai_id' => 51,
            'nama_bank' => 'BRI',
            'nomor_rekening' => '1234567890',
            'nama_rekening' => 'Muhammad Ramadhan Prinada',
        ]);
        Rekening::create([
            'pegawai_id' => 52,
            'nama_bank' => 'Mandiri',
            'nomor_rekening' => '0987654321',
            'nama_rekening' => 'Annisa Putri Yuniar',
        ]);
    }
}