<?php

namespace App\Livewire\Dashboard\Cuti;

use App\Models\Cuti;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteCuti extends Component
{
    public $open = false;
    public $cutiId;

    #[On('openModalDelete')]
    public function open($id = null)
    {
        $this->resetErrorBag();
        $this->cutiId = $id;
        $this->open = true;
    }

    public function close()
    {
        $this->open = false;
        $this->cutiId = null;
        $this->resetErrorBag();
    }

    public function delete()
    {
        $cuti = Cuti::where('pegawai_id', Auth::user()?->pegawai?->id)->findOrFail($this->cutiId);

        if ($cuti->status === 'disetujui') {
            $this->addError('cuti', 'Cuti yang sudah disetujui tidak dapat dihapus.');
            return;
        }

        if ($cuti->dokumen_pendukung) {
            Storage::disk('public')->delete($cuti->dokumen_pendukung);
        }

        $cuti->delete();

        $this->dispatch('cuti-deleted');
        $this->close();
    }

    public function render()
    {
        return view('livewire.dashboard.cuti.delete-cuti');
    }
}
