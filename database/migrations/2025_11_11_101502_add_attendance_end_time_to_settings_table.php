<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Tambahkan key baru untuk waktu pulang
            // Kita asumsikan ini akan diisi sebagai key-value di tabel settings
            // Kita tidak perlu mengubah tabel settings, cukup pastikan setting ini digunakan.
            // Biarkan migration ini kosong jika Anda mengelola settings sebagai key-value.
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
             // Jika Anda ingin membersihkan data (opsional, karena ini hanya key-value)
        });
    }
};