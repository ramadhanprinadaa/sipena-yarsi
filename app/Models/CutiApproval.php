<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CutiApproval extends Model
{
    protected $table = 'cuti_approval';

    protected $fillable = [
        'cuti_id',
        'approved_by',
        'role_approval',
        'status',
        'catatan',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function cuti()
    {
        return $this->belongsTo(Cuti::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
