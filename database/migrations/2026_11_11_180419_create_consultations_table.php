<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('counselor_id')
                ->constrained()
                ->restrictOnDelete();

            // Materi jurnal yang dipilih pengguna untuk dibagikan
            $table->foreignId('journal_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('ai_reflection_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('topic', 120);
            $table->text('context')->nullable();

            $table->dateTime('preferred_at');

            // Kontrol persetujuan berbagi materi
            $table->boolean('share_journal')->default(false);
            $table->boolean('share_ai_reflection')->default(false);
            $table->timestamp('consent_at')->nullable();

            // Ringkasan persiapan konsultasi dari Hermes
            $table->longText('consultation_brief')->nullable();

            // pending_confirmation, accepted, rejected,
            // completed, cancelled
            $table->string('status', 30)
                ->default('pending_confirmation');

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['counselor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};