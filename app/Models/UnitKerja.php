<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $parent_id
 * @property int|null $pimpinan_id
 * @property int|null $unit_sdm_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read UnitKerja|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pegawai> $pegawai
 * @property-read int|null $pegawai_count
 * @property-read \App\Models\Pegawai|null $pimpinan
 * @property-read \App\Models\UnitSdm|null $unitSdm
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitKerja newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitKerja newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitKerja query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitKerja whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitKerja whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitKerja whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitKerja whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitKerja wherePimpinanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitKerja whereUnitSdmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitKerja whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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