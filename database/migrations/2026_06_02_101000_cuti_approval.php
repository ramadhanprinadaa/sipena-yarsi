<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuti_approval', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuti_id');
            $table->foreignId('approved_by');
            $table->enum('role_approval', [
                'Pimpinan',
                'Rektor',
                'SDM Yayasan',
                'SDM Universitas',
            ]);
            $table->enum('status', [
                'Disetujui',
                'Ditolak',
            ]);
            $table->text('catatan')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuti_approval');
    }
};
