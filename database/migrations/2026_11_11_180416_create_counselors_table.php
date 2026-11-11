<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('counselors', function (Blueprint $table) {
            $table->id();

            $table->string('name', 120);
            $table->string('role', 100)->default('Konselor Sebaya');

            $table->string('photo_path')->nullable();

            $table->text('bio')->nullable();

            // Contoh: ["Akademik", "Relasi Pertemanan"]
            $table->json('specialties')->nullable();

            $table->text('availability_description')->nullable();

            $table->boolean('is_available')->default(true);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counselors');
    }
};