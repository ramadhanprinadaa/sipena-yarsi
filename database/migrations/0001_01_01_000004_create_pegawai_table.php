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
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_kerja_id')->nullable();
            $table->foreignId('jenis_pegawai_id')->nullable(); // kontrak / tetap
            $table->foreignId('status_pegawai_id')->nullable();
            $table->string('nip')->unique();
            $table->string('ktp')->unique();
            $table->string('npwp')->unique()->nullable();
            $table->string('nama');
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir');
            $table->date('tanggal_bergabung');
            $table->date('tanggal_habis_kontrak')->nullable();
            $table->date('tanggal_pensiun')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->text('alamat_ktp')->nullable();
            $table->text('alamat_domisili')->nullable();
            $table->string('no_telpon')->nullable();
            $table->string('email_yarsi')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
