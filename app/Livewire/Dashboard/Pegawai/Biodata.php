<?php

namespace App\Livewire\Dashboard\Pegawai;

use App\Models\Pegawai;
use Livewire\Component;

class Biodata extends Component
{
    public ?Pegawai $pegawai;

    public function mount(?Pegawai $pegawai = null)
    {
        $this->pegawai = $pegawai;
    }

    public function render()
    {
        return view('livewire.dashboard.pegawai.biodata');
    }
}