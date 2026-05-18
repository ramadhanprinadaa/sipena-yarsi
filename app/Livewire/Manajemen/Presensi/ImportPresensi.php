<?php

namespace App\Livewire\Manajemen\Presensi;

use App\Imports\PresensiImport;

use App\Models\ImportPresensi as ImportPresensiModel;

use Illuminate\Support\Facades\Auth;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Maatwebsite\Excel\Facades\Excel;


class ImportPresensi extends Component
{
    use WithFileUploads;

    #[Validate('required|file|mimes:xlsx,xls,csv,ods,tsv|max:10240')]
    public $file;

    public ?string $errorMessage = null;

    public bool $showResult = false;

    public function messages()
    {
        return [
            'file.required' => 'Silahkan pilih file terlebih dahulu.',
            'file.mimes' => 'File harus berformat .xlsx, .xls, .csv, .ods, atau .tsv.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
        ];
    }

    public function downloadTemplate()
    {
        $path = storage_path('app/public/templates/template_import_presensi.xlsx');
        if (!file_exists($path)) {
            $this->errorMessage = 'File template tidak ditemukan. Silahkan hubungi administrator.';
            return;
        }

        return response()->download(
            file: $path,
            name: 'template_import_presensi.xlsx',
            headers: [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    public function import()
    {
        $originalFileName = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $this->file->getClientOriginalExtension();
        $filename = $originalFileName . '_' . time() . '.' . $extension;
        $filepath = $this->file->storeAs('imports/presensi', $filename);

        $this->validate();

        $importPresensi = ImportPresensiModel::create([
            'file_name'       => $filename,
            'file_path'       => $filepath,
            'imported_by'     => Auth::id(),
            'total_rows'      => null,
            'total_success'   => null,
            'total_failed'    => null,
            'total_duplicate' => null,
            'total_updated'   => null,
            'total_skipped'   => null,
            'summary'         => null,
        ]);

        Excel::import(
            new PresensiImport($importPresensi),
            $filepath
        );

        session()->flash(
            'success',
            'Import presensi berhasil.'
        );
    }

    public function save()
    {
        $this->validate();
        try {
            $this->import();
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
        $this->showResult = true;
        $this->dispatch('refresh-table');
        $this->reset('file');
    }

    #[On('close-import-modal')]
    public function handleClose()
    {
        $this->reset('file');
        $this->resetValidation();
        $this->showResult = false;
    }

    public function resetImport()
    {
        $this->reset('file');
        $this->resetValidation();
        $this->showResult = false;
    }

    public function render()
    {
        return view('livewire.manajemen.presensi.import-presensi');
    }
}
