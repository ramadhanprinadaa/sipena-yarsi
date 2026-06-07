<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArsipFile extends Model
{
    protected $fillable = [
        'pegawai_id',
        'file_name',
        'file_path',
        'jenis_file',
        'uploaded_by',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }
}