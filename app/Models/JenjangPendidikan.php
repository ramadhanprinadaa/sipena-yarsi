<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class JenjangPendidikan extends Model
{
    protected $table = 'jenjang_pendidikan';

    protected $fillable = [
        'id',
        'kode',
        'nama',
        'urutan',
    ];

    public function riwayatPendidikan()
    {
        return $this->hasMany(RiwayatPendidikan::class, 'jenjang_pendidikan_id', 'id');
    }
}