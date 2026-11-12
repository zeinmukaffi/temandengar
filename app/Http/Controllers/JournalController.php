<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\AiReflection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class JournalController extends Controller
{
    /**
     * Menampilkan daftar jurnal milik pengguna yang sedang login.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $journals = $user->journals()
            ->with('moodCheckin')
            ->latest('journaled_at')
            ->paginate(8);

        $recentMoodCheckins = $user->moodCheckins()
            ->latest('checked_at')
            ->take(5)
            ->get();

        return view('journals.index', compact(
            'journals',
            'recentMoodCheckins'
        ));
    }

    /**
     * Menampilkan form penulisan jurnal.
     */
    public function create(): View
    {
        $moods = config('temandengar.moods');

        return view('journals.create', compact('moods'));
    }

    /**
     * Menyimpan jurnal baru.
     */

    /**
     * Menyimpan jurnal baru dan memproses refleksi menggunakan Hermes.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:160'],
            'content' => ['required', 'string', 'min:3', 'max:20000'],
            'entry_type' => ['required', Rule::in(['free', 'guided'])],
            'mood' => [
                'nullable',
                Rule::in(array_keys(config('temandengar.moods'))),
            ],
            'mood_note' => ['nullable', 'string', 'max:500'],
        ], [
            'content.required' => 'Ceritamu belum diisi.',
            'content.min' => 'Ceritamu terlalu singkat.',
            'content.max' => 'Cerita maksimal 20.000 karakter.',
            'entry_type.required' => 'Pilih jenis journaling.',
            'mood_note.max' => 'Catatan mood maksimal 500 karakter.',
        ]);

        /*
         * TAHAP 1:
         * Simpan mood check-in dan jurnal ke database.
         *
         * Transaksi selesai sebelum menghubungi n8n.
         * Jadi, kegagalan Hermes tidak membatalkan jurnal.
         */
        $journal = DB::transaction(function () use ($request, $validated) {
            $user = $request->user();

            $moodCheckin = null;

            if (!empty($validated['mood'])) {
                $moodCheckin = $user->moodCheckins()->create([
                    'mood' => $validated['mood'],
                    'note' => $validated['mood_note'] ?? null,
                    'checked_at' => now(),
                ]);
            }

            return $user->journals()->create([
                'mood_checkin_id' => $moodCheckin?->id,
                'title' => $validated['title'] ?? null,
                'content' => $validated['content'],
                'entry_type' => $validated['entry_type'],
                'privacy' => 'private',
                'journaled_at' => now(),
            ]);
        });

        /*
         * TAHAP 2:
         * Kirim jurnal yang sudah tersimpan ke n8n.
         */
        $reflectionSucceeded = $this->sendJournalToN8n(
            $journal,
            (int) $request->user()->id
        );

        $message = $reflectionSucceeded
            ? 'Jurnal berhasil disimpan dan refleksi AI berhasil dibuat.'
            : 'Jurnal berhasil disimpan, tetapi refleksi AI belum berhasil dibuat.';

        return redirect()
            ->route('journals.show', $journal->id)
            ->with('success', $message);
    }


    /**
     * Mengirim jurnal ke n8n dan menyimpan hasil refleksi Hermes.
     */
    private function sendJournalToN8n(Journal $journal, int $userId): bool
    {
        /*
         * Buat record awal dengan status pending.
         * updateOrCreate menggunakan kembali record yang sudah ada
         * untuk jurnal dan pengguna yang sama.
         */
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

        // Ambil konfigurasi n8n dari config/services.php.
        $webhookUrl = config('services.n8n.journal_reflection_url');
        $headerName = config(
            'services.n8n.webhook_header',
            'X-TemanDengar-Token'
        );
        $webhookToken = config('services.n8n.webhook_token');

        // Pastikan konfigurasi webhook tersedia.
        if (
            !is_string($webhookUrl) || trim($webhookUrl) === '' ||
            !is_string($headerName) || trim($headerName) === '' ||
            !is_string($webhookToken) || trim($webhookToken) === ''
        ) {
            $this->markReflectionFailed($aiReflection, [
                'error' => [
                    'code' => 'N8N_CONFIG_MISSING',
                    'message' => 'Konfigurasi webhook belum lengkap.',
                ],
            ]);

            Log::warning('TemanDengar n8n configuration is incomplete.', [
                'journal_id' => $journal->id,
            ]);

            return false;
        }

        // Pastikan data mood tersedia.
        $journal->loadMissing('moodCheckin');

        /*
         * Payload harus mengikuti struktur yang digunakan
         * oleh Node 02 - Validate Journal Request.
         */
        $payload = [
            'journal_id' => $journal->id,
            'title' => $journal->title,
            'entry_type' => $journal->entry_type,
            'mood' => $journal->moodCheckin?->mood,
            'mood_note' => $journal->moodCheckin?->note,
            'content' => $journal->content,
        ];

        try {
            /*
             * Kirim request POST ke Production Webhook n8n.
             * Timeout 130 detik karena pemanggilan Hermes
             * memiliki timeout 120 detik di Node 04.
             */
            $response = Http::connectTimeout(10)
                ->timeout(130)
                ->withHeaders([
                    $headerName => $webhookToken,
                    'Accept' => 'application/json',
                ])
                ->post($webhookUrl, $payload);

            $responseData = $response->json();

            /*
             * Periksa:
             * 1. HTTP response berhasil.
             * 2. success bernilai true.
             * 3. journal_id sesuai.
             * 4. Struktur reflection valid.
             */
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

                $this->markReflectionFailed(
                    $aiReflection,
                    $rawResponse
                );

                Log::warning('TemanDengar received an invalid n8n response.', [
                    'journal_id' => $journal->id,
                    'http_status' => $response->status(),
                ]);

                return false;
            }

            /*
             * Simpan seluruh hasil refleksi Hermes ke MySQL.
             */
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
            // Koneksi atau timeout ke n8n gagal.
            $this->markReflectionFailed($aiReflection, [
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
            // Tangani kegagalan lainnya tanpa menghapus jurnal.
            $this->markReflectionFailed($aiReflection, [
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


    /**
     * Memeriksa kelengkapan struktur hasil refleksi Hermes.
     */
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
            is_string($summary) &&
            trim($summary) !== '' &&

            is_string($insight) &&
            trim($insight) !== '' &&

            is_array($concerns) &&
            count($concerns) <= 3 &&
            collect($concerns)->every(
                fn($item) => is_string($item) && trim($item) !== ''
            ) &&

            is_array($questions) &&
            count($questions) >= 2 &&
            count($questions) <= 3 &&
            collect($questions)->every(
                fn($item) => is_string($item) && trim($item) !== ''
            ) &&

            is_array($steps) &&
            count($steps) <= 3 &&
            collect($steps)->every(
                fn($item) => is_string($item) && trim($item) !== ''
            );
    }

    /**
     * Mengubah status refleksi menjadi failed.
     */
    private function markReflectionFailed(
        AiReflection $reflection,
        array $rawResponse = []
    ): void {
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



    /**
     * Menampilkan detail jurnal milik pengguna.
     */

    /**
     * Menampilkan detail jurnal milik pengguna.
     */
    public function show(Request $request, int $journal): View
    {
        // Ambil jurnal milik pengguna yang sedang login.
        $journal = $request->user()
            ->journals()
            ->with(['moodCheckin', 'aiReflections'])
            ->findOrFail($journal);

        // Ambil refleksi AI terbaru untuk jurnal tersebut.
        $reflection = $journal->aiReflections
            ->sortByDesc('created_at')
            ->first();

        // Kirim jurnal dan refleksi ke Blade.
        return view('journals.show', compact('journal', 'reflection'));
    }


    /**
     * Menampilkan form edit jurnal.
     */
    public function edit(Request $request, int $journal): View
    {
        $journal = $request->user()
            ->journals()
            ->findOrFail($journal);

        $moods = config('temandengar.moods');

        return view('journals.edit', compact('journal', 'moods'));
    }

    /**
     * Memperbarui jurnal.
     */

    /**
     * Memperbarui jurnal dan memperbarui refleksi AI jika diperlukan.
     */
    public function update(Request $request, int $journal): RedirectResponse
    {
        $journal = $request->user()
            ->journals()
            ->findOrFail($journal);

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:160'],
            'content' => ['required', 'string', 'min:3', 'max:20000'],
            'entry_type' => ['required', Rule::in(['free', 'guided'])],
        ], [
            'content.required' => 'Ceritamu belum diisi.',
            'content.min' => 'Ceritamu terlalu singkat.',
            'content.max' => 'Cerita maksimal 20.000 karakter.',
        ]);

        // Periksa apakah ada perubahan pada data yang diproses Hermes.
        $reflectionNeedsRefresh =
            $journal->title !== ($validated['title'] ?? null) ||
            $journal->content !== $validated['content'] ||
            $journal->entry_type !== $validated['entry_type'];

        // Simpan perubahan jurnal terlebih dahulu.
        $journal->update([
            'title' => $validated['title'] ?? null,
            'content' => $validated['content'],
            'entry_type' => $validated['entry_type'],
        ]);

        $message = 'Jurnal berhasil diperbarui.';

        // Jika data berubah, proses ulang menggunakan Hermes.
        if ($reflectionNeedsRefresh) {
            $reflectionSucceeded = $this->sendJournalToN8n(
                $journal,
                (int) $request->user()->id
            );

            $message = $reflectionSucceeded
                ? 'Jurnal berhasil diperbarui dan refleksi AI berhasil diperbarui.'
                : 'Jurnal berhasil diperbarui, tetapi refleksi AI gagal diperbarui.';
        }

        return redirect()
            ->route('journals.show', $journal->id)
            ->with('success', $message);
    }


    /**
     * Menghapus jurnal milik pengguna.
     */
    public function destroy(Request $request, int $journal): RedirectResponse
    {
        $journal = $request->user()
            ->journals()
            ->findOrFail($journal);

        $journal->delete();

        return redirect()
            ->route('journals.index')
            ->with('success', 'Jurnal berhasil dihapus.');
    }
}