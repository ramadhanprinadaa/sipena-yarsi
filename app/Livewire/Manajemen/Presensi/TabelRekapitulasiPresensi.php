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

    public array $unitKerja;
    public array $unitKerjaUniversitas;

    // Filter
    #[Session]
    public ?string $selectedUnitKerja = null;
    #[Session]
    public ?string $selectedPeriodeMulai = null;
    #[Session]
    public ?string $selectedPeriodeSelesai = null;

    // Search
    #[Session]
    public string $search = '';

    #[On('refresh-table-riwayat-rekapitulasi')]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->unitKerja = UnitKerja::orderBy('name')
            ->pluck('name')
            ->toArray();

        $this->unitKerjaUniversitas = UnitKerja::query()
            ->whereHas('unitSdm', function ($q) {
                $q->where('name', 'SDM Universitas');
            })
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
    }

    public function updated(string $property): void
    {
        if (in_array($property, [
            'search',
            'selectedUnitKerja',
            'selectedPeriodeMulai',
            'selectedPeriodeSelesai',
        ])) {
            $this->resetPage();
        }
    }

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

    protected function baseQuery()
    {
        $user = Auth::user();

        $mulai = $this->selectedPeriodeMulai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeMulai)->startOfDay()
            : now()->startOfMonth();

        $selesai = $this->selectedPeriodeSelesai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeSelesai)->endOfDay()
            : now()->endOfMonth();

        $query = Pegawai::query()
            ->select('pegawai.*')
            ->leftJoin('unit_kerja', 'pegawai.unit_kerja_id', '=', 'unit_kerja.id')
            ->with([
                'presensi' => function ($q) use ($mulai, $selesai) {
                    $q->whereBetween('tanggal', [$mulai, $selesai]);
                },
                'lembur' => function ($q) use ($mulai, $selesai) {
                    $q->whereBetween('tanggal_lembur', [$mulai, $selesai])
                        ->where('status', 'Selesai')
                        ->with('laporan');
                },
                'unit_kerja:id,name' // Tambahan relasi unit_kerja untuk kemudahan di Export
            ]);

        // Scoping data
        if ($user->hasRole('SDM Universitas')) {
            $unitKerjaIds = UnitKerja::whereHas('unitSdm', function ($q) {
                $q->where('name', 'SDM Universitas');
            })->pluck('id');

            $query->whereIn('pegawai.unit_kerja_id', $unitKerjaIds);
            // ->where('pegawai.id', '!=', $user->pegawai?->id);
        } elseif ($user->hasRole('Pimpinan')) {
            $unit_id = $user->pegawai?->memimpin_unit?->id;

            if (!$unit_id) {
                $query->whereNull('pegawai.id');
            } else {
                $query->where('pegawai.unit_kerja_id', $unit_id);
                // ->where('pegawai.id', '!=', $user->pegawai?->id);
            }
        }

        $query->orderBy('unit_kerja.name', 'asc')->orderBy('pegawai.nama', 'asc');

        if ($this->selectedUnitKerja) {
            $query->where('unit_kerja.name', $this->selectedUnitKerja);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('pegawai.nama', 'like', '%' . $this->search . '%')
                    ->orWhere('pegawai.nip', 'like', '%' . $this->search . '%');
            });
        }

        return $query;
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

    #[Computed] // Untuk mengecek apakah data presensi pada periode terpilih sudah ada
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
        // Cek jika pegawainya yang kosong
        if (!$this->baseQuery()->exists()) {
            return $this->search
                ? "Pegawai dengan kata pencarian '{$this->search}' tidak ditemukan."
                : "Tidak ada data pegawai pada filter atau unit kerja yang dipilih.";
        }

        // Jika pegawai ada, berarti presensi yang kosong.
        [$mulai, $selesai] = $this->getPeriode();
        $formatMulai   = $mulai->translatedFormat('d F Y');
        $formatSelesai = $selesai->translatedFormat('d F Y');

        if ($this->selectedPeriodeMulai || $this->selectedPeriodeSelesai) {
            return "Belum ada data rekapitulasi presensi yang terekam untuk periode: {$formatMulai} s/d {$formatSelesai}.";
        }

        return "Belum ada data rekapitulasi presensi yang terekam untuk bulan " . now()->translatedFormat('F Y') . ".";
    }

    #[Computed]
    public function exportPreviewData()
    {
        $isPegawaiEmpty = !$this->baseQuery()->exists();
        $hasPresensi = $this->hasPresensiData;

        // Export dianggap KOSONG (tombol mati) jika pegawai tidak ada atau presensi tidak ada
        $isEmpty = $isPegawaiEmpty || !$hasPresensi;

        $singleEmployeeName = null;

        if (!$isPegawaiEmpty && !empty($this->search)) {
            $uniqueNips = $this->baseQuery()
                ->reorder() // Mencegah error SQL Strict Mode
                ->select('pegawai.nip')
                ->distinct()
                ->limit(2)
                ->pluck('pegawai.nip');

            if ($uniqueNips->count() === 1) {
                $pegawai = Pegawai::where('nip', $uniqueNips->first())->first();
                $singleEmployeeName = $pegawai ? $pegawai->nama : 'NIP. ' . $uniqueNips->first();
            }
        }

        return [
            'isEmpty'        => $isEmpty,
            'periode'        => $this->infoPeriodeAktif,
            'unit'           => $this->selectedUnitKerja ?? 'Semua Unit Kerja',
            'singleEmployee' => $singleEmployeeName,
        ];
    }

    public function exportData()
    {
        if (!$this->baseQuery()->exists() || !$this->hasPresensiData) {
            return;
        }

        $query = $this->baseQuery();
        $preview = $this->exportPreviewData;

        $fileNameParts = ['Rekapitulasi_Presensi'];

        if (!empty($preview['singleEmployee'])) {
            $fileNameParts[] = str_replace(' ', '_', $preview['singleEmployee']);
        }
        if ($this->selectedUnitKerja) {
            $fileNameParts[] = str_replace(' ', '_', $this->selectedUnitKerja);
        }

        $periodeAman = str_replace([' ', '(', ')', '/', '—'], ['_', '', '', '-', '-'], $preview['periode']);
        $fileNameParts[] = $periodeAman;

        $fileName = implode('_', $fileNameParts) . '.xlsx';

        return (new RekapitulasiPresensiExport($query))->download($fileName);
    }

    public function openExportPreview(): void
    {
        $this->dispatch('open-export');
    }

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

    public function render()
    {
        return view('livewire.manajemen.presensi.tabel-rekapitulasi-presensi');
    }
}