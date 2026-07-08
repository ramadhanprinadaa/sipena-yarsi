<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisCuti extends Model
{
    protected $table = 'jenis_cuti';

    protected $fillable = [
        'nama',
        'minimal_masa_kerja_bulan',
        'maksimal_hari',
        'maksimal_hari_per_bulan',
        'memotong_saldo',
        'butuh_surat_dokter',
        'dihitung_per_jam',
        'sekali_seumur_kerja',
        'deskripsi'
    ];

    public function cuti()
    {
        return $this->hasMany(Cuti::class);
    }
}