<?php

namespace App\Livewire\Config\Kalender;

use Livewire\Component;

class TambahHariLibur extends Component
{
    public $jenisHariLibur = [
        'Hari Libur Nasional',
        'Hari Libur Cuti Bersama',
        'Hari Libur Institusi',
    ];
    public function render()
    {
        return view('livewire.config.kalender.tambah-hari-libur');
    }
}