<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportPresensi extends Model
{
    protected $table = 'import_presensi';

    protected $fillable = [
        'file_name',
        'total_rows',
        'total_success',
        'total_failed',
        'total_duplicate',
        'summary'
    ];

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'import_presensi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}