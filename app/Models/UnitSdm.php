<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $hr_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Pegawai|null $hr
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UnitKerja> $unitKerja
 * @property-read int|null $unit_kerja_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitSdm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitSdm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitSdm query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitSdm whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitSdm whereHrId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitSdm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitSdm whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnitSdm whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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