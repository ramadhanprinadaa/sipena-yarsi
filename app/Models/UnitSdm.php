<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitSdm extends Model
{
    protected $table = 'unit_sdm';

    protected $guarded = ['id'];

    public function hr()
    {
        return $this->belongsTo(Pegawai::class, 'hr_id');
    }

    public function unitKerja()
    {
        return $this->hasMany(UnitKerja::class, 'unit_sdm_id');
    }
}