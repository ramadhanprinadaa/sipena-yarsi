<?php

namespace App\Livewire\Manajemen\Pegawai\DetailPegawai\Keluarga;

use App\Models\JenisKeluarga;
use App\Models\Keluarga as KeluargaModel;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class EditKeluarga extends Component
{
    #[Locked]
    public int $pegawai_id;
    #[Locked]
    public ?int $keluarga_id = null;

    public string $nama_pegawai = '';
    public array $jenisKeluarga;

    public array $form = [
        'jenis_keluarga_id' => '',
        'nama'              => '',
        'tempat_lahir'      => '',
        'tanggal_lahir'     => '',
        'pekerjaan'         => '',
        'no_telpon'         => '',
        'alamat'            => '',
    ];
    public array $originalForm = [];

    public function mount(int $pegawai_id)
    {
        $this->pegawai_id = $pegawai_id;
        $this->nama_pegawai = Pegawai::where('id', $pegawai_id)->value('nama') ?? '';
        $this->jenisKeluarga = JenisKeluarga::pluck('jenis', 'id')->toArray();
    }

    #[On('load-edit-keluarga')]
    public function loadData(int $keluarga_id)
    {
        $this->keluarga_id = $keluarga_id;

        $keluarga = KeluargaModel::select(
            'id',
            'jenis_keluarga_id',
            'nama',
            'tempat_lahir',
            'tanggal_lahir',
            'pekerjaan',
            'no_telpon',
            'alamat'
        )->find($keluarga_id);

        if ($keluarga) {
            $this->form = [
                'jenis_keluarga_id' => $keluarga->jenis_keluarga_id,
                'nama'              => $keluarga->nama,
                'tempat_lahir'      => $keluarga->tempat_lahir,
                'tanggal_lahir'     => Carbon::parse($keluarga->tanggal_lahir)->format('d/m/Y'),
                'pekerjaan'         => $keluarga->pekerjaan,
                'no_telpon'         => $keluarga->no_telpon,
                'alamat'            => $keluarga->alamat,
            ];
            $this->originalForm = $this->form;
        }
        $this->dispatch('edit-keluarga-loaded');
    }

    public function update()
    {
        if (!$this->isDirty) {
            $this->dispatch('close-modal');
            return;
        }

        $validated = $this->validate();

        $validated['form']['tanggal_lahir'] = Carbon::createFromFormat('d/m/Y', $validated['form']['tanggal_lahir'])->format('Y-m-d');

        DB::transaction(function () use ($validated) {
            KeluargaModel::where('id', $this->keluarga_id)->update($validated['form']);
        });

        $this->dispatch('close-edit-modal');
        $this->dispatch('refresh-table');
        $this->resetForm();
    }

    protected function rules()
    {
        return [
            'form.jenis_keluarga_id' => 'required|exists:jenis_keluarga,id',
            'form.nama'              => 'required|string|max:255',
            'form.tempat_lahir'      => 'nullable|string|max:255',
            'form.tanggal_lahir'     => 'required',
            'form.pekerjaan'         => 'nullable|string|max:255',
            'form.no_telpon'         => 'nullable|string|max:20',
            'form.alamat'            => 'nullable|string',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'form.jenis_keluarga_id' => 'Hubungan Keluarga',
            'form.nama'              => 'Nama Lengkap',
            'form.tempat_lahir'      => 'Tempat Lahir',
            'form.tanggal_lahir'     => 'Tanggal Lahir',
            'form.pekerjaan'         => 'Pekerjaan',
            'form.no_telpon'         => 'No. Telepon',
            'form.alamat'            => 'Alamat',
        ];
    }

    protected function messages(): array
    {
        return [];
    }

    public function resetForm()
    {
        $this->reset(['form', 'originalForm', 'keluarga_id']);
        $this->resetValidation();
    }

    public function getIsDirtyProperty()
    {
        return $this->form != $this->originalForm;
    }

    #[On('close-modal')]
    public function handleClose()
    {
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.manajemen.pegawai.detail-pegawai.keluarga.edit-keluarga');
    }
}
