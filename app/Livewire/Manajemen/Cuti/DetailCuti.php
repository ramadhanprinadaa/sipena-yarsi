<?php

namespace App\Livewire\Manajemen\Cuti;

use Livewire\Component;
use App\Models\Cuti;

class DetailCuti extends Component
{
    protected $listeners = ['openDetailModal' => 'openDetailModal'];

    public $openDetail = false;
    public $selectedCuti = null;

    public function render() 
    {
        return view('livewire.manajemen.cuti.detail-cuti');
    }

    public function openDetailModal($cutiId)
    {
        $this->selectedCuti = Cuti::with([
            'jenisCuti',
            'pegawai',
            'approvals.approver.pegawai',
            'approvals.approver.role'
        ])->findOrFail($cutiId);

        $this->openDetail = true;
    }

    public function closeDetailModal()
    {
        $this->openDetail = false;
        $this->selectedCuti = null;
    }   

}
