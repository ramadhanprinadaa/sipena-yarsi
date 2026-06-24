<?php

namespace App\Livewire\Manajemen\Presensi;

use App\Imports\PresensiImpor;
use App\Models\ImportPresensi as ImportPresensiModel;
use App\Models\Presensi;
use Illuminate\Support\Facades\Auth;
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

    // ... (method messages() dan downloadTemplate() biarkan sama) ...

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
            // 2. Jalankan Import HANYA SEKALI
            $importInstance = new PresensiImpor($importPresensi, Auth::id());
            Excel::import($importInstance, $filepath);

            // 3. Ambil data kegagalan validasi (Failures)
            $failures = $importInstance->failures();
            $total_failed = count($failures);

            // Kelompokkan error agar sesuai dengan struktur Blade (message, count, rows)
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
            $errorSummary = array_values($errorSummary); // Re-index array

            // 4. Kalkulasi Data Sukses dari Database
            $querySukses = Presensi::where('last_import_presensi_id', $importPresensi->id);
            $total_success = $querySukses->count();

            $total_created = (clone $querySukses)
                ->where('created_at', '>=', $importPresensi->created_at)
                ->count();

            $total_updated = $total_success - $total_created;
            $total_rows = $total_success + $total_failed;

            // 5. Update Record Import
            $importPresensi->update([
                'periode_mulai'   => $importInstance->periodeMulai,
                'periode_selesai' => $importInstance->periodeSelesai,
                'total_rows'      => $total_rows,
                'total_created'   => $total_created,
                'total_updated'   => $total_updated,
                'total_failed'    => $total_failed,
                'error_summary'   => $total_failed > 0 ? $errorSummary : null,
            ]);

            return $importPresensi;

        } catch (\Exception $e) {
            // Jika gagal di tengah jalan, hapus/update record
            $importPresensi->update(['error_summary' => json_encode(['System Error' => $e->getMessage()])]);
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

    // ... (method lainnya biarkan sama) ...
}