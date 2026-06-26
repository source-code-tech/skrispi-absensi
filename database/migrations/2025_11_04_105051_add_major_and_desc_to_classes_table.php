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
        Schema::table('classes', function (Blueprint $table) {
            // 🚨 Tambahkan kolom 'major'
            if (!Schema::hasColumn('classes', 'major')) {
                $table->string('major', 100)->nullable()->after('grade');
            }
            // 🚨 Tambahkan kolom 'description'
            if (!Schema::hasColumn('classes', 'description')) {
                $table->text('description')->nullable()->after('major');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'major')) {
                $table->dropColumn('major');
            }
            if (Schema::hasColumn('classes', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};