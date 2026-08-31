<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PegawaiController extends Controller
{
    public function pegawai()
    {
        $user = Auth::user();

        $user->load([
            'pegawai.jenis_pegawai',
            'pegawai.unit_kerja',
            'pegawai.status_pegawai'
        ]);

        $pegawai = $user->pegawai;

        return view('dashboard.pegawai', compact('pegawai'));
    }
}