<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('mood_checkin_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('title', 160)->nullable();

            $table->longText('content');

            // free = cerita bebas, guided = menggunakan pertanyaan panduan
            $table->string('entry_type', 20)->default('free');

            // Semua jurnal dimulai sebagai private
            $table->string('privacy', 20)->default('private');

            $table->timestamp('journaled_at')->useCurrent();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};