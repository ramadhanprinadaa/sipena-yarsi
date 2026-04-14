<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
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