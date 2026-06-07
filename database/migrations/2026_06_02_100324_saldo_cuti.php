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
        Schema::create('saldo_cuti', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pegawai_id');
        $table->year('tahun');
        $table->integer('hak_cuti');
        $table->integer('cuti_terpakai');
        $table->integer('sisa_cuti');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldo_cuti');
    }
};
