<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateJournalReflectionJob;
use App\Models\Journal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JournalController extends Controller
{
    /**
     * Menampilkan daftar jurnal milik pengguna yang sedang login.
     */
    public function index(Request $request): View
    {
        $user = $request->user();$moods = config('temandengar.moods');

        $keyword = trim((string)$request->query('q', ''));
        $selectedMood = trim((string)$request->query('mood', ''));

        if ($selectedMood !== '' && !array_key_exists($selectedMood, $moods)) {$selectedMood = '';
        }

        $query =$user->journals()
            ->with('moodCheckin');

        if ($keyword !== '') {
            $searchTerm = '\%' . mb_strtolower($keyword) . '%';

            $query->where(function ($builder) use ($searchTerm) {
                $builder->whereRaw('LOWER(COALESCE(title, "")) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(content) LIKE ?', [$searchTerm]);
            });
        }

        if ($selectedMood !== '') {$query->whereHas('moodCheckin', function ($builder) use ($selectedMood) {
                $builder->where('mood',$selectedMood);
            });
        }

        $journals =$query
            ->latest('journaled_at')
            ->paginate(8)
            ->withQueryString();

        $recentMoodCheckins =$user->moodCheckins()
            ->latest('checked_at')
            ->take(5)
            ->get();

        return view('journals.index', compact(
            'journals',
            'recentMoodCheckins',
            'moods',
            'keyword',
            'selectedMood'
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
     * Menyimpan jurnal baru dan menjadwalkan refleksi AI di latar belakang.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated =$request->validate([
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

        $journal = DB::transaction(function () use ($request,$validated) {
            $user =$request->user();

            $moodCheckin = null;

            if (!empty($validated['mood'])) {
                $moodCheckin =$user->moodCheckins()->create([
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

        $this->queueReflection($journal, (int)$request->user()->id);

        return redirect()
            ->route('journals.show', $journal->id)
            ->with('success', 'Jurnal berhasil disimpan. Hermes sedang menyiapkan refleksi untukmu.');
    }

    /**
     * Menampilkan detail jurnal milik pengguna.
     */
    public function show(Request $request, int$journal): View
    {
        $journal =$request->user()
            ->journals()
            ->with(['moodCheckin', 'aiReflections'])
            ->findOrFail($journal);

        $reflection =$journal->aiReflections
            ->sortByDesc('created_at')
            ->first();

        return view('journals.show', compact('journal', 'reflection'));
    }

    /**
     * Menampilkan form edit jurnal.
     */
    public function edit(Request $request, int$journal): View
    {
        $journal =$request->user()
            ->journals()
            ->findOrFail($journal);

        $moods = config('temandengar.moods');

        return view('journals.edit', compact('journal', 'moods'));
    }

    /**
     * Memperbarui jurnal dan menjadwalkan pembaruan refleksi AI jika diperlukan.
     */
    public function update(Request $request, int$journal): RedirectResponse
    {
        $journal =$request->user()
            ->journals()
            ->findOrFail($journal);

        $validated =$request->validate([
            'title' => ['nullable', 'string', 'max:160'],
            'content' => ['required', 'string', 'min:3', 'max:20000'],
            'entry_type' => ['required', Rule::in(['free', 'guided'])],
        ], [
            'content.required' => 'Ceritamu belum diisi.',
            'content.min' => 'Ceritamu terlalu singkat.',
            'content.max' => 'Cerita maksimal 20.000 karakter.',
        ]);

        $reflectionNeedsRefresh =
            $journal->title !== ($validated['title'] ?? null) ||
            $journal->content !==$validated['content'] ||
            $journal->entry_type !==$validated['entry_type'];

        $journal->update([
            'title' => $validated['title'] ?? null,
            'content' => $validated['content'],
            'entry_type' => $validated['entry_type'],
        ]);

        $message = 'Jurnal berhasil diperbarui.';

        if ($reflectionNeedsRefresh) {
            $this->queueReflection($journal, (int) $request->user()->id);$message = 'Jurnal berhasil diperbarui. Hermes sedang memperbarui refleksi AI.';
        }

        return redirect()
            ->route('journals.show', $journal->id)
            ->with('success', $message);
    }

    /**
     * Menghapus jurnal milik pengguna.
     */
    public function destroy(Request $request, int$journal): RedirectResponse
    {
        $journal =$request->user()
            ->journals()
            ->findOrFail($journal);

        $journal->delete();

        return redirect()
            ->route('journals.index')
            ->with('success', 'Jurnal berhasil dihapus.');
    }

    /**
     * Mencoba ulang pembuatan refleksi AI (Hermes) di latar belakang.
     */
    public function retryReflection(Request $request, int$journal): RedirectResponse
    {
        $user =$request->user();

        $journalRecord =$user->journals()
            ->with('aiReflections')
            ->findOrFail($journal);

        $latestReflection =$journalRecord->aiReflections
            ->sortByDesc('created_at')
            ->first();

        if ($latestReflection &&$latestReflection->status === 'completed') {
            return redirect()
                ->route('journals.show', $journalRecord->id)
                ->with('success', 'Refleksi AI untuk jurnal ini sudah tersedia.');
        }

        $this->queueReflection($journalRecord, (int)$user->id);

        return redirect()
            ->route('journals.show', $journalRecord->id)
            ->with('success', 'Pembuatan ulang refleksi AI sedang diproses di latar belakang.');
    }

    /**
     * Menyimpan umpan balik Pencerita terhadap hasil refleksi AI.
     */
    public function feedbackReflection(Request $request, int$journal): RedirectResponse
    {
        $user =$request->user();

        $validated =$request->validate([
            'feedback' => ['required', Rule::in(['helpful', 'not_helpful'])],
        ]);

        $journalRecord =$user->journals()
            ->with('aiReflections')
            ->findOrFail($journal);

        $latestReflection =$journalRecord->aiReflections
            ->sortByDesc('created_at')
            ->first();

        if (!$latestReflection || $latestReflection->status !== 'completed') {
            return redirect()
                ->route('journals.show', $journalRecord->id)
                ->with('error', 'Umpan balik hanya dapat diberikan pada refleksi yang sudah selesai.');
        }

        $latestReflection->update([
            'user_feedback' => $validated['feedback'],
        ]);

        return redirect()
            ->route('journals.show', $journalRecord->id)
            ->with('success', 'Terima kasih atas umpan balikmu terhadap refleksi AI.');
    }

    /**
     * Endpoint JSON untuk mengecek status pembuatan refleksi AI.
     */
    public function reflectionStatus(Request $request, int$journal): JsonResponse
    {
        $journalRecord =$request->user()
            ->journals()
            ->with('aiReflections')
            ->findOrFail($journal);

        $reflection =$journalRecord->aiReflections
            ->sortByDesc('created_at')
            ->first();

        return response()->json([
            'status' => $reflection?->status ?? 'pending',
        ]);
    }

    /**
     * Menyiapkan record status pending dan mengirim job refleksi ke queue.
     */
    private function queueReflection(Journal $journal, int$userId): void
    {
        $journal->aiReflections()->updateOrCreate(
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

        GenerateJournalReflectionJob::dispatch($journal->id,$userId);
    }
}