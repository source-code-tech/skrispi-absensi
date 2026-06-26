<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parents', function (Blueprint $table) {
            $table->string('nik', 16)->primary();
            // User ID adalah akun login untuk orang tua
            $table->string('user_username', 50)->unique();
            $table->foreign('user_username')->references('username')->on('users')->onDelete('cascade');
            $table->string('name', 100); // Nama lengkap orang tua (bisa berbeda dari nama user)
            $table->string('relation_status', 50)->nullable(); // Misal: Ayah/Ibu/Wali
            $table->string('phone_number', 20)->unique();  
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};