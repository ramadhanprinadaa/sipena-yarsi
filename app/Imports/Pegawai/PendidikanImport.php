<?php

namespace App\Imports\Pegawai;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PendidikanImport implements ToCollection, WithHeadingRow
{
    public int $totalRows = 0;
    public int $successRows = 0;
    public int $failedRows = 0;
    public int $duplicateRows = 0;

    public array $failures = [];
    public array $duplicates = [];
    public array $successData = [];

    public function headingRow(): int
    {
        return 9; // Menyamakan posisi heading baris ke-9
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $this->headingRow() + $index + 1;

            // Filter baris kosong
            if (empty($row['nip']) && empty($row['tingkat_pendidikan'])) {
                continue;
            }

            $this->totalRows++;

            // Contoh simulasi validasi sederhana
            if (empty($row['nip']) || empty($row['tingkat_pendidikan'])) {
                $this->failedRows++;
                $this->failures[] = [
                    'row'    => $rowNumber,
                    'type'   => 'validation',
                    'errors' => ['Kolom NIP dan Tingkat Pendidikan wajib diisi.'],
                    'data'   => $row->toArray(),
                ];
                continue;
            }

            // Jika valid, masukkan ke sukses
            $this->successRows++;
            $this->successData[] = [
                'row'        => $rowNumber,
                'nip'        => $row['nip'],
                'nama'       => $row['nama'] ?? '-', // Untuk display nama pegawai di UI
                'unit_kerja' => $row['tingkat_pendidikan'] . ' - ' . ($row['institusi'] ?? '-'),
            ];
        }
    }

    public function getSummary()
    {
        return [
            'total_rows'      => $this->totalRows,
            'total_success'   => $this->successRows,
            'total_failed'    => $this->failedRows,
            'total_duplicate' => $this->duplicateRows,
            'failures'        => $this->failures,
            'duplicates'      => $this->duplicates,
            'success_data'    => $this->successData,
        ];
    }
}