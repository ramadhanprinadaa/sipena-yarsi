<?php

namespace App\Livewire\Manajemen\Pegawai\DetailPegawai\Rekening;

use App\Models\Pegawai;
use App\Models\Rekening as RekeningModel;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class Rekening extends Component
{
    #[Locked]
    public int $pegawai_id;

    public string $nama_pegawai = '';

    public ?array $currentRekening = null;

    public function mount(int $pegawai_id)
    {
        $this->pegawai_id = $pegawai_id;
        $this->nama_pegawai = Pegawai::where('id', $pegawai_id)->value('nama') ?? '';
        $this->loadRekening();
    }

    public function loadRekening(): void
    {
        $rekening = RekeningModel::where('rekening.pegawai_id', $this->pegawai_id)
            ->leftJoin('users', 'rekening.updated_by', '=', 'users.id')
            ->select([
                'rekening.id',
                'rekening.nama_bank',
                'rekening.nomor_rekening',
                'rekening.nama_rekening',
                'rekening.updated_at',
                'users.username as editor_name',
            ])
            ->first();

        $this->currentRekening = $rekening?->toArray();
    }

    #[On('rekening-saved')]
    public function refreshRekening(): void
    {
        $this->loadRekening();
    }

    public function render()
    {
        return view('livewire.manajemen.pegawai.detail-pegawai.rekening.rekening');
    }
}