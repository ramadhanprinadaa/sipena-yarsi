<?php

namespace App\Livewire\Dashboard\Pegawai;

use App\Models\Pegawai;
use App\Models\Rekening as RekeningModel;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Rekening extends Component
{
    #[Locked]
    public ?int $pegawai_id = null;
    public ?string $nama_pegawai = null;

    public function mount(?int $pegawai_id = null)
    {
        $this->pegawai_id = $pegawai_id;
        $this->nama_pegawai = Pegawai::where('id', $pegawai_id)->value('nama') ?? '';
    }

    #[Computed]
    public function rekening()
    {
        if ($this->pegawai_id === null) {
            return null;
        }

        $rekening = RekeningModel::where('rekening.pegawai_id', $this->pegawai_id)
            ->leftJoin('users', 'rekening.updated_by', '=', 'users.id')
            ->select([
                'rekening.id',
                'rekening.nama_bank',
                'rekening.nomor_rekening',
                'rekening.nama_rekening',
                'rekening.updated_at',
                'users.username as editor_name'
            ])
            ->first();

        return $rekening;
    }

    #[Computed]
    public function emptyStateMessage(): string
    {
        if ($this->pegawai_id === null) {
            return "Akun Anda belum tertaut dengan data pegawai manapun.";
        }

        return "Belum ada data rekening yang terdaftar.";
    }

    public function render()
    {
        return view('livewire.dashboard.pegawai.rekening');
    }
}