<?php

namespace App\Livewire\Manajemen\Pegawai;

use App\Imports\Pegawai\PegawaiImportData;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;

use Maatwebsite\Excel\Facades\Excel;

class ImportPegawai extends Component
{
    use WithFileUploads;

    #[Validate('required|file|mimes:xlsx,xls,csv,ods,tsv|max:10240')]
    public $file;

    public ?string $errorMessage = null;

    public bool $showResult = false;

    public array $importSummary = [];

    public $iteration = 0;

    public function messages()
    {
        return [
            'file.required' => 'Silahkan pilih file terlebih dahulu.',
            'file.mimes' => 'File harus berformat .xlsx, .xls, .csv, .ods, atau .tsv.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
        ];
    }

    public function import(string $filePath): array
    {
        $import = new PegawaiImportData();
        Excel::import($import, $filePath);
        return $import->getSummaries() ?? [];
    }

    public function downloadTemplate()
    {
        $path = storage_path('app/public/templates/template_import_pegawai.xlsx');
        if (!file_exists($path)) {
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
        try {
            $originalName = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $this->file->getClientOriginalExtension();
            $filename = $originalName . '_' . time() . '.' . $extension;
            $path = $this->file->storeAs('imports/pegawai', $filename);

            // Import Excel
            $summary = $this->import($path);

            // Hitung akumulasi (total) dari seluruh sheet untuk disimpan ke Log DB
            $totalRows = $totalSuccess = $totalFailed = $totalDuplicate = 0;
            foreach ($summary as $sheetName => $sheetSummary) {
                $totalRows += $sheetSummary['total_rows'] ?? 0;
                $totalSuccess += $sheetSummary['total_success'] ?? 0;
                $totalFailed += $sheetSummary['total_failed'] ?? 0;
                $totalDuplicate += $sheetSummary['total_duplicate'] ?? 0;
            }

            // dd($summary);

            // Save Log to DB
            DB::table('import_pegawai')->insert([
                'file_name'         => $filename,
                'file_path'         => $path,
                'imported_by'       => Auth::id(),
                'total_rows'        => $totalRows,
                'total_success'     => $totalSuccess,
                'total_failed'      => $totalFailed,
                'total_duplicate'   => $totalDuplicate,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            $this->importSummary = $summary;
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
            // dd($e->getMessage());
        }

        $this->showResult = true;
        $this->dispatch('refresh-table');
        $this->reset('file');
        // $this->dispatch('open-progress-modal');
    }

    // handle close
    #[On('close-import-modal')]
    public function handleClose()
    {
        $this->reset('file');
        $this->resetValidation();
        $this->showResult = false;
    }

    public function resetImport()
    {
        $this->reset(['file', 'importSummary', 'errorMessage']);
        $this->resetValidation();
        $this->iteration++;
        $this->showResult = false;
    }

    public function render()
    {
        return view('livewire.manajemen.pegawai.import-pegawai');
    }
}