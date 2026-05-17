<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PegawaiController extends Controller
{
    public function pegawai()
    {
        $pegawai = Auth::user()->pegawai()->with(['jenis_pegawai', 'unit_kerja', 'status_pegawai'])->first();

        return view('dashboard.pegawai', compact('pegawai'));
    }
}