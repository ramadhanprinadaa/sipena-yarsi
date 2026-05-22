<?php

namespace App\Imports;

use App\Models\HariLibur;
use App\Models\ImportPresensi;
use App\Models\Pegawai;
use App\Models\Presensi;
use App\Models\PresensiLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

    const HADIR_NORMAL = 1;
    const HADIR_KURANG_JAM = 2;
    const TIDAK_HADIR_KURANG_JAM = 3;
    const ABSEN_1X = 4;
    const TIDAK_HADIR_TANPA_KETERANGAN = 5;
    const IZIN = 6;
    const SAKIT = 7;
    const CUTI = 8;
    const LEMBUR = 9;

    protected ImportPresensi $import;

    protected int $totalRows = 0;
    protected int $created = 0;
    protected int $updated = 0;
    protected int $skipped = 0;
    protected int $failed = 0;

    protected ?string $periodeMulai = null;
    protected ?string $periodeSelesai = null;

    protected array $pegawaiList = [];
    protected array $hariLiburList = [];

    protected array $importErrors = [];

    public function __construct(ImportPresensi $import)
    {
        $this->import = $import;
        $this->pegawaiList = Pegawai::pluck('id', 'nip')->toArray();
        $this->hariLiburList = HariLibur::pluck('tanggal')->map(fn($tanggal) => Carbon::parse($tanggal)->format('Y-m-d'))->toArray();
    }

    public function model(array $row)
    {
        $this->totalRows++;
        $rowNumber = $this->totalRows + 8;

        try {
            $pegawaiNip         = trim($row['id']);
            $tanggal            = Carbon::parse($row['date'])->format('Y-m-d');
            $attendanceStatus   = trim($row['attendance_status']);
            $jamMasuk           = $row['actual_check_in_time'] !== '-'
                ? Carbon::parse($row['actual_check_in_time'])->format('H:i:s')
                : null;
            $jamKeluar          = $row['actual_check_out_time'] !== '-'
                ? Carbon::parse($row['actual_check_out_time'])->format('H:i:s')
                : null;

            // Get tanggal periode mulai absensi
            if (!$this->periodeMulai || $tanggal < $this->periodeMulai) {
                $this->periodeMulai = $tanggal;
            }

            // Get tanggal periode selesai absensi
            if (!$this->periodeSelesai || $tanggal > $this->periodeSelesai) {
                $this->periodeSelesai = $tanggal;
            }

            // Handle Error / Skipped Jika Data NIP Pegawai Tidak Ditemukan
            if (!isset($this->pegawaiList[$pegawaiNip])) {
                $this->failed++;
                $this->recordError('Pegawai tidak ditemukan', $rowNumber);
                DB::rollBack();
                return null;
            }

            $isHariLibur = $this->isHariLibur($tanggal);

            // Handle Skipped Jika Tanggal adalah Hari Libur
            if ($isHariLibur && !$jamMasuk && !$jamKeluar) {
                $this->skipped++;
                return null;
            }

            $statusKehadiranId = $this->getStatusKehadiran(
                $pegawaiNip,
                $tanggal,
                $attendanceStatus,
                $jamMasuk,
                $jamKeluar,
                $isHariLibur
            );

            $newData = [
                'jam_masuk' => $jamMasuk,
                'jam_keluar' => $jamKeluar,
                'status_kehadiran_id' => $statusKehadiranId,
                'last_import_presensi_id' => $this->import->id,
            ];

            // Simpan / update
            $presensi = Presensi::updateOrCreate(
                [
                    'pegawai_nip' => $pegawaiNip,
                    'tanggal' => $tanggal,
                ],
                $newData
            );

            if ($presensi->wasRecentlyCreated) {
                $this->created++;
            } else {
                $this->updated++;
            }

            DB::commit();
            return $presensi;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->failed++;
            $this->recordError($e->getMessage(), $rowNumber ?? 0);
            return null;
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function () {

                $this->import->update([
                    'total_rows'      => $this->totalRows,
                    'total_created'   => $this->created,
                    'total_failed'    => $this->failed,
                    'total_updated'   => $this->updated,
                    'total_skipped'   => $this->skipped,
                    'summary'         => json_encode(
                        array_values($this->importErrors)
                    ),
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
        $totalJamKerja = 0;
        if ($jamMasuk && $jamKeluar) {
            $totalJamKerja = Carbon::parse($jamMasuk)
                ->diffInHours(Carbon::parse($jamKeluar));
        }

        // Cek Lembur di Hari Libur
        if ($isHariLibur && ($jamMasuk || $jamKeluar)) {
            return self::LEMBUR;
        }

        // Cek Izin
        if ($this->isIzin($pegawaiNip, $tanggal)) {
            return self::IZIN;
        }

        // Cek Sakit
        if ($this->isSakit($pegawaiNip, $tanggal)) {
            return self::SAKIT;
        }

        // Cek Cuti
        if ($this->isCuti($pegawaiNip, $tanggal, $jamMasuk, $jamKeluar, $attendanceStatus)) {
            return self::CUTI;
        }

        // Absen 2x & ≥ 8 Jam (Hadir Normal)
        if ($jamMasuk && $jamKeluar && $totalJamKerja >= 8) {
            return self::HADIR_NORMAL;
        }

        // Absen 2x & 6 - 7,59 Jam (Hadir Kurang Jam)
        if ($jamMasuk && $jamKeluar && $totalJamKerja >= 6 && $totalJamKerja < 8) {
            return self::HADIR_KURANG_JAM;
        }

        // Tidak Absen & Tanpa Ket. (Tidak Hadir dan TIdak Absen)
        if (!$jamMasuk && !$jamKeluar && $attendanceStatus === 'A') {
            return self::TIDAK_HADIR_TANPA_KETERANGAN;
        }

        // Absen 1x
        if (!$jamMasuk || !$jamKeluar) {
            return self::ABSEN_1X;
        }

        // Default Absen 2x & < 6 Jam
        return self::HADIR_KURANG_JAM;
    }

    public function isCuti($pegawaiNip, $tanggal, $jamMasuk, $jamKeluar, $attendanceStatus)
    {
        // cek sementara
        if ($pegawaiNip && $tanggal && !$jamMasuk && !$jamKeluar && $attendanceStatus === 'CUTI') {
            return true;
        }
        // query cek data cuti pegawai
        return false;
    }

    public function isIzin($pegawaiNip, $tanggal)
    {
        // query cek data izin pegawai
        return false;
    }

    public function isSakit($pegawaiNip, $tanggal)
    {
        // query cek data sakit pegawai
        return false;
    }

    public function isHariLibur($tanggal): bool
    {
        // Cek Sabtu & Minggu
        if (Carbon::parse($tanggal)->isWeekend()) {
            return true;
        }

        // Cek Hari Libur Nasional / Custom
        return in_array(
            Carbon::parse($tanggal)->format('Y-m-d'),
            $this->hariLiburList
        );
    }

    protected function recordError(string $message, int $rowNumber): void
    {
        if (!isset($this->importErrors[$message])) {

            $this->importErrors[$message] = [
                'message' => $message,
                'count' => 0,
                'rows' => [],
            ];
        }

        $this->importErrors[$message]['count']++;

        if (!in_array($rowNumber, $this->importErrors[$message]['rows'])) {
            $this->importErrors[$message]['rows'][] = $rowNumber;
        }
    }

    public function rules(): array
    {
        return [
            '*.id' => ['required'],
            '*.date' => ['required'],
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
        return 100;
    }
}