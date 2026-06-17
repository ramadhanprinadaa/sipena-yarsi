<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanHasilLembur extends Model
{
    protected $table = 'laporan_hasil_lembur';

    protected $fillable = [
        'lembur_id',
        'jam_mulai',
        'jam_selesai',
        'hasil_pekerjaan',
        'file_laporan'
    ];

    public function lembur()
    {
        return $this->belongsTo(Lembur::class);
    }

    public function persetujuan()
    {
        return $this->hasMany(PersetujuanLaporan::class, 'laporan_hasil_lembur_id');
    }
}