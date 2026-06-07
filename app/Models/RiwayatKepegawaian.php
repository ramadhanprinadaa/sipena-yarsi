<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatKepegawaian extends Model
{
    protected $table = 'riwayat_kepegawaian';

    protected $fillable = [
        'id',
        'pegawai_id',
        'unit_kerja_id',
        'status_pegawai_id',
        'tanggal_bergabung',
        'nomor_sk',
        'tanggal_sk',
        'isi_sk',
        'tanggal_berlaku',
        'tanggal_berakhir',
        'tanggal_pensiun',
        'status_keaktifan',
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
        'tanggal_sk' => 'date',
        'tanggal_berlaku' => 'date',
        'tanggal_berakhir' => 'date',
        'tanggal_pensiun' => '',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}