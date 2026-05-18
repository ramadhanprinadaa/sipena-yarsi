<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UnitKerja;

class SuratPerintahLembur extends Model
{
    protected $table = 'surat_perintah_lembur';

    protected $fillable = [
        'nomor_surat',
        'unit_kerja_id',
        'tanggal_dibuat',
        'nama_kegiatan',
        'deskripsi_tugas',
        'jenis_hari',
        'tanggal_lembur',
        'jam_mulai',
        'jam_selesai',
        'status'
    ];

    public function pegawai()
    {
        return $this->belongsToMany(
            Pegawai::class,
            'surat_perintah_lembur_pegawai'
        );
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }
    
    public function lembur()
    {
        return $this->hasMany(
            Lembur::class,
            'surat_perintah_lembur_id'
        );
    }

}
