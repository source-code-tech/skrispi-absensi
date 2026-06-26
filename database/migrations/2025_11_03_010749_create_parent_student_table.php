<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_student', function (Blueprint $table) {
            $table->string('parent_nik', 16);
            $table->foreign('parent_nik')->references('nik')->on('parents')->onDelete('cascade');
            $table->string('student_nisn', 20);
            $table->foreign('student_nisn')->references('nisn')->on('students')->onDelete('cascade');
            $table->primary(['parent_nik', 'student_nisn']); // Compound Primary Key
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_student');
    }
};