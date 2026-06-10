<?php

namespace App\Livewire\Manajemen\Pegawai;

use App\Imports\Pegawai\DataPegawaiImport;
use App\Models\ImportPegawai;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ImporPegawai extends Component
{
    use WithFileUploads;

    public bool $showResult = false;
    public ?string $errorMessage = null;

    public array $importResults = [];
    public ?string $activeTab = null;

    #[Validate('required|file|mimes:xlsx,xls,csv|max:10240')]
    public $file;

    protected function messages()
    {
        return [
            'file.required' => 'Silahkan pilih file terlebih dahulu.',
            'file.mimes'    => 'File harus berformat .xlsx, .xls, .csv',
            'file.max'      => 'Ukuran file maksimal 10 MB.',
        ];
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

    public function import()
    {
        $this->validate();

        $originalName = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $this->file->getClientOriginalExtension();
        $filename = $originalName . '_' . time() . '.' . $extension;
        $filepath = $this->file->storeAs('imports/pegawai', $filename);

        $this->reset('file');

        try {
            $import = new DataPegawaiImport();
            Excel::import($import, $filepath);

            $this->importResults = [];
            $totalRowsAll = 0;
            $totalSuccessAll = 0;
            $totalFailedAll = 0;

            // Mapping dinamis error per sheet
            foreach ($import->sheetImports as $sheetName => $sheetImport) {
                $failures = $sheetImport->failures();
                $sheetErrors = [];
                $uniqueFailedRowsArray = [];

                foreach ($failures as $failure) {
                    $row = $failure->row();
                    $uniqueFailedRowsArray[$row] = true;

                    foreach ($failure->errors() as $errorMessage) {
                        if (!isset($sheetErrors[$errorMessage])) {
                            $sheetErrors[$errorMessage] = [];
                        }
                        if (!in_array($row, $sheetErrors[$errorMessage])) {
                            $sheetErrors[$errorMessage][] = $row;
                        }
                    }
                }

                $totalFailed = count($uniqueFailedRowsArray);
                $totalRows = property_exists($sheetImport, 'totalRows') ? $sheetImport->totalRows : 0;
                $totalSuccess = max(0, $totalRows - $totalFailed);

                foreach ($sheetErrors as $msg => &$rows) {
                    sort($rows);
                }

                $this->importResults[$sheetName] = [
                    'errors'        => $sheetErrors,
                    'total_rows'    => $totalRows,
                    'total_success' => $totalSuccess,
                    'total_failed'  => $totalFailed,
                ];

                $totalRowsAll += $totalRows;
                $totalSuccessAll += $totalSuccess;
                $totalFailedAll += $totalFailed;
            }

            ImportPegawai::create([
                'file_name'         => $filename,
                'file_path'         => $filepath,
                'imported_by'       => Auth::id(),
                'total_rows'        => $totalRowsAll,
                'total_success'     => $totalSuccessAll,
                'total_failed'      => $totalFailedAll,
                'total_duplicate'   => null,
            ]);
        } catch (\Exception $e) {
            $this->errorMessage = "Terjadi kesalahan sistem: " . $e->getMessage();
            return;
        }

        if (count($this->importResults) > 0) {
            $this->activeTab = array_key_first($this->importResults);
        }

        $this->showResult = true;
        $this->reset('file');
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function resetImport()
    {
        $this->showResult = false;
        $this->reset(['file', 'importResults', 'activeTab']);
        $this->resetValidation();
    }

    #[On('close-import-modal')]
    public function close()
    {
        $this->resetImport();
        $this->dispatch('refresh-table');
    }

    public function render()
    {
        return view('livewire.manajemen.pegawai.impor-pegawai');
    }
}
