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
        // Using raw SQL to avoid doctrine/dbal requirement
        DB::statement("ALTER TABLE parents MODIFY phone_number VARCHAR(255) NULL");
        DB::statement("ALTER TABLE parents MODIFY relation_status VARCHAR(255) NULL");
    }

    public function down(): void
    {
        // Reverting back to NOT NULL (be careful if nulls exist)
        DB::statement("ALTER TABLE parents MODIFY phone_number VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE parents MODIFY relation_status VARCHAR(255) NULL"); // Keeping nullable just in case
    }
};
