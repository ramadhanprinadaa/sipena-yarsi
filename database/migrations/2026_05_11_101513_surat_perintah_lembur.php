<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surat_perintah_lembur', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->foreignId('unit_kerja_id');
            $table->date('tanggal_dibuat');
            $table->string('nama_kegiatan');
            $table->text('deskripsi_tugas');
            $table->enum('jenis_hari', [
                'Hari Kerja Normal',
                'Hari Libur Mingguan',
                'Hari Libur Nasional'
            ]);
            $table->date('tanggal_lembur');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            
            $table->enum('status', [
                'Diterbitkan',
                'Dibatalkan'
            ])->default('Diterbitkan');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_perintah_lembur');
    }
};
