<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportPresensi extends Model
{
    protected $table = 'import_presensi';

    protected $fillable = [
        'file_name',
        'file_path',
        'periode_mulai',
        'periode_selesai',
        'total_rows',
        'total_created',
        'total_updated',
        'total_skipped',
        'total_failed',
        'error_summary',
        'imported_by',
    ];

    protected function casts(): array
    {
        return [
            'periode_mulai' => 'date',
            'periode_selesai' => 'date',
            'error_summary' => 'array'
        ];
    }

    public function presensiLogs()
    {
        return $this->hasMany(PresensiLog::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}