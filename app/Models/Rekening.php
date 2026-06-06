<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    protected $table = 'rekening';

    protected $fillable = [
        'pegawai_id',
        'nama_bank',
        'nomor_rekening',
        'nama_rekening',
    ];

    protected function namaRekening(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? strtoupper($value) : null,
            set: fn(?string $value) => $value ? strtolower($value) : null,
        );
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}