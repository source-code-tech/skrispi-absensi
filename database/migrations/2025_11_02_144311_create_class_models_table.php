<?php
// database/migrations/..._create_class_models_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->string('code', 20)->primary(); // Contoh: X-RPL-1
            $table->string('name', 50)->unique(); // Contoh: X RPL 1
            $table->string('grade', 10);          // Contoh: X, XI, XII
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};