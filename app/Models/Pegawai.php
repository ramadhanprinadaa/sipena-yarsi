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

    protected $guarded = ['id'];

    protected $attributes = [
        'status' => 'active'
    ];

    protected $casts = [
        'ktp' => 'encrypted',
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

}