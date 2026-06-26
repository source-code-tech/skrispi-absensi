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
        Schema::table('parents', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->change();
            $table->string('relation_status')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->string('phone_number')->nullable(false)->change();
            $table->string('relation_status')->nullable(false)->change();
        });
    }
};
