<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    protected $fillable = [
        'pegawai_id',
        'import_presensi_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status_kehadiran_id',
        'keterangan',
        'updated_by'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function status_kehadiran()
    {
        return $this->belongsTo(StatusKehadiran::class, 'status_kehadiran_id');
    }

    public function import_presensi()
    {
        return $this->belongsTo(ImportPresensi::class, 'import_presensi_id');
    }

    public function presensi_logs()
    {
        return $this->hasMany(PresensiLog::class, 'presensi_id');
    }
}