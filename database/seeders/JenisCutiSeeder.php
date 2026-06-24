<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JenisCuti;

class JenisCutiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JenisCuti::insert([
            [
                'id' => 1,
                'nama' => 'Cuti Tahunan',
                'minimal_masa_kerja_bulan' => 12,
                'minimal_hari_pengajuan' => 3,
                'maksimal_hari' => 2,
                'maksimal_hari_per_bulan' => 2,
                'memotong_saldo' => true,
                'butuh_surat_dokter' => false,
                'dihitung_per_jam' => false,
                'sekali_seumur_kerja' => false,
                'deskripsi' => 'Hak cuti tahunan bagi pegawai dengan masa kerja minimal 12 bulan berturut-turut.',
            ],
            [
                'id' => 2,
                'nama' => 'Cuti Besar',
                'minimal_masa_kerja_bulan' => 72, // 6 tahun
                'minimal_hari_pengajuan' => 3,
                'maksimal_hari' => 66,
                'maksimal_hari_per_bulan' => 3,
                'memotong_saldo' => false,
                'butuh_surat_dokter' => false,
                'dihitung_per_jam' => false,
                'sekali_seumur_kerja' => false,
                'deskripsi' => 'Hak cuti besar setelah masa kerja 6 tahun berturut-turut.',
            ],
            [
                'id' => 3,
                'nama' => 'Cuti Melahirkan',
                'minimal_masa_kerja_bulan' => null,
                'minimal_hari_pengajuan' => 3,
                'maksimal_hari' => 90,
                'maksimal_hari_per_bulan' => null,
                'memotong_saldo' => false,
                'butuh_surat_dokter' => true,
                'dihitung_per_jam' => false,
                'sekali_seumur_kerja' => false,
                'deskripsi' => 'Cuti melahirkan maksimal 3 bulan dan wajib melampirkan surat dokter.',
            ],
            [
                'id' => 4,
                'nama' => 'Izin Sakit',
                'minimal_masa_kerja_bulan' => null,
                'minimal_hari_pengajuan' => null,
                'maksimal_hari' => null,
                'maksimal_hari_per_bulan' => null,
                'memotong_saldo' => false,
                'butuh_surat_dokter' => true,
                'dihitung_per_jam' => false,
                'sekali_seumur_kerja' => false,
                'deskripsi' => 'Izin sakit dengan kewajiban melampirkan surat dokter.',
            ],
            [
                'id' => 5,
                'nama' => 'Izin di Jam Kerja',
                'minimal_masa_kerja_bulan' => null,
                'minimal_hari_pengajuan' => null,
                'maksimal_hari' => null,
                'maksimal_hari_per_bulan' => null,
                'memotong_saldo' => false,
                'butuh_surat_dokter' => false,
                'dihitung_per_jam' => true,
                'sekali_seumur_kerja' => false,
                'deskripsi' => 'Izin yang dihitung berdasarkan jam, bukan hari.',
            ],
            [
                'id' => 6,
                'nama' => 'Izin Menikah',
                'minimal_masa_kerja_bulan' => null,
                'minimal_hari_pengajuan' => 3,
                'maksimal_hari' => null,
                'maksimal_hari_per_bulan' => null,
                'memotong_saldo' => false,
                'butuh_surat_dokter' => false,
                'dihitung_per_jam' => false,
                'sekali_seumur_kerja' => true,
                'deskripsi' => 'Izin menikah sesuai ketentuan perusahaan dan UU Ketenagakerjaan.',
            ],
            [
                'id' => 7,
                'nama' => 'Izin Menikahkan Anak',
                'minimal_masa_kerja_bulan' => null,
                'minimal_hari_pengajuan' => null,
                'maksimal_hari' => null,
                'maksimal_hari_per_bulan' => null,
                'memotong_saldo' => false,
                'butuh_surat_dokter' => false,
                'dihitung_per_jam' => false,
                'sekali_seumur_kerja' => false,
                'deskripsi' => 'Izin khusus untuk menikahkan anak.',
            ],
            [
                'id' => 8,
                'nama' => 'Izin Mengkhitankan Anak',
                'minimal_masa_kerja_bulan' => null,
                'minimal_hari_pengajuan' => null,
                'maksimal_hari' => null,
                'maksimal_hari_per_bulan' => null,
                'memotong_saldo' => false,
                'butuh_surat_dokter' => false,
                'dihitung_per_jam' => false,
                'sekali_seumur_kerja' => false,
                'deskripsi' => 'Izin khusus untuk mengkhitankan anak.',
            ],
            [
                'id' => 9,
                'nama' => 'Izin Keluarga Meninggal',
                'minimal_masa_kerja_bulan' => null,
                'minimal_hari_pengajuan' => null,
                'maksimal_hari' => null,
                'maksimal_hari_per_bulan' => null,
                'memotong_saldo' => false,
                'butuh_surat_dokter' => false,
                'dihitung_per_jam' => false,
                'sekali_seumur_kerja' => false,
                'deskripsi' => 'Izin karena anggota keluarga meninggal dunia.',
            ],
            [
                'id' => 10,
                'nama' => 'Ibadah Haji',
                'minimal_masa_kerja_bulan' => null,
                'minimal_hari_pengajuan' => 3,
                'maksimal_hari' => null,
                'maksimal_hari_per_bulan' => null,
                'memotong_saldo' => false,
                'butuh_surat_dokter' => false,
                'dihitung_per_jam' => false,
                'sekali_seumur_kerja' => true,
                'deskripsi' => 'Izin ibadah haji yang hanya dapat digunakan satu kali selama menjadi pegawai.',
            ],
        ]);
    }
}
