<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersetujuanLaporan extends Model
{
    protected $table = 'laporan_approval';

    protected $fillable = [
        'laporan_hasil_lembur_id',
        'approved_by',
        'role_approval',
        'status',
        'catatan',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function laporanHasilLembur()
    {
        return $this->belongsTo(LaporanHasilLembur::class, 'laporan_hasil_lembur_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
