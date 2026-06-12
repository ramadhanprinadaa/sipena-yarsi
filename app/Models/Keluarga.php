<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Keluarga extends Model
{
    use SoftDeletes;

    protected $table = 'keluarga';

    protected $fillable = [
        'pegawai_id',
        'jenis_keluarga_id',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'pekerjaan',
        'no_telpon',
        'alamat',
        'updated_by'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date'
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

    public function jenisKeluarga()
    {
        return $this->belongsTo(JenisKeluarga::class, 'jenis_keluarga_id', 'id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}