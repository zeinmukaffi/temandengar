<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateConsultationSummaryJob;
use App\Models\Counselor;
use App\Notifications\ConsultationStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless($user->role === 'pencerita', 403);

        $allowedStatuses = [
            'pending_confirmation',
            'accepted',
            'completed',
            'cancelled',
            'rejected',
        ];

        $selectedStatus = trim((string) $request->query('status', ''));

        if (!in_array($selectedStatus, $allowedStatuses, true)) {
            $selectedStatus = '';
        }

        $statusCounts = [
            'all' => $user->consultations()->count(),
            'pending_confirmation' => $user->consultations()->where('status', 'pending_confirmation')->count(),
            'accepted' => $user->consultations()->where('status', 'accepted')->count(),
            'completed' => $user->consultations()->where('status', 'completed')->count(),
            'cancelled' => $user->consultations()->where('status', 'cancelled')->count(),
            'rejected' => $user->consultations()->where('status', 'rejected')->count(),
        ];

        $query = $user->consultations()
            ->with('counselor');

        if ($selectedStatus !== '') {
            $query->where('status', $selectedStatus);
        }

        $consultations = $query
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('consultations.index', compact(
            'consultations',
            'selectedStatus',
            'statusCounts'
        ));
    }

    public function show(Request $request, int $consultation): View
    {
        $user = $request->user();

        abort_unless($user->role === 'pencerita', 403);

        $consultation = $user->consultations()
            ->with([
                'counselor',
                'journal',
                'aiReflection',
            ])
            ->findOrFail($consultation);

        return view('consultations.show', compact('consultation'));
    }

    /**
     * Menampilkan form pengajuan konsultasi.
     */
    public function create(Request $request, Counselor $counselor): View
    {
        $user = $request->user();

        abort_unless($user->role === 'pencerita', 403);

        abort_unless(
            $counselor->is_active && $counselor->is_available,
            404
        );

        $journals = $user->journals()
            ->with([
                'aiReflections' => function ($query) {
                    $query->where('status', 'completed')
                        ->latest('created_at');
                }
            ])
            ->latest('journaled_at')
            ->get();

        return view('consultations.create', compact(
            'counselor',
            'journals'
        ));
    }

    /**
     * Menyimpan permintaan konsultasi baru dan menjadwalkan pembuatan ringkasan di latar belakang.
     */
    public function store(
        Request $request,
        Counselor $counselor
    ): RedirectResponse {
        $user = $request->user();

        abort_unless($user->role === 'pencerita', 403);

        abort_unless(
            $counselor->is_active && $counselor->is_available,
            404
        );

        $validated = $request->validate([
            'topic' => ['required', 'string', 'max:120'],
            'context' => ['nullable', 'string', 'max:10000'],

            'preferred_at' => [
                'required',
                'date',
                'after:now',
            ],

            'share_journal' => ['nullable', 'boolean'],
            'share_ai_reflection' => ['nullable', 'boolean'],

            'journal_id' => [
                'nullable',
                'integer',
                Rule::exists('journals', 'id')
                    ->where(
                        fn($query) =>
                            $query->where('user_id', $user->id)
                    ),
            ],
        ]);

        $shareJournal = $request->boolean('share_journal');
        $shareAiReflection = $request->boolean('share_ai_reflection');

        if (
            ($shareJournal || $shareAiReflection) &&
            empty($validated['journal_id'])
        ) {
            return back()
                ->withErrors([
                    'journal_id' => 'Pilih jurnal yang ingin digunakan.'
                ])
                ->withInput();
        }

        $selectedJournal = null;
        $selectedReflection = null;

        if ($shareJournal || $shareAiReflection) {
            $selectedJournal = $user->journals()
                ->findOrFail($validated['journal_id']);
        }

        if ($shareAiReflection) {
            $selectedReflection = $selectedJournal
                ->aiReflections()
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->latest('created_at')
                ->first();

            if (!$selectedReflection) {
                return back()
                    ->withErrors([
                        'share_ai_reflection' =>
                            'Jurnal ini belum memiliki refleksi AI yang selesai.'
                    ])
                    ->withInput();
            }
        }

        $sharedJournalSnapshot = null;
        $sharedAiReflectionSnapshot = null;

        if ($shareJournal && $selectedJournal) {
            $sharedJournalSnapshot = [
                'title' => $selectedJournal->title,
                'entry_type' => $selectedJournal->entry_type,
                'content' => $selectedJournal->content,
                'journaled_at' => $selectedJournal->journaled_at?->toISOString(),
            ];
        }

        if ($shareAiReflection && $selectedReflection) {
            $sharedAiReflectionSnapshot = [
                'summary' => $selectedReflection->summary,
                'emotional_insight' => $selectedReflection->emotional_insight,
                'key_concerns' => $selectedReflection->key_concerns ?? [],
                'reflection_questions' => $selectedReflection->reflection_questions ?? [],
                'suggested_steps' => $selectedReflection->suggested_steps ?? [],
                'ai_model' => $selectedReflection->ai_model,
            ];
        }

        $consultation = $user->consultations()->create([
            'counselor_id' => $counselor->id,
            'journal_id' => $shareJournal ? $selectedJournal->id : null,
            'ai_reflection_id' => $shareAiReflection ? $selectedReflection->id : null,

            'topic' => $validated['topic'],
            'context' => $validated['context'] ?? null,
            'preferred_at' => $validated['preferred_at'],

            'share_journal' => $shareJournal,
            'share_ai_reflection' => $shareAiReflection,
            'consent_at' => ($shareJournal || $shareAiReflection) ? now() : null,

            'shared_journal_snapshot' => $sharedJournalSnapshot,
            'shared_ai_reflection_snapshot' => $sharedAiReflectionSnapshot,

            'consultation_brief' => null,
            'brief_status' => 'processing',
            'status' => 'pending_confirmation',
        ]);

        GenerateConsultationSummaryJob::dispatch($consultation->id);

        if ($counselor->user) {
            $counselor->user->notify(new ConsultationStatusNotification(
                consultation: $consultation,
                title: 'Pengajuan Konsultasi Baru',
                message: "{$user->name} mengajukan konsultasi dengan topik \"{$consultation->topic}\".",
                actionUrl: route('counselor.consultations.show', $consultation->id),
                eventType: 'submitted'
            ));
        }

        return redirect()
            ->route('consultations.show', $consultation->id)
            ->with('success', 'Permintaan konsultasi berhasil dikirim. Ringkasan persiapan sedang dibuat untuk konselor.');
    }

    /**
     * Membatalkan pengajuan konsultasi oleh Pencerita.
     */
    public function cancel(Request $request, int $consultation): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->role === 'pencerita', 403);

        $validated = $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ], [
            'cancellation_reason.max' => 'Alasan pembatalan maksimal 500 karakter.',
        ]);

        return DB::transaction(function () use ($user, $consultation, $validated) {
            $record = $user->consultations()
                ->lockForUpdate()
                ->findOrFail($consultation);

            if (!in_array($record->status, ['pending_confirmation', 'accepted'], true)) {
                return back()->withErrors([
                    'status' => 'Permintaan konsultasi ini sudah tidak dapat dibatalkan.',
                ]);
            }

            if ($record->scheduled_at && $record->scheduled_at->isPast()) {
                return back()->withErrors([
                    'status' => 'Konsultasi yang jadwalnya sudah berlalu tidak dapat dibatalkan.',
                ]);
            }

            $record->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => filled($validated['cancellation_reason'] ?? null)
                    ? trim($validated['cancellation_reason'])
                    : null,
            ]);

            $record->loadMissing('counselor.user');
            if ($record->counselor?->user) {
                $record->counselor->user->notify(new ConsultationStatusNotification(
                    consultation: $record,
                    title: 'Konsultasi Dibatalkan Pencerita',
                    message: "{$user->name} membatalkan pengajuan konsultasi topik \"{$record->topic}\".",
                    actionUrl: route('counselor.consultations.show', $record->id),
                    eventType: 'cancelled'
                ));
            }

            return redirect()
                ->route('consultations.show', $record->id)
                ->with('success', 'Permintaan konsultasi berhasil dibatalkan.');
        });
    }
}