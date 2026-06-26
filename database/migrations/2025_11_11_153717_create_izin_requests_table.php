<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('izin_requests', function (Blueprint $table) {
            $table->id();
            $table->string('student_nisn', 20);
            $table->foreign('student_nisn')->references('nisn')->on('students')->onDelete('cascade');
            $table->date('request_date'); // Tanggal izin berlaku
            $table->enum('type', ['Sakit', 'Izin']); // Jenis izin
            $table->text('reason'); // Keterangan dari orang tua
            $table->string('attachment_path', 255)->nullable(); // Path foto surat/bukti
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->string('approved_by_username', 50)->nullable();
            $table->foreign('approved_by_username')->references('username')->on('users'); // Wali Kelas/Admin yang menyetujui
            $table->timestamps();
            
            $table->unique(['student_nisn', 'request_date'], 'student_date_unique'); // Hanya boleh 1 izin per siswa per hari
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('izin_requests');
    }
};