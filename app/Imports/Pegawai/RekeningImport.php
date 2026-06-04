<?php

namespace App\Imports\Pegawai;

use App\Models\Pegawai;
use App\Models\Rekening;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RekeningImport implements ToCollection, WithHeadingRow
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
        return 9;
    }

    public function rules()
    {
        return [
            'nik_pegawai'    => 'required|string',
            'nama_bank'      => 'required|string|max:100',
            'nomor_rekening' => 'required|string|max:50',
            'nama_rekening'  => 'required|string|max:255',
        ];
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) return;

        // Proteksi Sheet
        $firstRow = $rows->first()->toArray();
        if (!array_key_exists('nomor_rekening', $firstRow) && !array_key_exists('nik_pegawai', $firstRow)) {
            return;
        }

        $this->totalRows += $rows->count();

        // Prefetch existing nip pegawai
        $validNips = array_flip(Pegawai::pluck('nip')->filter()->toArray());

        // Prefetch existing nomor rekening untuk cek duplikasi
        $existingRekening = Rekening::pluck('pegawai_nip', 'nomor_rekening')->toArray();

        $upsertData = [];
        $nipsInCurrentExcel = [];
        $rekeningInCurrentExcel = [];

        foreach ($rows as $index => $row) {

            $rowNumber = $index + 10;
            $data = $row->toArray();

            // get data nip dan nomor rekening from file excel
            $nip = isset($data['nik_pegawai']) ? trim((string) $data['nik_pegawai']) : null;
            $noRekening = isset($data['nomor_rekening']) ? trim((string) $data['nomor_rekening']) : null;

            // Validasi format dan required
            $validator = Validator::make($data, $this->rules());
            if ($validator->fails()) {
                $this->failedRows++;
                $this->failures[] = [
                    'row'     => $rowNumber,
                    'message' => implode(', ', $validator->errors()->all()),
                ];
                continue;
            }

            // Validasi NIP Pegawai harus ada di database
            if (!isset($validNips[$nip])) {
                $this->failedRows++;
                $this->failures[] = [
                    'row'     => $rowNumber,
                    'message' => "Pegawai dengan NIP/NIK {$nip} tidak ditemukan di database.",
                ];
                continue;
            }

            // Cek Duplikasi Apakah NIP muncul 2x di Excel
            $isDuplicate = false;
            $duplicateReasons = [];
            // A. Duplikat Internal (Dalam 1 file excel yg sama)
            if (isset($nipsInCurrentExcel[$nip])) {
                $isDuplicate = true;
                $duplicateReasons[] = "NIP {$nip} muncul lebih dari satu kali di file ini.";
            } else {
                $nipsInCurrentExcel[$nip] = true;
            }

            if (isset($rekeningInCurrentExcel[$noRekening])) {
                $isDuplicate = true;
                $duplicateReasons[] = "Nomor Rekening {$noRekening} muncul lebih dari satu kali di file ini.";
            } else {
                $rekeningInCurrentExcel[$noRekening] = true;
            }

            // B. Duplikat Database (Nomor rekening dipakai orang lain)
            if (isset($existingRekening[$noRekening]) && $existingRekening[$noRekening] !== $nip) {
                $isDuplicate = true;
                $pemilikAsli = $existingRekening[$noRekening];
                $duplicateReasons[] = "Nomor Rekening {$noRekening} sudah digunakan oleh NIP {$pemilikAsli}.";
            }

            if ($isDuplicate) {
                $this->duplicateRows++;
                $this->duplicates[] = [
                    'row'     => $rowNumber,
                    'message' => implode(' | ', $duplicateReasons),
                ];
                continue;
            }

            try {
                $upsertData[] = [
                    'pegawai_nip'    => $nip,
                    'nama_bank'      => $data['nama_bank'],
                    'nomor_rekening' => $noRekening,
                    'nama_rekening'  => $data['nama_rekening'],
                    'updated_at'     => now(),
                ];

                $this->successRows++;
                $this->successData[] = [
                    'row' => $rowNumber,
                    'nip' => $nip,
                    'nama_bank' => $data['nama_bank'],
                    'nomor_rekening' => $noRekening
                ];
            } catch (\Throwable $e) {
                $this->failedRows++;
                $this->failures[] = [
                    'row'     => $rowNumber,
                    'message' => 'System error: ' . $e->getMessage(),
                ];
            }
        }

        if (!empty($upsertData)) {
            foreach (array_chunk($upsertData, 500) as $chunk) {
                Rekening::upsert(
                    $chunk,
                    ['pegawai_nip'],
                    ['nama_bank', 'nomor_rekening', 'nama_rekening', 'updated_at'] // Update data jika NIP sudah ada
                );
            }
        }
    }

    public function getSummary()
    {
        return [
            'total_rows'         => $this->totalRows,
            'total_success'      => $this->successRows,
            'total_failed'       => $this->failedRows,
            'total_duplicate'    => $this->duplicateRows,
            'failures'           => $this->failures,
            'duplicates'         => $this->duplicates,
            'success_data'       => $this->successData,
        ];
    }
}