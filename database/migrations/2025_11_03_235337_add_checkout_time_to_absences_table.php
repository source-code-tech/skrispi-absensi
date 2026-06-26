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
        Schema::table('absences', function (Blueprint $table) {
            // Tambahkan kolom waktu pulang (bisa NULL karena diisi saat scan kedua)
            $table->dateTime('checkout_time')->nullable()->after('attendance_time');
            
            // Hapus unique constraint lama jika ada (unique(['student_id', 'attendance_time']))
            // Laravel 12 tidak selalu memiliki nama constraint yang sama, tapi coba hapus constraint yang memblokir.
            // Biasanya, kita perlu tahu nama indeksnya. Jika migrate:fresh sudah dilakukan, kita bisa mengabaikan ini.

            // Tambahkan unique constraint baru per hari (Jika Anda ingin memastikan hanya ada 1 record per hari)
            // Jika Anda TIDAK menggunakan unique constraint di tabel absences, abaikan ini.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            $table->dropColumn('checkout_time');
        });
    }
};