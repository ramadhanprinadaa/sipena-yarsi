<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPelatihan extends Model
{
    protected $table = 'riwayat_pelatihan';

    protected $fillable = [
        'pegawai_id',
        'nama_kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'pembiayaan',
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
        'tanggal_habis_kontrak' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}