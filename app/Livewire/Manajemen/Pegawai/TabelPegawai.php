<?php

namespace App\Livewire\Manajemen\Pegawai;

use Livewire\Component;
use App\Models\Pegawai;

class TabelPegawai extends Component
{
    public $pegawai;

    public function mount(){
        $this->pegawai = Pegawai::with('unitKerja')->get();
    }
    public function render()
    {
        return view('livewire.manajemen.pegawai.tabel-pegawai');
    }
}