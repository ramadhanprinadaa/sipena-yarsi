<?php

namespace App\Services;

// use App\Models\Cuti;
// use App\Models\Izin;
// use App\Models\Sakit;
use App\Models\HariLibur;
use App\Models\Lembur;
use Carbon\Carbon;

class StatusKehadiranService
{
    public const HADIR_NORMAL = 1;
    public const HADIR_KURANG_JAM = 2;
    public const TIDAK_HADIR_KURANG_JAM = 3;
    public const TIDAK_HADIR_ABSEN_1X = 4;
    public const TIDAK_HADIR_TANPA_KETERANGAN = 5;
    public const IZIN = 6;
    public const SAKIT = 7;
    public const CUTI = 8;
    public const LEMBUR = 9;

    public function resolve(
        string $pegawaiNip,
        string $tanggal,
        ?string $attendanceStatus,
        ?string $jamMasuk,
        ?string $jamKeluar,
        ?bool $isHariLibur = null,
    ): int {

        if ($isHariLibur === null) {
            $isHariLibur = $this->cekHariLibur($tanggal);
        }

        // Cek Lembur di Hari Libur
        if ($isHariLibur && $this->isLemburHariLibur($pegawaiNip, $tanggal)) {
            return self::LEMBUR;
        }

        // 1. Jika tidak ada jam masuk dan jam keluar sama sekali
        if (!$jamMasuk && !$jamKeluar) {
            // if ($this->isCuti($pegawaiNip, $tanggal, $jamMasuk, $jamKeluar, $attendanceStatus)) return self::CUTI;
            // if ($this->isSakit($pegawaiNip, $tanggal)) return self::SAKIT;
            // if ($this->isIzin($pegawaiNip, $tanggal)) return self::IZIN;

            return self::TIDAK_HADIR_TANPA_KETERANGAN;
        }

        // 2. Jika hanya mengisi salah satu (hanya jam masuk atau hanya jam keluar)
        if (!$jamMasuk || !$jamKeluar) {
            return self::TIDAK_HADIR_ABSEN_1X;
        }

        // 3. Jika jam masuk dan jam keluar lengkap, hitung total jam kerja (contoh batasan standar: 7.5 jam)
        $masuk = Carbon::parse($jamMasuk);
        $keluar = Carbon::parse($jamKeluar);
        $totalJamKerja = $masuk->diffInMinutes($keluar) / 60;

        if ($totalJamKerja < 6) {
            return self::TIDAK_HADIR_KURANG_JAM;
        }

        if ($totalJamKerja < 7.99) {
            return self::HADIR_KURANG_JAM;
        }

        return self::HADIR_NORMAL;
    }

    protected function isLemburHariLibur(string $pegawaiNip, string $tanggal): bool
    {
        return Lembur::query()
            ->whereHas('pegawai', function ($query) use ($pegawaiNip) {
                $query->where('nip', $pegawaiNip);
            })
            ->whereDate('tanggal_lembur', $tanggal)
            ->whereIn('jenis_hari', ['Hari Libur', 'Libur Nasional'])
            ->where('status', 'Disetujui')
            ->exists();
    }

    protected function cekHariLibur(string $tanggal): bool
    {
        // Cek Weekend (Sabtu / Minggu)
        if (Carbon::parse($tanggal)->isWeekend()) {
            return true;
        }

        // Cek Database Hari Libur Nasional (Hanya 1 query, aman untuk request satuan)
        return HariLibur::whereDate('tanggal', $tanggal)->exists();
    }

    // protected function isIzin(string $pegawaiNip, string $tanggal): bool
    // {
    //     return Izin::query()->where('pegawai_nip', $pegawaiNip)->whereDate('tanggal', $tanggal)->exists();
    // }

    // protected function isSakit(string $pegawaiNip, string $tanggal): bool
    // {
    //     return Sakit::query()->where('pegawai_nip', $pegawaiNip)->whereDate('tanggal', $tanggal)->exists();
    // }

    // protected function isCuti(string $pegawaiNip, string $tanggal, ?string $jamMasuk, ?string $jamKeluar, ?string $attendanceStatus): bool
    // {
    //     return Cuti::query()
    //         ->where('pegawai_nip', $pegawaiNip)
    //         ->whereDate('tanggal_mulai', '<=', $tanggal)
    //         ->whereDate('tanggal_selesai', '>=', $tanggal)
    //         ->exists();
    // }

    protected function isHariLibur($tanggal): bool
    {
        return false;
    }
}