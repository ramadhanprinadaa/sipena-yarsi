<?php

namespace App\Services;

use App\Models\Cuti;
use App\Models\HariLibur;
use App\Models\Lembur;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

/**
 * Service untuk menghitung rekapitulasi presensi pegawai dalam suatu periode.
 *
 * Aturan jam kerja:
 * - Hari biasa   : maks. 8 jam (480 menit), kelebihan tidak dihitung jam kerja reguler.
 * - Hari libur   : seluruh jam kerja dihitung sebagai lembur, bukan jam kerja reguler.
 *
 * Aturan lembur:
 * - Hari biasa   : lembur dihitung dari jam 16.00, maks. 2 jam (jam 16.00–18.00).
 * - Hari libur   : seluruh jam hadir dihitung lembur, maks. 5 jam (300 menit).
 *
 * Aturan kategori cuti (berdasarkan JenisCuti):
 * - Cuti Tahunan, Cuti Besar, Cuti Melahirkan, Ibadah Haji → kategori "cuti"
 * - Izin Sakit                                              → kategori "sakit"
 * - Selainnya                                               → kategori "izin"
 */

class RekapitulasiPresensiService
{
    // Inisialisasi Variable Konstan

    // ID JenisCuti yang masuk kategori "Cuti" dalam rekapitulasi
    private const JENIS_CUTI_IDS     = [1, 2, 3, 10];   // Tahunan, Besar, Melahirkan, Haji
    private const JENIS_SAKIT_ID     = 4;               // Izin Sakit

    // Jam batas kerja reguler & lembur hari biasa
    private const JAM_BATAS_REGULER  = '16:00:00';   // Batas kerja reguler hari biasa
    private const JAM_BATAS_LEMBUR   = '18:00:00';   // Batas lembur hari biasa

    // Batas menit (jam kerja)
    private const MENIT_KERJA_MAX    = 480;    // 8 jam
    private const MENIT_LEMBUR_BIASA = 120;    // 2 jam (16.00–18.00)
    private const MENIT_LEMBUR_LIBUR = 300;    // 5 jam

    // Cache data
    private array $loadedMonths  = [];
    private array $hariLiburMap  = [];      // ['Y-m-d' => true]
    private array $lemburMap     = [];      // [pegawai_id]['Y-m-d'] => Lembur model
    private array $cutiMap       = [];      // [pegawai_id]['Y-m-d'] => jenis_cuti_id


    // Load data per bulan (lazy, dipanggil otomatis saat dibutuhkan)

    private function loadDataBulan(string $tahunBulan): void
    {
        if (isset($this->loadedMonths[$tahunBulan])) {
            return;
        }

        $startDate = Carbon::createFromFormat('Y-m', $tahunBulan)->startOfMonth()->toDateString();
        $endDate   = Carbon::createFromFormat('Y-m', $tahunBulan)->endOfMonth()->toDateString();

        $this->loadHariLibur($startDate, $endDate);
        $this->loadLembur($startDate, $endDate);
        $this->loadCuti($startDate, $endDate);

        $this->loadedMonths[$tahunBulan] = true;
    }

    // Load Data Hari Libur (Tanggal Merah)
    private function loadHariLibur(string $startDate, string $endDate): void
    {
        HariLibur::query()
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->pluck('tanggal')
            ->each(function ($tanggal) {
                $this->hariLiburMap[Carbon::parse($tanggal)->format('Y-m-d')] = true;
            });
    }

    // Load Data Lembur Pegawai
    private function loadLembur(string $startDate, string $endDate): void
    {
        Lembur::query()
            ->whereBetween('tanggal_lembur', [$startDate, $endDate])
            ->where('status', 'Selesai')
            ->with('laporan')
            ->get()
            ->each(function ($lembur) {
                $tanggal = Carbon::parse($lembur->tanggal_lembur)->format('Y-m-d');
                $this->lemburMap[$lembur->pegawai_id][$tanggal] = $lembur;
            });
    }

    // Load Data Cuti Pegawai
    private function loadCuti(string $startDate, string $endDate): void
    {
        Cuti::query()
            ->where('status', 'disetujui')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_mulai', [$startDate, $endDate])
                    ->orWhereBetween('tanggal_selesai', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('tanggal_mulai', '<=', $startDate)
                            ->where('tanggal_selesai', '>=', $endDate);
                    });
            })
            ->get(['pegawai_id', 'jenis_cuti_id', 'tanggal_mulai', 'tanggal_selesai'])
            ->each(function ($cuti) use ($startDate, $endDate) {
                $mulai    = max($cuti->tanggal_mulai, $startDate);
                $selesai  = min($cuti->tanggal_selesai, $endDate);

                CarbonPeriod::create($mulai, $selesai)
                    ->forEach(function (Carbon $date) use ($cuti) {
                        $tanggal = $date->format('Y-m-d');
                        $this->cutiMap[$cuti->pegawai_id][$tanggal] = $cuti->jenis_cuti_id;
                    });
            });
    }

    private function ensureDataLoaded(string $tanggal): void
    {
        $bulan = Carbon::parse($tanggal)->format('Y-m');
        $this->loadDataBulan($bulan);
    }

    // Helper cek hari libur
    private function isHariLibur(string $tanggal): bool
    {
        return isset($this->hariLiburMap[$tanggal]) || Carbon::parse($tanggal)->isWeekend();
    }

    // Helper cek kategori cuti
    private function kategoriCuti(int $jenisCutiId): string
    {
        if (in_array($jenisCutiId, self::JENIS_CUTI_IDS)) {
            return 'cuti';
        }

        if ($jenisCutiId === self::JENIS_SAKIT_ID) {
            return 'sakit';
        }

        return 'izin';
    }

    /**
     * Hitung menit jam kerja reguler untuk hari biasa.
     *
     * Aturan:
     * - Jika ada lembur: batas kerja reguler adalah jam 16.00.
     * - Jika tidak ada lembur: batas kerja adalah 8 jam dari jam masuk (maks. 480 menit).
     * - Kelebihan tidak dihitung.
     */

    // Helper kalkulasi jam kerja harian / total menit (jam kerja)

    public function hitungMenitKerjaHariBiasa(
        string  $tanggal,
        string  $jamMasuk,
        string  $jamKeluar,
        bool    $adaLembur
    ): int {
        // dd($tanggal, $jamMasuk, $jamKeluar);

        $masuk  = Carbon::parse($jamMasuk);
        $keluar = Carbon::parse($jamKeluar);

        if ($adaLembur) {
            // Batas kerja reguler adalah jam 16.00
            $batasKerja = Carbon::parse(self::JAM_BATAS_REGULER);
            $akhirKerja = $keluar->min($batasKerja);

            if ($akhirKerja->lessThanOrEqualTo($masuk)) {
                return 0;
            }

            return (int) $masuk->diffInMinutes($akhirKerja);
        }

        // Tanpa lembur: maks. 8 jam dari jam masuk
        $totalMenit = (int) $masuk->diffInMinutes($keluar);
        return min($totalMenit, self::MENIT_KERJA_MAX);
    }

    // Helper Kalkulasi Lembur
    /**
     * Hari biasa (Senin–Jumat).
     * Lembur dihitung dari irisan antara jam presensi pegawai
     * dan window lembur resmi (16.00–18.00).
     */
    public function hitungMenitLemburHariBiasa(
        string $tanggal,
        string $jamMasuk,
        string $jamKeluar
    ): int {
        $keluar          = Carbon::parse($jamKeluar);
        $batasLemburMulai = Carbon::parse(self::JAM_BATAS_REGULER);
        $batasLemburAkhir = Carbon::parse(self::JAM_BATAS_LEMBUR);

        // Pegawai harus masih ada setelah jam 16.00
        if ($keluar->lessThanOrEqualTo($batasLemburMulai)) {
            return 0;
        }

        // Irisan waktu antara jam keluar pegawai dan window lembur
        $akhirLembur = $keluar->min($batasLemburAkhir);
        $menitLembur = (int) $batasLemburMulai->diffInMinutes($akhirLembur);

        return min(max($menitLembur, 0), self::MENIT_LEMBUR_BIASA);
    }

    /**
     * Hari libur / weekend.
     * Seluruh jam presensi dianggap lembur, maks. 5 jam.
     */
    public function hitungMenitLemburHariLibur(
        string $tanggal,
        string $jamMasuk,
        string $jamKeluar
    ): int {
        $masuk  = Carbon::parse($jamMasuk);
        $keluar = Carbon::parse($jamKeluar);

        $totalMenit = (int) $masuk->diffInMinutes($keluar);
        return min($totalMenit, self::MENIT_LEMBUR_LIBUR);
    }

    // Helper Utama (Hitung Rekapitulasi)
    public function hitungRekap(int $pegawaiId, Collection $presensiCollection): array
    {
        $hadir            = 0;
        $tidakHadir       = 0;
        $lembur           = 0;
        $cuti             = 0;
        $izin             = 0;
        $sakit            = 0;
        $totalMenitKerja  = 0;
        $totalMenitLembur = 0;

        foreach ($presensiCollection as $presensi) {
            $tanggal = Carbon::parse($presensi->tanggal)->format('Y-m-d');

            // Load Data
            $this->ensureDataLoaded($tanggal);

            $isHariLibur  = $this->isHariLibur($tanggal);
            $adaLembur    = isset($this->lemburMap[$pegawaiId][$tanggal]);
            $statusId     = $presensi->status_kehadiran_id;

            // 1. Hitung frekuensi kehadiran

            if ($isHariLibur) {
                // Hari libur: hanya dicatat sebagai lembur, tidak masuk hitungan hadir/tidak hadir
                if ($adaLembur && $presensi->jam_masuk && $presensi->jam_keluar) {
                    $menitLembur = $this->hitungMenitLemburHariLibur(
                        $tanggal,
                        $presensi->jam_masuk,
                        $presensi->jam_keluar
                    );

                    if ($menitLembur > 0) {
                        $lembur++;
                        $totalMenitLembur += $menitLembur;
                    }
                }
                continue;
            }

            // 2. Hari kerja biasa
            $statusCuti = $this->resolveStatusCuti($pegawaiId, $tanggal, $statusId);

            if ($statusCuti !== null) {
                // Pegawai cuti/izin/sakit pada hari ini
                match ($statusCuti) {
                    'cuti'  => $cuti++,
                    'sakit' => $sakit++,
                    'izin'  => $izin++,
                };
                continue;
            }

            if ($this->isHadirBiasa($statusId)) {
                $hadir++;
            } elseif ($this->isTidakHadir($statusId)) {
                $tidakHadir++;
                continue; // Tidak ada jam kerja/lembur yang dihitung
            }

            // 3. Hitung jam kerja & lembur hari biasa

            if (!$presensi->jam_masuk || !$presensi->jam_keluar) {
                continue;
            }

            $menitKerja = $this->hitungMenitKerjaHariBiasa(
                $tanggal,
                $presensi->jam_masuk,
                $presensi->jam_keluar,
                $adaLembur
            );
            $totalMenitKerja += $menitKerja;

            if ($adaLembur) {
                $menitLemburHariIni = $this->hitungMenitLemburHariBiasa(
                    $tanggal,
                    $presensi->jam_masuk,
                    $presensi->jam_keluar
                );

                if ($menitLemburHariIni > 0) {
                    $lembur++;
                    $totalMenitLembur += $menitLemburHariIni;
                }
            }
        }

        return [
            'hadir'             => $hadir,
            'tidak_hadir'       => $tidakHadir,
            'lembur'            => $lembur,
            'cuti'              => $cuti,
            'izin'              => $izin,
            'sakit'             => $sakit,
            'total_menit_kerja'  => $totalMenitKerja,
            'total_menit_lembur' => $totalMenitLembur,
        ];
    }

    /**
     * Method utilitas publik
     */

    // Format data menit menjadi string "Xh Ym".
    public function formatMenit(int $totalMenit): string
    {
        $jam   = (int) floor($totalMenit / 60);
        $menit = $totalMenit % 60;
        return "{$jam}h {$menit}m";
    }

    // Get total jam kerja format string.
    public function getTotalJamKerja(int $pegawaiId, Collection $presensiCollection): string
    {
        $rekap = $this->hitungRekap($pegawaiId, $presensiCollection);
        return $this->formatMenit($rekap['total_menit_kerja']);
    }

    // Get total jam lembur format string.
    public function getTotalJamLembur(int $pegawaiId, Collection $presensiCollection): string
    {
        $rekap = $this->hitungRekap($pegawaiId, $presensiCollection);
        return $this->formatMenit($rekap['total_menit_lembur']);
    }

    // Get frekuensi hadir.
    public function getTotalHadir(int $pegawaiId, Collection $presensiCollection): int
    {
        return $this->hitungRekap($pegawaiId, $presensiCollection)['hadir'];
    }

    // Get frekuensi tidak hadir.
    public function getTotalTidakHadir(int $pegawaiId, Collection $presensiCollection): int
    {
        return $this->hitungRekap($pegawaiId, $presensiCollection)['tidak_hadir'];
    }

    // Get frekuensi lembur.
    public function getTotalLembur(int $pegawaiId, Collection $presensiCollection): int
    {
        return $this->hitungRekap($pegawaiId, $presensiCollection)['lembur'];
    }

    // Get total cuti.
    public function getTotalCuti(int $pegawaiId, Collection $presensiCollection): int
    {
        return $this->hitungRekap($pegawaiId, $presensiCollection)['cuti'];
    }

    // Get total izin.
    public function getTotalIzin(int $pegawaiId, Collection $presensiCollection): int
    {
        return $this->hitungRekap($pegawaiId, $presensiCollection)['izin'];
    }

    // Get total sakit.
    public function getTotalSakit(int $pegawaiId, Collection $presensiCollection): int
    {
        return $this->hitungRekap($pegawaiId, $presensiCollection)['sakit'];
    }


    /**
     * Helper privat.
     */

    // Get status cuti, izin, dan sakit.
    private function resolveStatusCuti(int $pegawaiId, string $tanggal, ?int $statusId): ?string
    {
        if (isset($this->cutiMap[$pegawaiId][$tanggal])) {
            return $this->kategoriCuti($this->cutiMap[$pegawaiId][$tanggal]);
        }

        return null;
    }

    // Get hadir.
    private function isHadirBiasa(?int $statusId): bool
    {
        return in_array($statusId, [
            StatusKehadiranService2::HADIR_NORMAL,
            StatusKehadiranService2::HADIR_KURANG_JAM,
        ]);
    }

    // Get tidak hadir.
    private function isTidakHadir(?int $statusId): bool
    {
        return in_array($statusId, [
            StatusKehadiranService2::TIDAK_HADIR_KURANG_JAM,
            StatusKehadiranService2::TIDAK_HADIR_ABSEN_1X,
            StatusKehadiranService2::TIDAK_HADIR_TANPA_KETERANGAN,
        ]);
    }
}