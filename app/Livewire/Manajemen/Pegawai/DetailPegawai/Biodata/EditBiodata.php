<?php

namespace App\Livewire\Manajemen\Pegawai\DetailPegawai\Biodata;

use App\Models\JenisPegawai;
use App\Models\Pegawai;
use App\Models\StatusPegawai;
use App\Models\UnitKerja;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class EditBiodata extends Component
{
    const STATUS_TETAP = 1;
    const STATUS_KONTRAK = 2;

    #[Locked]
    public int $pegawai_id;

    public string $nama_pegawai = '';
    public string $nik_pegawai = '';

    public array $form = [];
    public array $originalForm = [];

    public Collection $unitKerja;
    public Collection $jenisPegawai;
    public Collection $statusPegawai;

    public function mount(int $pegawai_id)
    {
        $this->pegawai_id = $pegawai_id;

        $this->unitKerja = Cache::remember(
            'unit_kerja',
            3600,
            fn() => UnitKerja::orderBy('name')->get(['id', 'name'])
        );
        $this->jenisPegawai = Cache::remember(
            'jenis_pegawai',
            3600,
            fn() => JenisPegawai::get(['id', 'jenis'])
        );
        $this->statusPegawai = Cache::remember(
            'status_pegawai',
            3600,
            fn() => StatusPegawai::get(['status', 'id'])
        );

        $this->loadPegawai();
    }

    #[On('open-edit-biodata-modal')]
    public function handleOpen(): void
    {
        $this->loadPegawai();
        $this->resetErrorBag();
        $this->resetValidation();
    }

    #[On('close-edit-biodata-modal')]
    public function handleClose(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->loadPegawai();
    }

    private function loadPegawai(): void
    {
        $pegawai = Pegawai::findOrFail($this->pegawai_id);
        $this->nama_pegawai = $pegawai->nama;
        $this->nik_pegawai = $pegawai->nip;

        $this->form = [
            'jenis_pegawai_id' => $pegawai->jenis_pegawai_id,
            'status_pegawai_id' => $pegawai->status_pegawai_id,
            'unit_kerja_id' => $pegawai->unit_kerja_id,
            'unit_bagian' => $pegawai->unit_bagian,

            'ktp' => $pegawai->ktp,
            'npwp' => $pegawai->npwp,

            'gelar_depan' => $pegawai->gelar_depan,
            'gelar_belakang' => $pegawai->gelar_belakang,
            'tempat_lahir' => $pegawai->tempat_lahir,
            'jenis_kelamin' => $pegawai->getRawOriginal('jenis_kelamin'),

            'tanggal_lahir' => optional($pegawai->tanggal_lahir)->format('d/m/Y'),
            'tanggal_bergabung' => optional($pegawai->tanggal_bergabung)->format('d/m/Y'),
            'tanggal_habis_kontrak' => optional($pegawai->tanggal_habis_kontrak)->format('d/m/Y'),
            'tanggal_pensiun' => optional($pegawai->tanggal_pensiun)->format('d/m/Y'),

            'no_telpon' => $pegawai->no_telpon,
            'email_yarsi' => $pegawai->email_yarsi,
            'alamat_ktp' => $pegawai->alamat_ktp,
            'alamat_domisili' => $pegawai->alamat_domisili,

            'status' => $pegawai->status,
        ];

        $this->originalForm = $this->form;
    }

    protected function rules(): array
    {
        return [
            'form.unit_kerja_id'        => ['required', 'exists:unit_kerja,id'],
            'form.jenis_pegawai_id'     => ['required', 'exists:jenis_pegawai,id'],
            'form.status_pegawai_id'    => ['required', 'exists:status_pegawai,id'],
            'form.unit_bagian'          => ['nullable', 'string', 'max:255'],

            'form.ktp'  => ['required', 'digits:16', Rule::unique('pegawai', 'ktp')->ignore($this->pegawai_id)],
            'form.npwp' => ['nullable', 'digits_between:10,16', Rule::unique('pegawai', 'npwp')->ignore($this->pegawai_id)],

            'form.gelar_depan'      => ['nullable', 'string', 'max:50'],
            'form.gelar_belakang'   => ['nullable', 'string', 'max:50'],
            'form.tempat_lahir'     => ['required', 'string', 'max:50'],
            'form.tanggal_lahir'    => ['required', 'date_format:d/m/Y'],
            'form.jenis_kelamin'    => ['required', 'in:L,P'],

            'form.tanggal_bergabung'        => ['required', 'date_format:d/m/Y'],
            'form.tanggal_habis_kontrak'    => ['nullable', 'date_format:d/m/Y', 'required_if:form.status_pegawai_id,' . self::STATUS_KONTRAK],
            'form.tanggal_pensiun'          => ['nullable', 'date_format:d/m/Y', 'required_if:form.status_pegawai_id,' . self::STATUS_TETAP],

            'form.no_telpon'        => ['required', 'regex:/^\d{10,15}$/'],
            'form.email_yarsi'      => ['nullable', 'email', Rule::unique('pegawai', 'email_yarsi')->ignore($this->pegawai_id)],
            'form.alamat_ktp'       => ['required', 'string', 'max:255'],
            'form.alamat_domisili'  => ['nullable', 'string', 'max:255'],

            'form.status'           => ['required', 'in:active,inactive'],
        ];
    }

    protected function messages(): array
    {
        return [
            'form.unit_kerja_id.required' => 'Unit kerja wajib dipilih.',
            'form.unit_kerja_id.exists' => 'Unit kerja tidak valid.',

            'form.jenis_pegawai_id.required' => 'Jenis pegawai wajib dipilih.',
            'form.jenis_pegawai_id.exists' => 'Jenis pegawai tidak valid.',

            'form.status_pegawai_id.required' => 'Status pegawai wajib dipilih.',
            'form.status_pegawai_id.exists' => 'Status pegawai tidak valid.',

            'form.unit_bagian.max' => 'Unit/bagian maksimal 255 karakter.',

            'form.ktp.required' => 'NIK wajib diisi.',
            'form.ktp.digits' => 'NIK harus terdiri dari 16 digit.',
            'form.ktp.unique' => 'NIK sudah terdaftar pada pegawai lain.',

            'form.npwp.digits_between' => 'NPWP harus terdiri dari 10–16 digit.',
            'form.npwp.unique' => 'NPWP sudah terdaftar pada pegawai lain.',

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

            'form.no_telpon.required' => 'Nomor telepon wajib diisi.',
            'form.no_telpon.regex' => 'Nomor telepon harus 10–15 digit angka.',

            'form.email_yarsi.email' => 'Format email tidak valid.',
            'form.email_yarsi.unique' => 'Email sudah terdaftar pada pegawai lain.',

            'form.alamat_ktp.required' => 'Alamat KTP wajib diisi.',
            'form.alamat_ktp.max' => 'Alamat KTP maksimal 255 karakter.',

            'form.alamat_domisili.max' => 'Alamat domisili maksimal 255 karakter.',

            'form.status.required' => 'Status akun wajib dipilih.',
            'form.status.in' => 'Status akun tidak valid.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.unit_kerja_id' => 'unit kerja',
            'form.jenis_pegawai_id' => 'jenis pegawai',
            'form.status_pegawai_id' => 'status pegawai',
            'form.unit_bagian' => 'unit/bagian',

            'form.ktp' => 'NIK',
            'form.npwp' => 'NPWP',

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

            'form.status' => 'status akun',
        ];
    }

    public function getIsDirtyProperty()
    {
        return $this->form != $this->originalForm;
    }


    public function updated($property): void
    {
        if (!str_starts_with($property, 'form.')) {
            return;
        }

        $this->validateOnly($property, $this->rules());
    }

    private function normalizeDate($date)
    {
        return filled($date)
            ? Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d')
            : null;
    }

    public function save(): void
    {
        $this->validate($this->rules());

        $pegawai = Pegawai::findOrFail($this->pegawai_id);

        $data = $this->form;
        $data['tanggal_lahir']          = $this->normalizeDate($data['tanggal_lahir']);
        $data['tanggal_bergabung']      = $this->normalizeDate($data['tanggal_bergabung']);
        $data['tanggal_habis_kontrak']  = $this->normalizeDate($data['tanggal_habis_kontrak']) ?: null;
        $data['tanggal_pensiun']        = $this->normalizeDate($data['tanggal_pensiun']) ?: null;
        $data['npwp']                   = $data['npwp'] ?: null;

        $pegawai->update($data);

        $this->dispatch('close-edit-biodata-modal');
        $this->dispatch('refresh-biodata');
        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Biodata pegawai berhasil diperbarui'
        );
    }


    public function render()
    {
        return view('livewire.manajemen.pegawai.detail-pegawai.biodata.edit-biodata');
    }
}