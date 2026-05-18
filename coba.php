<?php

namespace App\Imports;

use Throwable;
use Carbon\Carbon;

use App\Models\Presensi;
use App\Models\HariLibur;
use App\Models\ImportPresensi;

use Maatwebsite\Excel\Events\AfterImport;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PresensiImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    WithBatchInserts,
    WithChunkReading,
    SkipsOnFailure,
    SkipsOnError,
    WithEvents
{
    use SkipsFailures;
    use SkipsErrors;

    protected $import;

    protected $totalRows = 0;
    protected $success = 0;
    protected $failed = 0;
    protected $updated = 0;
    protected $skipped = 0;

    protected $detail = [];

    public function __construct(ImportPresensi $import)
    {
        $this->import = $import;
    }

    public function model(array $row)
    {
        $this->totalRows++;

        try {

            /*
            |--------------------------------------------------------------------------
            | Mapping Row
            |--------------------------------------------------------------------------
            */

            $pegawaiNip       = trim($row['id']);
            $tanggal          = $row['date'];

            $attendanceStatus = $row['attendance_status'] ?? null;

            $jamMasuk         = $row['actual_check_in_time'] ?? null;
            $jamKeluar        = $row['actual_check_out_time'] ?? null;

            /*
            |--------------------------------------------------------------------------
            | Skip Hari Libur Tanpa Aktivitas
            |--------------------------------------------------------------------------
            */

            $isHariLibur = $this->isHariLibur($tanggal);

            if ($isHariLibur && !$jamMasuk && !$jamKeluar) {

                $this->skipped++;

                $this->detail[] = [
                    'pegawai_nip' => $pegawaiNip,
                    'tanggal' => $tanggal,
                    'status' => 'skipped',
                    'message' => 'Hari libur tanpa aktivitas',
                ];

                return null;
            }

            /*
            |--------------------------------------------------------------------------
            | Simpan Presensi
            |--------------------------------------------------------------------------
            */

            $presensi = Presensi::updateOrCreate(
                [
                    'pegawai_nip' => $pegawaiNip,
                    'tanggal' => $tanggal,
                ],
                [
                    'import_presensi_id' => $this->import->id,

                    'jam_masuk' => $jamMasuk,
                    'jam_keluar' => $jamKeluar,

                    'status_kehadiran_id' => $this->getStatusKehadiran(
                        $pegawaiNip,
                        $tanggal,
                        $attendanceStatus,
                        $jamMasuk,
                        $jamKeluar,
                        $isHariLibur
                    ),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Summary
            |--------------------------------------------------------------------------
            */

            if ($presensi->wasRecentlyCreated) {

                $this->success++;

                $this->detail[] = [
                    'pegawai_nip' => $pegawaiNip,
                    'tanggal' => $tanggal,
                    'status' => 'created',
                    'message' => 'Presensi berhasil ditambahkan',
                ];
            } else {

                $this->updated++;

                $this->detail[] = [
                    'pegawai_nip' => $pegawaiNip,
                    'tanggal' => $tanggal,
                    'status' => 'updated',
                    'message' => 'Presensi berhasil diperbarui',
                ];
            }

            return $presensi;
        } catch (Throwable $e) {

            $this->failed++;

            $this->detail[] = [
                'pegawai_nip' => $row['id'] ?? null,
                'tanggal' => $row['date'] ?? null,
                'status' => 'failed',
                'message' => $e->getMessage(),
            ];

            return null;
        }
    }

    public function registerEvents(): array
    {
        return [

            AfterImport::class => function () {

                $this->import->update([

                    'total_rows' => $this->totalRows,
                    'total_success' => $this->success,
                    'total_failed' => $this->failed,
                    'total_updated' => $this->updated,
                    'total_skipped' => $this->skipped,

                    'summary' => json_encode([
                        'rows' => $this->totalRows,
                        'success' => $this->success,
                        'failed' => $this->failed,
                        'updated' => $this->updated,
                        'skipped' => $this->skipped,
                        'detail' => $this->detail,
                    ]),
                ]);
            },
        ];
    }

    public function getStatusKehadiran(
        $pegawaiNip,
        $tanggal,
        $attendanceStatus,
        $jamMasuk,
        $jamKeluar,
        $isHariLibur = false
    ) {

        /*
        |--------------------------------------------------------------------------
        | Hari Libur Tapi Masuk
        |--------------------------------------------------------------------------
        */

        if ($isHariLibur && ($jamMasuk || $jamKeluar)) {
            return 9; // Masuk Hari Libur / Lembur
        }

        /*
        |--------------------------------------------------------------------------
        | Pengajuan
        |--------------------------------------------------------------------------
        */

        if ($this->isIzin($pegawaiNip, $tanggal)) {
            return 6;
        }

        if ($this->isSakit($pegawaiNip, $tanggal)) {
            return 7;
        }

        if ($this->isCuti($pegawaiNip, $tanggal)) {
            return 8;
        }

        /*
        |--------------------------------------------------------------------------
        | Tidak Absen
        |--------------------------------------------------------------------------
        */

        if (!$jamMasuk && !$jamKeluar) {
            return 5;
        }

        /*
        |--------------------------------------------------------------------------
        | Absen 1x
        |--------------------------------------------------------------------------
        */

        if (!$jamMasuk || !$jamKeluar) {
            return 4;
        }

        /*
        |--------------------------------------------------------------------------
        | Total Jam Kerja
        |--------------------------------------------------------------------------
        */

        $totalJamKerja = Carbon::parse($jamMasuk)
            ->diffInHours(Carbon::parse($jamKeluar));

        if ($totalJamKerja >= 8) {
            return 1;
        }

        if ($totalJamKerja >= 6) {
            return 2;
        }

        return 3;
    }

    public function isCuti($pegawaiNip, $tanggal): bool
    {
        return false;
    }

    public function isIzin($pegawaiNip, $tanggal): bool
    {
        return false;
    }

    public function isSakit($pegawaiNip, $tanggal): bool
    {
        return false;
    }

    public function isHariLibur($tanggal): bool
    {
        return HariLibur::whereDate('tanggal', $tanggal)
            ->exists();
    }

    public function rules(): array
    {
        return [
            '*.id' => ['required'],
            '*.date' => ['required'],
        ];
    }

    public function headerRow(): int
    {
        return 8;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}