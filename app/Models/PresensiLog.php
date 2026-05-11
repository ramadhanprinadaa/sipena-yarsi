<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresensiLog extends Model
{

    protected $fillable = [
        'presensi_id',
        'action',
        'old_data',
        'new_data',
        'edited_fields',
        'edited_by',
    ];

    public function presensi()
    {
        return $this->belongsTo(Presensi::class, 'presensi_id');
    }
}