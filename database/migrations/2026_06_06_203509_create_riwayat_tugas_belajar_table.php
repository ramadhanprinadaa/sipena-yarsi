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
        Schema::create('riwayat_tugas_belajar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id');
            $table->string('nama_institusi');
            $table->string('program_studi');
            $table->string('nama_program');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('pembiayaan');
            $table->string('file_name');
            $table->string('file_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_tugas_belajar');
    }
};