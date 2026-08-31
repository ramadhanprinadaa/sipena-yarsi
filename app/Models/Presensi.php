<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    protected $fillable = [
        // 'pegawai_nip',
        'pegawai_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status_kehadiran_id',
        'last_import_presensi_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jam_masuk' => 'datetime:H:i:s',
            'jam_keluar' => 'datetime:H:i:s',
        ];
    }

    // public function pegawai()
    // {
    //     return $this->belongsTo(Pegawai::class, 'pegawai_nip', 'nip');
    // }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
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
        return $this->hasMany(PresensiLog::class, 'presensi_id');
    }

    public function latestLog()
    {
        return $this->hasOne(PresensiLog::class, 'presensi_id')->latestOfMany();
    }

    public function scopeByPegawaiDanTanggal($query, $pegawaiId, $tanggal)
    {
        return $query
            ->where('pegawai_id', $pegawaiId)
            ->where('tanggal', $tanggal);
    }

    public function getTotalJamKerjaAttribute(): ?string
    {
        if (!$this->jam_masuk || !$this->jam_keluar) {
            return null;
        }

        $masuk = Carbon::parse($this->jam_masuk);
        $keluar = Carbon::parse($this->jam_keluar);

        $diff = $masuk->diff($keluar);

        $jam = $diff->h;
        $menit = $diff->i;

        return "{$jam} Jam {$menit} Menit";
    }
}