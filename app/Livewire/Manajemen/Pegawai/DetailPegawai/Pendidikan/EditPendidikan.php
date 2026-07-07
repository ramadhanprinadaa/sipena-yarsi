<?php

namespace App\Livewire\Manajemen\Pegawai\DetailPegawai\Pendidikan;

use App\Models\ArsipFile;
use App\Models\JenjangPendidikan;
use App\Models\Pegawai;
use App\Models\RiwayatPendidikan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditPendidikan extends Component
{
    use WithFileUploads;

    #[Locked]
    public int $pegawai_id;
    #[Locked]
    public int $riwayat_pendidikan_id;

    public string $nama_pegawai = '';

    public array $jenjangPendidikan;

    public ?string $existingFileName = null;

    public array $form = [
        'jenjang_pendidikan_id' => '',
        'tahun_masuk'           => '',
        'tahun_lulus'           => '',
        'file_ijazah'           => '',
    ];
    public array $originalForm = [];

    public function mount(int $pegawai_id): void
    {
        $this->pegawai_id = $pegawai_id;
        $this->nama_pegawai = Pegawai::where('id', $pegawai_id)->value('nama') ?? '';
        $this->jenjangPendidikan = JenjangPendidikan::pluck('kode', 'id')->toArray();
    }

    #[On('load-edit-pendidikan')]
    public function loadData(int $pendidikan_id)
    {
        $this->riwayat_pendidikan_id = $pendidikan_id;
        $pendidikan = RiwayatPendidikan::select(
            'jenjang_pendidikan_id',
            'tahun_masuk',
            'tahun_lulus',
            'file_ijazah',
        )->find($pendidikan_id);

        if ($pendidikan) {
            $this->form = [
                'jenjang_pendidikan_id' => $pendidikan->jenjang_pendidikan_id,
                'tahun_masuk' => $pendidikan->tahun_masuk,
                'tahun_lulus' => $pendidikan->tahun_lulus,
                'file_ijazah' => $pendidikan->file_ijazah,
            ];
        }
        $this->originalForm = $this->form;
        $this->existingFileName = $this->form['file_ijazah'];
        $this->dispatch('edit-pendidikan-loaded');
    }

    protected function rules(): array
    {
        return [
            'form.jenjang_pendidikan_id' => ['required', 'exists:jenjang_pendidikan,id'],
            'form.tahun_masuk'           => ['required', 'regex:/^\d{4}$/'],
            'form.tahun_lulus'           => ['required', 'regex:/^\d{4}$/'],
            'form.file_ijazah'           => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
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

            'form.file_ijazah.file'               => 'File ijazah harus berupa file yang valid.',
            'form.file_ijazah.mimes'              => 'File ijazah harus berformat PDF, DOC, atau DOCX.',
            'form.file_ijazah.max'                => 'Ukuran file ijazah maksimal 10 MB.',
        ];
    }

    public function update(): void
    {
        if (!$this->isDirty) {
            $this->dispatch('close-edit-pendidikan-modal');
            return;
        }

        if (! $this->form['file_ijazah'] instanceof UploadedFile) {
            $this->form['file_ijazah'] = null;
        }

        $this->validate();

        $riwayat = RiwayatPendidikan::findOrFail($this->riwayat_pendidikan_id);

        $fileName = $riwayat->file_ijazah;
        $directory = $riwayat->file_path;

        $oldFileName = $riwayat->file_ijazah;
        $oldDirectory = $riwayat->file_path;
        
        $isFileReplaced = false;

        // Hanya proses file baru jika user benar-benar memilih file pengganti
        if ($this->form['file_ijazah']) {
            $kodeJenjang = $this->jenjangPendidikan[$this->form['jenjang_pendidikan_id']] ?? 'Pendidikan';
            $kodeJenjang = Str::slug($kodeJenjang, '');
            $extension = $this->form['file_ijazah']->getClientOriginalExtension();

            $fileName = sprintf('Ijazah_%s_%s.%s', $kodeJenjang, now()->format('YmdHis'), $extension);
            $directory = "arsip_file/pegawai/{$riwayat->pegawai_id}/ijazah";

            $this->form['file_ijazah']->storeAs(path: $directory, name: $fileName);
            $isFileReplaced = true;
        }

        try {
            DB::transaction(function () use ($riwayat, $fileName, $directory, $isFileReplaced) {
                $riwayat->update([
                    'jenjang_pendidikan_id' => $this->form['jenjang_pendidikan_id'],
                    'tahun_masuk'           => $this->form['tahun_masuk'],
                    'tahun_lulus'           => $this->form['tahun_lulus'],
                    'file_ijazah'           => $fileName,
                    'file_path'             => $directory,
                    'updated_by'            => Auth::id(),
                ]);

                if ($isFileReplaced) {
                    ArsipFile::create([
                        'pegawai_id'  => $riwayat->pegawai_id,
                        'file_name'   => $fileName,
                        'file_path'   => $directory,
                        'jenis_file'  => 'Ijazah',
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            });
        } catch (\Throwable $e) {
            if ($isFileReplaced) {
                Storage::delete("{$directory}/{$fileName}");
            }
            throw $e;
        }

        if ($isFileReplaced && $oldFileName) {
            Storage::delete("{$oldDirectory}/{$oldFileName}");
        }
        $this->form['file_ijazah'] = $fileName;

        $this->dispatch('refresh-table-pendidikan');
        $this->dispatch('close-edit-pendidikan-modal');
    }

    public function updated($property): void
    {
        if ($property === 'form.file_ijazah') {
            return;
        }
        $this->validateOnly($property);
    }

    public function getIsDirtyProperty()
    {
        return $this->form != $this->originalForm;
    }

    public function resetForm()
    {
        $this->reset(['form', 'originalForm', 'riwayat_pendidikan_id']);
        $this->resetValidation();
    }

    #[On('close-edit-pendidikan-modal')]
    public function handleClose()
    {
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.manajemen.pegawai.detail-pegawai.pendidikan.edit-pendidikan');
    }
}