<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE lembur MODIFY status ENUM('Menunggu Pelaksanaan', 'Menunggu Verifikasi Atasan', 'Menunggu Verifikasi Rektor', 'Menunggu Verifikasi SDM Universitas', 'Menunggu Verifikasi SDM Yayasan', 'Menunggu Laporan', 'Selesai', 'Ditolak') NOT NULL DEFAULT 'Menunggu Verifikasi Atasan'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE lembur SET status = 'Menunggu Verifikasi Atasan' WHERE status = 'Menunggu Verifikasi Rektor'");
            DB::statement("ALTER TABLE lembur MODIFY status ENUM('Menunggu Pelaksanaan', 'Menunggu Verifikasi Atasan', 'Menunggu Verifikasi SDM Universitas', 'Menunggu Verifikasi SDM Yayasan', 'Menunggu Laporan', 'Selesai', 'Ditolak') NOT NULL DEFAULT 'Menunggu Verifikasi Atasan'");
        }
    }
};
