<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            // Poin 1: Informasi media / lokasi pertemuan saat jadwal final ditetapkan
            $table->string('meeting_link', 500)->nullable()->after('scheduled_at');
            $table->text('meeting_notes')->nullable()->after('meeting_link');

            // Poin 2: Informasi pembatalan oleh Pencerita
            $table->timestamp('cancelled_at')->nullable()->after('completed_at');
            $table->text('cancellation_reason')->nullable()->after('cancelled_at');

            // Poin 3: Catatan pasca-sesi dari Konselor
            $table->text('counselor_notes')->nullable()->after('cancellation_reason');
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn([
                'meeting_link',
                'meeting_notes',
                'cancelled_at',
                'cancellation_reason',
                'counselor_notes',
            ]);
        });
    }
};