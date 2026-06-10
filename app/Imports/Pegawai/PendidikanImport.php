<?php

namespace App\Imports\Pegawai;

use App\Models\JenjangPendidikan;
use App\Models\Pegawai;
use App\Models\RiwayatPendidikan;
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

class PendidikanImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, WithSkipDuplicates, SkipsOnFailure, SkipsEmptyRows, WithEvents
{
    use Importable, SkipsFailures;

    private array $pegawaiMap = [];
    private array $jenjangPendidikanMap = [];
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
        $this->jenjangPendidikanMap = JenjangPendidikan::pluck('id', 'kode')->toArray();
    }

    public function __construct($userId = null)
    {
        $this->userId = $userId ?? Auth::id();
    }

    public function prepareForValidation($data, $index)
    {
        $this->totalRows++;
        // Jika user menginput 's1', ubah otomatis secara sistem menjadi 'S1'
        if (isset($data['jenjang_pendidikan'])) {
            $data['jenjang_pendidikan'] = strtoupper(trim($data['jenjang_pendidikan']));
        }

        // Bersihkan juga tahun seperti yang dibahas sebelumnya
        if (isset($data['tahun_masuk'])) {
            $data['tahun_masuk'] = trim($data['tahun_masuk']);
        }
        if (isset($data['tahun_lulus'])) {
            $data['tahun_lulus'] = trim($data['tahun_lulus']);
        }

        return $data;
    }

    public function model(array $row)
    {
        return new RiwayatPendidikan([
            'pegawai_id'            => $this->pegawaiMap[$row['nik_pegawai']] ?? null,
            'jenjang_pendidikan_id' => $this->jenjangPendidikanMap[$row['jenjang_pendidikan']] ?? null,
            'tahun_masuk'           => $row['tahun_masuk'] ?? null,
            'tahun_lulus'           => $row['tahun_lulus'] ?? null,
            'file_ijazah'           => null,
            'file_path'             => null,
            'updated_by'            => $this->userId,
        ]);
    }

    public function rules(): array
    {
        return [
            'nik_pegawai'        => ['required', 'string', 'exists:pegawai,nip'],
            'jenjang_pendidikan' => ['required', 'string', 'exists:jenjang_pendidikan,kode'],
            'tahun_masuk'        => ['nullable', 'integer', 'digits:4'],
            'tahun_lulus'        => ['nullable', 'integer', 'digits:4'],
        ];
    }

    public function customValidationAttributes(): array
    {
        return [
            'nik_pegawai'        => 'NIP / NIK Pegawai',
            'jenjang_pendidikan' => 'Jenjang Pendidikan',
            'tahun_masuk'        => 'Tahun Masuk',
            'tahun_lulus'        => 'Tahun Lulus',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            // NIP / NIK Pegawai
            'nik_pegawai.required' => 'NIP / NIK Pegawai kosong / tidak diisi.',
            'nik_pegawai.string'   => 'NIP / NIK Pegawai tidak berupa teks yang valid.',
            'nik_pegawai.exists'   => 'NIP / NIK Pegawai tidak ditemukan pada data referensi sistem.',

            // Jenjang Pendidikan
            'jenjang_pendidikan.required' => 'Jenjang Pendidikan kosong / tidak diisi.',
            'jenjang_pendidikan.string'   => 'Jenjang Pendidikan tidak berupa teks yang valid.',
            'jenjang_pendidikan.exists'   => 'Jenjang Pendidikan tidak ditemukan pada data referensi sistem.',

            // Tahun Masuk
            'tahun_masuk.integer'   => 'Tahun Masuk tidak menggunakan format tahun yang valid.',
            'tahun_masuk.digits'    => 'Tahun Masuk tidak menggunakan format tahun yang valid 4 digit.',

            // Tahun Lulus
            'tahun_lulus.integer'   => 'Tahun Lulus tidak menggunakan format tahun yang valid.',
            'tahun_lulus.digits'    => 'Tahun Lulus tidak menggunakan format tahun yang valid 4 digit.',
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
