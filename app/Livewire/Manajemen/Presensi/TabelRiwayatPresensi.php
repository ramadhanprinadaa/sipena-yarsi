<?php

namespace App\Livewire\Manajemen\Presensi;

use App\Models\JenisPegawai;
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

    #[Computed]
    public function riwayatPresensi()
    {
        $today = now()->subDay()->format('Y-m-d');
        $user = Auth::user();

        $filterMulai = $this->selectedPeriodeMulai
            ? Carbon::createFromFormat(
                'd/m/Y',
                $this->selectedPeriodeMulai
            )->format('Y-m-d')
            : null;

        $filterSelesai = $this->selectedPeriodeSelesai
            ? Carbon::createFromFormat(
                'd/m/Y',
                $this->selectedPeriodeSelesai
            )->format('Y-m-d')
            : null;

        if ($filterSelesai && !$filterMulai) {
            $filterMulai = $filterSelesai;
        }

        // Inisialisasi Base query dan join
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

        // Scoping data untuk pimpinan dan sdm universitas
        if ($user->hasRole('SDM Universitas')) {
            $unitKerjaIds = UnitKerja::whereHas('unitSdm', function ($q) {
                $q->where('name', 'SDM Universitas');
            })->pluck('id');

            $query->whereIn('pegawai.unit_kerja_id', $unitKerjaIds)
                ->where('pegawai.id', '!=', $user->pegawai?->id)
                ->whereHas('pegawai.user.role', function ($q) {
                    $q->where('name', '!=', 'SDM Universitas');
                });
        } elseif ($user->hasRole('Pimpinan')) {
            $unit_id = $user->pegawai?->memimpin_unit?->id;

            if (!$unit_id) {
                $query->whereNull('presensi.id'); // Kosongkan hasil jika tidak punya unit
            } else {
                $query->where('pegawai.unit_kerja_id', $unit_id)
                    ->where('pegawai.id', '!=', $user->pegawai?->id);
            }
        }

        // Return
        return $query

            // Default Hari Ini
            ->when(
                !$filterMulai && !$filterSelesai,
                fn($q) => $q->whereDate('presensi.tanggal', $today)
            )

            // Periode Tanggal Mulai dan Selesai
            ->when($filterMulai, function ($q) use (
                $filterMulai,
                $filterSelesai
            ) {
                if ($filterSelesai) {
                    $q->whereBetween('presensi.tanggal', [$filterMulai, $filterSelesai]);
                    return;
                }
                $q->whereDate('presensi.tanggal', $filterMulai);
            })

            // Filter Search
            ->when($this->search, function ($q) {
                $q->where(function ($subQuery) {
                    $subQuery->where('pegawai.nama', 'like', '%' . $this->search . '%')
                        ->orWhere('pegawai.nip', 'like', '%' . $this->search . '%');
                });
            })

            // Filter Unit Kerja
            ->when($this->selectedUnitKerja, function ($q) {
                $q->where('unit_kerja.name', $this->selectedUnitKerja);
            })

            // Filter Jenis Pegawai
            ->when($this->selectedJenisPegawai, function ($q) {
                $q->whereHas('pegawai.jenis_pegawai', function ($jenis) {
                    $jenis->where('jenis', $this->selectedJenisPegawai);
                });
            })

            // Filter Status Kehadiran
            ->when($this->selectedStatusKehadiran, function ($query) {
                $query->whereHas('statusKehadiran', function ($status) {
                    $status->where(
                        'status',
                        $this->selectedStatusKehadiran
                    );
                });
            })

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

    public function render()
    {
        return view('livewire.manajemen.presensi.tabel-riwayat-presensi');
    }
}