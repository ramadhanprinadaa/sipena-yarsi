<?php

namespace App\Livewire\Manajemen\Pegawai;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Pegawai;
use App\Models\UnitKerja;

class TabelPegawai extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'tailwind';

    public $unit_kerja;
    public $unit_kerja_universitas;

    // Filter
    public $selectedUnitKerja;
    public $selectedGelar;
    public $selectedStatus;
    public $selectedMasaKerja;

    public $sortField = null;
    public $sortDirection = 'asc';

    public function mount()
    {
        $this->unit_kerja = UnitKerja::with('unitSdm')->get();
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

        return view('livewire.manajemen.pegawai.tabel-pegawai', [
            'pegawai' => $pegawai->paginate(10),
            'unit_kerja' => $this->unit_kerja
        ]);
    }

    public function updatedSelectedUnitKerja()
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