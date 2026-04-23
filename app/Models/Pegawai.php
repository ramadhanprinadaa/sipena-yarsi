<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';

    protected $guarded = ['id'];

    protected $attribute = [
        'status' => 'active'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'pegawai_id');
    }
}