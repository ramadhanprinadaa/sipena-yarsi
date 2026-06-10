<?php

namespace App\Livewire\Manajemen\Pegawai;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use Livewire\Attributes\On;

class TabelPegawai extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'tailwind';

    public $unit_kerja;
    public $unit_kerja_universitas;

    public array $jenisPegawai;

    // Filter
    public $selectedUnitKerja;
    public $selectedJabatan;
    public $selectedGelar;
    public $selectedStatus;
    public $selectedMasaKerja;

    // Search
    public $search = '';

    public $sortField = null;
    public $sortDirection = 'asc';

    #[On('refresh-table')]
    public function refreshTable(): void {}

    public function mount()
    {
        $this->unit_kerja = UnitKerja::with('unitSdm')->orderBy('name', 'asc')->get();
        $this->unit_kerja_universitas = UnitKerja::with('unitSdm')
            ->whereHas('unitSdm', function ($query) {
                $query->where('name', 'SDM Universitas');
            })
            ->get();
    }

    public function render()
    {
        $user = Auth::user();

        $pegawai = Pegawai::query()
            ->leftJoin('unit_kerja', 'pegawai.unit_kerja_id', '=', 'unit_kerja.id')
            ->select('pegawai.*');

        if ($this->sortField === 'unit_kerja') {
            $pegawai->orderBy('unit_kerja.name', $this->sortDirection)
                ->orderBy('pegawai.nama', 'asc');
        } elseif ($this->sortField) {
            $pegawai->orderBy($this->sortField, $this->sortDirection);
        } else {
            $pegawai->orderBy('unit_kerja.name', 'asc')
                ->orderBy('pegawai.nama', 'asc');
        }

        if ($user->hasRole('SDM Universitas')) {

            $unitKerjaIds = UnitKerja::whereHas('unitSdm', function ($q) {
                $q->where('name', 'SDM Universitas');
            })->pluck('id');

            $pegawai->whereIn('pegawai.unit_kerja_id', $unitKerjaIds)
                ->where('pegawai.id', '!=', $user->pegawai->id)
                ->whereHas('user.role', function ($q) {
                    $q->where('name', '!=', 'SDM Universitas');
                });
        }

        if ($user->hasRole('Pimpinan')) {
            $unit_id = $user->pegawai?->memimpin_unit?->id;
            if (!$unit_id) {
                $pegawai->whereRaw('1 = 0');
            } else {
                $pegawai->where('pegawai.unit_kerja_id', $unit_id)
                    ->where('pegawai.id', '!=', $user->pegawai->id);
            }
        }

        if ($this->selectedUnitKerja) {
            $pegawai->where('pegawai.unit_kerja_id', $this->selectedUnitKerja);
        }

        if ($this->selectedJabatan) {
            if ($this->selectedJabatan === 'Pimpinan') {
                $pegawai->whereHas('memimpin_unit');
            }
            if ($this->selectedJabatan === 'Pegawai') {
                $pegawai->whereDoesntHave('memimpin_unit');
            }
        }

        if ($this->selectedGelar) {
            if ($this->selectedGelar === 'Sarjana') {
                $pegawai->where('pegawai.gelar_belakang', 'like', 'S.%');
            }
            if ($this->selectedGelar === 'Magister') {
                $pegawai->where('pegawai.gelar_belakang', 'like', 'M.%');
            }
            if ($this->selectedGelar === 'Doktor') {
                $pegawai->where(function ($q) {
                    $q->where('pegawai.gelar_depan', 'like', 'Dr%')
                        ->orWhere('pegawai.gelar_belakang', 'like', 'Dr%');
                });
            }
            if ($this->selectedGelar === 'Professor') {
                $pegawai->where('pegawai.gelar_depan', 'like', 'Prof%');
            }
        }

        if ($this->selectedStatus) {
            $pegawai->where('pegawai.status', $this->selectedStatus);
        }

        if ($this->selectedMasaKerja) {

            if ($this->selectedMasaKerja == '0-2 Tahun') {
                $pegawai->whereBetween('pegawai.tanggal_bergabung', [
                    now()->subYears(2),
                    now()->subYears(0)
                ]);
            }

            if ($this->selectedMasaKerja == '2-5 Tahun') {
                $pegawai->whereBetween('pegawai.tanggal_bergabung', [
                    now()->subYears(5),
                    now()->subYears(2)
                ]);
            }

            if ($this->selectedMasaKerja == '5-10 Tahun') {
                $pegawai->whereBetween('pegawai.tanggal_bergabung', [
                    now()->subYears(10),
                    now()->subYears(5)
                ]);
            }

            if ($this->selectedMasaKerja == '> 10 Tahun') {
                $pegawai->where('pegawai.tanggal_bergabung', '<=', now()->subYears(10));
            }
        }

        if ($this->search) {
            $pegawai->where(function ($q) {
                $q->where('pegawai.nip', 'like', '%' . $this->search . '%')
                    ->orWhere('pegawai.nama', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.manajemen.pegawai.tabel-pegawai', [
            'pegawai' => $pegawai->paginate(10),
            'unit_kerja' => $this->unit_kerja
        ]);
    }

    public function updatedSelectedUnitKerja()
    {
        $this->resetPage();
    }

    public function updatedSelectedJabatan()
    {
        $this->resetPage();
    }

    public function updatedSelectedGelar()
    {
        $this->resetPage();
    }

    public function updatedSelectedStatus()
    {
        $this->resetPage();
    }

    public function updatedSelectedMasaKerja()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
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
            return 'fa-sort-up';
        }
        return $this->sortDirection === 'asc'
            ? 'fa-sort-down'
            : 'fa-sort-up';
    }
}