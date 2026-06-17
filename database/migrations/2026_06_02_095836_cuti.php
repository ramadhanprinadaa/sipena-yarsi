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
        Schema::create('cuti', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pegawai_id');
        $table->foreignId('jenis_cuti_id');
        $table->date('tanggal_pengajuan');
        $table->date('tanggal_mulai');
        $table->date('tanggal_selesai');
        $table->time('jam_mulai')->nullable();
        $table->time('jam_selesai')->nullable();
        $table->integer('jumlah_hari_cuti')->nullable();
        $table->integer('jumlah_jam')->nullable();
        $table->integer('saldo_cuti_sebelum')->nullable();
        $table->integer('saldo_cuti_sesudah')->nullable();
        $table->string('dokumen_pendukung')->nullable();
        $table->enum('metode_potongan', ['potong_cuti', 'potong_gaji'])->nullable();
        $table->text('keterangan')->nullable();
        $table->enum('status', [
            'pending_atasan',
            'pending_rektor',
            'pending_sdm_universitas',
            'pending_sdm_yayasan',
            'disetujui',
            'ditolak'
            ])->default('pending_atasan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuti');
    }
};