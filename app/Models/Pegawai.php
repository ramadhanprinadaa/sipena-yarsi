<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';

    protected $fillable = [
        'id',
        'unit_kerja_id',
        'jenis_pegawai_id',
        'status_pegawai_id',
        'nip',
        'ktp',
        'npwp',
        'nama',
        'gelar_depan',
        'gelar_belakang',
        'tempat_lahir',
        'tanggal_lahir',
        'tanggal_bergabung',
        'tanggal_habis_kontrak',
        'tanggal_pensiun',
        'jenis_kelamin',
        'alamat_ktp',
        'alamat_domisili',
        'no_telpon',
        'email_yarsi',
        'status',
    ];

    protected $attributes = [
        'status' => 'active'
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date:d/m/Y',
        'tanggal_habis_kontrak' => 'date:d/m/Y',
        'tanggal_pensiun' => 'date:d/m/Y',
        'tanggal_lahir' => 'date:d/m/Y'
    ];

    protected function nama(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => ucwords(strtolower($value)),
            set: fn(string $value) => strtolower($value),
        );
    }

    protected function namaGelar(): Attribute
    {
        return Attribute::make(
            get: fn($value) => trim("{$this->gelar_depan} {$this->nama}, {$this->gelar_belakang}")
        );
    }

    public function getAgeAttribute()
    {
        return Carbon::parse($this->tanggal_lahir)->age;
    }

    public function getMasaKerjaAttribute(): string
    {
        if (!$this->tanggal_bergabung) {
            return '-';
        }
        $diff = $this->tanggal_bergabung->diff(now());
        $parts = [];
        if ($diff->y > 0) {
            $parts[] = "{$diff->y} Tahun";
        }
        if ($diff->m > 0) {
            $parts[] = "{$diff->m} Bulan";
        }
        if (empty($parts)) {
            $parts[] = 'Kurang dari 1 Bulan';
        }
        return implode(' ', $parts);
    }

    public function getJenisKelaminAttribute($value)
    {
        return match ($value) {
            'L' => 'Laki-Laki',
            'P' => 'Perempuan',
            default => '-',
        };
    }

    public function getTanggalLahirFormattedAttribute()
    {
        return Carbon::parse($this->tanggal_lahir)->translatedFormat('d F Y');
    }

    public function getTanggalBergabungFormattedAttribute()
    {
        return Carbon::parse($this->tanggal_bergabung)->translatedFormat('d F Y');
    }

    public function getTanggalHabisKontrakFormattedAttribute()
    {
        return Carbon::parse($this->tanggal_habis_kontrak)->translatedFormat('d F Y');
    }

    public function getTanggalPensiunFormattedAttribute()
    {
        return Carbon::parse($this->tanggal_pensiun)->translatedFormat('d F Y');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'pegawai_id');
    }

    public function unit_kerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }

    public function memimpin_unit()
    {
        return $this->hasOne(UnitKerja::class, 'pimpinan_id');
    }

    public function status_pegawai()
    {
        return $this->belongsTo(StatusPegawai::class, 'status_pegawai_id');
    }

    public function jenis_pegawai()
    {
        return $this->belongsTo(JenisPegawai::class, 'jenis_pegawai_id');
    }

    public function keluarga()
    {
        return $this->hasMany(Keluarga::class, 'pegawai_id');
    }

    public function rekening()
    {
        return $this->hasOne(Rekening::class, 'pegawai_id', 'id');
    }

    public function riwayatKepegawaian()
    {
        return $this->hasMany(RiwayatKepegawaian::class);
    }

    public function riwayatKepangkatan()
    {
        return $this->hasMany(RiwayatKepangkatan::class);
    }

    public function riwayatPelatihan()
    {
        return $this->hasMany(RiwayatPelatihan::class);
    }

    public function riwayatPendidikan()
    {
        return $this->hasMany(RiwayatPendidikan::class);
    }

    public function riwayatTugasBelajar()
    {
        return $this->hasMany(RiwayatTugasBelajar::class);
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'pegawai_nip', 'nip');
    }

    public function lembur()
    {
        return $this->hasMany(Lembur::class);
    }

     public function suratPerintahLembur()
    {
        return $this->belongsToMany(
            SuratPerintahLembur::class,
            'surat_perintah_lembur_pegawai'
        );
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
            default => '-',
        };
    }
}