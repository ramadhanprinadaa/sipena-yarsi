<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPendidikan extends Model
{
    protected $table = 'riwayat_pendidikan';

    protected $fillable = [
        'pegawai_id',
        'jenjang_pendidikan',
        'file_iajazah',
        'file_path',
        'tahun_masuk',
        'tahun_lulus',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}