<?php

namespace App\Livewire\Resources;

use App\Models\SumberDaya;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class TambahSumberDaya extends Component
{
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public ?string $judul = '';

    #[Validate('required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:10240')]
    public $file;

    protected function messages()
    {
        return [
            'judul.required' => 'Judul wajib diisi.',
            'file.required'  => 'File wajib diunggah.',
            'file.mimes'     => 'Format file harus berupa PDF, WORD, PPT, atau EXCEL.',
            'file.max'       => 'Ukuran file maksimal 10MB.',
        ];
    }

    public function save()
    {
        $this->validate();

        $fileName = $this->file->getClientOriginalName();
        $extension = $this->file->getClientOriginalExtension();
        $mimeType = $this->file->getMimeType();

        // Simpan file ke direktori public/sumber_daya
        $filePath = $this->file->storeAs('sumber_daya', time() . '_' . $fileName, 'public');

        SumberDaya::create([
            'judul'       => $this->judul,
            'file_name'   => $fileName,
            'file_path'   => $filePath,
            'extension'   => $extension,
            'mime_type'   => $mimeType,
            'uploaded_by' => Auth::id(),
        ]);

        $this->resetForm();
        $this->dispatch('close-add-modal');
        $this->dispatch('refresh-table');
        $this->dispatch('notify', type: 'success', message: 'Sumber Daya berhasil ditambahkan!');
    }

    #[On('close-add-modal')]
    public function resetForm()
    {
        $this->reset(['judul', 'file']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.resources.tambah-sumber-daya');
    }
}