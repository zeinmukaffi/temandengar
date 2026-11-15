<?php

namespace App\Services;

use App\Models\AiReflection;
use App\Models\Journal;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class JournalReflectionService
{
    /**
     * Mengirim jurnal ke n8n dan menyimpan hasil refleksi Hermes.
     */
    public function generate(Journal $journal, int $userId): bool
    {
        $aiReflection = $journal->aiReflections()->updateOrCreate(
            ['user_id' => $userId],
            [
                'summary' => '',
                'emotional_insight' => null,
                'key_concerns' => [],
                'reflection_questions' => [],
                'suggested_steps' => [],
                'status' => 'pending',
                'ai_model' => 'temandengar',
                'raw_response' => null,
            ]
        );

        $webhookUrl = config('services.n8n.journal_reflection_url');
        $headerName = config('services.n8n.webhook_header', 'X-TemanDengar-Token');
        $webhookToken = config('services.n8n.webhook_token');

        if (
            !is_string($webhookUrl) || trim($webhookUrl) === '' ||
            !is_string($headerName) || trim($headerName) === '' ||
            !is_string($webhookToken) || trim($webhookToken) === ''
        ) {
            $this->markFailed($aiReflection, [
                'error' => [
                    'code' => 'N8N_CONFIG_MISSING',
                    'message' => 'Konfigurasi webhook belum lengkap.',
                ],
            ]);

            return false;
        }

        $journal->loadMissing('moodCheckin');

        $payload = [
            'journal_id' => $journal->id,
            'title' => $journal->title,
            'entry_type' => $journal->entry_type,
            'mood' => $journal->moodCheckin?->mood,
            'mood_note' => $journal->moodCheckin?->note,
            'content' => $journal->content,
        ];

        try {
            $response = Http::connectTimeout(10)
                ->timeout(130)
                ->withHeaders([
                    $headerName => $webhookToken,
                    'Accept' => 'application/json',
                ])
                ->post($webhookUrl, $payload);

            $responseData = $response->json();
            $reflectionData = is_array($responseData)
                ? ($responseData['reflection'] ?? null)
                : null;

            $isValidResponse =
                $response->successful() &&
                is_array($responseData) &&
                ($responseData['success'] ?? false) === true &&
                (int) ($responseData['journal_id'] ?? 0) === (int) $journal->id &&
                $this->isValidReflectionData($reflectionData);

            if (!$isValidResponse) {
                $rawResponse = is_array($responseData)
                    ? $responseData
                    : [
                        'http_status' => $response->status(),
                        'response_body' => $response->body(),
                    ];

                $this->markFailed($aiReflection, $rawResponse);
                return false;
            }

            $aiReflection->update([
                'summary' => $reflectionData['summary'],
                'emotional_insight' => $reflectionData['emotional_insight'],
                'key_concerns' => $reflectionData['key_concerns'],
                'reflection_questions' => $reflectionData['reflection_questions'],
                'suggested_steps' => $reflectionData['suggested_steps'],
                'status' => 'completed',
                'ai_model' => $responseData['model'] ?? 'temandengar',
                'raw_response' => $responseData,
            ]);

            return true;

        } catch (ConnectionException $exception) {
            $this->markFailed($aiReflection, [
                'error' => [
                    'code' => 'N8N_CONNECTION_ERROR',
                    'message' => 'Tidak dapat terhubung ke layanan refleksi.',
                ],
            ]);
            Log::warning('TemanDengar could not connect to n8n.', [
                'journal_id' => $journal->id,
                'exception' => $exception->getMessage(),
            ]);
            return false;

        } catch (Throwable $exception) {
            $this->markFailed($aiReflection, [
                'error' => [
                    'code' => 'AI_PROCESSING_ERROR',
                    'message' => 'Terjadi kesalahan saat memproses refleksi.',
                ],
            ]);
            Log::error('TemanDengar AI reflection processing failed.', [
                'journal_id' => $journal->id,
                'exception' => $exception->getMessage(),
            ]);
            return false;
        }
    }

    private function isValidReflectionData(mixed $reflection): bool
    {
        if (!is_array($reflection)) {
            return false;
        }

        $summary = $reflection['summary'] ?? null;
        $insight = $reflection['emotional_insight'] ?? null;
        $concerns = $reflection['key_concerns'] ?? null;
        $questions = $reflection['reflection_questions'] ?? null;
        $steps = $reflection['suggested_steps'] ?? null;

        return
            is_string($summary) && trim($summary) !== '' &&
            is_string($insight) && trim($insight) !== '' &&
            is_array($concerns) && count($concerns) <= 3 &&
            collect($concerns)->every(fn($item) => is_string($item) && trim($item) !== '') &&
            is_array($questions) && count($questions) >= 2 && count($questions) <= 3 &&
            collect($questions)->every(fn($item) => is_string($item) && trim($item) !== '') &&
            is_array($steps) && count($steps) <= 3 &&
            collect($steps)->every(fn($item) => is_string($item) && trim($item) !== '');
    }

    private function markFailed(AiReflection $reflection, array $rawResponse = []): void
    {
        $reflection->update([
            'summary' => '',
            'emotional_insight' => null,
            'key_concerns' => [],
            'reflection_questions' => [],
            'suggested_steps' => [],
            'status' => 'failed',
            'raw_response' => $rawResponse ?: null,
        ]);
    }
}