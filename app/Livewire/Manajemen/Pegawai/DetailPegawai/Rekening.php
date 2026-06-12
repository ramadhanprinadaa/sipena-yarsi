<?php

namespace App\Livewire\Manajemen\Pegawai\DetailPegawai;

use App\Models\Pegawai;
use App\Models\Rekening as RekeningModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Rekening extends Component
{
    #[Locked]
    public int $pegawai_id;

    public string $nama_pegawai = '';

    public array $form = [
        'nama_bank'      => '',
        'nomor_rekening' => '',
        'nama_rekening'  => '',
    ];

    public array $originalForm = [];
    public ?array $currentRekening = null;

    public function mount(int $pegawai_id)
    {
        $this->pegawai_id = $pegawai_id;
        $this->nama_pegawai = Pegawai::where('id', $pegawai_id)->value('nama') ?? '';
        $this->loadRekening();
    }

    public function loadRekening()
    {
        $rekening = RekeningModel::where('rekening.pegawai_id', $this->pegawai_id)
            ->leftJoin('users', 'rekening.updated_by', '=', 'users.id')
            ->select([
                'rekening.id',
                'rekening.nama_bank',
                'rekening.nomor_rekening',
                'rekening.nama_rekening',
                'rekening.updated_at',
                'users.username as editor_name' // Menangkap nama pengubah data
            ])
            ->first();

        if ($rekening) {
            $this->currentRekening = $rekening->toArray();

            $this->form = [
                'nama_bank'      => $rekening->nama_bank,
                'nomor_rekening' => $rekening->nomor_rekening,
                'nama_rekening'  => $rekening->nama_rekening,
            ];
        } else {
            $this->currentRekening = null;
            $this->resetForm();
        }

        $this->originalForm = $this->form;
    }

    public function openEditModal()
    {
        $this->loadRekening();
        $this->resetValidation();
        $this->dispatch('open-rekening-modal');
    }

    public function update()
    {
        if (!$this->isDirty) {
            $this->dispatch('close-rekening-modal');
            return;
        }

        $validated = $this->validate();

        DB::transaction(function () use ($validated) {
            RekeningModel::updateOrCreate(
                ['pegawai_id' => $this->pegawai_id],
                [
                    'nama_bank'      => $validated['form']['nama_bank'],
                    'nomor_rekening' => $validated['form']['nomor_rekening'],
                    'nama_rekening'  => $validated['form']['nama_rekening'],
                    'updated_by'     => Auth::id(), // Menyimpan ID user yang aktif melakukan perubahan
                ]
            );
        });

        $this->loadRekening();
        $this->dispatch('close-rekening-modal');
        $this->dispatch('notify', type: 'success', message: 'Data rekening berhasil diperbarui.');
    }

    protected function rules()
    {
        return [
            'form.nama_bank'      => 'required|string|max:100',
            'form.nomor_rekening' => 'required|string|numeric|max_digits:30',
            'form.nama_rekening'  => 'required|string|max:150',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'form.nama_bank'      => 'Nama Bank',
            'form.nomor_rekening' => 'Nomor Rekening',
            'form.nama_rekening'  => 'Nama Pemilik Rekening',
        ];
    }

    public function getIsDirtyProperty()
    {
        return $this->form !== $this->originalForm;
    }

    public function resetForm()
    {
        $this->form = [
            'nama_bank'      => '',
            'nomor_rekening' => '',
            'nama_rekening'  => '',
        ];
        $this->originalForm = $this->form;
    }

    public function render()
    {
        return view('livewire.manajemen.pegawai.detail-pegawai.rekening');
    }
}