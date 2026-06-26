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
        Schema::table('students', function (Blueprint $table) {
            // Kolom untuk Tempat Lahir (birth_place)
            $table->string('birth_place', 100)->nullable()->after('address');
            // Kolom untuk Tanggal Lahir (birth_date)
            $table->date('birth_date')->nullable()->after('birth_place');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Rollback: Hapus kolom jika migrasi di-rollback
            $table->dropColumn(['birth_place', 'birth_date']);
        });
    }
};