<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $table = 'presensi';

    protected $fillable = [
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'total_jam',
        'status',
        'status_lembur',
        'jam_lembur',
    ];
}