<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatTugasBelajar extends Model
{
    protected $table = 'riwayat_tugas_belajar';

    protected $fillable = [
        'pegawai_id',
        'nama_institusi',
        'program_studi',
        'nama_program',
        'tanggal_mulai',
        'tanggal_selesai',
        'pembiayaan',
        'file_name',
        'file_path',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}