<?php

namespace App\Livewire\Manajemen\Pegawai;

use App\Exports\PegawaiExport;
use App\Models\JenisPegawai;
use App\Models\JenjangPendidikan;
use App\Models\Pegawai;
use App\Models\StatusPegawai;
use App\Models\UnitKerja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class TabelPegawai extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'tailwind';

    public array $unitKerja;
    public array $unitKerjaUniversitas;
    public array $jenisPegawai;
    public array $jenjangPendidikan;
    public array $statusPegawai;

    // Filter
    #[Session]
    public ?string $selectedUnitKerja = null;
    #[Session]
    public ?string $selectedJabatan = null;
    #[Session]
    public ?string $selectedJenjangPendidikan = null;
    #[Session]
    public ?string $selectedJenisPegawai = null;
    #[Session]
    public ?string $selectedStatusPegawai = null;
    #[Session]
    public ?string $selectedStatusAktif = null;

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
        $this->statusPegawai = StatusPegawai::pluck('status')->toArray();
    }

    public function updated(string $property): void
    {
        if (in_array($property, [
            'search',
            'selectedUnitKerja',
            'selectedJabatan',
            'selectedJenisPegawai',
            'selectedJenjangPendidikan',
            'selectedStatusPegawai',
            'selectedStatusAktif',
        ])) {
            $this->resetPage();
        }
    }

    public function baseQuery()
    {
        $user = Auth::user();

        // 1. Inisialisasi Base Query & Eager Loading
        $query = Pegawai::query()
            ->select([
                'pegawai.id',
                'pegawai.unit_kerja_id',
                'pegawai.jenis_pegawai_id',
                'pegawai.status_pegawai_id',
                'pegawai.nip',
                'pegawai.nama',
                'pegawai.status',
                'pegawai.tanggal_bergabung',
                'pegawai.tanggal_habis_kontrak',
                'pegawai.tanggal_pensiun',
            ])
            ->leftJoin('unit_kerja', 'pegawai.unit_kerja_id', '=', 'unit_kerja.id')
            ->with([
                'unit_kerja:id,name',
                'jenis_pegawai:id,jenis',
                'status_pegawai:id,status',
                'riwayatPendidikan.jenjangPendidikan',
                'memimpin_unit',
            ]);

        // 2. Scoping data berdasarkan Role Auth
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

        // 3. Implementasi Filter
        return $query

            // Search
            ->when($this->search, function ($q) {
                $q->where(function ($subQuery) {
                    $subQuery->where('pegawai.nama', 'like', '%' . $this->search . '%')
                        ->orWhere('pegawai.nip', 'like', '%' . $this->search . '%');
                });
            })

            // Filter Unit Kerja
            ->when($this->selectedUnitKerja, fn($q) => $q->where('unit_kerja.name', $this->selectedUnitKerja))

            // Filter Jenis Pegawai
            ->when($this->selectedJenisPegawai, function ($q) {
                $q->whereHas('jenis_pegawai', fn($jenis) => $jenis->where('jenis', $this->selectedJenisPegawai));
            })

            // Filter Status Pegawai
            ->when($this->selectedStatusPegawai, function ($q) {
                $q->whereHas('status_pegawai', function ($sub) {
                    $sub->where('status', $this->selectedStatusPegawai);
                });
            })

            // Filter Status Aktif
            ->when($this->selectedStatusAktif, fn($q) => $q->where('pegawai.status', $this->selectedStatusAktif))

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
                } elseif ($this->sortField === 'tanggal_berakhir') {
                    // sort tanggal berakhir
                    $query->orderByRaw("COALESCE(pegawai.tanggal_habis_kontrak, pegawai.tanggal_pensiun) IS NULL")
                        ->orderByRaw("COALESCE(pegawai.tanggal_habis_kontrak, pegawai.tanggal_pensiun) {$this->sortDirection}")
                        ->orderBy('pegawai.id', 'asc');
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

        if ($this->selectedUnitKerja || $this->selectedJabatan || $this->selectedJenisPegawai || $this->selectedStatusAktif || $this->selectedJenjangPendidikan) {
            return "Belum ada data pegawai yang sesuai dengan filter yang Anda terapkan.";
        }

        return "Belum ada data pegawai yang terdaftar.";
    }

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
            return 'fa-sort-up text-gray-300';
        }
        return $this->sortDirection === 'asc'
            ? 'fa-sort-down'
            : 'fa-sort-up';
    }

    #[On('export-table')]
    public function openExportPreview(): void
    {
        $this->dispatch('open-export');
    }

    #[Computed]
    public function exportPreviewData(): array
    {
        $data = $this->pegawai;
        $isEmpty = $data->isEmpty();

        return [
            'isEmpty'        => $isEmpty,
            'unit'           => $this->selectedUnitKerja ?? 'Semua Unit Kerja',
            'pendidikan'     => $this->selectedJenjangPendidikan ?? 'Semua Jenjang Pendidikan',
            'jabatan'        => $this->selectedJabatan ?? 'Semua Jabatan',
            'jenis'          => $this->selectedJenisPegawai ?? 'Semua Jenis Pegawai',
            'status_pegawai' => $this->selectedStatusPegawai ?? 'Semua Status Pegawai',
            'status_aktif'   => $this->selectedStatusAktif ?? 'Semua Status Aktif',

        ];
    }

    public function exportData()
    {
        $query = $this->baseQuery();

        if ($query->count() === 0) {
            return;
        }

        // Get Sort Urutan Data
        if ($this->sortField) {
            if ($this->sortField === 'unit_kerja') {
                $query->orderBy('unit_kerja.name', $this->sortDirection)
                    ->orderBy('pegawai.nama', 'asc');
            } elseif ($this->sortField === 'tanggal_berakhir') {
                $query->orderByRaw("COALESCE(pegawai.tanggal_habis_kontrak, pegawai.tanggal_pensiun) IS NULL")
                    ->orderByRaw("COALESCE(pegawai.tanggal_habis_kontrak, pegawai.tanggal_pensiun) {$this->sortDirection}")
                    ->orderBy('pegawai.id', 'asc');
            } else {
                $query->orderBy($this->sortField, $this->sortDirection);
            }
        } else {
            $query->orderBy('unit_kerja.name', 'asc')
                ->orderBy('pegawai.nama', 'asc');
        }

        // Set Nama File Dasar
        $fileNameParts = ['Data_Pegawai'];

        // 2. Set Filter
        if ($this->selectedUnitKerja) {
            $fileNameParts[] = Str::slug($this->selectedUnitKerja, '_');
        }

        if ($this->selectedJenjangPendidikan) {
            $fileNameParts[] = Str::slug($this->selectedJenjangPendidikan, '_');
        }

        if ($this->selectedJenisPegawai) {
            $fileNameParts[] = Str::slug($this->selectedJenisPegawai, '_');
        }

        if ($this->selectedJabatan) {
            $fileNameParts[] = Str::slug($this->selectedJabatan, '_');
        }

        if ($this->selectedStatusPegawai) {
            $fileNameParts[] = Str::slug($this->selectedStatusPegawai, '_');
        }

        if ($this->selectedStatusAktif) {
            $fileNameParts[] = Str::slug($this->selectedStatusAktif, '_');
        }

        // Set Timestamp (Waktu) di akhir agar file tidak tertimpa/duplikat
        $fileNameParts[] = now()->format('Ymd_His');

        // Gabungkan menjadi 1 nama file
        $fileName = implode('_', $fileNameParts) . '.xlsx';

        return (new PegawaiExport($query))->download($fileName);
    }

    public function render()
    {
        return view('livewire.manajemen.pegawai.tabel-pegawai');
    }
}