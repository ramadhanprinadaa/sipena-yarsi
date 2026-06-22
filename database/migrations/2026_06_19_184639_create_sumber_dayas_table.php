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
        Schema::create('sumber_daya', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('extension');
            $table->string('mime_type');
            $table->foreignId('uploaded_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sumber_daya');
    }
};