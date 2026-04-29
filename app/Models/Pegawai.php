<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';

    protected $guarded = ['id'];

    protected $attribute = [
        'status' => 'active'
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
        'tanggal_pensiun' => 'date',
        'tanggal_lahir' => 'date'
    ];

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

}