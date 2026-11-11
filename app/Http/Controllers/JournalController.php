<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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
         * Mood dan jurnal disimpan dalam satu transaksi.
         * Jika salah satu proses gagal, keduanya dibatalkan.
         */
        $journal = DB::transaction(function () use ($request, $validated) {
            $user = $request->user();

            $moodCheckin = null;

            // Buat mood check-in jika pengguna memilih mood.
            if (!empty($validated['mood'])) {
                $moodCheckin = $user->moodCheckins()->create([
                    'mood' => $validated['mood'],
                    'note' => $validated['mood_note'] ?? null,
                    'checked_at' => now(),
                ]);
            }

            // Simpan jurnal dan hubungkan dengan mood check-in.
            return $user->journals()->create([
                'mood_checkin_id' => $moodCheckin?->id,
                'title' => $validated['title'] ?? null,
                'content' => $validated['content'],
                'entry_type' => $validated['entry_type'],
                'privacy' => 'private',
                'journaled_at' => now(),
            ]);
        });

        return redirect()
            ->route('journals.show', $journal->id)
            ->with('success', 'Jurnal berhasil disimpan.');
    }

    /**
     * Menampilkan detail jurnal milik pengguna.
     */
    public function show(Request $request, int $journal): View
    {
        $journal = $request->user()
            ->journals()
            ->with(['moodCheckin', 'aiReflections'])
            ->findOrFail($journal);

        return view('journals.show', compact('journal'));
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

        $journal->update([
            'title' => $validated['title'] ?? null,
            'content' => $validated['content'],
            'entry_type' => $validated['entry_type'],
        ]);

        return redirect()
            ->route('journals.show', $journal->id)
            ->with('success', 'Jurnal berhasil diperbarui.');
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