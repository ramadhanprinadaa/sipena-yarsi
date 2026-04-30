<?php

namespace App\Livewire\Config\UnitKerja;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use Livewire\Attributes\On;

class TabelUnitKerja extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'tailwind';

    public string $selectedUnitSdm = '';
    public string $search = '';

    public string $sortField = 'unit_kerja.name';
    public string $sortDirection = 'asc';

    public bool $showDetail = false;

    public function updatedSelectedUnitSdm(){
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
            return 'fa-sort-down';
        }
        return $this->sortDirection === 'asc'
            ? 'fa-sort-down'
            : 'fa-sort-up';
    }

    public function render()
    {
        $unit_kerja = UnitKerja::with(['unitSdm', 'pegawai']);

        $unit_kerja->orderBy($this->sortField, $this->sortDirection);

        if ($this->search) {
            $unit_kerja->where(function ($q) {
                $q->where('unit_kerja.name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('pimpinan', function ($q) {
                        $q->where('nama', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->selectedUnitSdm) {
            $unit_kerja->where('unit_sdm_id', $this->selectedUnitSdm);
        }

        return view('livewire.config.unit-kerja.tabel-unit-kerja', [
            'unit_kerja' => $unit_kerja->paginate(10),
        ]);
    }

    public function openDetail($id)
    {
        $this->dispatch('open-unit-detail', $id);
        $this->showDetail = true;
    }

    #[On('refresh-table')]
    public function refreshTable() {}
}