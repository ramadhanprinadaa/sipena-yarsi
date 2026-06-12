<?php

namespace App\Livewire\Manajemen\Presensi;

use App\Exports\RiwayatPresensiExport;
use App\Models\JenisPegawai;
use App\Models\Pegawai;
use App\Models\Presensi;
use App\Models\StatusKehadiran;
use App\Models\UnitKerja;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\WithPagination;

class TabelRiwayatPresensi extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'tailwind';

    public array $unitKerja;
    public array $unitKerjaUniversitas;
    public array $jenisPegawai;
    public array $statusKehadiran;

    // Filter
    #[Session]
    public ?string $selectedUnitKerja = null;
    #[Session]
    public ?string $selectedJenisPegawai = null;
    #[Session]
    public ?string $selectedStatusKehadiran = null;
    #[Session]
    public ?string $selectedPeriodeMulai = null;
    #[Session]
    public ?string $selectedPeriodeSelesai = null;

    // Search
    #[Session]
    public $search = '';

    #[On('refresh-table-riwayat-presensi')]
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

        $this->jenisPegawai = JenisPegawai::pluck('jenis')->toArray();
        $this->statusKehadiran = StatusKehadiran::pluck('status')->toArray();
    }

    public function updated(string $property): void
    {
        if (in_array($property, [
            'search',
            'selectedUnitKerja',
            'selectedJenisPegawai',
            'selectedStatusKehadiran',
            'selectedPeriodeMulai',
            'selectedPeriodeSelesai',
        ])) {
            $this->resetPage();
        }
    }

    protected function baseQuery()
    {
        $today = now()->subDay()->format('Y-m-d');
        $user = Auth::user();

        // 1. Parsing Tanggal
        $filterMulai = $this->selectedPeriodeMulai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeMulai)->format('Y-m-d')
            : null;

        $filterSelesai = $this->selectedPeriodeSelesai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeSelesai)->format('Y-m-d')
            : null;

        if ($filterSelesai && !$filterMulai) {
            $filterMulai = $filterSelesai;
        }

        // 2. Inisialisasi Base Query & Relasi
        $query = Presensi::query()
            ->join('pegawai', 'pegawai.nip', '=', 'presensi.pegawai_nip')
            ->join('unit_kerja', 'unit_kerja.id', '=', 'pegawai.unit_kerja_id')
            ->select([
                'presensi.id',
                'presensi.pegawai_nip',
                'presensi.tanggal',
                'presensi.jam_masuk',
                'presensi.jam_keluar',
                'presensi.status_kehadiran_id',
            ])
            ->with([
                'statusKehadiran:id,status,warna',
                'pegawai:nip,nama,jenis_pegawai_id,unit_kerja_id',
                'pegawai.jenis_pegawai:id,jenis',
                'pegawai.unit_kerja:id,name',
            ]);

        // 3. Scoping data berdasarkan Role Auth
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

        // 4. Implementasi Filter
        return $query
            // Filter Tanggal
            ->when(!$filterMulai && !$filterSelesai, fn($q) => $q->whereDate('presensi.tanggal', $today))
            ->when($filterMulai, function ($q) use ($filterMulai, $filterSelesai) {
                if ($filterSelesai) {
                    $q->whereBetween('presensi.tanggal', [$filterMulai, $filterSelesai]);
                } else {
                    $q->whereDate('presensi.tanggal', $filterMulai);
                }
            })
            // Filter Search (Pencarian)
            ->when($this->search, function ($q) {
                $q->where(function ($subQuery) {
                    $subQuery->where('pegawai.nama', 'like', '%' . $this->search . '%')
                        ->orWhere('pegawai.nip', 'like', '%' . $this->search . '%');
                });
            })
            // Filter Dropdown
            ->when($this->selectedUnitKerja, fn($q) => $q->where('unit_kerja.name', $this->selectedUnitKerja))
            ->when($this->selectedJenisPegawai, function ($q) {
                $q->whereHas('pegawai.jenis_pegawai', fn($jenis) => $jenis->where('jenis', $this->selectedJenisPegawai));
            })
            ->when($this->selectedStatusKehadiran, function ($query) {
                $query->whereHas('statusKehadiran', fn($status) => $status->where('status', $this->selectedStatusKehadiran));
            });
    }

    #[Computed]
    public function riwayatPresensi()
    {
        return $this->baseQuery()
            ->orderBy('unit_kerja.name')
            ->orderBy('pegawai.nama')
            ->paginate(10);
    }

    #[Computed]
    public function emptyStateMessage(): string
    {
        $tanggal = now()->subDay()->translatedFormat('d M Y');

        $mulai = $this->selectedPeriodeMulai
            ? Carbon::createFromFormat(
                'd/m/Y',
                $this->selectedPeriodeMulai
            )->format('Y-m-d')
            : null;

        $selesai = $this->selectedPeriodeSelesai
            ? Carbon::createFromFormat(
                'd/m/Y',
                $this->selectedPeriodeSelesai
            )->format('Y-m-d')
            : null;

        // Default terakhir tersedia / H-1
        if (!$mulai && !$selesai) {
            return "Belum ada data presensi pada tanggal {$tanggal}.";
        }

        // Jika hanya mulai
        if ($mulai && !$selesai) {

            $tanggal = Carbon::parse($mulai)
                ->translatedFormat('d F Y');

            return "Belum ada data presensi pada {$tanggal}.";
        }

        // Jika periode
        $tanggalMulai = Carbon::parse($mulai)
            ->translatedFormat('d F Y');

        $tanggalSelesai = Carbon::parse($selesai)
            ->translatedFormat('d F Y');

        return "Belum ada data presensi pada periode {$tanggalMulai} - {$tanggalSelesai}.";
    }

    public function showDetailRiwayat(int $id): void
    {
        $this->dispatch('load-detail-riwayat', presensiId: $id);
    }

    public function openExportPreview(): void
    {
        $this->dispatch('open-export');
    }

    #[Computed]
    public function exportPreviewData(): array
    {
        $data = $this->riwayatPresensi;
        $isEmpty = $data->isEmpty();

        // 1. Format Periode Presensi
        $tanggalMulai = $this->selectedPeriodeMulai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeMulai)->translatedFormat('d F Y')
            : null;
        $tanggalSelesai = $this->selectedPeriodeSelesai
            ? Carbon::createFromFormat('d/m/Y', $this->selectedPeriodeSelesai)->translatedFormat('d F Y')
            : null;

        if ($tanggalMulai && $tanggalSelesai) {
            $periode = $tanggalMulai . ' - ' . $tanggalSelesai;
        } elseif ($tanggalMulai) {
            $periode = $tanggalMulai;
        } else {
            $periode = 'Hari Ini (' . now()->subDay()->translatedFormat('d M Y') . ')';
        }

        // 2. Deteksi Satu Pegawai (Jika filter search aktif)
        $singleEmployeeName = null;
        if (!$isEmpty && !empty($this->search)) {
            $uniqueNips = $this->baseQuery()
                ->select('pegawai_nip')
                ->distinct()
                ->limit(2)
                ->pluck('pegawai_nip');

            if ($uniqueNips->count() === 1) {
                $pegawai = Pegawai::where('nip', $uniqueNips->first())->first();
                $singleEmployeeName = $pegawai ? $pegawai->nama : 'NIP. ' . $uniqueNips->first();
            }
        }

        return [
            'isEmpty'        => $isEmpty,
            'periode'        => $periode,
            'unit'           => $this->selectedUnitKerja ?? 'Semua Unit Kerja',
            'jenis'          => $this->selectedJenisPegawai ?? 'Semua Jenis Pegawai',
            'status'         => $this->selectedStatusKehadiran ?? 'Semua Status Kehadiran',
            'singleEmployee' => $singleEmployeeName,
        ];
    }

    public function exportData()
    {
        $query = $this->baseQuery();

        if ($query->count() === 0) {
            return;
        }

        // 3. Bangun nama file dinamis berdasarkan filter yang aktif
        $preview = $this->exportPreviewData;
        $fileNameParts = ['Riwayat_Presensi'];

        // Jika filter satu pegawai terdeteksi
        if (!empty($preview['singleEmployee'])) {
            $fileNameParts[] = str_replace(' ', '_', $preview['singleEmployee']);
        }

        // Jika filter unit kerja aktif
        if ($this->selectedUnitKerja) {
            $fileNameParts[] = str_replace(' ', '_', $this->selectedUnitKerja);
        }

        // Bersihkan string periode agar aman untuk nama file (hapus spasi, kurung, garis miring)
        $periodeAman = str_replace([' ', '(', ')', '/'], ['_', '', '', '-'], $preview['periode']);
        $fileNameParts[] = $periodeAman;

        $fileName = implode('_', $fileNameParts) . '.xlsx';

        return (new RiwayatPresensiExport($query))->download($fileName);
    }

    public function render()
    {
        return view('livewire.manajemen.presensi.tabel-riwayat-presensi');
    }
}
