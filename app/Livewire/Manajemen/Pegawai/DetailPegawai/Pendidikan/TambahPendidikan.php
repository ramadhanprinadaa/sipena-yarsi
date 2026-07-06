<?php

namespace App\Livewire\Manajemen\Pegawai\DetailPegawai\Pendidikan;

use App\Models\ArsipFile;
use App\Models\JenjangPendidikan;
use App\Models\Pegawai;
use App\Models\RiwayatPendidikan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class TambahPendidikan extends Component
{
    use WithFileUploads;

    #[Locked]
    public int $pegawai_id;

    public ?Pegawai $pegawai = null;
    public array $jenjangPendidikan;

    public array $form = [
        'jenjang_pendidikan_id' => '',
        'tahun_masuk'           => '',
        'tahun_lulus'           => '',
        'file_ijazah'           => '',
    ];

    public function mount(int $pegawai_id)
    {
        $this->pegawai_id = $pegawai_id;
        $this->pegawai = Pegawai::select('id', 'nama')->find($pegawai_id);
        $this->jenjangPendidikan = JenjangPendidikan::pluck('kode', 'id')->toArray();
    }

    protected function rules(): array
    {
        return [
            'form.jenjang_pendidikan_id'    => ['required', 'exists:jenjang_pendidikan,id'],
            'form.tahun_masuk'              => ['required', 'regex:/^\d{4}$/'],
            'form.tahun_lulus'              => ['required', 'regex:/^\d{4}$/'],
            'form.file_ijazah'              => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.jenjang_pendidikan_id' => 'Jenjang Pendidikan',
            'form.tahun_masuk'           => 'Tahun Masuk',
            'form.tahun_lulus'           => 'Tahun Lulus',
            'form.file_ijazah'           => 'File Ijazah',
        ];
    }

    protected function messages(): array
    {
        return [
            'form.jenjang_pendidikan_id.required' => 'Jenjang pendidikan wajib dipilih.',
            'form.jenjang_pendidikan_id.exists'   => 'Jenjang pendidikan yang dipilih tidak valid.',

            'form.tahun_masuk.required'           => 'Tahun masuk wajib diisi.',
            'form.tahun_masuk.regex'              => 'Tahun masuk harus berupa tahun 4 digit (contoh: 2020).',

            'form.tahun_lulus.required'          => 'Tahun lulus wajib diisi.',
            'form.tahun_lulus.regex'             => 'Tahun lulus harus berupa tahun 4 digit (contoh: 2024).',

            'form.file_ijazah.required'           => 'File ijazah wajib diunggah.',
            'form.file_ijazah.file'               => 'File ijazah harus berupa file yang valid.',
            'form.file_ijazah.mimes'              => 'File ijazah harus berformat PDF, DOC, atau DOCX.',
            'form.file_ijazah.max'                => 'Ukuran file ijazah maksimal 10 MB.',
        ];
    }

    public function save()
    {
        $this->validate();

        // Builder File Name
        $kodeJenjang = $this->jenjangPendidikan[$this->form['jenjang_pendidikan_id']] ?? 'Pendidikan';
        $kodeJenjang = Str::slug($kodeJenjang, '');
        $extension = $this->form['file_ijazah']->getClientOriginalExtension();

        $fileName = sprintf('Ijazah_%s_%s.%s', $kodeJenjang, now()->format('YmdHis'), $extension);

        // Direktori penyimpanan file per pegawai
        $directory = "arsip_file/pegawai/{$this->pegawai_id}/ijazah";

        // Simpan file ke storage
        $this->form['file_ijazah']->storeAs(path: $directory, name: $fileName);

        try {
            DB::transaction(function () use ($fileName, $directory) {
                // Simpan data riwayat pendidikan
                RiwayatPendidikan::create([
                    'pegawai_id'            => $this->pegawai_id,
                    'jenjang_pendidikan_id' => $this->form['jenjang_pendidikan_id'],
                    'tahun_masuk'           => $this->form['tahun_masuk'],
                    'tahun_lulus'           => $this->form['tahun_lulus'],
                    'file_ijazah'           => $fileName,
                    'file_path'             => $directory,
                    'updated_by'            => Auth::id(),
                ]);

                // Catat juga file yang diunggah ke arsip file
                ArsipFile::create([
                    'pegawai_id'  => $this->pegawai_id,
                    'file_name'   => $fileName,
                    'file_path'   => $directory,
                    'jenis_file'  => 'Ijazah',
                    'uploaded_by' => Auth::id(),
                ]);
            });
        } catch (\Throwable $e) {
            // Bersihkan file yang sudah terlanjur diunggah jika penyimpanan data gagal
            Storage::disk('private')->delete("{$directory}/{$fileName}");
            throw $e;
        }

        $this->dispatch('refresh-table-pendidikan');
        $this->dispatch('close-add-pendidikan-modal');
        $this->resetForm();
    }

    #[On('open-add-pendidikan-modal')]
    public function handleModalOpened()
    {
        $this->reset('form');
        $this->resetValidation();
    }

    #[On('close-add-pendidikan-modal')]
    public function resetForm()
    {
        $this->reset('form');
        $this->resetValidation();
    }

    public function updated($property)
    {
        $this->validateOnly($property);
    }

    public function render()
    {
        return view('livewire.manajemen.pegawai.detail-pegawai.pendidikan.tambah-pendidikan');
    }
}