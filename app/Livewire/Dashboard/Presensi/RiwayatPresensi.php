<?php

namespace App\Livewire\Dashboard\Presensi;

use App\Models\Presensi;
use App\Models\StatusKehadiran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\WithPagination;

class RiwayatPresensi extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public array $statusKehadiranList = [];

    // Filter
    #[Session]
    public ?string $selectedStatusKehadiran = null;
    #[Session]
    public ?string $selectedPeriodeMulai = null;
    #[Session]
    public ?string $selectedPeriodeSelesai = null;

    public function mount()
    {
        $this->statusKehadiranList = StatusKehadiran::pluck('status')->toArray();
    }

    public function updated(string $property): void
    {
        if (in_array($property, [
            'selectedStatusKehadiran',
            'selectedPeriodeMulai',
            'selectedPeriodeSelesai',
        ])) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function infoPeriodeAktif(): string
    {
        $mulai = $this->selectedPeriodeMulai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeMulai)->startOfDay()
            : now()->startOfMonth();

        $selesai = $this->selectedPeriodeSelesai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeSelesai)->endOfDay()
            : now()->endOfMonth();

        if ($this->selectedPeriodeMulai || $this->selectedPeriodeSelesai) {
            $formatMulai = $mulai->translatedFormat('d M Y');
            $formatSelesai = $selesai->translatedFormat('d M Y');
            return "{$formatMulai} — {$formatSelesai}";
        }
        return "Bulan Berjalan (" . now()->translatedFormat('F Y') . ")";
    }

    protected function getPegawaiNip(): ?string
    {
        return Auth::user()->pegawai?->nip;
    }

    protected function baseQuery()
    {
        $nip = $this->getPegawaiNip();

        // 1. Parsing Tanggal
        $filterMulai = $this->selectedPeriodeMulai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeMulai)->format('Y-m-d')
            : null;

        $filterSelesai = $this->selectedPeriodeSelesai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeSelesai)->format('Y-m-d')
            : null;

        // Jika hanya filter selesai yang diisi, samakan nilai mulai dengan selesai
        if ($filterSelesai && !$filterMulai) {
            $filterMulai = $filterSelesai;
        }

        // 2. Inisialisasi Base Query khusus untuk pegawai login
        $query = Presensi::query()
            ->where('pegawai_nip', $nip)
            ->with(['statusKehadiran:id,status,warna']); // Eager loading untuk optimasi performa

        // 3. Implementasi Filter
        return $query
            // Filter Tanggal
            ->when(!$filterMulai && !$filterSelesai, function ($q) {
                // Default view (jika tidak ada filter): Tampilkan Bulan Berjalan
                $q->whereBetween('tanggal', [
                    now()->startOfMonth()->format('Y-m-d'),
                    now()->endOfMonth()->format('Y-m-d')
                ]);
            })
            ->when($filterMulai, function ($q) use ($filterMulai, $filterSelesai) {
                if ($filterSelesai) {
                    $q->whereBetween('tanggal', [$filterMulai, $filterSelesai]);
                } else {
                    $q->whereDate('tanggal', $filterMulai);
                }
            })
            // Filter Dropdown Status Kehadiran
            ->when($this->selectedStatusKehadiran, function ($q) {
                $q->whereHas('statusKehadiran', fn($status) => $status->where('status', $this->selectedStatusKehadiran));
            });
    }

    #[Computed]
    public function riwayatData()
    {
        // Proteksi jika akun belum direlasikan ke data pegawai
        if (!$this->getPegawaiNip()) {
            return Presensi::whereNull('id')->paginate(10); // Kembalikan paginator kosong
        }

        return $this->baseQuery()
            ->paginate(10);
    }

    #[Computed]
    public function emptyStateMessage(): string
    {
        if (!$this->getPegawaiNip()) {
            return "Data Pegawai tidak ditemukan.";
        }
        $mulai = $this->selectedPeriodeMulai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeMulai)->format('Y-m-d')
            : null;

        $selesai = $this->selectedPeriodeSelesai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeSelesai)->format('Y-m-d')
            : null;

        // Default Message (Bulan Berjalan)
        if (!$mulai && !$selesai) {
            $bulan = now()->translatedFormat('F Y');
            return "Anda belum memiliki riwayat presensi di bulan {$bulan}.";
        }

        // Jika hanya filter 'mulai' yang diatur
        if ($mulai && !$selesai) {
            $tanggal = Carbon::parse($mulai)->translatedFormat('d F Y');
            return "Anda belum memiliki data presensi pada {$tanggal}.";
        }

        // Jika filter rentang periode diatur
        $tanggalMulai = Carbon::parse($mulai)->translatedFormat('d F Y');
        $tanggalSelesai = Carbon::parse($selesai)->translatedFormat('d F Y');

        return "Tidak ditemukan data presensi pada periode {$tanggalMulai} - {$tanggalSelesai}.";

    }

    public function showDetailRiwayat(int $id): void
    {
        $this->dispatch('load-detail-riwayat', presensiId: $id);
    }

    public function render()
    {
        return view('livewire.dashboard.presensi.riwayat-presensi');
    }
}