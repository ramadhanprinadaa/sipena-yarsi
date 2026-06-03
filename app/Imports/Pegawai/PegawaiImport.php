<?php

namespace App\Imports\Pegawai;

use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\JenisPegawai;
use App\Models\StatusPegawai;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use PhpOffice\PhpSpreadsheet\Shared\Date;

class PegawaiImport implements ToCollection, WithHeadingRow
{
    public int $totalRows = 0;
    public int $successRows = 0;
    public int $failedRows = 0;
    public int $duplicateRows = 0;

    public array $failures = [];
    public array $duplicates = [];
    public array $successData = [];

    /**
     * Posisi Heading (Agar mengabaikan 7 baris keterangan di atasnya)
     */
    public function headingRow(): int
    {
        return 10;
    }

    private function parseDate($value, $format = 'Y-m-d')
    {
        if (empty($value)) return null;
        try {
            if (is_numeric($value)) {
                return Carbon::instance(Date::excelToDateTimeObject($value))->format($format);
            }
            return Carbon::parse($value)->format($format);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function rules()
    {
        return [
            'nama'                  => 'required|string|max:255',
            'nik_pegawai'           => 'required|string|max:50',
            'nik_ktp'               => 'required|string|max:50',
            'unit_kerja'            => 'required|exists:unit_kerja,name',
            'jenis_pegawai'         => 'required|exists:jenis_pegawai,jenis',
            'status_pegawai'        => 'required|exists:status_pegawai,status',
            'jenis_kelamin'         => 'nullable|in:L,P',
            'tanggal_lahir'         => 'required',
            'tanggal_bergabung'     => 'required',
            'email_yarsi'           => 'nullable|email',
            'alamat_ktp'            => 'nullable|string|max:255',
            'alamat_domisili'       => 'nullable|string|max:255',
        ];
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) return;

        // Proteksi Multi-Sheet: Abaikan sheet "SETTINGS" atau yang tidak punya format biodata
        $firstRow = $rows->first()->toArray();
        if (!array_key_exists('nik_pegawai', $firstRow) && !array_key_exists('nama', $firstRow)) {
            return;
        }

        $this->totalRows += $rows->count();

        // 1. PRE-FETCHING DATA UNTUK MENGHEMAT MEMORI & CPU
        // Ambil relasi foreign key
        $unitKerjaMap = UnitKerja::pluck('id', 'name')->toArray();
        $jenisPegawaiMap = JenisPegawai::pluck('id', 'jenis')->toArray();
        $statusPegawaiMap = StatusPegawai::pluck('id', 'status')->toArray();

        // Ambil data NIP, KTP, dan NPWP yang sudah ada di DB.
        // Fungsi array_flip membuat pencarian (lookup) menjadi O(1) atau super instan.
        $existingNips  = array_flip(Pegawai::pluck('nip')->filter()->toArray());
        $existingKtps  = array_flip(Pegawai::pluck('ktp')->filter()->toArray());
        $existingNpwps = array_flip(Pegawai::whereNotNull('npwp')->pluck('npwp')->filter()->toArray());

        $newPegawais = []; // Array penampung untuk Batch Insert

        foreach ($rows as $index => $row) {

            $rowNumber = $index + 11;
            $data = $row->toArray();

            // Casting & Bersihkan spasi berlebih
            $nip  = isset($data['nik_pegawai']) ? trim((string) $data['nik_pegawai']) : null;
            $ktp  = isset($data['nik_ktp'])     ? trim((string) $data['nik_ktp'])     : null;
            $npwp = isset($data['npwp'])        ? trim((string) $data['npwp'])        : null;

            $data['nik_pegawai'] = $nip;
            $data['nik_ktp']     = $ktp;
            $data['npwp']        = $npwp;
            $data['no_telpon']   = isset($data['no_telpon']) ? (string) $data['no_telpon'] : null;

            // 2. Validasi Format Dasar
            $validator = Validator::make($data, $this->rules());
            if ($validator->fails()) {
                $this->failedRows++;
                $this->failures[] = [
                    'row'     => $rowNumber,
                    'type'    => 'validation',
                    'message' => implode(', ', $validator->errors()->all()),
                    'errors'  => $validator->errors()->all(),
                    'data'    => $data
                ];
                continue;
            }

            // 3. Validasi Duplikat (Memori Instan)
            $isDuplicate = false;
            $duplicateReasons = [];

            if (isset($existingNips[$nip])) {
                $isDuplicate = true;
                $duplicateReasons[] = 'NIP sudah terdaftar';
            }
            if (isset($existingKtps[$ktp])) {
                $isDuplicate = true;
                $duplicateReasons[] = 'KTP sudah terdaftar';
            }
            if (!empty($npwp) && isset($existingNpwps[$npwp])) {
                $isDuplicate = true;
                $duplicateReasons[] = 'NPWP sudah terdaftar';
            }

            if ($isDuplicate) {
                $this->duplicateRows++;
                $this->duplicates[] = [
                    'row'     => $rowNumber,
                    'type'    => 'duplicate',
                    'message' => implode(' | ', $duplicateReasons),
                    'errors'  => $duplicateReasons,
                    'data'    => $data
                ];
                continue;
            }

            // Catat data ini di array lokal agar jika di file Excel yang sama ada duplikat (misal baris 9 & 10 NIP-nya sama),
            // baris 10 akan langsung terdeteksi sebagai duplikat.
            if ($nip) $existingNips[$nip] = true;
            if ($ktp) $existingKtps[$ktp] = true;
            if ($npwp) $existingNpwps[$npwp] = true;

            // 4. Siapkan Array Untuk Disimpan (TIDAK langsung Insert agar hemat database)
            try {
                $newPegawais[] = [
                    'nama'              => $data['nama'],
                    'nip'               => $nip,
                    'ktp'               => $ktp,
                    'npwp'              => $npwp,
                    'jenis_kelamin'     => $data['jenis_kelamin'] ?? null,
                    'tempat_lahir'      => $data['tempat_lahir'] ?? null,
                    'tanggal_lahir'     => $this->parseDate($data['tanggal_lahir']),
                    'tanggal_bergabung' => $this->parseDate($data['tanggal_bergabung']),
                    'no_telpon'         => $data['no_telpon'],
                    'email_yarsi'       => $data['email_yarsi'] ?? null,
                    'alamat_ktp'        => $data['alamat_ktp'] ?? null,
                    'alamat_domisili'   => $data['alamat_domisili'] ?? null,
                    'unit_kerja_id'     => $unitKerjaMap[$data['unit_kerja']] ?? null,
                    'jenis_pegawai_id'  => $jenisPegawaiMap[$data['jenis_pegawai']] ?? null,
                    'status_pegawai_id' => $statusPegawaiMap[$data['status_pegawai']] ?? null,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];

                $this->successRows++;
                $this->successData[] = [
                    'row'        => $rowNumber,
                    'nama'       => $data['nama'],
                    'nip'        => $nip,
                    'unit_kerja' => $data['unit_kerja'],
                ];
            } catch (\Throwable $e) {
                $this->failedRows++;
                $this->failures[] = [
                    'row'     => $rowNumber,
                    'type'    => 'system',
                    'message' => 'Gagal format data: ' . $e->getMessage(),
                    'errors'  => ['Gagal format data: ' . $e->getMessage()],
                    'data'    => $data,
                ];
            }
        }

        // 5. BATCH INSERT KE DATABASE (Jika ada data baru yang valid)
        if (!empty($newPegawais)) {
            foreach (array_chunk($newPegawais, 500) as $chunk) {
                Pegawai::insert($chunk);
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