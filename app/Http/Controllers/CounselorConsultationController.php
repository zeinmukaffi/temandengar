<?php

namespace App\Http\Controllers;

use App\Models\Counselor;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CounselorConsultationController extends Controller
{
    /**
     * Memastikan pengguna adalah Konselor
     * dan memiliki profil konselor yang aktif.
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
     * Dashboard awal / inbox permintaan konsultasi konselor.
     */
    public function index(Request $request): View
    {
        $counselor = $this->getCounselor($request);

        // Ringkasan jumlah permintaan.
        $totalRequests = $counselor->consultations()->count();

        $pendingRequests = $counselor->consultations()
            ->where('status', 'pending_confirmation')
            ->count();

        $acceptedRequests = $counselor->consultations()
            ->where('status', 'accepted')
            ->count();

        $completedRequests = $counselor->consultations()
            ->where('status', 'completed')
            ->count();

        // Permintaan milik konselor yang sedang login.
        // Permintaan yang menunggu konfirmasi ditampilkan lebih dahulu.
        $consultations = $counselor->consultations()
            ->with('user')
            ->orderByRaw(
                "CASE WHEN status = 'pending_confirmation' THEN 0 ELSE 1 END"
            )
            ->latest('created_at')
            ->paginate(10);

        return view('counselor.consultations.index', compact(
            'counselor',
            'totalRequests',
            'pendingRequests',
            'acceptedRequests',
            'completedRequests',
            'consultations'
        ));
    }

    /**
     * Detail permintaan konsultasi.
     * Materi jurnal dan refleksi hanya diberikan
     * kepada konselor jika Pencerita menyetujuinya.
     */
    public function show(Request $request, int $consultation): View
    {
        $counselor = $this->getCounselor($request);

        // Membatasi konsultasi berdasarkan profil konselor login.
        $consultation = $counselor->consultations()
            ->with([
                'user',
                'journal',
                'aiReflection',
            ])
            ->findOrFail($consultation);

        /*
         * Verifikasi kembali kepemilikan jurnal.
         * Jangan tampilkan jurnal jika:
         * - Tidak ada persetujuan berbagi.
         * - Materi tidak tersedia.
         * - Jurnal ternyata bukan milik Pencerita pengaju.
         */
        $sharedJournal = null;

        if (
            $consultation->share_journal &&
            $consultation->journal &&
            (int) $consultation->journal->user_id ===
            (int) $consultation->user_id
        ) {
            $sharedJournal = $consultation->journal;
        }

        /*
         * Verifikasi persetujuan dan kepemilikan AI Reflection.
         * Refleksi yang tidak disetujui tidak diberikan ke view.
         */
        $sharedReflection = null;

        if (
            $consultation->share_ai_reflection &&
            $consultation->aiReflection &&
            (int) $consultation->aiReflection->user_id ===
            (int) $consultation->user_id &&
            $consultation->aiReflection->status === 'completed'
        ) {
            $sharedReflection = $consultation->aiReflection;
        }

        return view('counselor.consultations.show', compact(
            'counselor',
            'consultation',
            'sharedJournal',
            'sharedReflection'
        ));
    }

    /**
     * Menerima permintaan konsultasi.
     */
    public function accept(
        Request $request,
        int $consultation
    ): RedirectResponse {
        return $this->updateRequestStatus(
            $request,
            $consultation,
            'accepted',
            'Permintaan konsultasi berhasil diterima.'
        );
    }

    /**
     * Menolak permintaan konsultasi.
     */
    public function reject(
        Request $request,
        int $consultation
    ): RedirectResponse {
        return $this->updateRequestStatus(
            $request,
            $consultation,
            'rejected',
            'Permintaan konsultasi telah ditolak.'
        );
    }

    /**
     * Memperbarui status permintaan.
     * Hanya permintaan pending_confirmation
     * yang boleh diproses.
     */
    private function updateRequestStatus(
        Request $request,
        int $consultation,
        string $newStatus,
        string $successMessage
    ): RedirectResponse {
        $counselor = $this->getCounselor($request);

        return DB::transaction(function () use ($counselor, $consultation, $newStatus, $successMessage) {
            // Kunci record selama proses perubahan status.
            $consultationRecord = $counselor->consultations()
                ->lockForUpdate()
                ->findOrFail($consultation);

            // Status hanya dapat diubah satu kali dari pending.
            if ($consultationRecord->status !== 'pending_confirmation') {
                return redirect()
                    ->route('counselor.consultations.show', $consultationRecord->id)
                    ->withErrors([
                        'status' => 'Permintaan ini sudah pernah diproses.'
                    ]);
            }

            $consultationRecord->update([
                'status' => $newStatus,
            ]);

            return redirect()
                ->route('counselor.consultations.show', $consultationRecord->id)
                ->with('success', $successMessage);
        });
    }


    /**
     * Menetapkan atau memperbarui jadwal final konsultasi.
     * Hanya Konselor pemilik permintaan yang boleh melakukannya.
     */
    public function schedule(
        Request $request,
        int $consultation
    ): RedirectResponse {
        $counselor = $this->getCounselor($request);

        $validated = $request->validate([
            'scheduled_at' => [
                'required',
                'date',
                'after:now',
            ],
        ], [
            'scheduled_at.required' => 'Tentukan jadwal konsultasi.',
            'scheduled_at.after' => 'Jadwal final harus berada setelah waktu sekarang.',
        ]);

        return DB::transaction(function () use ($counselor, $consultation, $validated) {
            $record = $counselor->consultations()
                ->lockForUpdate()
                ->findOrFail($consultation);

            if ($record->status !== 'accepted') {
                return back()->withErrors([
                    'status' => 'Jadwal hanya dapat ditetapkan untuk permintaan yang sudah diterima.',
                ]);
            }

            $record->update([
                'scheduled_at' => $validated['scheduled_at'],
            ]);

            return redirect()
                ->route('counselor.consultations.show', $record->id)
                ->with('success', 'Jadwal konsultasi berhasil ditetapkan.');
        });
    }

    /**
     * Menandai konsultasi telah selesai.
     */
    public function complete(
        Request $request,
        int $consultation
    ): RedirectResponse {
        $counselor = $this->getCounselor($request);

        return DB::transaction(function () use ($counselor, $consultation) {
            $record = $counselor->consultations()
                ->lockForUpdate()
                ->findOrFail($consultation);

            if (
                $record->status !== 'accepted' ||
                !$record->scheduled_at
            ) {
                return back()->withErrors([
                    'status' => 'Konsultasi harus diterima dan memiliki jadwal final terlebih dahulu.',
                ]);
            }

            if ($record->scheduled_at->isFuture()) {
                return back()->withErrors([
                    'status' => 'Konsultasi belum dapat diselesaikan karena jadwalnya belum tiba.',
                ]);
            }

            $record->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return redirect()
                ->route('counselor.consultations.show', $record->id)
                ->with('success', 'Konsultasi berhasil ditandai selesai.');
        });
    }

}
