<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_reflections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('journal_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->longText('summary');

            $table->longText('emotional_insight')->nullable();

            // Data berbentuk array yang disimpan sebagai JSON
            $table->json('key_concerns')->nullable();
            $table->json('reflection_questions')->nullable();
            $table->json('suggested_steps')->nullable();

            // Feedback pengguna terhadap hasil AI
            $table->string('user_feedback', 30)->nullable();

            // pending, completed, failed
            $table->string('status', 20)->default('completed');

            $table->string('ai_model', 100)->nullable();

            // Opsional untuk menyimpan respons asli Hermes
            $table->json('raw_response')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_reflections');
    }
};