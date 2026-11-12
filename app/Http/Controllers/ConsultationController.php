<?php

namespace App\Http\Controllers;

use App\Models\Counselor;
use App\Services\ConsultationSummaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless($user->role === 'pencerita', 403);

        $consultations = $user->consultations()
            ->with('counselor')
            ->latest('created_at')
            ->paginate(10);

        return view('consultations.index', compact('consultations'));
    }

    public function show(Request $request, int $consultation): View
    {
        $user = $request->user();

        abort_unless($user->role === 'pencerita', 403);

        // Ambil konsultasi melalui relasi user agar
        // pengguna tidak bisa membuka konsultasi orang lain.
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

        // Ambil jurnal milik pengguna beserta refleksi AI terbaru
        // yang sudah selesai dibuat.
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
     * Menyimpan permintaan konsultasi baru.
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

        // Ambil persetujuan secara eksplisit.
        $shareJournal = $request->boolean('share_journal');
        $shareAiReflection = $request->boolean('share_ai_reflection');

        // Jika salah satu materi ingin dibagikan, wajib memilih jurnal acuan.
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

        // Cari refleksi terbaru dari jurnal yang sama.
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

        /*
         * Buat snapshot materi berdasarkan persetujuan Pencerita.
         * Snapshot tidak mengambil materi yang tidak disetujui.
         */

        $sharedJournalSnapshot = null;
        $sharedAiReflectionSnapshot = null;

        // Snapshot jurnal hanya jika Pencerita menyetujuinya.
        if ($shareJournal && $selectedJournal) {
            $sharedJournalSnapshot = [
                'title' => $selectedJournal->title,
                'entry_type' => $selectedJournal->entry_type,
                'content' => $selectedJournal->content,
                'journaled_at' => $selectedJournal->journaled_at?->toISOString(),
            ];
        }

        // Snapshot AI Reflection terpisah dari snapshot jurnal.
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


        // Simpan permintaan konsultasi.
        $consultation = $user->consultations()->create([
            'counselor_id' => $counselor->id,

            // Jurnal hanya disimpan sebagai materi jika disetujui.
            'journal_id' => $shareJournal
                ? $selectedJournal->id
                : null,

            // Refleksi dipilih otomatis dari jurnal yang sama.
            'ai_reflection_id' => $shareAiReflection
                ? $selectedReflection->id
                : null,

            'topic' => $validated['topic'],
            'context' => $validated['context'] ?? null,
            'preferred_at' => $validated['preferred_at'],

            'share_journal' => $shareJournal,
            'share_ai_reflection' => $shareAiReflection,

            'consent_at' => ($shareJournal || $shareAiReflection)
                ? now()
                : null,

            // Simpan snapshot materi yang telah disetujui.
            'shared_journal_snapshot' => $sharedJournalSnapshot,
            'shared_ai_reflection_snapshot' => $sharedAiReflectionSnapshot,

            'consultation_brief' => null,
            'status' => 'pending_confirmation',
        ]);



        /*
         * Setelah konsultasi tersimpan, minta Hermes
         * membuat ringkasan melalui workflow n8n.
         *
         * Kegagalan Hermes tidak membatalkan konsultasi.
         */
        $briefGenerated = app(ConsultationSummaryService::class)
            ->generate($consultation);

        // Berikan notifikasi sesuai hasil pemrosesan.
        $message = $briefGenerated
            ? 'Permintaan konsultasi berhasil dikirim. Ringkasan persiapan telah dibuat.'
            : 'Permintaan konsultasi berhasil dikirim. Ringkasan persiapan belum berhasil dibuat.';

        return redirect()
            ->route('consultations.show', $consultation->id)
            ->with('success', $message);

    }
}
