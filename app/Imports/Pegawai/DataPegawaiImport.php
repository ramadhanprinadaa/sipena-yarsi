<?php

namespace App\Imports\Pegawai;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;

class DataPegawaiImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public array $sheetImports = [];

    public function __construct()
    {
        $this->sheetImports = [
            'BIODATA'   => new BiodataImport(),
            'REKENING'  => new RekeningImport(),
            'KELUARGA'  => new KeluargaImport(),
            'PENDIDIKAN' => new PendidikanImport(),
        ];
    }

    public function sheets(): array
    {
        return $this->sheetImports;
    }

    public function onUnknownSheet($sheetName)
    {
        info("Sheet {$sheetName} was skipped");
    }
}
