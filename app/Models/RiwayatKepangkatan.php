<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatKepangkatan extends Model
{
    protected $table = 'riwayat_kepangkatan';

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}