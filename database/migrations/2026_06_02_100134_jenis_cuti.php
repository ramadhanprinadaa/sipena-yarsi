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
        Schema::create('jenis_cuti', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->integer('minimal_masa_kerja_bulan')->nullable();
        $table->integer('minimal_hari_pengajuan')->nullable();
        $table->integer('maksimal_hari')->nullable();
        $table->integer('maksimal_hari_per_bulan')->nullable();
        $table->boolean('memotong_saldo')->default(true);
        $table->boolean('butuh_surat_dokter')->default(false);
        $table->boolean('dihitung_per_jam')->default(false);
        $table->boolean('sekali_seumur_kerja')->default(false);
        $table->text('deskripsi')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_cuti');
    }
};
