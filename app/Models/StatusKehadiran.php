<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusKehadiran extends Model
{
    protected $table = 'status_kehadiran';

    protected $fillable = [
        'kondisi',
        'status',
        'warna',
    ];

    protected $attributes = [
        'warna' => '########'
    ];
}