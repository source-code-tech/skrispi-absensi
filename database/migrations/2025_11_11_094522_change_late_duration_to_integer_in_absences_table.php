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
        // 💡 Mengubah kolom late_duration menjadi integer, bisa bernilai NULL
        Schema::table('absences', function (Blueprint $table) {
            $table->integer('late_duration')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 💡 Jika Anda ingin mengembalikan ke tipe data sebelumnya (misalnya string)
        Schema::table('absences', function (Blueprint $table) {
            // Jika tipe sebelumnya adalah string/time (ganti jika Anda yakin tipe sebelumnya)
            $table->string('late_duration')->nullable()->change(); 
        });
    }
};