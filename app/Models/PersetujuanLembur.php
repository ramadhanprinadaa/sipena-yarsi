<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersetujuanLembur extends Model
{
    protected $table = 'lembur_approval';

    protected $fillable = [
        'lembur_id',
        'approved_by',
        'role_approval',
        'status',
        'catatan',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function lembur()
    {
        return $this->belongsTo(Lembur::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
