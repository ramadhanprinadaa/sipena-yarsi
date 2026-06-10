<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisKeluarga extends Model
{
    protected $table = 'jenis_keluarga';

    protected $fillable = [
        'id',
        'jenis'
    ];

    public function keluarga()
    {
        return $this->hasMany(Keluarga::class, 'jenis_keluarga_id', 'id');
    }
}