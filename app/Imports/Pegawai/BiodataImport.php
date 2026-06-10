<?php

namespace App\Imports\Pegawai;

use App\Models\JenisPegawai;
use App\Models\Pegawai;
use App\Models\StatusPegawai;
use App\Models\UnitKerja;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithSkipDuplicates;
use Maatwebsite\Excel\Concerns\WithValidation;

class BiodataImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, WithSkipDuplicates, SkipsOnFailure, SkipsEmptyRows
{

    use Importable, SkipsFailures;

    private array $unitKerjaMap = [];
    private array $statusPegawaiMap = [];
    private array $jenisPegawaiMap = [];

    public int $totalRows = 0;

    public function __construct()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->unitKerjaMap = UnitKerja::pluck('id', 'name')->toArray();
        $this->jenisPegawaiMap = JenisPegawai::pluck('id', 'jenis')->toArray();
        $this->statusPegawaiMap = StatusPegawai::pluck('id', 'status')->toArray();
    }

    public function prepareForValidation($data, $index)
    {
        $this->totalRows++;
        return $data;
    }

    public function model(array $row)
    {
        return new Pegawai([
            'jenis_pegawai_id'      => $this->jenisPegawaiMap[$row['jenis_pegawai']] ?? null,
            'status_pegawai_id'     => $this->statusPegawaiMap[$row['status_pegawai']] ?? null,
            'unit_kerja_id'         => $this->unitKerjaMap[$row['unit_kerja']] ?? null,
            'unit_bagian'           => $row['unit_bagian'],
            'nip'                   => $row['nik_pegawai'],
            'ktp'                   => $row['nik_ktp'],
            'npwp'                  => $row['npwp'],
            'nama'                  => $row['nama'],
            'gelar_depan'           => $row['gelar_depan'],
            'gelar_belakang'        => $row['gelar_belakang'],
            'tempat_lahir'          => $row['tempat_lahir'],
            'tanggal_lahir'         => $row['tanggal_lahir'],
            'tanggal_bergabung'     => $row['tanggal_bergabung'],
            'tanggal_habis_kontrak' => $row['tanggal_habis_kontrak'],
            'tanggal_pensiun'       => $row['tanggal_pensiun'],
            'jenis_kelamin'         => $row['jenis_kelamin'],
            'alamat_ktp'            => $row['alamat_ktp'],
            'alamat_domisili'       => $row['alamat_domisili'],
            'no_telpon'             => $row['no_telpon'],
            'email_yarsi'           => $row['email_yarsi'],
            'status'                => $row['status'] ?? 'active',
        ]);
    }

    public function rules(): array
    {
        return [
            'jenis_pegawai'         => ['required', 'exists:jenis_pegawai,jenis'],
            'status_pegawai'        => ['required', 'exists:status_pegawai,status'],
            'unit_kerja'            => ['required', 'exists:unit_kerja,name'],
            'unit_bagian'           => ['nullable', 'string'],
            'nik_pegawai'           => ['required', 'string', 'min:5', 'max:20', 'unique:pegawai,nip'],
            'nik_ktp'               => ['nullable', 'string', 'min:10', 'max:20', 'unique:pegawai,ktp'],
            'npwp'                  => ['nullable', 'string', 'min:10', 'max:20', 'unique:pegawai,npwp'],
            'nama'                  => ['required', 'string'],
            'gelar_depan'           => ['nullable', 'string'],
            'gelar_belakang'        => ['nullable', 'string'],
            'tempat_lahir'          => ['nullable', 'string'],
            'tanggal_lahir'         => ['required', 'date'],
            'tanggal_bergabung'     => ['required', 'date'],
            'tanggal_habis_kontrak' => ['nullable', 'date'],
            'tanggal_pensiun'       => ['nullable', 'date'],
            'jenis_kelamin'         => ['nullable', Rule::in(['L', 'P']),],
            'alamat_ktp'            => ['nullable', 'string'],
            'alamat_domisili'       => ['nullable', 'string'],
            'no_telpon'             => ['nullable', 'string'],
            'email_yarsi'           => ['nullable', 'email', 'unique:pegawai,email_yarsi'],
            'status'                => ['nullable', 'string'],
        ];
    }

    public function customValidationAttributes(): array
    {
        return [
            'jenis_pegawai'         => 'Jenis Pegawai',
            'status_pegawai'        => 'Status Pegawai',
            'unit_kerja'            => 'Unit Kerja',
            'unit_bagian'           => 'Bagian / Unit',
            'nik_pegawai'           => 'NIP / NIK Pegawai',
            'nik_ktp'               => 'NIK KTP',
            'npwp'                  => 'NPWP',
            'nama'                  => 'Nama Lengkap',
            'gelar_depan'           => 'Gelar Depan',
            'gelar_belakang'        => 'Gelar Belakang',
            'tempat_lahir'          => 'Tempat Lahir',
            'tanggal_lahir'         => 'Tanggal Lahir',
            'tanggal_bergabung'     => 'Tanggal Bergabung',
            'tanggal_habis_kontrak' => 'Tanggal Habis Kontrak',
            'tanggal_pensiun'       => 'Tanggal Pensiun',
            'jenis_kelamin'         => 'Jenis Kelamin',
            'alamat_ktp'            => 'Alamat KTP',
            'alamat_domisili'       => 'Alamat Domisili',
            'no_telpon'             => 'Nomor Telepon',
            'email_yarsi'           => 'Email YARSI',
            'status'                => 'Status Aktif',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            // Jenis Pegawai
            'jenis_pegawai.required' => 'Jenis Pegawai kosong / tidak diisi.',
            'jenis_pegawai.exists'   => 'Jenis Pegawai tidak ditemukan pada data referensi sistem.',

            // Status Pegawai
            'status_pegawai.required' => 'Status Pegawai kosong / tidak diisi.',
            'status_pegawai.exists'   => 'Status Pegawai tidak ditemukan pada data referensi sistem.',

            // Unit Kerja
            'unit_kerja.required' => 'Unit Kerja kosong / tidak diisi.',
            'unit_kerja.exists'   => 'Unit Kerja tidak ditemukan pada data referensi sistem.',

            // Unit Bagian
            'unit_bagian.string' => 'Unit Bagian tidak berupa teks yang valid.',

            // NIP / NIK Pegawai
            'nik_pegawai.required' => 'NIP / NIK Pegawai kosong / tidak diisi.',
            'nik_pegawai.string'   => 'NIP / NIK Pegawai tidak berupa teks yang valid.',
            'nik_pegawai.min'      => 'NIP / NIK Pegawai kurang dari 5 karakter.',
            'nik_pegawai.max'      => 'NIP / NIK Pegawai lebih dari 20 karakter.',
            'nik_pegawai.unique'   => 'NIP / NIK Pegawai sudah terdaftar pada sistem.',

            // NIK KTP
            'nik_ktp.string' => 'NIK KTP tidak berupa teks yang valid.',
            'nik_ktp.min'    => 'NIK KTP kurang dari 10 karakter.',
            'nik_ktp.max'    => 'NIK KTP lebih dari 20 karakter.',
            'nik_ktp.unique' => 'NIK KTP sudah terdaftar pada sistem.',

            // NPWP
            'npwp.string' => 'NPWP tidak berupa teks yang valid.',
            'npwp.min'    => 'NPWP kurang dari 10 karakter.',
            'npwp.max'    => 'NPWP lebih dari 20 karakter.',
            'npwp.unique' => 'NPWP sudah terdaftar pada sistem.',

            // Nama
            'nama.required' => 'Nama Lengkap kosong / tidak diisi.',
            'nama.string'   => 'Nama Lengkap tidak berupa teks yang valid.',

            // Gelar
            'gelar_depan.string'    => 'Gelar Depan tidak berupa teks yang valid.',
            'gelar_belakang.string' => 'Gelar Belakang tidak berupa teks yang valid.',

            // Tempat Lahir
            'tempat_lahir.string' => 'Tempat Lahir tidak berupa teks yang valid.',

            // Tanggal
            'tanggal_lahir.required' => 'Tanggal Lahir kosong / tidak diisi.',
            'tanggal_lahir.date'     => 'Tanggal Lahir tidak menggunakan format tanggal yang valid.',

            'tanggal_bergabung.required' => 'Tanggal Bergabung kosong / tidak diisi.',
            'tanggal_bergabung.date'     => 'Tanggal Bergabung tidak menggunakan format tanggal yang valid.',

            'tanggal_habis_kontrak.date' => 'Tanggal Habis Kontrak tidak menggunakan format tanggal yang valid.',

            'tanggal_pensiun.date' => 'Tanggal Pensiun tidak menggunakan format tanggal yang valid.',

            // Jenis Kelamin
            'jenis_kelamin.in' => 'Jenis Kelamin hanya boleh diisi dengan "L" (Laki-laki) atau "P" (Perempuan).',

            // Alamat
            'alamat_ktp.string'      => 'Alamat KTP tidak berupa teks yang valid.',
            'alamat_domisili.string' => 'Alamat Domisili tidak berupa teks yang valid.',

            // Nomor Telepon
            'no_telpon.string' => 'Nomor Telepon tidak berupa teks yang valid.',

            // Email
            'email_yarsi.email'  => 'Format Email YARSI tidak valid.',
            'email_yarsi.unique' => 'Email YARSI sudah terdaftar pada sistem.',

            // Status Aktif
            'status.string' => 'Status Aktif tidak berupa teks yang valid.',


            '*.required' => ':attribute kosong / tidak diisi.',
            '*.exists'   => ':attribute tidak ditemukan pada data referensi sistem.',
            '*.unique'   => ':attribute sudah terdaftar pada sistem.',
            '*.date'     => ':attribute tidak menggunakan format tanggal yang valid.',
            '*.email'    => ':attribute tidak menggunakan format email yang valid.',
            '*.string'   => ':attribute tidak berupa teks yang valid.',
            '*.min'      => ':attribute kurang dari :min karakter.',
            '*.max'      => ':attribute lebih dari :max karakter.',
            '*.digits_between' => ':attribute harus berupa angka dengan panjang antara :min hingga :max digit.',
        ];
    }

    public function headingRow(): int
    {
        return 10;
    }

    public function batchSize(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
