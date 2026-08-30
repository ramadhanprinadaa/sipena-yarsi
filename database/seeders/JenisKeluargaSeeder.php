<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JenisKeluarga;

class JenisKeluargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JenisKeluarga::create([
            'id' => 1,
            'jenis' => 'Ayah'
        ]);
        JenisKeluarga::create([
            'id' => 2,
            'jenis' => 'Ibu'
        ]);
        JenisKeluarga::create([
            'id' => 3,
            'jenis' => 'Anak'
        ]);
        JenisKeluarga::create([
            'id' => 4,
            'jenis' => 'Istri'
        ]);
        JenisKeluarga::create([
            'id' => 5,
            'jenis' => 'Suami'
        ]);
        JenisKeluarga::create([
            'id' => 6,
            'jenis' => 'Saudara Laki-Laki'
        ]);
        JenisKeluarga::create([
            'id' => 7,
            'jenis' => 'Saudara Perempuan'
        ]);
        JenisKeluarga::create([
            'id' => 8,
            'jenis' => 'Mertua'
        ]);
    }
}
