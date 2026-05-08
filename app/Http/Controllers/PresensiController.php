<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\PresensiExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RiwayatPresensiExport;
use App\Exports\RekapPresensiExport;

class PresensiController extends Controller
{

    public function exportRiwayat()
{
    return Excel::download(
        new RiwayatPresensiExport,
        'riwayat-presensi.xlsx'
    );
}

public function exportRekap()
{
    return Excel::download(
        new RekapPresensiExport,
        'rekap-presensi.xlsx'
    );
}
}