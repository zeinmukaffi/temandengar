<?php

namespace App\Services;

use App\Models\Consultation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class N8nWebhookService
{
    /**
     * Mengirim event perubahan status atau jadwal konsultasi ke n8n lokal.
     */
    public function sendConsultationEvent(
        Consultation $consultation,
        string $eventType,
        string $title,
        string $message,
        string $actionUrl
    ): bool {
        if (!config('services.n8n.enabled', false)) {
            return false;
        }

        $url = (string) config('services.n8n.schedule_webhook_url', '');
        if (trim($url) === '') {
            return false;
        }

        $consultation->loadMissing(['user', 'counselor.user']);

        $payload = [
            'event' => 'consultation.' . $eventType,
            'event_type' => $eventType,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'counselor_inbox' => config('services.n8n.counselor_inbox'),
            'consultation' => [
                'id' => $consultation->id,
                'topic' => $consultation->topic,
                'status' => $consultation->status,
                'preferred_at' => $consultation->preferred_at?->format('d M Y, H:i') . ' WIB',
                'scheduled_at' => $consultation->scheduled_at
                    ? $consultation->scheduled_at->format('d M Y, H:i') . ' WIB'
                    : null,
                'meeting_link' => $consultation->meeting_link,
                'meeting_notes' => $consultation->meeting_notes,
                'cancellation_reason' => $consultation->cancellation_reason,
            ],
            'pencerita' => [
                'name' => $consultation->user?->name,
                'email' => $consultation->user?->email,
            ],
            'counselor' => [
                'name' => $consultation->counselor?->name,
                'email' => $consultation->counselor?->user?->email,
            ],
            'sent_at' => now()->toIso8601String(),
        ];

        $headerName = (string) config('services.n8n.webhook_header', 'X-TemanDengar-Token');
        $webhookToken = (string) config('services.n8n.webhook_token', '');
        $timeout = max(2, (int) config('services.n8n.timeout', 5));

        try {
            $headers = ['Accept' => 'application/json'];
            if (trim($webhookToken) !== '') {
                $headers[$headerName] = $webhookToken;
            }

            $response = Http::connectTimeout(2)
                ->timeout($timeout)
                ->withHeaders($headers)
                ->post($url, $payload);

            if (!$response->successful()) {
                Log::warning('n8n schedule webhook returned non-2xx.', [
                    'consultation_id' => $consultation->id,
                    'event_type' => $eventType,
                    'status' => $response->status(),
                ]);

                return false;
            }

            return true;
        } catch (Throwable $exception) {
            Log::info('n8n schedule webhook skipped (local offline/timeout).', [
                'consultation_id' => $consultation->id,
                'event_type' => $eventType,
                'reason' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}