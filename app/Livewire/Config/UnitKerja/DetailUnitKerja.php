<?php

namespace App\Livewire\Config\UnitKerja;

use Livewire\Component;
use App\Models\UnitKerja;
use Livewire\Attributes\On;

class DetailUnitKerja extends Component
{
    public $unitId;
    public $unit;

    public function mount($unitId = null)
    {
        $this->unitId = $unitId;
        $this->loadData();
    }

    public function loadData()
    {
        if (!$this->unitId) return;

        $this->unit = UnitKerja::with(['pimpinan', 'unitInduk'])
            ->find($this->unitId);
    }
}