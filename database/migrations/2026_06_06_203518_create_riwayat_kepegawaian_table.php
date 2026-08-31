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
        Schema::create('riwayat_kepegawaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id');
            $table->foreignId('unit_kerja_id');
            $table->foreignId('status_pegawai_id');
            $table->date('tanggal_bergabung');
            $table->string('nomor_sk');
            $table->date('tanggal_sk');
            $table->text('isi_sk');
            $table->date('tanggal_berlaku');
            $table->date('tanggal_berakhir')->nullable();
            $table->date('tanggal_pensiun')->nullable();
            $table->string('status_keaktifan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_kepegawaian');
    }
};