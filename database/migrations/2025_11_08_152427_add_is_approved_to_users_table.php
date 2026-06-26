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
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom is_approved sebagai boolean, default FALSE (0)
            // Default FALSE berarti pengguna baru perlu persetujuan.
            $table->boolean('is_approved')->default(false)->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rollback: Hapus kolom jika migrasi di-rollback
            $table->dropColumn('is_approved');
        });
    }
};