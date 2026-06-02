<?php

namespace App\Livewire\Dashboard\Presensi;

use App\Models\Presensi;
use App\Models\HariLibur;
use App\Services\StatusKehadiranService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Component;

class RingkasanPresensi extends Component
{
    #[Session]
    public ?string $selectedPeriodeMulai = null;
    #[Session]
    public ?string $selectedPeriodeSelesai = null;

    public function updated(string $property): void
    {
        // if (in_array($property, [
        //     'selectedPeriodeMulai',
        //     'selectedPeriodeSelesai',
        // ])) {
        //     $this->resetPage();
        // }
    }

    protected function getPegawaiNip(): ?string
    {
        return Auth::user()->pegawai?->nip;
    }

    /**
     * Helper untuk memparsing rentang waktu dari filter atau default
     */
    protected function getPeriodeAktif(): array
    {
        $mulai = $this->selectedPeriodeMulai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeMulai)->startOfDay()
            : now()->startOfMonth();

        $selesai = $this->selectedPeriodeSelesai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeSelesai)->endOfDay()
            : now()->endOfMonth();

        return [$mulai, $selesai];
    }

    /**
     * Computed Property untuk menampilkan teks informasi periode di header blade
     * Menggunakan translatedFormat() untuk lokalisasi bahasa (Indonesia)
     */
    #[Computed]
    public function infoPeriodeAktif(): string
    {
        // Ambil objek Carbon mulai dan selesai dari method yang sudah kita buat sebelumnya
        [$mulai, $selesai] = $this->getPeriodeAktif();

        // Jika user memfilter menggunakan tanggal kustom
        if ($this->selectedPeriodeMulai || $this->selectedPeriodeSelesai) {
            $formatMulai = $mulai->translatedFormat('d M Y');
            $formatSelesai = $selesai->translatedFormat('d M Y');
            return "{$formatMulai} — {$formatSelesai}";
        }

        // Default jika filter kosong (Bulan Berjalan)
        return "Bulan Berjalan (" . now()->translatedFormat('F Y') . ")";
    }

    /**
     * Computed Property untuk menghitung Ringkasan Presensi
     */
    #[Computed]
    public function ringkasanData(): ?array
    {
        $nip = $this->getPegawaiNip();
        if (!$nip) return null;

        [$mulai, $selesai] = $this->getPeriodeAktif();
        $pegawai = Auth::user()->pegawai;

        // Fetch presensi pada periode aktif
        $presensiList = Presensi::where('pegawai_nip', $nip)
            ->whereBetween('tanggal', [$mulai, $selesai])
            ->get();

        // Fetch lembur yang disetujui pada periode aktif lalu mapping berdasarkan tanggal untuk pencarian O(1)
        $lemburByDate = $pegawai->lembur()
            ->whereBetween('tanggal_lembur', [$mulai, $selesai])
            ->where('status', 'Disetujui')
            ->get()
            ->keyBy(function ($l) {
                return Carbon::parse($l->tanggal_lembur)->format('Y-m-d');
            });

        // Inisialisasi variabel hitung
        $hadir = $tidakHadir = $lembur = $cuti = $izin = $sakit = $totalMenitKerja = $totalMenitLembur = 0;

        foreach ($presensiList as $presensi) {
            $tanggalStr = Carbon::parse($presensi->tanggal)->format('Y-m-d');
            $statusId = $presensi->status_kehadiran_id;

            // A. Hitung Kehadiran
            switch ($statusId) {
                case StatusKehadiranService::HADIR_NORMAL:
                case StatusKehadiranService::HADIR_KURANG_JAM:
                    $hadir++;
                    break;
                case StatusKehadiranService::TIDAK_HADIR_KURANG_JAM:
                case StatusKehadiranService::TIDAK_HADIR_ABSEN_1X:
                case StatusKehadiranService::TIDAK_HADIR_TANPA_KETERANGAN:
                    $tidakHadir++;
                    break;
                case StatusKehadiranService::IZIN:
                    $izin++;
                    break;
                case StatusKehadiranService::SAKIT:
                    $sakit++;
                    break;
                case StatusKehadiranService::CUTI:
                    $cuti++;
                    break;
            }

            // B. Hitung Menit Kerja Aktual
            $menitKerjaHariIni = 0;
            if ($presensi->jam_masuk && $presensi->jam_keluar) {
                $masuk = Carbon::parse($presensi->jam_masuk);
                $keluar = Carbon::parse($presensi->jam_keluar);
                $menitKerjaHariIni = $masuk->diffInMinutes($keluar);
            }

            // C. Logika Lembur & Jam Kerja Terhitung
            $isLemburDisetujui = $lemburByDate->has($tanggalStr);
            $dataLembur = $isLemburDisetujui ? $lemburByDate->get($tanggalStr) : null;
            $isWeekend = Carbon::parse($presensi->tanggal)->isWeekend();
            $isHariLibur = $statusId == StatusKehadiranService::LEMBUR || $isWeekend || ($dataLembur && in_array($dataLembur->jenis_hari, ['Hari Libur', 'Libur Nasional']));

            $menitLemburValidHariIni = 0;

            if (in_array($statusId, [
                StatusKehadiranService::HADIR_NORMAL,
                StatusKehadiranService::HADIR_KURANG_JAM,
                StatusKehadiranService::LEMBUR
            ])) {
                if (!$isHariLibur) {
                    $menitKerjaReguler = min($menitKerjaHariIni, 480); // Maks 8 jam
                    $totalMenitKerja += $menitKerjaReguler;

                    if ($isLemburDisetujui && $menitKerjaHariIni > 480) {
                        $menitLemburValidHariIni = min(($menitKerjaHariIni - 480), 120); // Maks 2 jam lembur
                        $totalMenitLembur += $menitLemburValidHariIni;
                    }
                } else {
                    if ($isLemburDisetujui) {
                        $menitLemburValidHariIni = min($menitKerjaHariIni, 300); // Maks 5 jam weekend
                        $totalMenitLembur += $menitLemburValidHariIni;
                    }
                }
            }

            if ($menitLemburValidHariIni > 0) {
                $lembur++;
            }
        }

        return [
            'hadir'            => $hadir,
            'tidak_hadir'      => $tidakHadir,
            'lembur'           => $lembur,
            'cuti'             => $cuti,
            'izin'             => $izin,
            'sakit'            => $sakit,
            'total_jam_kerja'  => [
                'jam'   => floor($totalMenitKerja / 60),
                'menit' => $totalMenitKerja % 60
            ],
            'total_jam_lembur' => [
                'jam'   => floor($totalMenitLembur / 60),
                'menit' => $totalMenitLembur % 60
            ],
            'is_empty'         => $presensiList->isEmpty()
        ];
    }

    /**
     * Computed Property untuk Riwayat Presensi 5 Hari Terakhir
     */
    #[Computed]
    public function riwayatTerakhir()
    {
        $nip = $this->getPegawaiNip();
        if (!$nip) return collect();

        // Tentukan rentang 5 hari (Hari kemarin s/d 5 hari yang lalu)
        $endDate = now()->subDay();
        $startDate = now()->subDays(14);

        // 1. Ambil data presensi pada rentang tanggal tersebut dan ubah jadi key-value berdasarkan tanggal
        $presensiList = Presensi::with('statusKehadiran')
            ->where('pegawai_nip', $nip)
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->tanggal)->format('Y-m-d');
            });

        // 2. Ambil data Hari Libur Nasional (jadikan key-value berdasarkan tanggal Y-m-d)
        $hariLiburList = HariLibur::whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->tanggal)->format('Y-m-d');
            });

        $riwayat = collect();
        $hariDitemukan = 0;
        $hariMundur = 1;

        while ($hariDitemukan < 5 && $hariMundur <= 14) {
            $currentDate = now()->subDays($hariMundur);
            $dateString = $currentDate->format('Y-m-d');

            // Cek status hari ini
            $adaDataPresensi = $presensiList->has($dateString);
            $isAkhirPekan = $currentDate->isWeekend();
            $isLiburNasional = $hariLiburList->has($dateString);

            if ($adaDataPresensi) {
                // SKENARIO 1: ADA DATA PRESENSI
                // (Meskipun hari libur / akhir pekan, jika pegawai absen lembur, tetap dimasukkan ke riwayat)
                $item = $presensiList->get($dateString);

                $statusDatang = '-';
                $statusPulang = '-';

                if ($item->jam_masuk) {
                    $waktuMasuk = Carbon::parse($item->jam_masuk)->format('H:i:s');
                    if ($waktuMasuk > '08:00:00') {
                        $statusDatang = 'Terlambat';
                    } elseif ($waktuMasuk < '08:00:00') {
                        $statusDatang = 'Datang Lebih Awal';
                    } else {
                        $statusDatang = 'Tepat Waktu';
                    }
                }

                if ($item->jam_keluar) {
                    $waktuKeluar = Carbon::parse($item->jam_keluar)->format('H:i:s');
                    if ($waktuKeluar < '16:00:00') {
                        $statusPulang = 'Pulang Lebih Awal';
                    } else {
                        $statusPulang = 'Tepat Waktu';
                    }
                }

                $item->status_datang = $statusDatang;
                $item->status_pulang = $statusPulang;
                $item->is_empty_day = false;

                $riwayat->push($item);
                $hariDitemukan++;

            } else {
                // SKENARIO 2: TIDAK ADA DATA PRESENSI

                // Pastikan bukan akhir pekan DAN bukan libur nasional
                if (!$isAkhirPekan && !$isLiburNasional) {

                    // Ini adalah Hari Kerja Normal (Senin-Jumat, bukan tanggal merah), tapi belum/tidak absen
                    $riwayat->push((object)[
                        'is_empty_day' => true,
                        'tanggal'      => $dateString,
                        'pesan'        => $this->getEmptyStateMessagePerHari()
                    ]);
                    $hariDitemukan++; // Dihitung sebagai 1 slot riwayat
                }
            }

            $hariMundur++;
        }

        return $riwayat;
    }

    /**
     * Computed Property untuk mengecek apakah data presensi 5 hari terakhir kosong total
     */
    #[Computed]
    public function isRiwayatKosongTotal(): bool
    {
        return $this->riwayatTerakhir->every('is_empty_day', true);
    }

    /**
     * Helper function untuk pesan empty state TOTAL (jika 5 hari kosong semua)
     */
    #[Computed]
    public function emptyStateMessageRiwayat(): string
    {
        if (!$this->getPegawaiNip()) {
            return "Akun Anda belum tertaut dengan data pegawai manapun.";
        }

        $awal = now()->subDays(5)->translatedFormat('d F Y');
        $akhir = now()->subDay()->translatedFormat('d F Y');

        return "Belum ada riwayat presensi yang tercatat dalam 5 hari terakhir ({$awal} s/d {$akhir}).";
    }

    /**
     * Helper function untuk pesan empty state PER HARI
     */
    protected function getEmptyStateMessagePerHari(): string
    {
        return 'Data presensi untuk hari ini belum tersedia. Silahkan Hubungi Administrator.';
    }

    /**
     * Computed Property untuk Empty State Message
     */
    #[Computed]
    public function emptyStateMessage(): string
    {
        if (!$this->getPegawaiNip()) {
            return "Akun Anda belum tertaut dengan data pegawai manapun.";
        }

        [$mulai, $selesai] = $this->getPeriodeAktif();
        $formatMulai = $mulai->translatedFormat('d F Y');
        $formatSelesai = $selesai->translatedFormat('d F Y');

        if ($this->selectedPeriodeMulai || $this->selectedPeriodeSelesai) {
            return "Anda belum memiliki riwayat presensi untuk periode: {$formatMulai} s/d {$formatSelesai}.";
        }

        return "Anda belum memiliki riwayat presensi yang terekam untuk bulan " . now()->translatedFormat('F Y') . ".";
    }

    public function render()
    {
        return view('livewire.dashboard.presensi.ringkasan-presensi');
    }
}