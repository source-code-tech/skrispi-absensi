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
            // 🚨 Tambahkan kolom 'address' setelah kolom 'phone_number' (atau di posisi yang sesuai)
            $table->string('address', 500)->nullable()->after('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // 🚨 Hapus kolom 'address'
            $table->dropColumn('address');
        });
    }
};