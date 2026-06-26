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
        Schema::table('homeroom_teachers', function (Blueprint $table) {
            // 💡 PERBAIKAN KUNCI: Ubah class_code menjadi nullable
            $table->string('class_code', 20)->nullable()->change();
            
            // Catatan: Jika class_code adalah foreign key, pastikan database engine mengizinkan perubahan ini.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('homeroom_teachers', function (Blueprint $table) {
            // Kembalikan ke NOT NULL (jika diperlukan untuk rollback)
            // Ini akan gagal jika ada NULL di kolom saat ini, jadi jalankan hanya jika yakin.
            $table->string('class_code', 20)->nullable(false)->change();
        });
    }
};