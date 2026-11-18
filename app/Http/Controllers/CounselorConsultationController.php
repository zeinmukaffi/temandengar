<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateConsultationSummaryJob;
use App\Models\Counselor;
use App\Notifications\ConsultationStatusNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function index(Request $request): View
    {
        $counselor = $this->getCounselor($request);

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

        $keyword = trim((string) $request->query('q', ''));

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

        $cancelledRequests = $counselor->consultations()
            ->where('status', 'cancelled')
            ->count();

        $rejectedRequests = $counselor->consultations()
            ->where('status', 'rejected')
            ->count();

        $query = $counselor->consultations()
            ->with('user');

        if ($selectedStatus !== '') {
            $query->where('status', $selectedStatus);
        }

        if ($keyword !== '') {
            $query->where(function ($builder) use ($keyword) {
                $builder->where('topic', 'like', '%' . $keyword . '%')
                    ->orWhereHas('user', function ($userQuery) use ($keyword) {
                        $userQuery->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        $consultations = $query
            ->orderByRaw(
                "CASE WHEN status = 'pending_confirmation' THEN 0 ELSE 1 END"
            )
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('counselor.consultations.index', compact(
            'counselor',
            'totalRequests',
            'pendingRequests',
            'acceptedRequests',
            'completedRequests',
            'cancelledRequests',
            'rejectedRequests',
            'selectedStatus',
            'keyword',
            'consultations'
        ));
    }

    /**
     * Detail permintaan konsultasi.
     * Materi hanya diambil dari snapshot yang telah disetujui Pencerita.
     */
    public function show(Request $request, int $consultation): View
    {
        $counselor = $this->getCounselor($request);

        $consultation = $counselor->consultations()
            ->with('user')
            ->findOrFail($consultation);

        $sharedJournal = (
            $consultation->share_journal &&
            is_array($consultation->shared_journal_snapshot)
        )
            ? $consultation->shared_journal_snapshot
            : null;

        $sharedReflection = (
            $consultation->share_ai_reflection &&
            is_array($consultation->shared_ai_reflection_snapshot)
        )
            ? $consultation->shared_ai_reflection_snapshot
            : null;

        return view('counselor.consultations.show', compact(
            'counselor',
            'consultation',
            'sharedJournal',
            'sharedReflection'
        ));
    }

    /**
     * Endpoint JSON untuk mengecek status pembuatan ringkasan konsultasi.
     */
    public function briefStatus(Request $request, int $consultation): JsonResponse
    {
        $counselor = $this->getCounselor($request);
        $record = $counselor->consultations()->findOrFail($consultation);

        return response()->json([
            'brief_status' => $record->brief_status,
        ]);
    }

    /**
     * Mencoba ulang pembuatan ringkasan konsultasi oleh Nala AI di latar belakang.
     */
    public function retryBrief(
        Request $request,
        int $consultation
    ): RedirectResponse {
        $counselor = $this->getCounselor($request);

        $record = $counselor->consultations()
            ->findOrFail($consultation);

        $claimed = $counselor->consultations()
            ->whereKey($record->id)
            ->where('brief_status', 'failed')
            ->update([
                'brief_status' => 'processing',
                'brief_error' => null,
            ]);

        if ($claimed !== 1) {
            return redirect()
                ->route('counselor.consultations.show', $record->id)
                ->withErrors([
                    'brief' => 'Ringkasan sedang diproses, sudah tersedia, atau tidak dapat dicoba ulang.',
                ]);
        }

        GenerateConsultationSummaryJob::dispatch($record->id);

        return redirect()
            ->route('counselor.consultations.show', $record->id)
            ->with('success', 'Ringkasan persiapan konsultasi sedang dibuat ulang di latar belakang.');
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
     */
    private function updateRequestStatus(
        Request $request,
        int $consultation,
        string $newStatus,
        string $successMessage
    ): RedirectResponse {
        $counselor = $this->getCounselor($request);

        $consultationRecord = DB::transaction(function () use ($counselor, $consultation, $newStatus) {
            $consultationRecord = $counselor->consultations()
                ->lockForUpdate()
                ->findOrFail($consultation);

            if ($consultationRecord->status !== 'pending_confirmation') {
                return null;
            }

            $consultationRecord->update([
                'status' => $newStatus,
            ]);

            $consultationRecord->loadMissing('user');
            if ($consultationRecord->user) {
                $isAccepted = $newStatus === 'accepted';
                $consultationRecord->user->notify(new ConsultationStatusNotification(
                    consultation: $consultationRecord,
                    title: $isAccepted ? 'Pengajuan Konsultasi Diterima' : 'Pengajuan Konsultasi Ditolak',
                    message: $isAccepted
                    ? "{$counselor->name} menerima pengajuan konsultasimu (\"{$consultationRecord->topic}\") dan akan segera menetapkan jadwal."
                    : "{$counselor->name} belum dapat menerima pengajuan konsultasimu untuk topik \"{$consultationRecord->topic}\".",
                    actionUrl: route('consultations.show', $consultationRecord->id),
                    eventType: $newStatus
                ));
            }

            return $consultationRecord;
        });

        if (!$consultationRecord) {
            return redirect()
                ->route('counselor.consultations.show', $consultation)
                ->withErrors(['status' => 'Permintaan ini sudah pernah diproses.']);
        }

        $isAccepted = $newStatus === 'accepted';
        app(\App\Services\N8nWebhookService::class)->sendConsultationEvent(
            consultation: $consultationRecord,
            eventType: $newStatus,
            title: $isAccepted ? 'Pengajuan Konsultasi Diterima' : 'Pengajuan Konsultasi Ditolak',
            message: $isAccepted
            ? "{$counselor->name} menerima pengajuan konsultasi topik \"{$consultationRecord->topic}\"."
            : "{$counselor->name} menolak pengajuan konsultasi topik \"{$consultationRecord->topic}\".",
            actionUrl: route('consultations.show', $consultationRecord->id)
        );

        return redirect()
            ->route('counselor.consultations.show', $consultationRecord->id)
            ->with('success', $successMessage);
    }

    /**
     * Menetapkan atau memperbarui jadwal final serta media pertemuan konsultasi.
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
            'meeting_link' => [
                'required',
                'string',
                'max:500',
            ],
            'meeting_notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'scheduled_at.required' => 'Tentukan jadwal konsultasi.',
            'scheduled_at.after' => 'Jadwal final harus berada setelah waktu sekarang.',
            'meeting_link.required' => 'Sertakan tautan meeting (Google Meet/Zoom) atau lokasi pertemuan.',
            'meeting_link.max' => 'Tautan atau lokasi pertemuan maksimal 500 karakter.',
            'meeting_notes.max' => 'Catatan pertemuan maksimal 1.000 karakter.',
        ]);

        $result = DB::transaction(function () use ($counselor, $consultation, $validated) {
            $record = $counselor->consultations()
                ->lockForUpdate()
                ->findOrFail($consultation);

            if ($record->status !== 'accepted') {
                return back()->withErrors([
                    'status' => 'Jadwal hanya dapat ditetapkan untuk permintaan yang sudah diterima.',
                ]);
            }

            $isReschedule = $record->scheduled_at !== null;

            $record->update([
                'scheduled_at' => $validated['scheduled_at'],
                'meeting_link' => trim($validated['meeting_link']),
                'meeting_notes' => filled($validated['meeting_notes'] ?? null)
                    ? trim($validated['meeting_notes'])
                    : null,
            ]);

            $record->loadMissing('user');
            if ($record->user) {
                $formattedSchedule = $record->scheduled_at->format('d M Y, H:i') . ' WIB';
                $consultationLabel = $isReschedule ? 'Jadwal Konsultasi Diperbarui' : 'Jadwal Konsultasi Ditetapkan';

                $record->user->notify(new ConsultationStatusNotification(
                    consultation: $record,
                    title: $consultationLabel,
                    message: "{$counselor->name} menetapkan jadwal sesi \"{$record->topic}\" pada {$formattedSchedule}.",
                    actionUrl: route('consultations.show', $record->id),
                    eventType: 'scheduled'
                ));
            }

            return [
                'record' => $record,
                'is_reschedule' => $isReschedule,
            ];
        });

        $record = $result['record'];
        $formattedSchedule = $record->scheduled_at->format('d M Y, H:i') . ' WIB';
        $consultationLabel = $result['is_reschedule'] ? 'Jadwal Konsultasi Diperbarui' : 'Jadwal Konsultasi Ditetapkan';

        app(\App\Services\N8nWebhookService::class)->sendConsultationEvent(
            consultation: $record,
            eventType: $result['is_reschedule'] ? 'rescheduled' : 'scheduled',
            title: $consultationLabel,
            message: "{$counselor->name} menetapkan jadwal sesi \"{$record->topic}\" pada {$formattedSchedule}.",
            actionUrl: route('consultations.show', $record->id)
        );

        return redirect()
            ->route('counselor.consultations.show', $record->id)
            ->with('success', 'Jadwal dan informasi pertemuan konsultasi berhasil disimpan.');
    }

    /**
     * Menandai konsultasi telah selesai beserta catatan pasca-sesi opsional.
     */
    public function complete(
        Request $request,
        int $consultation
    ): RedirectResponse {
        $counselor = $this->getCounselor($request);

        $validated = $request->validate([
            'counselor_notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'counselor_notes.max' => 'Catatan pasca-sesi maksimal 2.000 karakter.',
        ]);

        $record = DB::transaction(function () use ($counselor, $consultation, $validated) {
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
                'counselor_notes' => filled($validated['counselor_notes'] ?? null)
                    ? trim($validated['counselor_notes'])
                    : null,
            ]);

            $record->loadMissing('user');
            if ($record->user) {
                $record->user->notify(new ConsultationStatusNotification(
                    consultation: $record,
                    title: 'Sesi Konsultasi Selesai',
                    message: "{$counselor->name} telah menandai sesi \"{$record->topic}\" selesai."
                    . (filled($record->counselor_notes) ? ' Catatan tindak lanjut tersedia.' : ''),
                    actionUrl: route('consultations.show', $record->id),
                    eventType: 'completed'
                ));
            }

            return $record;
        });

        app(\App\Services\N8nWebhookService::class)->sendConsultationEvent(
            consultation: $record,
            eventType: 'completed',
            title: 'Sesi Konsultasi Selesai',
            message: "{$counselor->name} telah menandai sesi \"{$record->topic}\" selesai.",
            actionUrl: route('counselor.consultations.show', $record->id)
        );

        return redirect()
            ->route('counselor.consultations.show', $record->id)
            ->with('success', 'Konsultasi berhasil ditandai selesai.');
    }
}