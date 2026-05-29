<?php

namespace App\Models;

use App\Models\Presensi;
use Illuminate\Database\Eloquent\Model;

class StatusKehadiran extends Model
{
    protected $table = 'status_kehadiran';

    protected $fillable = [
        'id',
        'kondisi',
        'status',
        'warna',
    ];

    protected $attributes = [
        'warna' => '########'
    ];

    public function presensi()
    {
        return $this->hasMany(Presensi::class);
    }

    public function getBadgeStyleAttribute(): string
    {
        return "
            background-color: {$this->warna}20;
            color: {$this->warna};
            border-color: {$this->warna};
        ";
    }
}