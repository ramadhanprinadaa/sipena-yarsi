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
        Schema::create('lembur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id'); // pegawai yang mengajukan
            $table->foreignId('surat_perintah_lembur_id')->nullable();

            $table->date('tanggal_lembur');
            $table->enum('jenis_hari', [
                'Hari Kerja Normal',
                'Hari Libur Mingguan',
                'Hari Libur Nasional'
            ]);

            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->text('alasan_lembur')->nullable();

            $table->enum('status', [
                'Menunggu Pelaksanaan',
                'Menunggu Verifikasi Atasan',
                'Menunggu Verifikasi Rektor',
                'Menunggu Verifikasi SDM Universitas',
                'Menunggu Verifikasi SDM Yayasan',
                'Menunggu Laporan',
                'Selesai',
                'Ditolak'
            ])->default('Menunggu Verifikasi Atasan');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lembur');
    }
};