<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homeroom_teachers', function (Blueprint $table) {
            $table->string('nip', 20)->primary();
            
            // Relasi ke User (Wali Kelas)
            $table->string('user_username', 50);
            $table->foreign('user_username')->references('username')->on('users')->onDelete('cascade');
            
            // Relasi ke Kelas
            $table->string('class_code', 20);
            $table->foreign('class_code')->references('code')->on('classes')->onDelete('restrict'); 
            
            // Pastikan satu kelas hanya memiliki satu wali kelas, dan satu user hanya bisa jadi wali kelas satu kelas.
            $table->unique(['user_username', 'class_code']); 
            $table->unique('class_code'); // Satu kelas hanya boleh punya satu wali kelas

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homeroom_teachers');
    }
};