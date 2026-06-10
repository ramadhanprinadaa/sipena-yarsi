<?php

namespace App\Livewire\Manajemen\Pegawai;

use App\Exports\DataPegawaiExport; // Sesuaikan dengan nama class Export Anda
use App\Models\JenisPegawai;
use App\Models\JenjangPendidikan;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\WithPagination;

class TabelPegawai2 extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'tailwind';

    public array $unitKerja;
    public array $unitKerjaUniversitas;
    public array $jenisPegawai;
    public array $jenjangPendidikan;

    // Filter Baru
    #[Session]
    public ?string $selectedUnitKerja = null;
    #[Session]
    public ?string $selectedJabatan = null;
    #[Session]
    public ?string $selectedJenjangPendidikan = null;
    #[Session]
    public ?string $selectedJenisPegawai = null;
    #[Session]
    public ?string $selectedStatus = null;

    // Search
    #[Session]
    public $search = '';

    // Filter Urutan Data
    #[Session]
    public ?string $sortField = null;
    #[Session]
    public ?string $sortDirection = 'asc';

    #[On('refresh-table')]
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
        $this->jenjangPendidikan = JenjangPendidikan::pluck('kode')->toArray();
    }

    public function updated(string $property): void
    {
        if (in_array($property, [
            'search',
            'selectedUnitKerja',
            'selectedJabatan',
            'selectedJenisPegawai',
            'selectedJenjangPendidikan',
            'selectedStatus',
        ])) {
            $this->resetPage();
        }
    }

    protected function baseQuery()
    {
        $user = Auth::user();

        // 1. Inisialisasi Base Query & Eager Loading (Hemat Memory & Query)
        $query = Pegawai::query()
            ->select([
                'pegawai.id',
                'pegawai.unit_kerja_id',
                'pegawai.jenis_pegawai_id',
                'pegawai.status_pegawai_id',
                'pegawai.nip',
                'pegawai.nama',
                'pegawai.status',
                'pegawai.tanggal_habis_kontrak',
            ])
            ->leftJoin('unit_kerja', 'pegawai.unit_kerja_id', '=', 'unit_kerja.id')
            ->with([
                'unit_kerja:id,name',
                'jenis_pegawai:id,jenis',
                'memimpin_unit'
            ]);

        // 2. Scoping data berdasarkan Role Auth
        if ($user->hasRole('SDM Universitas')) {
            $unitKerjaIds = UnitKerja::whereHas('unitSdm', function ($q) {
                $q->where('name', 'SDM Universitas');
            })->pluck('id');

            $query->whereIn('pegawai.unit_kerja_id', $unitKerjaIds)
                // ->where('pegawai.id', '!=', $user->pegawai?->id)
                ->whereHas('user.role', function ($q) {
                    $q->where('name', '!=', 'SDM Universitas');
                });
        } elseif ($user->hasRole('Pimpinan')) {
            $unit_id = $user->pegawai?->memimpin_unit?->id;

            if (!$unit_id) {
                $query->whereNull('pegawai.id');
            } else {
                $query->where('pegawai.unit_kerja_id', $unit_id);
                    // ->where('pegawai.id', '!=', $user->pegawai?->id);
            }
        }

        // 3. Implementasi Filter Baru
        return $query
            // Pencarian
            ->when($this->search, function ($q) {
                $q->where(function ($subQuery) {
                    $subQuery->where('pegawai.nama', 'like', '%' . $this->search . '%')
                        ->orWhere('pegawai.nip', 'like', '%' . $this->search . '%');
                });
            })
            // Filter Unit Kerja (Menggunakan relasi join untuk efisiensi)
            ->when($this->selectedUnitKerja, fn($q) => $q->where('unit_kerja.name', $this->selectedUnitKerja))
            // Filter Jenis Pegawai
            ->when($this->selectedJenisPegawai, function ($q) {
                $q->whereHas('jenis_pegawai', fn($jenis) => $jenis->where('jenis', $this->selectedJenisPegawai));
            })
            // Filter Status
            ->when($this->selectedStatus, fn($q) => $q->where('pegawai.status', $this->selectedStatus))
            // Filter Jabatan (Pimpinan / Pegawai)
            ->when($this->selectedJabatan, function ($q) {
                if ($this->selectedJabatan === 'Pimpinan') {
                    $q->whereHas('memimpin_unit');
                } elseif ($this->selectedJabatan === 'Pegawai') {
                    $q->whereDoesntHave('memimpin_unit');
                }
            })
            // Filter Jenjang Pendidikan
            ->when($this->selectedJenjangPendidikan, function ($q) {
                $q->whereHas('riwayatPendidikan', function ($sub) {
                    $sub->whereHas('jenjangPendidikan', function ($jjg) {
                        $jjg->where('kode', $this->selectedJenjangPendidikan);
                    });
                });
            });
    }

    #[Computed]
    public function pegawai()
    {
        return $this->baseQuery()
            ->when($this->sortField, function ($query) {
                if ($this->sortField === 'unit_kerja') {
                    $query->orderBy('unit_kerja.name', $this->sortDirection)
                        ->orderBy('pegawai.nama', 'asc');
                } else {
                    $query->orderBy($this->sortField, $this->sortDirection);
                }
            }, function ($query) {
                // Default Sorting
                $query->orderBy('unit_kerja.name', 'asc')
                    ->orderBy('pegawai.nama', 'asc');
            })
            ->paginate(10);
    }

    #[Computed]
    public function emptyStateMessage(): string
    {
        if ($this->search) {
            return "Tidak ditemukan data pegawai dengan kata kunci '{$this->search}'.";
        }

        if ($this->selectedUnitKerja || $this->selectedJabatan || $this->selectedJenisPegawai || $this->selectedStatus || $this->selectedJenjangPendidikan) {
            return "Belum ada data pegawai yang sesuai dengan filter yang Anda terapkan.";
        }

        return "Belum ada data pegawai yang terdaftar.";
    }

    // --- Export Logic ---

    public function openExportPreview(): void
    {
        $this->dispatch('open-export');
    }

    #[Computed]
    public function exportPreviewData(): array
    {
        $data = $this->pegawai;
        $isEmpty = $data->isEmpty();

        // Deteksi Satu Pegawai (Jika filter search spesifik NIP/Nama)
        $singleEmployeeName = null;
        if (!$isEmpty && !empty($this->search)) {
            $uniqueNips = $this->baseQuery()
                ->select('pegawai.nip')
                ->distinct()
                ->limit(2)
                ->pluck('nip');

            if ($uniqueNips->count() === 1) {
                $pegawai = Pegawai::where('nip', $uniqueNips->first())->first();
                $singleEmployeeName = $pegawai ? $pegawai->nama : 'NIP. ' . $uniqueNips->first();
            }
        }

        return [
            'isEmpty'        => $isEmpty,
            'unit'           => $this->selectedUnitKerja ?? 'Semua Unit Kerja',
            'jabatan'        => $this->selectedJabatan ?? 'Semua Jabatan',
            'jenis'          => $this->selectedJenisPegawai ?? 'Semua Jenis Pegawai',
            'pendidikan'     => $this->selectedJenjangPendidikan ?? 'Semua Jenjang Pendidikan',
            'status'         => $this->selectedStatus ?? 'Semua Status',
            'singleEmployee' => $singleEmployeeName,
        ];
    }

    public function exportData()
    {
        $query = $this->baseQuery();

        if ($query->count() === 0) {
            return;
        }

        $preview = $this->exportPreviewData;
        $fileNameParts = ['Data_Pegawai'];

        // Nama file dinamis
        if (!empty($preview['singleEmployee'])) {
            $fileNameParts[] = str_replace(' ', '_', $preview['singleEmployee']);
        }
        if ($this->selectedUnitKerja) {
            $fileNameParts[] = str_replace(' ', '_', $this->selectedUnitKerja);
        }

        $fileName = implode('_', $fileNameParts) . '.xlsx';

        // Pastikan Anda sudah membuat class exportnya (misal: DataPegawaiExport)
        return (new DataPegawaiExport($query))->download($fileName);
    }

    // --- Sorting Logic ---

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
        $this->resetPage();
    }

    public function sortIcon($field)
    {
        if ($this->sortField !== $field) {
            return 'fa-sort-up text-gray-300'; // Sesuaikan dengan class styling Anda
        }
        return $this->sortDirection === 'asc'
            ? 'fa-sort-down'
            : 'fa-sort-up';
    }

    public function render()
    {
        return view('livewire.manajemen.pegawai.tabel-pegawai');
    }
}