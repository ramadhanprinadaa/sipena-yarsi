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
        Schema::create('riwayat_kepangkatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id');
            $table->string('golongan_internal_yarsi');
            $table->string('golongan_lldikti');
            $table->string('fungsional_internal_yarsi');
            $table->string('fungsional_lldikti');
            $table->string('no_sk_golongan_internal_yarsi');
            $table->string('no_sk_golongan_lldikti');
            $table->string('no_sk_fungsional_internal_yarsi');
            $table->string('no_sk_fungsional_lldikti');
            $table->date('tanggal_berlaku_gol_internal_yarsi');
            $table->date('tanggal_berlaku_gol_lldikti');
            $table->date('tanggal_berlaku_fung_internal_yarsi');
            $table->date('tanggal_berlaku_fung_lldikti');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_kepangkatan');
    }
};