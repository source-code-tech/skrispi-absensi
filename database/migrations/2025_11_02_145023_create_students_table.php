<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->string('nisn', 20)->primary(); // Nomor Induk Siswa Nasional
            $table->string('nis', 20)->unique()->nullable(); // Nomor Induk Siswa (Opsional)
            $table->string('name', 100);
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->string('class_code', 20); // Relasi ke tabel classes
            $table->foreign('class_code')->references('code')->on('classes')->onDelete('restrict');
            
            $table->string('barcode_data')->unique(); // Data unik untuk Barcode
            $table->string('phone_number')->nullable(); // Nomor HP siswa (opsional)
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};