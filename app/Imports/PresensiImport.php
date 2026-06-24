<?php

namespace App\Imports;

use App\Models\HariLibur;
use App\Models\ImportPresensi;
use App\Models\Lembur;
use App\Models\Pegawai;
use App\Models\Presensi;
use App\Models\PresensiLog;
use Carbon\Carbon;
use Illuminate\Support\Arr;
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
        $this->hariLiburList = HariLibur::toBase()->pluck('tanggal')->toArray();
    }

    public function model(array $row)
    {
        $this->totalRows++;
        $rowNumber = $this->totalRows + 8;

        try {
            $pegawaiNip         = trim($row['id']);
            $tanggal            = Carbon::parse($row['date'])->format('Y-m-d');
            $attendanceStatus   = trim($row['attendance_status']);
            $jamMasuk           = $this->parseExcelTime($row['actual_check_in_time'] ?? '-', $rowNumber, 'Actual Check In Time');
            $jamKeluar          = $this->parseExcelTime($row['actual_check_out_time'] ?? '-', $rowNumber, 'Actual Check Out Time');

            if (!$this->periodeMulai || $tanggal < $this->periodeMulai) {
                $this->periodeMulai = $tanggal;
            }

            if (!$this->periodeSelesai || $tanggal > $this->periodeSelesai) {
                $this->periodeSelesai = $tanggal;
            }

            if ($jamMasuk === false || $jamKeluar === false) {
                $this->failed++;
                return null;
            }

            if (!isset($this->pegawaiList[$pegawaiNip])) {
                $this->failed++;
                $this->recordError('Pegawai tidak ditemukan', $rowNumber);
                return null;
            }

            $isHariLibur = $this->isHariLibur($tanggal);

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

            $userId = $this->import->imported_by;

            $presensi = Presensi::firstOrNew([
                'pegawai_nip' => $pegawaiNip,
                'tanggal' => $tanggal,
            ]);

            if (!$presensi->exists) {
                $presensi->created_by = $userId;
            }

            // Assign / Update data
            $presensi->jam_masuk = $jamMasuk;
            $presensi->jam_keluar = $jamKeluar;
            $presensi->status_kehadiran_id = $statusKehadiranId;
            $presensi->last_import_presensi_id = $this->import->id;
            $presensi->updated_by = $userId; // Selalu diisi oleh user yang melakukan import/update terakhir

            // Simpan ke database
            $presensi->save();

            // 2. Deteksi perubahan untuk Presensi Log
            $changes = [];
            if ($presensi->wasRecentlyCreated) {
                $this->created++;
            } else {
                $rawChanges = $presensi->getChanges(); // Hanya mencatat kolom yang nilainya di-update
                $changes = Arr::except($rawChanges, [
                    'updated_at',
                    'updated_by',
                    'last_import_presensi_id'
                ]);
                if (!empty($changes)) {
                    $this->updated++;
                }
            }

            // 3. Insert ke Presensi Log jika ada data baru atau ada data yang berubah
            if (!empty($changes)) {
                PresensiLog::create([
                    'presensi_id' => $presensi->id,
                    'import_presensi_id' => $this->import->id,
                    'changes' => json_encode($changes),
                    'edited_by' => $userId, // Mencatat user yang melakukan import
                ]);
            }
            return null;
        } catch (Throwable $e) {
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
                    'periode_mulai'   => $this->periodeMulai,
                    'periode_selesai' => $this->periodeSelesai,
                    'total_rows'      => $this->totalRows,
                    'total_created'   => $this->created,
                    'total_failed'    => $this->failed,
                    'total_updated'   => $this->updated,
                    'total_skipped'   => $this->skipped,
                    'error_summary'   => $this->importErrors
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

    public function isLemburHariBiasa(string $pegawaiNip, string $tanggal) {}

    public function isLemburHariLibur(string $pegawaiNip, string $tanggal) {}

    public function isCuti($pegawaiNip, $tanggal, $jamMasuk, $jamKeluar, $attendanceStatus)
    {
        // cek sementara, jika pada file import terdapat keteranagan cuti, maka ditandai cuti
        if ($pegawaiNip && $tanggal && !$jamMasuk && !$jamKeluar && $attendanceStatus === 'CUTI') {
            return true;
        }
        return false;
    }

    public function isIzin($pegawaiNip, $tanggal)
    {
        return false;
    }

    public function isSakit($pegawaiNip, $tanggal)
    {
        return false;
    }

    public function isHariLibur($tanggal): bool
    {
        // Cek Sabtu & Minggu
        if (Carbon::parse($tanggal)->isWeekend()) {
            return true;
        }

        // Cek Hari Libur Nasional / Custom
        return in_array($tanggal, $this->hariLiburList);
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

    protected function parseExcelTime($timeValue, int $rowNumber, string $kolomName)
    {
        // Jika datanya memang kosong atau strip, anggap null (bukan error)
        if (empty(trim($timeValue)) || trim($timeValue) === '-') {
            return null;
        }

        try {
            // Skenario 1: Excel kadang membaca waktu sebagai angka desimal (Fraction of day)
            // Contoh: 0.3194444 = 07:40:00
            if (is_numeric($timeValue)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($timeValue)->format('H:i:s');
            }

            // Skenario 2: Bentuknya String tapi pakai titik (07.41 atau 07.41.00)
            // Kita normalkan titik (.) menjadi titik dua (:)
            $normalizedTime = str_replace('.', ':', trim($timeValue));

            // Skenario 3: Parsing string yang sudah dinormalkan menggunakan Carbon
            return Carbon::parse($normalizedTime)->format('H:i:s');
        } catch (\Throwable $e) {
            // Jika masuk ke catch, artinya format sudah sangat hancur dan tidak bisa ditebak
            $this->recordError("Format waktu tidak valid pada kolom {$kolomName} (Data: {$timeValue})", $rowNumber);
            return false;
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
