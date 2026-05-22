<?php

namespace App\Imports;

use App\Models\HariLibur;
use App\Models\ImportPresensi;
use App\Models\Pegawai;
use App\Models\Presensi;
use App\Models\PresensiLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class PresensiImport2 implements
    ToCollection,
    WithHeadingRow,
    WithChunkReading,
    ShouldQueue
{
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
    protected int $importedById;

    protected array $pegawaiList = [];
    protected array $hariLiburList = [];

    public function __construct(ImportPresensi $import, int $importedById)
    {
        $this->import = $import;
        $this->importedById = $importedById;

        // Cache Pegawai di Memori
        $this->pegawaiList = Pegawai::pluck('nip')->toArray();

        // Cache Hari Libur di Memori
        $this->hariLiburList = HariLibur::pluck('tanggal')
            ->map(fn($tanggal) => Carbon::parse($tanggal)->format('Y-m-d'))
            ->toArray();
    }

    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            $nips = [];
            $minDate = null;
            $maxDate = null;

            $createdCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;
            $failedCount  = 0;

            // 1. Get Data dalam Chunk (Kumpulkan NIP dan Rentang Tanggal)
            foreach ($rows as $row) {
                // PERBAIKAN BUG 1: Pemisahan fungsi empty() yang benar
                if (empty($row['date']) || empty($row['id'])) continue;

                $tanggal = Carbon::parse($row['date'])->format('Y-m-d');
                $nips[] = trim($row['id']);

                // get tanggal periode mulai dan periode selesai dalam chunk
                if (!$minDate || $tanggal < $minDate) $minDate = $tanggal;
                if (!$maxDate || $tanggal > $maxDate) $maxDate = $tanggal;
            }

            $nips = array_unique($nips);

            // Jika chunk kosong atau tidak ada data valid, hentikan proses chunk ini
            if (empty($nips)) {
                DB::commit();
                return;
            }

            // 2. Cache presensi yang sudah ada berdasarkan rentang tanggal tertentu
            $existingPresensi = Presensi::whereIn('pegawai_nip', $nips)
                ->whereBetween('tanggal', [$minDate ?? now()->format('Y-m-d'), $maxDate ?? now()->format('Y-m-d')])
                ->get()
                ->keyBy(fn($item) => $item->pegawai_nip . '_' . $item->tanggal);

            $upsertData = [];
            $logsToProcess = [];
            $now = now();

            // 3. Proses data di memori
            foreach ($rows as $index => $row) {
                // PERBAIKAN BUG 1: Pemisahan fungsi empty() yang benar
                if (empty($row['date']) || empty($row['id'])) continue;

                $pegawaiNip       = trim($row['id']);
                $tanggal          = Carbon::parse($row['date'])->format('Y-m-d');
                $attendanceStatus = trim($row['attendance_status'] ?? '');
                $jamMasuk         = ($row['actual_check_in_time'] ?? '-') !== '-'
                    ? Carbon::parse($row['actual_check_in_time'])->format('H:i:s') : null;
                $jamKeluar        = ($row['actual_check_out_time'] ?? '-') !== '-'
                    ? Carbon::parse($row['actual_check_out_time'])->format('H:i:s') : null;

                // Validasi Jika Pegawai Tidak Ditemukan
                if (!in_array($pegawaiNip, $this->pegawaiList)) {
                    $failedCount++;
                    $this->recordError("Pegawai NIP {$pegawaiNip} tidak ditemukan.", $index + 8);
                    continue;
                }

                $isHariLibur = $this->isHariLibur($tanggal);

                // Skip jika hari libur dan tidak ada aktivitas (jam masuk/keluar)
                if ($isHariLibur && !$jamMasuk && !$jamKeluar) {
                    $skippedCount++;
                    continue;
                }

                $statusKehadiranId = $this->getStatusKehadiran(
                    $pegawaiNip,
                    $tanggal,
                    $attendanceStatus,
                    $jamMasuk,
                    $jamKeluar,
                    $isHariLibur
                );

                // Cek apakah data lama exist di memori
                $key = $pegawaiNip . '_' . $tanggal;
                $oldDataModel = $existingPresensi->get($key);
                $oldDataArray = $oldDataModel ? $oldDataModel->toArray() : [];

                $newDataArray = [
                    'pegawai_nip'             => $pegawaiNip,
                    'tanggal'                 => $tanggal,
                    'jam_masuk'               => $jamMasuk,
                    'jam_keluar'              => $jamKeluar,
                    'status_kehadiran_id'     => $statusKehadiranId,
                    'last_import_presensi_id' => $this->import->id,
                    'created_by'              => $oldDataModel ? $oldDataModel->created_by : $this->importedById,
                    'updated_by'              => $this->importedById,
                    'created_at'              => $oldDataModel ? $oldDataModel->created_at : $now,
                    'updated_at'              => $now,
                ];

                $upsertData[] = $newDataArray;

                // Tambahkan instruksi untuk Log
                $logsToProcess[] = [
                    'key'    => $key,
                    'old'    => $oldDataArray,
                    'new'    => $newDataArray,
                    'is_new' => !$oldDataModel
                ];
            }

            // 4. QUERY 1x: Bulk Upsert Data Presensi
            if (!empty($upsertData)) {
                Presensi::upsert(
                    $upsertData,
                    ['pegawai_nip', 'tanggal'], // Kondisi Unique
                    ['jam_masuk', 'jam_keluar', 'status_kehadiran_id', 'last_import_presensi_id', 'updated_by', 'updated_at']
                );
            }

            // 5. QUERY 1x: Ambil ID presensi yang baru saja di-upsert (untuk Foreign Key log)
            $updatedPresensi = Presensi::whereIn('pegawai_nip', $nips)
                ->whereBetween('tanggal', [$minDate, $maxDate])
                ->get()
                ->keyBy(fn($item) => $item->pegawai_nip . '_' . $item->tanggal);

            // 6. Siapkan dan QUERY 1x: Bulk Insert Presensi Log
            $logsData = [];
            foreach ($logsToProcess as $log) {
                $presensiId = $updatedPresensi->get($log['key'])->id ?? null;

                if ($presensiId) {
                    $logsData[] = [
                        'presensi_id'        => $presensiId,
                        'import_presensi_id' => $this->import->id,
                        'changes'            => json_encode(['old' => $log['old'], 'new' => $log['new']]),
                        'edited_by'          => $this->importedById,
                        'created_at'         => $now,
                        'updated_at'         => $now,
                    ];

                    if ($log['is_new']) {
                        $createdCount++;
                    } else {
                        $updatedCount++;
                    }
                }
            }

            if (!empty($logsData)) {
                PresensiLog::insert($logsData);
            }

            // PERBAIKAN BUG 2: Panggil fungsi update rekap di sini
            $totalProcessedRows = $createdCount + $updatedCount + $skippedCount + $failedCount;
            $this->updateImportSummary($totalProcessedRows, $createdCount, $updatedCount, $skippedCount, $failedCount, $minDate, $maxDate);

            // PERBAIKAN BUG 3: Commit transaksi database agar data tersimpan permanen
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            $this->recordError("Terjadi error fatal di sistem: " . $e->getMessage(), 0);
            $this->import->increment('total_failed', $rows->count());
            $this->import->increment('total_rows', $rows->count());
        }
    }

    protected function updateImportSummary($rows, $created, $updated, $skipped, $failed, $minDate, $maxDate)
    {
        // Gunakan increment agar aman jika ada eksekusi chunk yg berjalan bersamaan
        if ($rows > 0) $this->import->increment('total_rows', $rows);
        if ($created > 0) $this->import->increment('total_created', $created);
        if ($updated > 0) $this->import->increment('total_updated', $updated);
        if ($skipped > 0) $this->import->increment('total_skipped', $skipped);
        if ($failed > 0) $this->import->increment('total_failed', $failed);

        // Update tanggal (hanya diupdate jika lebih kecil/besar dari data sebelumnya)
        $updateDates = [];
        $currentMulai = $this->import->periode_mulai;
        $currentSelesai = $this->import->periode_selesai;

        if ($minDate && (!$currentMulai || $minDate < $currentMulai)) {
            $updateDates['periode_mulai'] = $minDate;
        }
        if ($maxDate && (!$currentSelesai || $maxDate > $currentSelesai)) {
            $updateDates['periode_selesai'] = $maxDate;
        }

        if (!empty($updateDates)) {
            ImportPresensi::where('id', $this->import->id)->update($updateDates);
        }
    }

    protected function recordError(string $message, int $rowNumber = 0): void
    {
        $import = ImportPresensi::find($this->import->id);
        $errors = json_decode($import->error_summary ?? '{}', true);

        if (!isset($errors[$message])) {
            $errors[$message] = [
                'message' => $message,
                'count' => 0,
                'rows' => []
            ];
        }

        $errors[$message]['count']++;
        if ($rowNumber > 0 && !in_array($rowNumber, $errors[$message]['rows'])) {
            $errors[$message]['rows'][] = $rowNumber;
        }

        $import->update(['error_summary' => json_encode($errors)]);
    }

    public function getStatusKehadiran($pegawaiNip, $tanggal, $attendanceStatus, $jamMasuk, $jamKeluar, $isHariLibur = false)
    {
        $totalJamKerja = 0;
        if ($jamMasuk && $jamKeluar) {
            $totalJamKerja = Carbon::parse($jamMasuk)
                ->diffInHours(Carbon::parse($jamKeluar));
        }

        // Cek Lembur di Hari Libur
        if ($isHariLibur && ($jamMasuk || $jamKeluar)) return self::LEMBUR;

        // Cek Izin
        if ($this->isIzin($pegawaiNip, $tanggal)) return self::IZIN;

        // Cek Sakit
        if ($this->isSakit($pegawaiNip, $tanggal)) return self::SAKIT;

        // Cek Cuti
        if ($this->isCuti($pegawaiNip, $tanggal, $jamMasuk, $jamKeluar, $attendanceStatus)) return self::CUTI;

        // Absen 2x & ≥ 8 Jam (Hadir Normal)
        if ($jamMasuk && $jamKeluar && $totalJamKerja >= 8) return self::HADIR_NORMAL;

        // Absen 2x & 6 - 7,59 Jam (Hadir Kurang Jam)
        if ($jamMasuk && $jamKeluar && $totalJamKerja >= 6 && $totalJamKerja < 8) return self::HADIR_KURANG_JAM;

        // Absen 2x & < 6 Jam (Tidak Hadir Kurang Jam)
        if ($jamMasuk && $jamKeluar && $totalJamKerja < 6) return self::TIDAK_HADIR_KURANG_JAM;

        // Absen 1x (Tidak Hadir)
        if (!$jamMasuk || !$jamKeluar) return self::ABSEN_1X;

        // Default Tidak Hadir
        return self::TIDAK_HADIR_TANPA_KETERANGAN;
    }

    public function isCuti($pegawaiNip, $tanggal, $jamMasuk, $jamKeluar, $attendanceStatus)
    {
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
        if (Carbon::parse($tanggal)->isWeekend()) {
            return true;
        }

        return in_array(
            Carbon::parse($tanggal)->format('Y-m-d'),
            $this->hariLiburList
        );
    }

    public function headingRow(): int
    {
        return 8;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
