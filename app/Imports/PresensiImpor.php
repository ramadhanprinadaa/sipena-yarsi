<?php

namespace App\Imports;

use App\Models\ImportPresensi;
use App\Models\Pegawai;
use App\Models\Presensi;
use App\Services\StatusKehadiranService2;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Throwable;

class PresensiImpor implements ToModel, WithHeadingRow, WithBatchInserts, WithUpserts, WithChunkReading, SkipsOnFailure, SkipsOnError, SkipsEmptyRows, WithValidation
{
    use Importable, SkipsFailures, SkipsErrors;

    private array $pegawaiMap = [];
    private StatusKehadiranService2 $statusService;
    private ?int $userId;

    protected ?string $periodeMulai = null;
    protected ?string $periodeSelesai = null;

    protected ImportPresensi $import;
    protected ?int $import_id;

    public function __construct(ImportPresensi $importPresensi, $userId = null)
    {
        $this->userId = $userId ?? Auth::id();
        $this->import = $importPresensi;
        $this->import_id = $importPresensi->id;
        $this->statusService = app(StatusKehadiranService2::class);
        $this->pegawaiMap = Pegawai::pluck('id', 'nip')->toArray();
    }

    public function model(array $row)
    {
        // Inisialisasi data
        $pegawai_id            = $this->pegawaiMap[$row['id']] ?? null;
        $tanggal               = Carbon::parse($row['date'])->format('Y-m-d');
        $jamMasuk              = $this->parseExcelTime($row['actual_check_in_time']);
        $jamKeluar             = $this->parseExcelTime($row['actual_check_out_time']);
        $status_kehadiran_id   = null;

        $statusKehadiranExcel  = $row['attendance_status'];

        // Get Periode Tanggal Presensi
        if (!$this->periodeMulai || $tanggal < $this->periodeMulai) {
            $this->periodeMulai = $tanggal;
        }
        if (!$this->periodeSelesai || $tanggal > $this->periodeSelesai) {
            $this->periodeSelesai = $tanggal;
        }

        // Get Status Kehadiran from Service
        if ($pegawai_id) {
            $status_kehadiran_id = $this->statusService->resolve(
                $pegawai_id,
                $tanggal,
                $jamMasuk,
                $jamKeluar,
                $statusKehadiranExcel
            );
        }

        // Skip Hari Libur, pada service null = hari libur.
        if ($status_kehadiran_id === null) {
            return null;
        }

        return new Presensi([
            'pegawai_id'                => $pegawai_id,
            'tanggal'                   => $tanggal,
            'jam_masuk'                 => $jamMasuk,
            'jam_keluar'                => $jamKeluar,
            'status_kehadiran_id'       => $status_kehadiran_id,
            'last_import_presensi_id'   => $this->import_id,
            'created_by'                => $this->userId,
            'updated_by'                => $this->userId,
            'updated_at'                => now(),
        ]);

    }

    public function rules(): array
    {
        return [
            '*.id'                    => ['required', 'string', 'exists:pegawai,nip'],
            '*.date'                  => ['required', 'date'],
            '*.actual_check_in_time'  => ['nullable'],
            '*.actual_check_out_time' => ['nullable'],
            '*.attendance_status'     => ['nullable'],
        ];
    }

    public function customValidationAttributes(): array
    {
        return [
            'id'                    => 'NIP (NIK Pegawai)',
            'date'                  => 'Tanggal',
            'actual_check_in_time'  => 'Jam Masuk',
            'actual_check_out_time' => 'Jam Keluar',
            'attendance_status'     => 'Status Kehadiran Excel',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'id.required'   => 'NIP (NIK Pegawai) kosong.',
            'id.string'     => 'NIP (NIK Pegawai) tidak berupa teks yang valid.',
            'id.exists'     => 'NIP (NIK Pegawai) tidak ditemukan.',

            'date.required' => 'Tanggal kosong.',
            'date.date'     => 'Tanggal tidak tidak berupa tanggal yang valid.',
        ];
    }

    public function getPeriodeMulai(): ?string
    {
        return $this->periodeMulai;
    }

    public function getPeriodeSelesai(): ?string
    {
        return $this->periodeSelesai;
    }

    protected function parseExcelTime(?string $time)
    {
        // Jika data kosong atau strip, maka data = null
        if (empty(trim($time)) || trim($time) === '-') {
            return null;
        }

        try {
            // Jika data Excel membaca waktu sebagai angka desimal
            // Contoh: 0.3194444 = 07:40:00
            if (is_numeric($time)) {
                return Date::excelToDateTimeObject($time)->format('H:i:s');
            }

            // Normalisasi data jika bentuk data Excel 07.41 atau 00.00.00
            $normalizedTime = str_replace('.', ':', trim($time));

            return Carbon::parse($normalizedTime)->format('H:i:s');
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function uniqueBy(): array
    {
        return [
            'pegawai_id',
            'tanggal',
        ];
    }

    public function upsertColumns()
    {
        return [
            'jam_masuk',
            'jam_keluar',
            'status_kehadiran_id',
            'last_import_presensi_id',
            'updated_by',
            'last_updated_value_at'
        ];
    }

    public function headingRow(): int
    {
        return 8;
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