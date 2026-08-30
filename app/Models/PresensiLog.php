<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiLog extends Model
{
    use HasFactory;

    protected $table = 'presensi_log';

    protected $fillable = [
        'presensi_id',
        'import_presensi_id',
        'changes',
        'edited_by',
        'keterangan',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function presensi()
    {
        return $this->belongsTo(Presensi::class, 'presensi_id');
    }

    public function importPresensi()
    {
        return $this->belongsTo(ImportPresensi::class, 'import_presensi_id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}