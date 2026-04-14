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
            $table->string('nip')->unique();
            $table->string('ktp')->unique();
            $table->string('npwp')->unique();
            $table->string('nama');
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->date('tanggal_lahir');
            $table->string('tempat_lahir');
            $table->string('tanggal_pensiun');
            $table->text('alamat_ktp');
            $table->text('alamat_domisili');
            $table->string('no_telepon');
            $table->string('status');
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