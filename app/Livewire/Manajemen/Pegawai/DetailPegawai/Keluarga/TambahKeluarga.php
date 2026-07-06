<?php

namespace App\Livewire\Manajemen\Pegawai\DetailPegawai\Keluarga;

use App\Models\JenisKeluarga;
use App\Models\Keluarga;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class TambahKeluarga extends Component
{
    public int $pegawai_id;
    public ?Pegawai $pegawai = null;
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

    public function mount(int $pegawai_id)
    {
        $this->pegawai_id = $pegawai_id;
        $this->pegawai = Pegawai::select('id', 'nama')->find($pegawai_id);
        $this->jenisKeluarga = JenisKeluarga::pluck('jenis', 'id')->toArray();
    }

    protected function rules(): array
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


    protected function validationAttributes(): array
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
        return [
            'form.jenis_keluarga_id.required' => ':attribute wajib dipilih.',
            'form.jenis_keluarga_id.exists'   => ':attribute yang dipilih tidak valid.',

            'form.nama.required'             => ':attribute wajib diisi.',
            'form.nama.string'               => ':attribute harus berupa teks.',
            'form.nama.max'                  => ':attribute maksimal :max karakter.',

            'form.tempat_lahir.string'       => ':attribute harus berupa teks.',
            'form.tempat_lahir.max'          => ':attribute maksimal :max karakter.',

            'form.tanggal_lahir.required'    => ':attribute wajib diisi.',

            'form.pekerjaan.string'          => ':attribute harus berupa teks.',
            'form.pekerjaan.max'             => ':attribute maksimal :max karakter.',

            'form.no_telpon.string'          => ':attribute harus berupa teks.',
            'form.no_telpon.max'             => ':attribute maksimal :max karakter.',

            'form.alamat.string'             => ':attribute harus berupa teks.',
        ];
    }

    public function save()
    {
        $this->validate();

        Keluarga::create([
            'pegawai_id'        => $this->pegawai_id,
            'jenis_keluarga_id' => $this->form['jenis_keluarga_id'],
            'nama'              => $this->form['nama'],
            'tempat_lahir'      => $this->form['tempat_lahir'],
            'tanggal_lahir'     => Carbon::createFromFormat('d/m/Y', $this->form['tanggal_lahir'])->format('Y-m-d'),
            'pekerjaan'         => $this->form['pekerjaan'] ?: null,
            'no_telpon'         => $this->form['no_telpon'] ?: null,
            'alamat'            => $this->form['alamat'] ?: null,
            'updated_by'        => Auth::id(),
        ]);

        $this->dispatch('refresh-table');
        $this->dispatch('close-modal');
        $this->resetForm();
        session()->flash('success', 'Data keluarga berhasil ditambahkan.');
    }

    #[On('close-modal')]
    public function handleClose()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->form = [
            'jenis_keluarga_id' => '',
            'nama'              => '',
            'tempat_lahir'      => '',
            'tanggal_lahir'     => '',
            'pekerjaan'         => '',
            'no_telpon'         => '',
            'alamat'            => '',
        ];
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.manajemen.pegawai.detail-pegawai.keluarga.tambah-keluarga');
    }
}