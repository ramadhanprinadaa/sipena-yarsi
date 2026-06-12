<?php

namespace App\Imports\Pegawai;

use App\Models\Pegawai;
use App\Models\Rekening;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithSkipDuplicates;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeSheet;

class RekeningImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, WithSkipDuplicates, SkipsOnFailure, SkipsEmptyRows, WithEvents
{
    use Importable, SkipsFailures;

    private array $pegawaiMap = [];
    private ?int $userId;
    public int $totalRows = 0;

    // Handle data id pegawai yang barus saja di import
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $this->loadData();
            },
        ];
    }

    public function loadData()
    {
        $this->pegawaiMap = Pegawai::pluck('id', 'nip')->toArray();
    }

    public function __construct($userId = null)
    {
        $this->userId = $userId ?? Auth::id();
    }

    public function prepareForValidation($data, $index)
    {
        $this->totalRows++;
        return $data;
    }

    public function model(array $row)
    {
        return new Rekening([
            'pegawai_id'        => $this->pegawaiMap[$row['nik_pegawai']] ?? null,
            'nama_bank'         => $row['nama_bank'],
            'nomor_rekening'    => $row['nomor_rekening'],
            'nama_rekening'     => $row['nama_rekening'],
            'updated_by'        => $this->userId,
        ]);
    }

    public function rules(): array
    {
        return [
            'nik_pegawai'       => ['required', 'string', 'exists:pegawai,nip'],
            'nama_bank'         => ['required', 'string'],
            'nomor_rekening'    => ['required', 'string', 'unique:rekening,nomor_rekening'],
            'nama_rekening'     => ['required', 'string'],
        ];
    }

    public function customValidationAttributes(): array
    {
        return [
            'nik_pegawai'       => 'NIP / NIK Pegawai',
            'nama_bank'         => 'Nama Bank',
            'nomor_rekening'    => 'Nomor Rekening',
            'nama_rekening'     => 'Nama Rekening',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nik_pegawai.required' => 'NIP / NIK Pegawai kosong / tidak diisi.',
            'nik_pegawai.string'   => 'NIP / NIK Pegawai tidak berupa teks yang valid.',
            'nik_pegawai.exists'   => 'NIP / NIK Pegawai tidak ditemukan pada data referensi sistem.',

            'nama_bank.required' => 'Nama Bank kosong / tidak diisi.',
            'nama_bank.string' => 'Nama Bank tidak berupa teks yang valid.',

            'nomor_rekening.required' => 'Nomor Rekening kosong / tidak diisi.',
            'nomor_rekening.string' => 'Nomor Rekening tidak berupa teks yang valid.',
            'nomor_rekening.unique' => 'Nomor Rekening sudah terdaftar pada sistem.',

            'nama_rekening.required' => 'Nama Rekening kosong / tidak diisi.',
            'nama_rekening.string' => 'Nama Rekening tidak berupa teks yang valid.',

            // Default Validation Messages
            '*.required' => ':attribute kosong / tidak diisi.',
            '*.exists'   => ':attribute tidak ditemukan pada data referensi sistem.',
            '*.unique'   => ':attribute sudah terdaftar pada sistem.',
            '*.string'   => ':attribute tidak berupa teks yang valid.',
        ];
    }

    public function headingRow(): int
    {
        return 9;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}