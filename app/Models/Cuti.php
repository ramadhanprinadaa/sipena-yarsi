<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    protected $table = 'cuti';

    protected $fillable = [
        'pegawai_id',
        'jenis_cuti_id',
        'tanggal_pengajuan',
        'metode_potongan',
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_mulai',
        'jam_selesai',
        'jumlah_hari_cuti',
        'jumlah_jam',
        'saldo_cuti_sebelum',
        'saldo_cuti_sesudah',
        'dokumen_pendukung',
        'keterangan',
        'status'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jenisCuti()
    {
        return $this->belongsTo(JenisCuti::class);
    }

    public function approvals()
    {
        return $this->hasMany(CutiApproval::class);
    }
}