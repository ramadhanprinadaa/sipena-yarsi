<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    protected $table = 'unit_kerja';

    protected $guarded = ['id'];

    public function parent()
    {
        return $this->belongsTo(UnitKerja::class, 'parent_id');
    }
    public function pimpinan()
    {
        return $this->belongsTo(Pegawai::class, 'pimpinan_id');
    }
    public function unitSdm()
    {
        return $this->belongsTo(UnitSdm::class, 'unit_sdm_id');
    }
    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'unit_kerja_id');
    }
}