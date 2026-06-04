<?php

namespace App\Livewire\Manajemen\Pegawai\DetailPegawai;

use App\Models\Pegawai;
use Livewire\Component;

class Biodata extends Component
{
    public Pegawai $pegawai;

    public function mount(Pegawai $pegawai)
    {
        $this->pegawai = $pegawai;
    }

    public function render()
    {
        return view('livewire.manajemen.pegawai.detail-pegawai.biodata');
    }
}