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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('class_code', 20);
            $table->foreign('class_code')->references('code')->on('classes')->onDelete('cascade');
            $table->string('subject_code', 20);
            $table->foreign('subject_code')->references('code')->on('subjects')->onDelete('cascade');
            $table->string('day', 20); // Senin, Selasa, Rabu, Kamis, Jumat, Sabtu
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
