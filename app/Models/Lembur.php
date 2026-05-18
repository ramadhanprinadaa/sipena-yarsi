<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    protected $table = 'lembur';

    protected $fillable = [
        'pegawai_id',
        'surat_perintah_lembur_id',
        'tanggal_lembur',
        'jenis_hari',
        'jam_mulai',
        'jam_selesai',
        'alasan_lembur',
        'status'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function persetujuan()
    {
        return $this->hasMany(PersetujuanLembur::class);
    }

    public function laporan()
    {
        return $this->hasOne(LaporanHasilLembur::class);
    }

    public function surat()
    {
        return $this->belongsTo(SuratPerintahLembur::class);
    }

    public function suratPerintahLembur()
    {
        return $this->belongsTo(SuratPerintahLembur::class, 'surat_perintah_lembur_id');
    }

    public function laporanHasilLembur()
    {
        return $this->hasOne(LaporanHasilLembur::class);
    }
}