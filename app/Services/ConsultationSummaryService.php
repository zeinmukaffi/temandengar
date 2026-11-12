<?php

namespace App\Services;

use App\Models\Consultation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ConsultationSummaryService
{
    /**
     * Mengirim permintaan konsultasi ke n8n,
     * menerima hasil Hermes, dan menyimpannya ke database.
     *
     * true  = ringkasan berhasil dibuat.
     * false = ringkasan gagal dibuat.
     */
    public function generate(Consultation $consultation): bool
    {
        $webhookUrl = config(
            'services.n8n.consultation_summary_url'
        );

        $headerName = config(
            'services.n8n.webhook_header',
            'X-TemanDengar-Token'
        );

        $webhookToken = config(
            'services.n8n.webhook_token'
        );

        // Pastikan konfigurasi webhook tersedia.
        if (blank($webhookUrl) || blank($webhookToken)) {
            return $this->markFailed(
                $consultation,
                'Konfigurasi webhook n8n belum tersedia.'
            );
        }

        // Tandai proses pembuatan ringkasan dimulai.
        $consultation->update([
            'brief_status' => 'processing',
            'brief_error' => null,
        ]);

        /*
         * Payload menggunakan snapshot yang sudah tersimpan.
         * Tidak mengambil ulang isi jurnal dari database.
         */
        $payload = [
            'event' => 'consultation.summary.requested',

            'consultation_id' => $consultation->id,

            'submitted_at' => $consultation->created_at
                    ?->toIso8601String(),

            'topic' => $consultation->topic,

            'context' => $consultation->context,

            'sharing' => [
                'share_journal' => (bool) $consultation->share_journal,

                'share_ai_reflection' => (bool) $consultation->share_ai_reflection,

                'consent_at' => $consultation->consent_at
                        ?->toIso8601String(),
            ],

            'materials' => [
                'journal' => $consultation->share_journal
                    ? $consultation->shared_journal_snapshot
                    : null,

                'ai_reflection' => $consultation->share_ai_reflection
                    ? $consultation->shared_ai_reflection_snapshot
                    : null,
            ],
        ];

        try {
            // Kirim data konsultasi ke webhook n8n.
            $response = Http::withHeaders([
                $headerName => $webhookToken,
            ])
                ->acceptJson()
                ->connectTimeout(10)
                ->timeout(190)
                ->post($webhookUrl, $payload);

            $result = $response->json();

            // Periksa HTTP response dari n8n.
            if (!$response->successful()) {
                Log::warning('Consultation Summary webhook failed.', [
                    'consultation_id' => $consultation->id,
                    'http_status' => $response->status(),
                ]);

                $message = data_get(
                    $result,
                    'error.message',
                    'Webhook n8n gagal memproses ringkasan konsultasi.'
                );

                return $this->markFailed(
                    $consultation,
                    $message
                );
            }

            // Pastikan response memiliki format yang benar.
            if (
                !is_array($result) ||
                ($result['success'] ?? false) !== true
            ) {
                return $this->markFailed(
                    $consultation,
                    'Response n8n tidak menunjukkan proses berhasil.'
                );
            }

            // Pastikan ID konsultasi pada response sesuai.
            if (
                (int) ($result['consultation_id'] ?? 0)
                !== (int) $consultation->id
            ) {
                return $this->markFailed(
                    $consultation,
                    'ID konsultasi pada response n8n tidak sesuai.'
                );
            }

            $brief = trim(
                (string) ($result['consultation_brief'] ?? '')
            );

            // Ringkasan tidak boleh kosong.
            if ($brief === '') {
                return $this->markFailed(
                    $consultation,
                    'Hermes mengembalikan ringkasan kosong.'
                );
            }

            // Simpan ringkasan yang berhasil dibuat.
            $consultation->update([
                'consultation_brief' => $brief,
                'brief_status' => 'completed',
                'brief_error' => null,
                'brief_generated_at' => now(),
            ]);

            Log::info('Consultation Summary generated.', [
                'consultation_id' => $consultation->id,
            ]);

            return true;

        } catch (Throwable $exception) {

            // Kegagalan Hermes tidak membatalkan konsultasi.
            Log::error('Consultation Summary exception.', [
                'consultation_id' => $consultation->id,
                'error' => $exception->getMessage(),
            ]);

            return $this->markFailed(
                $consultation,
                'Terjadi kesalahan saat menghubungi layanan ringkasan.'
            );
        }
    }

    /**
     * Menandai ringkasan gagal tanpa menghapus konsultasi.
     */
    private function markFailed(
        Consultation $consultation,
        string $message
    ): bool {
        $consultation->update([
            'brief_status' => 'failed',
            'brief_error' => mb_substr($message, 0, 2000),
        ]);

        return false;
    }
}
