<?php

namespace App\Imports\Pegawai;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;

class PegawaiImportData implements WithMultipleSheets, SkipsUnknownSheets
{
    public array $sheetImports = [];

    public function sheets(): array
    {
        $this->sheetImports['BIODATA'] = new PegawaiImport();
        $this->sheetImports['PENDIDIKAN'] = new PendidikanImport();
        // $this->sheetImports['KELUARGA'] = new KeluargaImport();

        return $this->sheetImports;
    }

    public function onUnknownSheet($sheetName)
    {
        info("Sheet {$sheetName} was skipped");
    }

    // Method untuk mengambil ringkasan hasil impor dari setiap sheet
    public function getSummaries(): array
    {
        $summaries = [];
        foreach ($this->sheetImports as $sheetName => $importInstance) {
            if (method_exists($importInstance, 'getSummary')) {
                $summaries[$sheetName] = $importInstance->getSummary();
            }
        }
        return $summaries;
    }
}