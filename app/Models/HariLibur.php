<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class HariLibur extends Model
{
    protected $table = 'hari_libur';

    protected $fillable = [
        'tanggal',
        'nama_hari_libur',
        'jenis_hari_libur',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date:d/m/Y',
    ];

    protected function jenisHariLibur(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => ucwords(strtolower($value)),
            set: fn(string $value) => strtolower($value),
        );
    }
}