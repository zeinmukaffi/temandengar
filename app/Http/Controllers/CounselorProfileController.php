<?php

namespace App\Http\Controllers;

use App\Models\Counselor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CounselorProfileController extends Controller
{
    /**
     * Daftar opsi topik pendampingan standar untuk memudahkan pilihan konselor.
     */
    private const SUGGESTED_SPECIALTIES = [
        'Akademik',
        'Manajemen Waktu',
        'Relasi Pertemanan',
        'Keluarga',
        'Pengembangan Diri',
        'Karier',
        'Adaptasi Kampus',
        'Keseimbangan Hidup',
    ];

    /**
     * Memastikan pengguna adalah Konselor aktif.
     */
    private function getCounselor(Request $request): Counselor
    {
        $user = $request->user();

        abort_unless($user && $user->role === 'konselor', 403);

        $counselor = $user->counselor;

        abort_unless($counselor && $counselor->is_active, 403);

        return $counselor;
    }

    /**
     * Menampilkan halaman pengaturan profil konselor.
     */
    public function edit(Request $request): View
    {
        $counselor = $this->getCounselor($request);
        $suggestedSpecialties = self::SUGGESTED_SPECIALTIES;

        return view('counselor.profile.edit', compact(
            'counselor',
            'suggestedSpecialties'
        ));
    }

    /**
     * Memperbarui profil, foto, bidang pendampingan, dan status ketersediaan konselor.
     */
    public function update(Request $request): RedirectResponse
    {
        $counselor = $this->getCounselor($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['required', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'availability_description' => ['nullable', 'string', 'max:1000'],
            'specialties' => ['nullable', 'array', 'max:10'],
            'specialties.*' => ['string', 'max:60'],
            'custom_specialties' => ['nullable', 'string', 'max:250'],
            'is_available' => ['nullable', 'boolean'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama tampilan wajib diisi.',
            'role.required' => 'Peran atau gelar singkat wajib diisi.',
            'photo.image' => 'File foto harus berupa gambar.',
            'photo.mimes' => 'Format foto harus JPG, PNG, atau WebP.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        // Gabungkan pilihan checkbox specialties dengan input kustom (dipisahkan koma)
        $selectedSpecialties = collect($validated['specialties'] ?? []);

        if (filled($validated['custom_specialties'] ?? null)) {
            $customItems = collect(explode(',', $validated['custom_specialties']))
                ->map(fn($item) => trim($item))
                ->filter(fn($item) => $item !== '');

            $selectedSpecialties = $selectedSpecialties->merge($customItems);
        }

        $normalizedSpecialties = $selectedSpecialties
            ->map(fn($item) => trim($item))
            ->filter(fn($item) => $item !== '')
            ->unique(fn($item) => mb_strtolower($item))
            ->take(10)
            ->values()
            ->all();

        $photoPath = $counselor->photo_path;

        // Hapus foto lama jika diminta
        if ($request->boolean('remove_photo') && $photoPath) {
            Storage::disk('public')->delete($photoPath);
            $photoPath = null;
        }

        // Unggah foto baru jika ada
        if ($request->hasFile('photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('counselors/photos', 'public');
        }

        DB::transaction(function () use ($request, $counselor, $validated, $normalizedSpecialties, $photoPath) {
            $name = trim($validated['name']);

            $counselor->update([
                'name' => $name,
                'role' => trim($validated['role']),
                'bio' => filled($validated['bio'] ?? null) ? trim($validated['bio']) : null,
                'availability_description' => filled($validated['availability_description'] ?? null)
                    ? trim($validated['availability_description'])
                    : null,
                'specialties' => $normalizedSpecialties,
                'is_available' => $request->boolean('is_available'),
                'photo_path' => $photoPath,
            ]);

            // Sinkronkan nama akun User agar konsisten di navbar
            if ($request->user()->name !== $name) {
                $request->user()->update(['name' => $name]);
            }
        });

        return redirect()
            ->route('counselor.profile.edit')
            ->with('success', 'Profil dan status ketersediaan konselor berhasil diperbarui.');
    }
}