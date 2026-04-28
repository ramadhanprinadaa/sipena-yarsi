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

    // Filter
    public $selectedUnitKerja;
    public $selectedGelar;
    public $selectedStatus;
    public $selectedMasaKerja;

    public function mount()
    {
        $this->unit_kerja = UnitKerja::with('unitSdm')->get();
    }

    public function render()
    {
        $pegawai = Pegawai::with('UnitKerja')
            ->orderBy(UnitKerja::select('name')->whereColumn('pegawai.unit_kerja_id', 'unit_kerja.id'))
            ->orderBy('pegawai.nama');

        // ubah besok
        if (auth()->user()->hasRole('SDM Universitas')) {
            $unitId = auth()->user()->pegawai->unit_kerja_id;

            $unitIds = UnitKerja::where('unit_sdm_id', $unitId)
                ->pluck('id')
                ->push($unitId);

            $pegawai->whereIn('pegawai.unit_kerja_id', $unitIds);
        }
        if (auth()->user()->hasRole('Pimpinan')) {
            $pegawai->where('pegawai.unit_kerja_id', Auth::user()->pegawai->unit_kerja_id)
                ->where('pegawai.id', '!=', Auth::user()->pegawai->id);
        }
        if ($this->selectedUnitKerja) {
            $pegawai->where('pegawai.unit_kerja_id', $this->selectedUnitKerja);
        }
        return view('livewire.manajemen.pegawai.tabel-pegawai', [
            'pegawai' => $pegawai->paginate(10),
        ]);
    }

    public function updatedSelectedUnitKerja()
    {
        $this->resetPage();
    }
}
