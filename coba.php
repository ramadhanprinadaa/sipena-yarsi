<?php

namespace App\Livewire\Manajemen\Presensi;

use App\Exports\RekapitulasiPresensiExport;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Services\RekapitulasiPresensiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\WithPagination;

class TabelRekapitulasiPresensi extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public array $unitKerja            = [];
    public array $unitKerjaUniversitas = [];

    // Filter
    #[Session]
    public ?string $selectedUnitKerja      = null;
    #[Session]
    public ?string $selectedPeriodeMulai   = null;
    #[Session]
    public ?string $selectedPeriodeSelesai = null;

    // Search
    #[Session]
    public string $search = '';

    // ──────────────────────────────────────────────────────────────────
    // Lifecycle
    // ──────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->unitKerja = UnitKerja::orderBy('name')
            ->pluck('name')
            ->toArray();

        $this->unitKerjaUniversitas = UnitKerja::query()
            ->whereHas('unitSdm', fn($q) => $q->where('name', 'SDM Universitas'))
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
    }

    public function updated(string $property): void
    {
        $filterProperties = [
            'search',
            'selectedUnitKerja',
            'selectedPeriodeMulai',
            'selectedPeriodeSelesai',
        ];

        if (in_array($property, $filterProperties)) {
            $this->resetPage();
        }
    }

    #[On('refresh-table-riwayat-rekapitulasi')]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    // ──────────────────────────────────────────────────────────────────
    // Computed: data utama
    // ──────────────────────────────────────────────────────────────────

    #[Computed]
    public function rekapitulasiPresensi()
    {
        [$mulai, $selesai] = $this->getPeriode();

        $pegawais = $this->baseQuery()
            ->with([
                'presensi' => fn($q) => $q->whereBetween('tanggal', [$mulai, $selesai]),
                'unit_kerja:id,name',
            ])
            ->paginate(10);

        // Satu instance service untuk semua pegawai dalam satu request
        // (memanfaatkan cache bulan yang sudah di-load sebelumnya)
        $service = new RekapitulasiPresensiService();

        $pegawais->getCollection()->transform(function ($pegawai) use ($service) {
            $rekap = $service->hitungRekap($pegawai->id, $pegawai->presensi);

            $pegawai->rekap = [
                'hadir'            => $rekap['hadir'],
                'tidak_hadir'      => $rekap['tidak_hadir'],
                'lembur'           => $rekap['lembur'],
                'cuti'             => $rekap['cuti'],
                'izin'             => $rekap['izin'],
                'sakit'            => $rekap['sakit'],
                'total_jam_kerja'  => $service->formatMenit($rekap['total_menit_kerja']),
                'total_jam_lembur' => $service->formatMenit($rekap['total_menit_lembur']),
            ];

            return $pegawai;
        });

        return $pegawais;
    }

    #[Computed]
    public function infoPeriodeAktif(): string
    {
        [$mulai, $selesai] = $this->getPeriode();

        $formatMulai   = $mulai->translatedFormat('d F Y');
        $formatSelesai = $selesai->translatedFormat('d F Y');

        if (!$this->selectedPeriodeMulai && !$this->selectedPeriodeSelesai) {
            return "Bulan Berjalan ({$formatMulai} — {$formatSelesai})";
        }

        return "{$formatMulai} s/d {$formatSelesai}";
    }

    #[Computed]
    public function hasPresensiData(): bool
    {
        [$mulai, $selesai] = $this->getPeriode();

        return $this->baseQuery()
            ->without(['presensi', 'lembur', 'unit_kerja'])
            ->whereHas('presensi', fn($q) => $q->whereBetween('tanggal', [$mulai, $selesai]))
            ->exists();
    }

    #[Computed]
    public function emptyStateMessage(): string
    {
        if (!$this->baseQuery()->exists()) {
            return $this->search
                ? "Pegawai dengan kata pencarian '{$this->search}' tidak ditemukan."
                : "Tidak ada data pegawai pada filter atau unit kerja yang dipilih.";
        }

        [$mulai, $selesai] = $this->getPeriode();
        $formatMulai   = $mulai->translatedFormat('d F Y');
        $formatSelesai = $selesai->translatedFormat('d F Y');

        if ($this->selectedPeriodeMulai || $this->selectedPeriodeSelesai) {
            return "Belum ada data rekapitulasi presensi yang terekam untuk periode: {$formatMulai} s/d {$formatSelesai}.";
        }

        return "Belum ada data rekapitulasi presensi yang terekam untuk bulan " . now()->translatedFormat('F Y') . ".";
    }

    #[Computed]
    public function exportPreviewData(): array
    {
        $isPegawaiEmpty = !$this->baseQuery()->exists();
        $isEmpty        = $isPegawaiEmpty || !$this->hasPresensiData;

        $singleEmployeeName = null;

        if (!$isPegawaiEmpty && !empty($this->search)) {
            $uniqueNips = $this->baseQuery()
                ->reorder()
                ->select('pegawai.nip')
                ->distinct()
                ->limit(2)
                ->pluck('pegawai.nip');

            if ($uniqueNips->count() === 1) {
                $pegawai            = Pegawai::where('nip', $uniqueNips->first())->first();
                $singleEmployeeName = $pegawai?->nama ?? 'NIP. ' . $uniqueNips->first();
            }
        }

        return [
            'isEmpty'        => $isEmpty,
            'periode'        => $this->infoPeriodeAktif,
            'unit'           => $this->selectedUnitKerja ?? 'Semua Unit Kerja',
            'singleEmployee' => $singleEmployeeName,
        ];
    }

    // ──────────────────────────────────────────────────────────────────
    // Actions
    // ──────────────────────────────────────────────────────────────────

    public function exportData()
    {
        if (!$this->baseQuery()->exists() || !$this->hasPresensiData) {
            return;
        }

        $preview  = $this->exportPreviewData;
        $fileName = $this->buildExportFileName($preview);

        return (new RekapitulasiPresensiExport($this->baseQuery()))->download($fileName);
    }

    public function openExportPreview(): void
    {
        $this->dispatch('open-export');
    }

    // ──────────────────────────────────────────────────────────────────
    // Render
    // ──────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.manajemen.presensi.tabel-rekapitulasi-presensi');
    }

    // ──────────────────────────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────────────────────────

    /**
     * Mengembalikan [Carbon $mulai, Carbon $selesai] berdasarkan filter aktif.
     *
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

    /**
     * Base query pegawai dengan scoping berdasarkan role user.
     */
    private function baseQuery()
    {
        $user = Auth::user();

        $query = Pegawai::query()
            ->select('pegawai.*')
            ->leftJoin('unit_kerja', 'pegawai.unit_kerja_id', '=', 'unit_kerja.id')
            ->orderBy('unit_kerja.name')
            ->orderBy('pegawai.nama');

        // Scoping berdasarkan role
        if ($user->hasRole('SDM Universitas')) {
            $unitKerjaIds = UnitKerja::whereHas('unitSdm', fn($q) => $q->where('name', 'SDM Universitas'))
                ->pluck('id');

            $query->whereIn('pegawai.unit_kerja_id', $unitKerjaIds);
        } elseif ($user->hasRole('Pimpinan')) {
            $unitId = $user->pegawai?->memimpin_unit?->id;

            $unitId
                ? $query->where('pegawai.unit_kerja_id', $unitId)
                : $query->whereNull('pegawai.id');
        }

        // Filter unit kerja
        if ($this->selectedUnitKerja) {
            $query->where('unit_kerja.name', $this->selectedUnitKerja);
        }

        // Filter search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('pegawai.nama', 'like', "%{$this->search}%")
                    ->orWhere('pegawai.nip', 'like', "%{$this->search}%");
            });
        }

        return $query;
    }

    /**
     * Membangun nama file untuk export berdasarkan filter aktif.
     */
    private function buildExportFileName(array $preview): string
    {
        $parts = ['Rekapitulasi_Presensi'];

        if (!empty($preview['singleEmployee'])) {
            $parts[] = str_replace(' ', '_', $preview['singleEmployee']);
        }

        if ($this->selectedUnitKerja) {
            $parts[] = str_replace(' ', '_', $this->selectedUnitKerja);
        }

        $periode = str_replace([' ', '(', ')', '/', '—'], ['_', '', '', '-', '-'], $preview['periode']);
        $parts[] = $periode;

        return implode('_', $parts) . '.xlsx';
    }
}