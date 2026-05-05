<?php

namespace App\Livewire\Manajemen\Pegawai;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\UnitKerja;
use App\Models\Pegawai;
use App\Models\JenisPegawai;
use App\Models\StatusPegawai;

class TambahPegawai extends Component
{
    public $unitKerja = [];
    public $jenisPegawai = [];
    public $statusPegawai = [];
    public $form;

    public $currentTab = 0;

    public function mount()
    {
        $this->form = $this->defaultForm();
        $this->unitKerja = UnitKerja::orderBy('name')->get(['id', 'name']);
        $this->jenisPegawai = JenisPegawai::get(['id', 'jenis']);
        $this->statusPegawai = StatusPegawai::get(['id', 'status']);
    }

    #[On('close-add-modal')]
    public function handleClose()
    {
        $this->resetForm();
        $this->resetValidation();
        $this->currentTab = 0;
    }

    protected function defaultForm()
    {
        return [
            'nama' => '',
            'ktp' => '',
            'nip' => '',
            'npwp' => '',
            'unit_kerja_id' => null,
            'jenis_pegawai_id' => null,
            'status_pegawai_id' => null,
            'gelar_depan' => '',
            'gelar_belakang' => '',
            'tempat_lahir' => '',
            'tanggal_lahir' => '',
            'jenis_kelamin' => '',
            'tanggal_bergabung' => '',
            'tanggal_habis_kontrak' => '',
            'tanggal_pensiun' => '',
            'no_telpon' => '',
            'email_yarsi' => '',
            'alamat_ktp' => '',
            'alamat_domisili' => '',
        ];
    }

    protected function rules()
    {
        return [
            'form.nama' => ['required', 'string', 'max:100'],
            'form.ktp' => ['required', 'digits:16', 'unique:pegawai,ktp'],
            'form.nip' => ['required', 'regex:/^\d{10,20}$/', 'unique:pegawai,nip'],
            'form.npwp' => ['nullable', 'regex:/^\d{15,16}$/', 'unique:pegawai,npwp'],
            'form.unit_kerja_id' => ['required', 'exists:unit_kerja,id'],
            'form.jenis_pegawai_id' => ['required', 'exists:jenis_pegawai,id'],
            'form.status_pegawai_id' => ['required', 'exists:status_pegawai,id'],

            'form.gelar_depan' => ['nullable', 'string', 'max:50'],
            'form.gelar_belakang' => ['nullable', 'string', 'max:50'],
            'form.tempat_lahir' => ['required', 'string', 'max:50'],
            'form.tanggal_lahir' => ['required', 'date'],
            'form.jenis_kelamin' => ['required', 'in:L,P'],
            'form.tanggal_bergabung' => ['required', 'date'],
            'form.tanggal_habis_kontrak' => ['nullable', 'date', 'required_if:form.status_pegawai_id,2'],
            'form.tanggal_pensiun' => ['nullable', 'date', 'required_if:form.status_pegawai_id,1'],

            'form.no_telpon' => ['required', 'regex:/^\d{10,15}$/'],
            'form.email_yarsi' => ['nullable', 'email', 'unique:pegawai,email_yarsi'],
            'form.alamat_ktp' => ['required', 'string', 'max:255'],
            'form.alamat_domisili' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function rulesPerStep()
    {
        return [
            0 => [ // Data Pegawai
                'form.nama' => ['required', 'string', 'max:100'],
                'form.ktp' => ['required', 'digits:16', 'unique:pegawai,ktp'],
                'form.nip' => ['required', 'regex:/^\d{10,20}$/', 'unique:pegawai,nip'],
                'form.npwp' => ['nullable', 'regex:/^\d{15,16}$/', 'unique:pegawai,npwp'],
                'form.unit_kerja_id' => ['required', 'exists:unit_kerja,id'],
                'form.jenis_pegawai_id' => ['required', 'exists:jenis_pegawai,id'],
                'form.status_pegawai_id' => ['required', 'exists:status_pegawai,id'],
            ],
            1 => [ // Biodata
                'form.gelar_depan' => ['nullable', 'string', 'max:50'],
                'form.gelar_belakang' => ['nullable', 'string', 'max:50'],
                'form.tempat_lahir' => ['required', 'string', 'max:50'],
                'form.tanggal_lahir' => ['required', 'date'],
                'form.jenis_kelamin' => ['required', 'in:L,P'],
                'form.tanggal_bergabung' => ['required', 'date'],
                'form.tanggal_habis_kontrak' => ['nullable', 'date', 'required_if:form.status_pegawai_id,2'],
                'form.tanggal_pensiun' => ['nullable', 'date', 'required_if:form.status_pegawai_id,1'],
            ],
            2 => [ // Kontak & Alamat
                'form.no_telpon' => ['required', 'regex:/^\d{10,15}$/'],
                'form.email_yarsi' => ['nullable', 'email', 'unique:pegawai,email_yarsi'],
                'form.alamat_ktp' => ['required', 'string', 'max:255'],
                'form.alamat_domisili' => ['nullable', 'string', 'max:255'],
            ],
        ];
    }

    protected function messages()
    {
        return [
            // ===== DATA PEGAWAI =====
            'form.nama.required' => 'Nama wajib diisi.',
            'form.nama.max' => 'Nama maksimal 100 karakter.',

            'form.ktp.required' => 'NIK wajib diisi.',
            'form.ktp.digits' => 'NIK harus terdiri dari 16 digit.',
            'form.ktp.unique' => 'NIK sudah terdaftar.',

            'form.nip.required' => 'NIP wajib diisi.',
            'form.nip.regex' => 'NIP harus berupa angka 10–20 digit.',
            'form.nip.unique' => 'NIP sudah terdaftar.',

            'form.npwp.regex' => 'NPWP harus terdiri dari 15–16 digit.',
            'form.npwp.unique' => 'NPWP sudah terdaftar.',

            'form.unit_kerja_id.required' => 'Unit kerja wajib dipilih.',
            'form.unit_kerja_id.exists' => 'Unit kerja tidak valid.',

            'form.jenis_pegawai_id.required' => 'Jenis pegawai wajib dipilih.',
            'form.jenis_pegawai_id.exists' => 'Jenis pegawai tidak valid.',

            'form.status_pegawai_id.required' => 'Status pegawai wajib dipilih.',
            'form.status_pegawai_id.exists' => 'Status pegawai tidak valid.',

            // ===== BIODATA =====
            'form.gelar_depan.max' => 'Gelar depan maksimal 50 karakter.',
            'form.gelar_belakang.max' => 'Gelar belakang maksimal 50 karakter.',

            'form.tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'form.tempat_lahir.max' => 'Tempat lahir maksimal 50 karakter.',

            'form.tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'form.tanggal_lahir.date' => 'Format tanggal lahir tidak valid.',

            'form.jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'form.jenis_kelamin.in' => 'Jenis kelamin harus L atau P.',

            'form.tanggal_bergabung.required' => 'Tanggal bergabung wajib diisi.',
            'form.tanggal_bergabung.date' => 'Format tanggal bergabung tidak valid.',

            'form.tanggal_habis_kontrak.required_if' => 'Tanggal habis kontrak wajib diisi untuk pegawai kontrak.',
            'form.tanggal_habis_kontrak.date' => 'Format tanggal habis kontrak tidak valid.',

            'form.tanggal_pensiun.required_if' => 'Tanggal pensiun wajib diisi untuk pegawai tetap.',
            'form.tanggal_pensiun.date' => 'Format tanggal pensiun tidak valid.',

            // ===== KONTAK =====
            'form.no_telpon.required' => 'Nomor telepon wajib diisi.',
            'form.no_telpon.regex' => 'Nomor telepon harus 10–15 digit angka.',

            'form.email_yarsi.email' => 'Format email tidak valid.',
            'form.email_yarsi.unique' => 'Email sudah terdaftar.',

            'form.alamat_ktp.required' => 'Alamat KTP wajib diisi.',
            'form.alamat_ktp.max' => 'Alamat KTP maksimal 255 karakter.',

            'form.alamat_domisili.max' => 'Alamat domisili maksimal 255 karakter.',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'form.nama' => 'nama pegawai',
            'form.ktp' => 'NIK',
            'form.nip' => 'NIP',
            'form.npwp' => 'NPWP',

            'form.unit_kerja_id' => 'unit kerja',
            'form.jenis_pegawai_id' => 'jenis pegawai',
            'form.status_pegawai_id' => 'status pegawai',

            'form.gelar_depan' => 'gelar depan',
            'form.gelar_belakang' => 'gelar belakang',

            'form.tempat_lahir' => 'tempat lahir',
            'form.tanggal_lahir' => 'tanggal lahir',
            'form.jenis_kelamin' => 'jenis kelamin',

            'form.tanggal_bergabung' => 'tanggal bergabung',
            'form.tanggal_habis_kontrak' => 'tanggal habis kontrak',
            'form.tanggal_pensiun' => 'tanggal pensiun',

            'form.no_telpon' => 'nomor telepon',
            'form.email_yarsi' => 'email Yarsi',

            'form.alamat_ktp' => 'alamat lengkap',
            'form.alamat_domisili' => 'alamat domisili',
        ];
    }

    public function updated($property)
    {
        if (str_starts_with($property, 'form.')) {
            $this->validateOnly($property);
        }
    }

    public function resetForm()
    {
        $this->reset(['form', 'currentTab']);
        $this->form = $this->defaultForm();
        $this->resetValidation();
    }

    public function nextStep()
    {
        $this->validate($this->rulesPerStep()[$this->currentTab]);
        $this->currentTab++;
    }

    public function prevStep()
    {
        $this->currentTab--;
    }

    public function save()
    {
        $this->validate($this->rules());

        $data = $this->form;

        $data['tanggal_pensiun'] = $data['tanggal_pensiun'] ?: null;
        $data['tanggal_habis_kontrak'] = $data['tanggal_habis_kontrak'] ?: null;

        Pegawai::create($data);

        $this->resetForm();
        $this->dispatch('close-add-modal');
        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Pegawai berhasil ditambahkan'
        );
        $this->dispatch('refresh-table');
    }

    public function render()
    {
        return view('livewire.manajemen.pegawai.tambah-pegawai');
    }
}
