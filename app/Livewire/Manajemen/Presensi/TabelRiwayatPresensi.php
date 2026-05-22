<?php

namespace App\Livewire\Manajemen\Presensi;

use App\Models\JenisPegawai;
use App\Models\StatusKehadiran;
use App\Models\UnitKerja;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TabelRiwayatPresensi extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'tailwind';

    public $unit_kerja;
    public $unit_kerja_universitas;
    public $jenis_pegawai;
    public $status_kehadiran;

    // Filter
    public $selectedUnitKerja;
    public $selectedJenisPegawai;
    public $selectedStatusKehadiran;


    // Search
    public $search = '';

    public function mount()
    {
        $this->unit_kerja = UnitKerja::with('unitSdm')->orderBy('name', 'asc')->get();
        $this->unit_kerja_universitas = UnitKerja::with('unitSdm')
            ->whereHas('unitSdm', function ($query) {
                $query->where('name', 'SDM Universitas');
            })
            ->get();

        $this->jenis_pegawai = JenisPegawai::pluck('jenis');
        $this->status_kehadiran = StatusKehadiran::get();
    }

    public function render()
    {
        return view('livewire.manajemen.presensi.tabel-riwayat-presensi');
    }
}