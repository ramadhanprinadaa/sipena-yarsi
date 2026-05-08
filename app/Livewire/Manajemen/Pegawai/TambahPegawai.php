<?php

namespace App\Livewire\Manajemen\Pegawai;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

use App\Models\UnitKerja;
use App\Models\Pegawai;
use App\Models\JenisPegawai;
use App\Models\StatusPegawai;

class TambahPegawai extends Component
{
    const STATUS_TETAP = 1;
    const STATUS_KONTRAK = 2;

    public array $form;
    public int $currentTab = 0;

    public $unitKerja;
    public $jenisPegawai;
    public $statusPegawai;


    public function mount()
    {
        $this->form = $this->defaultForm();
        $this->unitKerja = Cache::remember(
            'unit_kerja',
            3600,
            fn() =>
            UnitKerja::orderBy('name')->get(['id', 'name'])
        );
        $this->jenisPegawai = Cache::remember(
            'jenis_pegawai',
            3600,
            fn() =>
            JenisPegawai::get(['id', 'jenis'])
        );
        $this->statusPegawai = Cache::remember(
            'status_pegawai',
            3600,
            fn() =>
            StatusPegawai::get(['status', 'id'])
        );
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
            'form.nip' => ['required', 'digits_between:10,20', 'unique:pegawai,nip'],
            'form.npwp' => ['nullable', 'digits_between:15,16', 'unique:pegawai,npwp'],
            'form.unit_kerja_id' => ['required', 'exists:unit_kerja,id'],
            'form.jenis_pegawai_id' => ['required', 'exists:jenis_pegawai,id'],
            'form.status_pegawai_id' => ['required', 'exists:status_pegawai,id'],

            'form.gelar_depan' => ['nullable', 'string', 'max:50'],
            'form.gelar_belakang' => ['nullable', 'string', 'max:50'],
            'form.tempat_lahir' => ['required', 'string', 'max:50'],
            'form.tanggal_lahir' => ['required', 'date_format:d/m/Y'],
            'form.jenis_kelamin' => ['required', 'in:L,P'],
            'form.tanggal_bergabung' => ['required', 'date_format:d/m/Y'],
            'form.tanggal_habis_kontrak' => ['nullable', 'date_format:d/m/Y', 'required_if:form.status_pegawai_id,' . self::STATUS_KONTRAK],
            'form.tanggal_pensiun' => ['nullable', 'date_format:d/m/Y', 'required_if:form.status_pegawai_id,' . self::STATUS_TETAP],

            'form.no_telpon' => ['required', 'regex:/^\d{10,15}$/'],
            'form.email_yarsi' => ['nullable', 'email', 'unique:pegawai,email_yarsi'],
            'form.alamat_ktp' => ['required', 'string', 'max:255'],
            'form.alamat_domisili' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function stepFields()
    {
        return [
            0 => [
                'form.nama',
                'form.ktp',
                'form.nip',
                'form.npwp',
                'form.unit_kerja_id',
                'form.jenis_pegawai_id',
                'form.status_pegawai_id',
            ],
            1 => [
                'form.gelar_depan',
                'form.gelar_belakang',
                'form.tempat_lahir',
                'form.tanggal_lahir',
                'form.jenis_kelamin',
                'form.tanggal_bergabung',
                'form.tanggal_habis_kontrak',
                'form.tanggal_pensiun',
            ],
            2 => [
                'form.no_telpon',
                'form.email_yarsi',
                'form.alamat_ktp',
                'form.alamat_domisili',
            ],
        ];
    }

    private function rulesPerCurrentStep(): array
    {
        $rules = $this->rules();
        return collect($this->stepFields()[$this->currentTab])
            ->mapWithKeys(fn($field) => [$field => $rules[$field]])
            ->toArray();
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
            'form.tanggal_lahir.date_format' => 'Format tanggal lahir tidak valid.',

            'form.jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'form.jenis_kelamin.in' => 'Jenis kelamin harus L atau P.',

            'form.tanggal_bergabung.required' => 'Tanggal bergabung wajib diisi.',
            'form.tanggal_bergabung.date_format' => 'Format tanggal bergabung tidak valid.',

            'form.tanggal_habis_kontrak.required_if' => 'Tanggal habis kontrak wajib diisi untuk pegawai kontrak.',
            'form.tanggal_habis_kontrak.date_format' => 'Format tanggal habis kontrak tidak valid.',

            'form.tanggal_pensiun.required_if' => 'Tanggal pensiun wajib diisi untuk pegawai tetap.',
            'form.tanggal_pensiun.date_format' => 'Format tanggal pensiun tidak valid.',

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
        if (!str_starts_with($property, 'form.')) return;
        $this->validateOnly($property, $this->rules());
    }

    public function resetForm()
    {
        $this->reset(['form', 'currentTab']);
        $this->form = $this->defaultForm();
        $this->resetValidation();
    }

    public function nextStep()
    {
        $this->validate($this->rulesPerCurrentStep());
        $this->currentTab++;
    }

    public function prevStep()
    {
        $this->currentTab--;
    }

    private function normalizeDate($date)
    {
        return filled($date)
            ? Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d')
            : null;
    }
    public function save()
    {
        $this->validate($this->rules());

        $data = $this->form;

        $data['tanggal_lahir'] = $this->normalizeDate($data['tanggal_lahir']);
        $data['tanggal_bergabung'] = $this->normalizeDate($data['tanggal_bergabung']);

        $data['npwp'] = $data['npwp'] ?: null;
        $data['tanggal_pensiun'] = $this->normalizeDate($data['tanggal_pensiun']) ?: null;
        $data['tanggal_habis_kontrak'] = $this->normalizeDate($data['tanggal_habis_kontrak']) ?: null;

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
