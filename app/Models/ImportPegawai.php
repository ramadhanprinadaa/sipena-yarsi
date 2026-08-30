<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportPegawai extends Model
{
    protected $table = 'import_pegawai';

    protected $fillable = [
        'file_name',
        'file_path',
        'imported_by',
        'total_rows',
        'total_success',
        'total_failed',
        'total_duplicate',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}
