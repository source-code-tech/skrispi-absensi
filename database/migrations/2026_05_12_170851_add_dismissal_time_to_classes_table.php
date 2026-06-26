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
        if (!Schema::hasColumn('classes', 'dismissal_time')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->time('dismissal_time')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('classes', 'dismissal_time')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->dropColumn('dismissal_time');
            });
        }
    }
};