<?php

namespace App\Livewire\Manajemen\Pegawai;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Pegawai;

#[Layout('layouts.app')]
class DetailPegawai extends Component
{

    public $pegawai;

    public function mount($id)
    {
        $this->pegawai = Pegawai::find($id);
    }

    public function render()
    {
        return view('livewire.manajemen.pegawai.detail-pegawai');
    }
}