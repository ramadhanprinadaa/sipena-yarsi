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
            $table->foreignId('imported_by');
            $table->integer('total_rows')->nullable();
            $table->integer('total_success')->nullable();
            $table->integer('total_failed')->nullable();
            $table->integer('total_duplicate')->nullable();
            $table->json('summary')->nullable();
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