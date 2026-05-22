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
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->string('pegawai_nip');
            $table->foreign('pegawai_nip')->references('nip')->on('pegawai');

            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();

            $table->foreignId('status_kehadiran_id')->nullable();
            $table->foreignId('last_import_presensi_id')->nullable();

            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();

            $table->unique(['pegawai_nip', 'tanggal']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
