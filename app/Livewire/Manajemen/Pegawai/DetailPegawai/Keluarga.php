<?php

namespace App\Livewire\Manajemen\Pegawai\DetailPegawai;

use App\Models\Pegawai;
use App\Models\Keluarga as KeluargaModel;
use Livewire\Component;
use Livewire\WithPagination;

class Keluarga extends Component
{
    use WithPagination;

    public Pegawai $pegawai;

    public $search = '';
    public $filterHubungan = '';

    public function mount(Pegawai $pegawai)
    {
        $this->pegawai = $pegawai;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterHubungan()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        $this->dispatch('open-modal-edit-keluarga', id: $id);
    }

    public function delete($id)
    {
        $this->dispatch('open-modal-konfirmasi-hapus', id: $id);
    }

    public function render()
    {
        $query = KeluargaModel::where('pegawai_id', $this->pegawai->id);

        if (!empty($this->search)) {
            $query->where('nama', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->filterHubungan)) {
            $query->where('hubungan', $this->filterHubungan);
        }

        $keluargas = $query->latest()->paginate(10);

        return view('livewire.manajemen.pegawai.detail-pegawai.keluarga', [
            'keluargas' => $keluargas
        ]);
    }
}