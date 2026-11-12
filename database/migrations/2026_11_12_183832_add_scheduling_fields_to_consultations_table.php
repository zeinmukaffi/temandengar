<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {

            // Tambahkan scheduled_at hanya jika belum tersedia.
            if (!Schema::hasColumn('consultations', 'scheduled_at')) {
                $table->dateTime('scheduled_at')
                    ->nullable()
                    ->after('preferred_at');
            }

            // Tambahkan completed_at hanya jika belum tersedia.
            if (!Schema::hasColumn('consultations', 'completed_at')) {
                $table->dateTime('completed_at')
                    ->nullable()
                    ->after('scheduled_at');
            }
        });
    }



    public function down(): void
    {
        // Kolom scheduling mungkin sudah tersedia sebelum migration ini
        // dijalankan pada database yang sudah memiliki data.
        // Jangan hapus secara otomatis.
    }

};
