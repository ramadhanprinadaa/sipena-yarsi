<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SumberDaya extends Model
{
    protected $table = 'sumber_daya';

    protected $fillable = [
        'id',
        'judul',
        'file_name',
        'file_path',
        'extension',
        'mime_type',
        'uploaded_by',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }
}