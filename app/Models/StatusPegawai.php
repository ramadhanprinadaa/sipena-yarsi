<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusPegawai extends Model
{
    protected $table = 'status_pegawai';

    protected $fillable = [
        'id',
        'status_pegawai',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'status_pegawai_id');
    }
}