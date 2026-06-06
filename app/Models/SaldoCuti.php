<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoCuti extends Model
{
    protected $table = 'saldo_cuti';

    protected $fillable = [
        'pegawai_id',
        'tahun',
        'hak_cuti',
        'cuti_terpakai',
        'sisa_cuti',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
