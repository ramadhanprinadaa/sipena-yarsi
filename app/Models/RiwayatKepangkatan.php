<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatKepangkatan extends Model
{
    protected $table = 'riwayat_kepangkatan';

    protected $fillable = [
        'pegawai_id',
        'golongan_internal_yarsi',
        'golongan_lldikti',
        'fungsional_internal_yarsi',
        'fungsional_lldikti',
        'no_sk_golongan_internal_yarsi',
        'no_sk_golongan_lldikti',
        'no_sk_fungsional_internal_yarsi',
        'no_sk_fungsional_lldikti',
        'tanggal_berlaku_gol_internal_yarsi',
        'tanggal_berlaku_gol_lldikti',
        'tanggal_berlaku_fung_internal_yarsi',
        'tanggal_berlaku_fung_lldikti',
    ];

    protected $casts = [
        'tanggal_berlaku_gol_internal_yarsi',
        'tanggal_berlaku_gol_lldikti',
        'tanggal_berlaku_fung_internal_yarsi',
        'tanggal_berlaku_fung_lldikti',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}