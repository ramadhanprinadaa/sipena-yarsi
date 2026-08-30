<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\Cuti\AddCuti;
use App\Models\Cuti;
use App\Models\JenisCuti;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionMethod;
use Tests\TestCase;

class CutiQuotaYear7Test extends TestCase
{
    use RefreshDatabase;

    public function test_izin_sakit_tidak_boleh_melebihi_kuota_tahun_ke_7(): void
    {
        $this->seed(\Database\Seeders\JenisCutiSeeder::class);

        $joinDate = now()->subYears(7)->subMonths(1);
        $pegawai = Pegawai::factory()->create([
            'tanggal_bergabung' => $joinDate->toDateString(),
        ]);

        $serviceYearStart = $joinDate->copy()->addYears(7)->startOfDay();
        $existingStart = $serviceYearStart->copy()->next(Carbon::MONDAY)->startOfDay();

        Cuti::create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti_id' => 4,
            'tanggal_pengajuan' => now()->toDateString(),
            'tanggal_mulai' => $existingStart->toDateString(),
            'tanggal_selesai' => $existingStart->copy()->addDays(32)->toDateString(),
            'jumlah_hari_cuti' => 33,
            'metode_potongan' => 'potong_cuti',
            'status' => 'Disetujui',
            'keterangan' => 'Test kuota cuti besar',
        ]);

        $component = new AddCuti();
        $component->tanggal_mulai = $existingStart->copy()->addDays(1)->toDateString();
        $component->tanggal_selesai = $existingStart->copy()->addDays(10)->toDateString();
        $component->metode_potongan = 'potong_cuti';

        $jenisCuti = JenisCuti::find(4);

        $method = new ReflectionMethod(AddCuti::class, 'passesBusinessRules');
        $method->setAccessible(true);

        $result = $method->invoke($component, $pegawai, $jenisCuti, 10, null);

        $this->assertFalse($result, 'Pengajuan izin sakit pada tahun ke-7 harus ditolak bila kuota tahun 33 hari sudah habis.');
        $this->assertTrue($component->getErrorBag()->has('tanggal_selesai'));
    }
}
