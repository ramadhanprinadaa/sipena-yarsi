<?php

namespace App\Imports;

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
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        // Get Relasi
        $unitKerjaMap = UnitKerja::pluck('id', 'name');
        $jenisPegawaiMap = JenisPegawai::pluck('id', 'jenis');
        $statusPegawaiMap = StatusPegawai::pluck('id', 'status');

        $this->totalRows = $rows->count();

        foreach ($rows as $index => $row) {

            $rowNumber = $index + 2;

            $data = $row->toArray();

            // Cast tipe data to string
            $data['nik_pegawai'] = isset($data['nik_pegawai']) ? (string) $data['nik_pegawai'] : null;
            $data['nik_ktp'] = isset($data['nik_ktp']) ? (string) $data['nik_ktp'] : null;
            $data['npwp'] = isset($data['npwp']) ? (string) $data['npwp'] : null;
            $data['no_telpon'] = isset($data['no_telpon']) ? (string) $data['no_telpon'] : null;

            // Cek Validasi
            $validator = Validator::make($data, $this->rules());
            if ($validator->fails()) {
                $this->failedRows++;
                $this->failures[] = [
                    'row'       => $rowNumber,
                    'type'      => 'validation',
                    'errors'    => $validator->errors()->all(),
                    'data'      => $data
                ];
                continue;
            }

            // Cek Duplikat
            $isDuplicateQuery = Pegawai::query()
                ->where('nip', $row['nik_pegawai'])
                ->orWhere('ktp', $row['nik_ktp']);

            if (!empty($row['npwp'])) {
                $isDuplicateQuery->orWhere('npwp', $row['npwp']);
            }

            $isDuplicate = $isDuplicateQuery->exists();
            if ($isDuplicate) {
                $this->duplicateRows++;
                $this->failedRows++;

                $this->duplicates[] = [
                    'row'       => $rowNumber,
                    'type'      => 'duplicate',
                    'message'   => 'Data pegawai sudah ada',
                    'data'      => $data,
                ];
                continue;
            }

            // Get Relasi ID
            $unitKerjaId = $unitKerjaMap[$row['unit_kerja']] ?? null;
            $jenisPegawaiId = $jenisPegawaiMap[$row['jenis_pegawai']] ?? null;
            $statusPegawaiId = $statusPegawaiMap[$row['status_pegawai']] ?? null;

            // Validasi Relasi
            if (!$unitKerjaId || !$jenisPegawaiId || !$statusPegawaiId) {
                $this->failedRows++;
                $this->failures[] = [
                    'row' => $rowNumber,
                    'type' => 'relation',
                    'errors' => [
                        'Unit kerja / jenis pegawai / status pegawai tidak ditemukan'
                    ],
                    'data' => $data,
                ];
                continue;
            }

            // Insert to database
            try {

                Pegawai::create([
                    'nama'                  => $row['nama'],
                    'nip'                   => $row['nik_pegawai'],
                    'ktp'                   => $row['nik_ktp'],
                    'npwp'                  => $row['npwp'],
                    'gelar_depan'           => $row['gelar_depan'],
                    'gelar_belakang'        => $row['gelar_belakang'],
                    'unit_kerja_id'         => $unitKerjaId,
                    'jenis_pegawai_id'      => $jenisPegawaiId,
                    'status_pegawai_id'     => $statusPegawaiId,
                    'tempat_lahir'          => $row['tempat_lahir'],
                    'tanggal_lahir'         => $this->transformDate($row['tanggal_lahir']),
                    'tanggal_bergabung'     => $this->transformDate($row['tanggal_bergabung']),
                    'tanggal_habis_kontrak' => $this->transformDate($row['tanggal_habis_kontrak']),
                    'tanggal_pensiun'       => $this->transformDate($row['tanggal_pensiun']),
                    'jenis_kelamin'         => $row['jenis_kelamin'],
                    'alamat_ktp'            => $row['alamat_ktp'],
                    'alamat_domisili'       => $row['alamat_domisili'],
                    'no_telpon'             => $row['no_telpon'],
                    'email_yarsi'           => $row['email_yarsi'],
                    'status'                => $row['status'] ?? 'active',
                ]);

                $this->successRows++;
                $this->successData[] = [
                    'row' => $rowNumber,
                    'nama' => $data['nama'],
                    'nip' => $data['nik_pegawai'],
                    'unit_kerja' => $data['unit_kerja'],
                ];
            } catch (\Throwable $e) {

                $this->failedRows++;
                $this->failures[] = [
                    'row' => $rowNumber,
                    'type' => 'system',
                    'errors' => [$e->getMessage()],
                    'data' => $data,
                ];
            }
        }
    }

    private function transformDate($value, $format = 'Y-m-d')
    {
        if (empty($value)) {
            return null;
        }
        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format($format);
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