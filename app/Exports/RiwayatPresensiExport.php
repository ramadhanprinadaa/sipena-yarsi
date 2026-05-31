<?php

namespace App\Exports;

use App\Models\Presensi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class RiwayatPresensiExport implements FromQuery
{
    use Exportable;

    public function query()
    {
        return Presensi::query();
    }
}