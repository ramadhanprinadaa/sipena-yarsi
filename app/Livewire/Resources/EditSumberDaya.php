<?php

namespace App\Livewire\Resources;

use App\Models\SumberDaya;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditSumberDaya extends Component
{
    use WithFileUploads;

    public ?int $sumberDayaId;
    public ?string $existingFileName;

    public ?string $originalJudul;

    #[Validate('required|string|max:255')]
    public ?string $judul;

    #[Validate('nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:10240')]
    public $file;

    #[On('load-edit-sumber-daya')]
    public function loadData(int $sumber_daya_id)
    {
        $sumberDaya = SumberDaya::findOrFail($sumber_daya_id);
        $this->sumberDayaId = $sumberDaya->id;
        $this->judul = $sumberDaya->judul;
        $this->originalJudul = $sumberDaya->judul;
        $this->existingFileName = $sumberDaya->file_name;
        $this->reset('file');
        $this->resetValidation();
        $this->dispatch('edit-sumber-daya-loaded');
    }

    public function save()
    {
        $this->validate();
        $sumberDaya = SumberDaya::findOrFail($this->sumberDayaId);

        $data = ['judul' => $this->judul];

        if ($this->file) {
            // Hapus file lama jika ada
            if (Storage::disk('public')->exists($sumberDaya->file_path)) {
                Storage::disk('public')->delete($sumberDaya->file_path);
            }

            $fileName = $this->file->getClientOriginalName();
            $extension = $this->file->getClientOriginalExtension();
            $mimeType = $this->file->getMimeType();
            $filePath = $this->file->storeAs('sumber_daya', time() . '_' . $fileName, 'public');

            $data['file_name']   = $fileName;
            $data['file_path']   = $filePath;
            $data['extension']   = $extension;
            $data['mime_type']   = $mimeType;
            $data['uploaded_by'] = Auth::id(); // Timpa uploader dengan pengedit terakhir
        }

        $sumberDaya->update($data);

        $this->dispatch('close-edit-modal');
        $this->dispatch('refresh-table');
        $this->dispatch('notify', type: 'success', message: 'Sumber Daya berhasil diperbarui!');
    }

    #[On('close-edit-modal')]
    public function close()
    {
        $this->reset(['sumberDayaId', 'judul', 'file', 'existingFileName']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.resources.edit-sumber-daya');
    }
}