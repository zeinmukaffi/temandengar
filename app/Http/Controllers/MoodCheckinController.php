<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class MoodCheckinController extends Controller
{
    /**
     * Menyimpan mood check-in mandiri.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mood' => [
                'required',
                Rule::in(array_keys(config('temandengar.moods'))),
            ],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        // Simpan mood sebagai milik pengguna yang login.
        $request->user()->moodCheckins()->create([
            'mood' => $validated['mood'],
            'note' => $validated['note'] ?? null,
            'checked_at' => now(),
        ]);

        return redirect()
            ->route('journals.index')
            ->with('success', 'Mood check-in berhasil disimpan.');
    }
}