<?php

namespace App\Http\Controllers;

use App\Models\Counselor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CounselorController extends Controller
{
    /**
     * Menampilkan direktori konselor yang aktif dan tersedia.
     */
    public function index(Request $request): View
    {
        $this->ensurePencerita($request);

        $query = Counselor::query()
            ->where('is_active', true)
            ->where('is_available', true);

        $keyword = trim((string) $request->query('q', ''));
        $selectedSpecialty = trim((string) $request->query('specialty', ''));

        if ($keyword !== '') {
            $query->where(function ($builder) use ($keyword) {
                $builder->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('bio', 'like', '%' . $keyword . '%')
                    ->orWhere('role', 'like', '%' . $keyword . '%');
            });
        }

        if ($selectedSpecialty !== '') {
            $query->whereJsonContains('specialties', $selectedSpecialty);
        }

        $counselors = $query
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        $specialties = Counselor::query()
            ->where('is_active', true)
            ->where('is_available', true)
            ->get(['specialties'])
            ->flatMap(fn (Counselor $counselor) => $counselor->specialties ?? [])
            ->filter(fn ($specialty) => is_string($specialty) && trim($specialty) !== '')
            ->unique()
            ->sort()
            ->values();

        return view('counselors.index', compact(
            'counselors',
            'specialties',
            'keyword',
            'selectedSpecialty'
        ));
    }

    /**
     * Menampilkan detail profil konselor yang aktif dan tersedia.
     */
    public function show(Request $request, int $counselor): View
    {
        $this->ensurePencerita($request);

        $counselor = Counselor::query()
            ->where('is_active', true)
            ->where('is_available', true)
            ->findOrFail($counselor);

        return view('counselors.show', compact('counselor'));
    }

    /**
     * Direktori konselor hanya ditujukan untuk role Pencerita.
     */
    private function ensurePencerita(Request $request): void
    {
        abort_unless($request->user()?->role === 'pencerita', 403);
    }
}
