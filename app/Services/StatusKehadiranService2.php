<?php

namespace App\Services;

use App\Models\Cuti;
use App\Models\HariLibur;
use App\Models\Lembur;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class StatusKehadiranService2
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

    protected array $loadedMonths = [];
    protected array $lemburMap = [];
    protected array $cutiMap = [];
    protected array $hariLiburMap = [];

    /**
     * Funtion untuk load data lembur dan cuti dalam satu bulan dari file Excel.
     * Function ini memuat data satu bulan penuh saat mendeteksi bulan baru dari Excel.
     */
    public function loadDataBulan(string $tahunBulan): void
    {
        $startDate = Carbon::createFromFormat('Y-m', $tahunBulan)->startOfMonth()->toDateString();
        $endDate   = Carbon::createFromFormat('Y-m', $tahunBulan)->endOfMonth()->toDateString();

        // 1. Load Hari Libur dalam 1 bulan
        $libur = HariLibur::query()
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->pluck('id', 'tanggal')
            ->toArray();
        $this->hariLiburMap = array_merge($this->hariLiburMap, $libur);

        // 2. Load Lembur yang Disetujui dalam 1 bulan untuk SEMUA pegawai
        $lembur = Lembur::query()
            ->whereBetween('tanggal_lembur', [$startDate, $endDate])
            ->where('status', 'Selesai')
            ->get(['pegawai_id', 'tanggal_lembur']);

        foreach ($lembur as $l) {
            $this->lemburMap[$l->pegawai_id][$l->tanggal_lembur] = true;
        }

        // 3. Load Cuti yang Disetujui yang bersinggungan dengan bulan ini untuk SEMUA pegawai
        $cuti = Cuti::query()
            ->where('status', 'disetujui')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_mulai', [$startDate, $endDate])
                    ->orWhereBetween('tanggal_selesai', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->whereDate('tanggal_mulai', '<=', $startDate)
                            ->whereDate('tanggal_selesai', '>=', $endDate);
                    });
            })
            ->get(['pegawai_id', 'tanggal_mulai', 'tanggal_selesai']);

        foreach ($cuti as $c) {
            $batasMulai = max($c->tanggal_mulai, $startDate);
            $batasSelesai = min($c->tanggal_selesai, $endDate);

            $period = CarbonPeriod::create($batasMulai, $batasSelesai);
            foreach ($period as $date) {
                $tanggalString = $date->format('Y-m-d');
                $this->cutiMap[$c->pegawai_id][$tanggalString] = true;
            }
        }

        $this->loadedMonths[$tahunBulan] = true;
    }

    public function resolve(int $pegawaiId, string $tanggal, ?string $jamMasuk, ?string $jamKeluar, ?string $statusKehadiranExcel = null): ?int
    {
        // Deteksi "Tahun-Bulan" (Contoh: "2025-10")
        $tahunBulan = Carbon::parse($tanggal)->format('Y-m');

        // Jika data untuk bulan tersebut belum ada di memori, Load!
        if (!isset($this->loadedMonths[$tahunBulan])) {
            $this->loadDataBulan($tahunBulan);
        }

        // Get Hari Libur
        $isHariLibur = isset($this->hariLiburMap[$tanggal]) || Carbon::parse($tanggal)->isWeekend();

        // Cek Lembur di Hari Libur
        if ($isHariLibur) {
            if (isset($this->lemburMap[$pegawaiId][$tanggal])) {
                return self::LEMBUR;
            }
            return null;
        }

        if ($isHariLibur && isset($this->lemburMap[$pegawaiId][$tanggal])) {
            return self::LEMBUR;
        }

        // Cek jika tidak ada jam masuk dan keluar.
        if (!$jamMasuk && !$jamKeluar) {

            // Cek jika ada pengajuan Cuti.
            if (isset($this->cutiMap[$pegawaiId][$tanggal]) || $statusKehadiranExcel === 'CUTI') {
                return self::CUTI;
            }

            return self::TIDAK_HADIR_TANPA_KETERANGAN;
        }

        // Cek jika hanya absen 1x.
        if (!$jamMasuk || !$jamKeluar) {
            return self::TIDAK_HADIR_ABSEN_1X;
        }

        // Kalkulasi Total Jam Kerja Harian (Jika hadir)
        $masuk = Carbon::parse($jamMasuk);
        $keluar = Carbon::parse($jamKeluar);
        $totalJamKerja = $masuk->diffInMinutes($keluar) / 60;
        $totalMenitKerja = $masuk->diffInMinutes($keluar);

        // Cek jika jam kerja kurang dari 6 jam.
        if ($totalMenitKerja < 360) {       // < 6 jam (6 * 60)
            return self::TIDAK_HADIR_KURANG_JAM;
        }

        // Cek jika jam kerja kurang dari 8 jam.
        if ($totalMenitKerja < 480) {       // < 8 jam (8 * 60)
            return self::HADIR_KURANG_JAM;
        }

        return self::HADIR_NORMAL;
    }
}