<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'username',
        'pegawai_id',
        'pimpinan_id',
        'role_id',
        'email',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $attributes = [
        'status' => 'active',
        'role_id' => 7
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasRole($roles)
    {
        $this->loadMissing('role');
        return in_array($this->role?->name, (array) $roles);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function import_pegawai()
    {
        return $this->hasMany(ImportPegawai::class, 'imported_by');
    }

    public function editKeluarga()
    {
        return $this->hasMany(Keluarga::class, 'updated_by');
    }

    public function editRekening()
    {
        return $this->hasMany(Rekening::class, 'updated_by');
    }

    public function editRiwayatPendidikan()
    {
        return $this->hasMany(RiwayatPendidikan::class, 'updated_by');
    }

    public function arsipFile()
    {
        return $this->hasMany(ArsipFile::class, 'uploaded_by', 'id');
    }

    public function cutiApproval()
    {
        return $this->hasMany(CutiApproval::class, 'approved_by');
    }

    public function getUnitKerjaLabelAttribute()
    {
        if ($this->hasRole('SDM Universitas')) {
            return $this->pegawai?->unit_kerja?->unitSdm?->name;
        }

        if ($this->hasRole('Pimpinan')) {
            return $this->pegawai?->unit_kerja?->name;
        }

        return null;
    }

    protected function username(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => strtolower($value) === 'admin' ? ucwords($value) : $value,
            set: fn(string $value) => strtolower($value),
        );
    }
}