<?php

namespace App\Imports\Pegawai;

use App\Models\JenisKeluarga;
use App\Models\Keluarga;
use App\Models\Pegawai;
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

class KeluargaImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, WithSkipDuplicates, SkipsOnFailure, SkipsEmptyRows, WithEvents
{
    use Importable, SkipsFailures;

    private array $pegawaiMap = [];
    private array $jenisKeluargaMap = [];
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
        $this->jenisKeluargaMap = JenisKeluarga::pluck('id', 'jenis')->toArray();
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
        return new Keluarga([
            'pegawai_id'        => $this->pegawaiMap[$row['nik_pegawai']] ?? null,
            'jenis_keluarga_id' => $this->jenisKeluargaMap[$row['hubungan']] ?? null,
            'nama'              => $row['nama'],
            'tempat_lahir'      => $row['tempat_lahir'],
            'tanggal_lahir'     => $row['tanggal_lahir'],
            'pekerjaan'         => $row['pekerjaan'] ?? null,
            'alamat'            => $row['alamat'] ?? null,
            'no_telpon'         => $row['no_telpon'] ?? null,
            'updated_by'        => $this->userId,
        ]);
    }

    public function rules(): array
    {
        return [
            'nik_pegawai'   => ['required', 'string', 'exists:pegawai,nip'],
            'hubungan'      => ['required', 'string', 'exists:jenis_keluarga,jenis'],
            'nama'          => ['required', 'string'],
            'tempat_lahir'  => ['nullable', 'string'],
            'tanggal_lahir' => ['required', 'date'],
            'pekerjaan'     => ['nullable', 'string'],
            'alamat'        => ['nullable', 'string'],
            'no_telpon'     => ['nullable', 'string'],
        ];
    }

    public function customValidationAttributes(): array
    {
        return [
            'nik_pegawai'   => 'NIP / NIK Pegawai',
            'hubungan'      => 'Hubungan Keluarga',
            'nama'          => 'Nama Keluarga',
            'tempat_lahir'  => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'pekerjaan'     => 'Pekerjaan',
            'alamat'        => 'Alamat',
            'no_telpon'     => 'Nomor Telepon',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            // NIP / NIK Pegawai
            'nik_pegawai.required' => 'NIP / NIK Pegawai kosong / tidak diisi.',
            'nik_pegawai.string'   => 'NIP / NIK Pegawai tidak berupa teks yang valid.',
            'nik_pegawai.exists'   => 'NIP / NIK Pegawai tidak ditemukan pada data referensi sistem.',

            // Hubungan
            'hubungan.required' => 'Hubungan keluarga kosong / tidak diisi.',
            'hubungan.string'   => 'Hubungan keluarga tidak berupa teks yang valid.',
            'hubungan.exists'   => 'Hubungan keluarga tidak ditemukan pada data referensi sistem.',

            // Nama
            'nama.required' => 'Nama keluarga kosong / tidak diisi.',
            'nama.string'   => 'Nama keluarga tidak berupa teks yang valid.',

            // Tempat Lahir
            'tempat_lahir.string' => 'Tempat Lahir tidak berupa teks yang valid.',

            // Tangal Lahir
            'tanggal_lahir.required' => 'Tanggal Lahir kosong / tidak diisi.',
            'tanggal_lahir.date'     => 'Tanggal Lahir tidak menggunakan format tanggal yang valid.',

            // Pekerjaan
            'pekerjaan.string' => 'Pekerjaan tidak berupa teks yang valid.',

            // Alamat
            'alamat.string' => 'Alamat tidak berupa teks yang valid.',

            // Nomor Telpon
            'no_telpon.string' => 'Nomor telepon tidak berupa teks yang valid.',

            // Default Validation Messages
            '*.required' => ':attribute kosong / tidak diisi.',
            '*.exists'   => ':attribute tidak ditemukan pada data referensi sistem.',
            '*.unique'   => ':attribute sudah terdaftar pada sistem.',
            '*.date'     => ':attribute tidak menggunakan format tanggal yang valid.',
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
