<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            // 💡 Field untuk audit koreksi manual
            $table->boolean('is_manual_corrected')->default(false)->after('recorded_by');
            $table->string('corrected_by')->nullable()->after('is_manual_corrected');
            $table->text('correction_note')->nullable()->after('corrected_by');
        });
    }

    public function down(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            $table->dropColumn('is_manual_corrected');
            $table->dropColumn('corrected_by');
            $table->dropColumn('correction_note');
        });
    }
};