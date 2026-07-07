<?php

namespace App\Livewire\Manajemen\Pegawai\DetailPegawai\Pendidikan;

use App\Models\ArsipFile;
use App\Models\JenjangPendidikan;
use App\Models\RiwayatPendidikan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditPendidikan extends Component
{
    use WithFileUploads;

    #[Locked]
    public int $riwayat_pendidikan_id;

    public array $jenjangPendidikan;

    public array $form = [
        'jenjang_pendidikan_id' => '',
        'tahun_masuk'           => '',
        'tahun_lulus'           => '',
        'file_ijazah'           => '',
    ];

    // Nama file ijazah yang sudah tersimpan, ditampilkan sebagai referensi di form
    public ?string $existingFileName = null;

    /**
     * Karena component ini dirender ulang (fresh mount) setiap kali
     * wire:key berubah, mount() otomatis menjadi titik reset yang bersih
     * tanpa perlu listener open/close modal secara manual.
     */
    public function mount(int $riwayat_pendidikan_id): void
    {
        $this->riwayat_pendidikan_id = $riwayat_pendidikan_id;
        $this->jenjangPendidikan = JenjangPendidikan::pluck('kode', 'id')->toArray();

        $riwayat = RiwayatPendidikan::findOrFail($riwayat_pendidikan_id);

        $this->form['jenjang_pendidikan_id'] = $riwayat->jenjang_pendidikan_id;
        $this->form['tahun_masuk'] = $riwayat->tahun_masuk;
        $this->form['tahun_lulus'] = $riwayat->tahun_lulus;

        $this->existingFileName = $riwayat->file_ijazah;
    }

    protected function rules(): array
    {
        return [
            'form.jenjang_pendidikan_id' => ['required', 'exists:jenjang_pendidikan,id'],
            'form.tahun_masuk'           => ['required', 'regex:/^\d{4}$/'],
            'form.tahun_lulus'           => ['required', 'regex:/^\d{4}$/'],
            // File bersifat opsional: hanya divalidasi jika user memilih file baru
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
        $this->validate();

        $riwayat = RiwayatPendidikan::findOrFail($this->riwayat_pendidikan_id);

        $fileName = $riwayat->file_ijazah;
        $directory = $riwayat->file_path;
        $oldFileName = $riwayat->file_ijazah;
        $oldDirectory = $riwayat->file_path;
        $isFileReplaced = false;

        // Hanya proses file baru jika user memilih file pengganti
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
            // Bersihkan file baru yang sudah terlanjur diunggah jika penyimpanan data gagal
            if ($isFileReplaced) {
                Storage::disk('private')->delete("{$directory}/{$fileName}");
            }
            throw $e;
        }

        // Hapus file lama HANYA setelah transaksi berhasil, agar tidak kehilangan file jika gagal
        if ($isFileReplaced && $oldFileName) {
            Storage::disk('private')->delete("{$oldDirectory}/{$oldFileName}");
        }

        $this->dispatch('refresh-table-pendidikan');
        $this->dispatch('close-edit-pendidikan-modal');
    }

    public function updated($property): void
    {
        $this->validateOnly($property);
    }

    public function render()
    {
        return view('livewire.manajemen.pegawai.detail-pegawai.pendidikan.edit-pendidikan');
    }
}