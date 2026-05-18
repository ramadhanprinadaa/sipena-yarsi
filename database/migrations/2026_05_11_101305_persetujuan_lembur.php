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
        Schema::create('lembur_approval', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lembur_id');
            $table->foreignId('approved_by'); // user/pegawai yang approve

            $table->enum('role_approval', [
                'Pimpinan',
                'Rektor',
                'SDM Yayasan',
                'SDM Universitas'
            ]);

            $table->enum('status', [
                'Menunggu Verifikasi Atasan',
                'Disetujui',
                'Ditolak'
            ])->default('Menunggu Verifikasi Atasan');

            $table->text('catatan')->nullable();

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lembur_approval');
    }
};
