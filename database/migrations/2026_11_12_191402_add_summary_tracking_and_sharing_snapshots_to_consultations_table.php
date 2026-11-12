<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->json('shared_journal_snapshot')->nullable();

            $table->json('shared_ai_reflection_snapshot')->nullable();
            $table->string('brief_status', 20)
                ->default('not_requested');

            // Pesan error jika pembuatan ringkasan gagal.
            $table->text('brief_error')->nullable();

            // Waktu ringkasan berhasil dibuat.
            $table->timestamp('brief_generated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn([
                'shared_journal_snapshot',
                'shared_ai_reflection_snapshot',
                'brief_status',
                'brief_error',
                'brief_generated_at',
            ]);
        });
    }
};
