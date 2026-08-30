<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;

class DetailPegawaiController extends Controller
{
    public function show(Pegawai $pegawai)
    {
        return view('manajemen.detail-pegawai', compact('pegawai'));
    }
}