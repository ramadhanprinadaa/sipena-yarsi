<?php

namespace App\Livewire\Manajemen\Pegawai\DetailPegawai\Biodata;

use App\Models\Pegawai;
use Livewire\Attributes\On;
use Livewire\Component;

class Biodata extends Component
{
    public Pegawai $pegawai;

    public function mount(Pegawai $pegawai)
    {
        $this->pegawai = $pegawai;
    }

    #[On('refresh-biodata')]
    public function refreshBiodata(): void
    {
        $this->pegawai = $this->pegawai->fresh();
    }

    public function render()
    {
        return view('livewire.manajemen.pegawai.detail-pegawai.biodata.biodata');
    }
}