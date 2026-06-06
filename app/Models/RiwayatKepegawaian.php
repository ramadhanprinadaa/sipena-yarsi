<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatKepegawaian extends Model
{
    protected $table = 'riwayat_kepegawaian';

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}