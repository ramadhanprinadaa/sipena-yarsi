<?php

namespace App\Livewire\Manajemen\Pegawai;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImportPegawai extends Component
{
    use WithFileUploads;

    #[Validate('required|file|mimes:xlsx,xls,csv,ods,tsv|max:10240')]
    public $file;

    public string $errorMessage;

    public function downloadTemplate()
    {
        $path = storage_path('app/public/templates/template_import_pegawai.xlsx');
        if(!file_exists($path)) {
            $this->errorMessage = 'File template tidak ditemukan. Silahkan hubungi administrator.';
            return;
        }

        return response()->download(
            file: $path,
            name: 'template_import_pegawai.xlsx',
            headers: [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    public function save()
    {
        $this->validate();
        $path = $this->file->store('imports/pegawai');
        $filename = $this->file->getClientOriginalName() . '_' . time();

        DB::table('import_pegawai')->insert([
            'file_name' => $filename,
            'file_path' => $path,
            'imported_by' => Auth::user()->id,
            'total_rows' => null,
            'success_rows' => null,
            'failed_rows' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->dispatch('close-import-modal');
        $this->dispatch('open-progress-modal');
        $this->reset('file');
    }

    // handle close
    #[On('close-import-modal')]
    public function handleClose()
    {
        $this->reset('file');
    }

    public function render()
    {
        return view('livewire.manajemen.pegawai.import-pegawai');
    }
}