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
        Schema::create('laporan_hasil_lembur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lembur_id')->unique();
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->text('hasil_pekerjaan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_hasil_lembur');
    }
};
