<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    protected $fillable = [
        'pegawai_nip',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status_kehadiran_id',
        'last_import_presensi_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime:H:i:s',
        'jam_keluar' => 'datetime:H:i:s',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_nip', 'nip');
    }

    public function statusKehadiran()
    {
        return $this->belongsTo(StatusKehadiran::class, 'status_kehadiran_id');
    }

    public function lastImport()
    {
        return $this->belongsTo(ImportPresensi::class, 'last_import_presensi_id');
    }

    public function presensiLogs()
    {
        return $this->hasMany(PresensiLog::class);
    }

    public function scopeByPegawaiDanTanggal($query, $pegawaiNip, $tanggal)
    {
        return $query
            ->where('pegawai_nip', $pegawaiNip)
            ->where('tanggal', $tanggal);
    }
}