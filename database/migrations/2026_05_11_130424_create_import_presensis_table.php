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
        Schema::create('import_presensi', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('file_path');
            $table->date('periode_mulai')->nullable();
            $table->date('periode_selesai')->nullable();
            $table->integer('total_rows')->nullable();
            $table->integer('total_created')->nullable();
            $table->integer('total_updated')->nullable();
            $table->integer('total_skipped')->nullable();
            $table->integer('total_failed')->nullable();
            $table->json('error_summary')->nullable();
            $table->foreignId('imported_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_presensi');
    }
};