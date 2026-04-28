<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

/**
 * @property int $id
 * @property int|null $unit_kerja_id
 * @property string $nip
 * @property string $ktp
 * @property string $npwp
 * @property string $nama
 * @property string|null $gelar_depan
 * @property string|null $gelar_belakang
 * @property string $tempat_lahir
 * @property \Illuminate\Support\Carbon $tanggal_lahir
 * @property \Illuminate\Support\Carbon $tanggal_bergabung
 * @property \Illuminate\Support\Carbon|null $tanggal_pensiun
 * @property string|null $jenis_kelamin
 * @property string $alamat_ktp
 * @property string $alamat_domisili
 * @property string $no_telpon
 * @property string $email_yarsi
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $age
 * @property-read \App\Models\UnitKerja|null $unitKerja
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\PegawaiFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereAlamatDomisili($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereAlamatKtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereEmailYarsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereGelarBelakang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereGelarDepan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereJenisKelamin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereKtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereNip($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereNoTelpon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereNpwp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereTanggalBergabung($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereTanggalLahir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereTanggalPensiun($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereTempatLahir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereUnitKerjaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pegawai whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';

    protected $guarded = ['id'];

    protected $attribute = [
        'status' => 'active'
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
        'tanggal_pensiun' => 'date',
        'tanggal_lahir' => 'date'
    ];

    public function getAgeAttribute()
    {
        return Carbon::parse($this->tanggal_lahir)->age;
    }

    public function user()
    {
        return $this->hasOne(User::class, 'pegawai_id');
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }

}