<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Keluarga extends Model
{
    protected $table = 'keluarga';

    protected $fillable = [
        'pegawai_nip',
        'nama',
        'hubungan',
        'tempat_lahir',
        'tanggal_lahir',
        'pekerjaan',
        'no_telpon',
        'alamat',
        'updated_by'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date:d/m/Y'
    ];

    protected function nama(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => ucwords(strtolower($value)),
            set: fn(string $value) => strtolower($value),
        );
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
