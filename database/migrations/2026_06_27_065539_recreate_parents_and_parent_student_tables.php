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
        // 1. Drop existing parent_student table
        Schema::dropIfExists('parent_student');
        
        // 2. Drop existing parents table
        Schema::dropIfExists('parents');

        // 3. Recreate parents table with id and email
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            // Referencing user_username since users table still uses username as PK
            $table->string('user_username', 50)->unique();
            $table->foreign('user_username')->references('username')->on('users')->onDelete('cascade');
            
            $table->string('name', 100);
            $table->string('relation_status', 50)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->timestamps();
        });

        // 4. Recreate parent_student table
        Schema::create('parent_student', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->string('student_nisn', 20);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('parents')->onDelete('cascade');
            $table->foreign('student_nisn')->references('nisn')->on('students')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Rollback is destructive here, but typically we would just drop them again.
        Schema::dropIfExists('parent_student');
        Schema::dropIfExists('parents');
    }
};
