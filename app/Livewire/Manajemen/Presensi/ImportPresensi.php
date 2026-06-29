<?php

namespace App\Livewire\Manajemen\Presensi;

use App\Imports\PresensiImpor;
use App\Models\ImportPresensi as ImportPresensiModel;
use App\Models\Presensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;


class ImportPresensi extends Component
{
    use WithFileUploads;

    #[Validate('required|file|mimes:xlsx,xls,csv,ods,tsv|max:10240')]
    public $file;

    public bool $showResult = false;
    public ?string $errorMessage = null;

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
        $this->validate();

        $originalFileName = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $this->file->getClientOriginalExtension();
        $filename = $originalFileName . '_' . time() . '.' . $extension;
        $filepath = $this->file->storeAs('imports/presensi', $filename);

        // 1. Buat record awal di database
        $importPresensi = ImportPresensiModel::create([
            'file_name'       => $filename,
            'file_path'       => $filepath,
            'imported_by'     => Auth::id(),
        ]);

        try {
            $importInstance = new PresensiImpor($importPresensi, Auth::id());
            Excel::import($importInstance, $filepath);

            // Get data kegagalan validasi (Failures)
            $failures = $importInstance->failures();
            $total_failed = count($failures);

            // Get Error Summary
            $errorSummary = [];
            foreach ($failures as $failure) {
                $row = $failure->row();
                foreach ($failure->errors() as $errorMessage) {
                    if (!isset($errorSummary[$errorMessage])) {
                        $errorSummary[$errorMessage] = [
                            'message' => $errorMessage,
                            'count' => 0,
                            'rows' => []
                        ];
                    }

                    // Increment count dan masukkan baris jika belum ada di array
                    $errorSummary[$errorMessage]['count']++;
                    if (!in_array($row, $errorSummary[$errorMessage]['rows'])) {
                        $errorSummary[$errorMessage]['rows'][] = $row;
                    }
                }
            }
            $errorSummary = array_values($errorSummary);

            // Kalkulasi Data Sukses
            $querySukses = Presensi::where('last_import_presensi_id', $importPresensi->id);
            $total_success = $querySukses->count();
            $total_updated = 0;
            $total_skipped = 0;
            $total_rows = $total_success + $total_failed;

            // Update Record Import
            $importPresensi->update([
                'periode_mulai'   => $importInstance->getPeriodeMulai(),
                'periode_selesai' => $importInstance->getPeriodeSelesai(),
                'total_rows'      => $total_rows,
                'total_created'   => $total_success,
                'total_updated'   => $total_updated,
                'total_failed'    => $total_failed,
                'total_skipped'   => $total_skipped,
                'error_summary'   => $total_failed > 0 ? $errorSummary : null,
            ]);

            return $importPresensi;

        } catch (\Exception $e) {
            $importPresensi->update([
                'error_summary' => [['message' => 'System Error: ' . $e->getMessage(), 'count' => 1, 'rows' => []]]
            ]);
            Storage::delete($filepath);
            throw $e;
        }
    }

    public function save()
    {
        try {
            $importRecord = $this->import();
            $this->dispatch('refresh-table-import');
            $this->dispatch('load-detail-import', fileId: $importRecord->id);
            $this->dispatch('open-loading-detail-import');
        } catch (\Throwable $e) {
            $this->errorMessage = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
        $this->resetImport();
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