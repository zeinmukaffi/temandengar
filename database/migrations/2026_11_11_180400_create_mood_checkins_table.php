<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mood_checkins', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('mood', 30);
            $table->text('note')->nullable();

            $table->timestamp('checked_at')->useCurrent();

            $table->timestamps();

            $table->index(['user_id', 'checked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mood_checkins');
    }
};