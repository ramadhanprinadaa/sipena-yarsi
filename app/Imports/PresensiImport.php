<?php

namespace App\Imports;

use App\Models\HariLibur;
use App\Models\ImportPresensi;
use App\Models\Presensi;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterImport;
use Throwable;

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

            $pegawaiNip         = $row['id'];
            $tanggal            = $row['date'];
            $attendaceStatus    = $row['attendance_status'];
            $jamMasuk           = $row['actual_check_in_time'];
            $jamKeluar          = $row['actual_check_out_time'];

            // Handle Hari Libur
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

            $presensi = Presensi::updateOrCreate([
                [
                    'pegawai_nip'         => $pegawaiNip,
                    'tanggal'             => $tanggal,
                ],
                [
                    'import_presensi_id'  => $this->import->id,
                    'jam_masuk'           => $jamMasuk,
                    'jam_keluar'          => $jamKeluar,
                    'status_kehadiran_id' => $this->getStatusKehadiran(
                        $pegawaiNip,
                        $tanggal,
                        $attendaceStatus,
                        $jamMasuk,
                        $jamKeluar,
                        $isHariLibur
                    ),
                ]
            ]);

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
                    'total_rows'      => $this->totalRows,
                    'total_success'   => $this->success,
                    'total_failed'    => $this->failed,
                    'total_updated'   => $this->updated,
                    'total_skipped'   => $this->skipped,
                    'summary'         => json_encode([
                        'detail' => $this->detail,
                    ]),
                ]);
            },
        ];
    }

    public function getStatusKehadiran(
        $pegawaiId,
        $tanggal,
        $attendaceStatus,
        $jamMasuk,
        $jamKeluar,
        $isHariLibur = false
    ) {
        $totalJamKerja = Carbon::parse($jamMasuk)->diffInHours(Carbon::parse($jamKeluar));

        /**
         * Cek dan Handle Hari Libur Jika Pegawai Masuk
         */
        if ($isHariLibur && ($jamMasuk || $jamKeluar)) {
            return 9; // Masuk Hari Libur / Lembur
        }

        /**
         * Cek Pengajuan Cuti, Izin, dan Sakit
         */

        // Cek Izin
        if ($this->isIzin($pegawaiId, $tanggal)) {
            return 6; // Izin
        }

        // Cek Sakit
        if ($this->isSakit($pegawaiId, $tanggal)) {
            return 7; // Sakit
        }

        // Cek Cuti
        if ($this->isCuti($pegawaiId, $tanggal)) {
            return 8; // Cuti
        }


        /**
         * Cek Absensi Normal
         */

        // Absen 2x & ≥ 8 Jam
        if ($jamMasuk && $jamKeluar && $totalJamKerja >= 8) {
            return 1; // Hadir Normal
        }

        // Absen 2x & 6 - 7,59 Jam
        if ($jamMasuk && $jamKeluar && $totalJamKerja >= 6 && $totalJamKerja < 8) {
            return 2; // Hadir Kurang Jam
        }

        // Absen 1x
        if (!$jamMasuk || !$jamKeluar) {
            return 4; // Tidak Hadir (Merah Bata)
        }

        // Tidak Absen & Tanpa Ket.
        if (!$jamMasuk && !$jamKeluar && $attendaceStatus === 'A') {
            return 5; // Tidak Hadir (Merah)
        }

        // Default Absen 2x & < 6 Jam
        return 3; // Tidak Hadir (Kuning)
    }

    public function isCuti($pegawaiId, $tanggal)
    {
        // query cek data cuti pegawai
        return false;
    }

    public function isIzin($pegawaiId, $tanggal)
    {
        // query cek data izin pegawai
        return false;
    }

    public function isSakit($pegawaiId, $tanggal)
    {
        // query cek data sakit pegawai
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
