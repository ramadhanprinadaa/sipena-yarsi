<?php

namespace App\Livewire\Dashboard\Presensi;

use App\Models\HariLibur;
use App\Models\Presensi;
use App\Services\RekapitulasiPresensiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Component;

class RingkasanPresensi extends Component
{
    #[Session]
    public ?string $selectedPeriodeMulai   = null;
    #[Session]
    public ?string $selectedPeriodeSelesai = null;

    // ──────────────────────────────────────────────────────────────────
    // Computed: data utama
    // ──────────────────────────────────────────────────────────────────

    #[Computed]
    public function infoPeriodeAktif(): string
    {
        [$mulai, $selesai] = $this->getPeriode();

        if ($this->selectedPeriodeMulai || $this->selectedPeriodeSelesai) {
            return $mulai->translatedFormat('d M Y') . ' — ' . $selesai->translatedFormat('d M Y');
        }

        return "Bulan Berjalan (" . now()->translatedFormat('F Y') . ")";
    }

    #[Computed]
    public function ringkasanData(): ?array
    {
        $pegawaiId = $this->getPegawaiId();
        if (!$pegawaiId) {
            return null;
        }

        [$mulai, $selesai] = $this->getPeriode();

        $presensiList = Presensi::where('pegawai_id', $pegawaiId)
            ->whereBetween('tanggal', [$mulai, $selesai])
            ->get();

        if ($presensiList->isEmpty()) {
            return ['is_empty' => true];
        }

        $service = new RekapitulasiPresensiService();
        $rekap   = $service->hitungRekap($pegawaiId, $presensiList);

        return [
            'hadir'       => $rekap['hadir'],
            'tidak_hadir' => $rekap['tidak_hadir'],
            'lembur'      => $rekap['lembur'],
            'cuti'        => $rekap['cuti'],
            'izin'        => $rekap['izin'],
            'sakit'       => $rekap['sakit'],
            // Format array agar kompatibel dengan blade view yang sudah ada
            'total_jam_kerja' => [
                'jam'   => (int) floor($rekap['total_menit_kerja'] / 60),
                'menit' => $rekap['total_menit_kerja'] % 60,
            ],
            'total_jam_lembur' => [
                'jam'   => (int) floor($rekap['total_menit_lembur'] / 60),
                'menit' => $rekap['total_menit_lembur'] % 60,
            ],
            'is_empty' => false,
        ];
    }

    /**
     * Riwayat presensi 5 hari kerja terakhir (tidak disentuh, tidak ada kalkulasi di sini).
     */
    #[Computed]
    public function riwayatTerakhir()
    {
        $pegawaiId = $this->getPegawaiId();
        if (!$pegawaiId) {
            return collect();
        }

        $endDate   = now()->subDay();
        $startDate = now()->subDays(14);

        $presensiList = Presensi::with('statusKehadiran')
            ->where('pegawai_id', $pegawaiId)
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->tanggal)->format('Y-m-d'));

        $hariLiburList = HariLibur::whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->tanggal)->format('Y-m-d'));

        $riwayat       = collect();
        $hariDitemukan = 0;
        $hariMundur    = 1;

        while ($hariDitemukan < 5 && $hariMundur <= 14) {
            $currentDate = now()->subDays($hariMundur);
            $dateString  = $currentDate->format('Y-m-d');

            $adaPresensi     = $presensiList->has($dateString);
            $isAkhirPekan    = $currentDate->isWeekend();
            $isLiburNasional = $hariLiburList->has($dateString);

            if ($adaPresensi) {
                $item = $presensiList->get($dateString);
                $item->status_datang = $this->resolveStatusDatang($item->jam_masuk);
                $item->status_pulang = $this->resolveStatusPulang($item->jam_keluar);
                $item->is_empty_day  = false;

                $riwayat->push($item);
                $hariDitemukan++;
            } elseif (!$isAkhirPekan && !$isLiburNasional) {
                $riwayat->push((object) [
                    'is_empty_day' => true,
                    'tanggal'      => $dateString,
                    'pesan'        => $this->getEmptyStateMessagePerHari(),
                ]);
                $hariDitemukan++;
            }

            $hariMundur++;
        }

        return $riwayat;
    }

    #[Computed]
    public function isRiwayatKosongTotal(): bool
    {
        return $this->riwayatTerakhir->every('is_empty_day', true);
    }

    // ──────────────────────────────────────────────────────────────────
    // Computed: empty state
    // ──────────────────────────────────────────────────────────────────

    #[Computed]
    public function emptyStateMessage(): string
    {
        if (!$this->getPegawaiId()) {
            return "Akun Anda belum tertaut dengan data pegawai manapun.";
        }

        [$mulai, $selesai] = $this->getPeriode();

        if ($this->selectedPeriodeMulai || $this->selectedPeriodeSelesai) {
            return "Anda belum memiliki riwayat presensi untuk periode: "
                . $mulai->translatedFormat('d F Y') . ' s/d ' . $selesai->translatedFormat('d F Y') . ".";
        }

        return "Anda belum memiliki riwayat presensi yang terekam untuk bulan "
            . now()->translatedFormat('F Y') . ".";
    }

    #[Computed]
    public function emptyStateMessageRiwayat(): string
    {
        if (!$this->getPegawaiId()) {
            return "Akun Anda belum tertaut dengan data pegawai manapun.";
        }

        $awal  = now()->subDays(5)->translatedFormat('d F Y');
        $akhir = now()->subDay()->translatedFormat('d F Y');

        return "Belum ada riwayat presensi yang tercatat dalam 5 hari terakhir ({$awal} s/d {$akhir}).";
    }

    // ──────────────────────────────────────────────────────────────────
    // Render
    // ──────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.dashboard.presensi.ringkasan-presensi');
    }

    // ──────────────────────────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────────────────────────

    private function getPegawaiId(): ?string
    {
        return Auth::user()->pegawai?->id;
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function getPeriode(): array
    {
        $mulai = $this->selectedPeriodeMulai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeMulai)->startOfDay()
            : now()->startOfMonth();

        $selesai = $this->selectedPeriodeSelesai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeSelesai)->endOfDay()
            : now()->endOfMonth();

        return [$mulai, $selesai];
    }

    private function resolveStatusDatang(?string $jamMasuk): string
    {
        if (!$jamMasuk) {
            return '-';
        }

        $waktu = Carbon::parse($jamMasuk)->format('H:i:s');

        return match (true) {
            $waktu > '08:00:00' => 'Terlambat',
            $waktu < '08:00:00' => 'Datang Lebih Awal',
            default             => 'Tepat Waktu',
        };
    }

    private function resolveStatusPulang(?string $jamKeluar): string
    {
        if (!$jamKeluar) {
            return '-';
        }

        return Carbon::parse($jamKeluar)->format('H:i:s') < '16:00:00'
            ? 'Pulang Lebih Awal'
            : 'Tepat Waktu';
    }

    private function getEmptyStateMessagePerHari(): string
    {
        return 'Data presensi untuk hari ini belum tersedia. Silahkan Hubungi Administrator.';
    }
}