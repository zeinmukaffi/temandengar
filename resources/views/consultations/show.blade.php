@extends('layouts.app')

@section('title', 'Detail Konsultasi')

@section('content')

    @if ($errors->has('status'))
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            {{ $errors->first('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <div class="consultation-show-page">

        @php
            $statusLabels = [
                'pending_confirmation' => [
                    'label' => 'Menunggu Konfirmasi',
                    'pill' => 'warning',
                    'description' => 'Permintaan konsultasimu sedang menunggu tanggapan dari konselor.',
                ],
                'accepted' => [
                    'label' => 'Diterima',
                    'pill' => 'success',
                    'description' => 'Konselor telah menerima permintaan konsultasimu.',
                ],
                'rejected' => [
                    'label' => 'Ditolak',
                    'pill' => 'danger',
                    'description' => 'Permintaan konsultasi ini belum dapat diterima oleh konselor.',
                ],
                'completed' => [
                    'label' => 'Selesai',
                    'pill' => 'primary',
                    'description' => 'Sesi konsultasi ini telah selesai dilaksanakan.',
                ],
                'cancelled' => [
                    'label' => 'Dibatalkan',
                    'pill' => 'secondary',
                    'description' => 'Permintaan konsultasi ini telah dibatalkan.',
                ],
            ];

            $status = $statusLabels[$consultation->status] ?? [
                'label' => ucfirst($consultation->status),
                'pill' => 'secondary',
                'description' => 'Status konsultasi sedang diperbarui.',
            ];
        @endphp

        <div class="mb-4">
            <a href="{{ route('consultations.index') }}" class="td-back-link text-decoration-none">
                <span aria-hidden="true">&larr;</span>
                Kembali ke Konsultasi Saya
            </a>

            <div class="mt-3">
                <span class="td-section-eyebrow">RINGKASAN SESI</span>
                <h3 class="fw-bold mt-1 mb-1">Detail Konsultasi</h3>
                <p class="text-td-muted small mb-0">
                    Informasi lengkap pengajuan konsultasi dan materi yang dibagikan.
                </p>
            </div>
        </div>

        <div class="row g-4 align-items-start">

            {{-- Kolom Kiri: Informasi Permintaan & Materi --}}
            <div class="col-lg-8 d-flex flex-column gap-4">

                {{-- Card 1: Informasi Permintaan --}}
                <section class="td-card">

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h4 class="fw-bold mb-0">{{ $consultation->topic }}</h4>

                        <span class="td-status-pill td-status-pill--{{ $status['pill'] }}">
                            {{ $status['label'] }}
                        </span>
                    </div>

                    <div class="td-mood-callout mb-4">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--td-primary)"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 mt-1"
                            aria-hidden="true">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 16v-4" />
                            <path d="M12 8h.01" />
                        </svg>
                        <p class="small text-td-primary mb-0" style="line-height: 1.65;">
                            {{ $status['description'] }}
                        </p>
                    </div>

                    @if (filled($consultation->context))
                        <div class="mb-4">
                            <span class="td-section-eyebrow mb-2">CATATAN TAMBAHAN</span>
                            <div class="p-3 rounded-3"
                                style="background: var(--td-surface-muted); border: 1px solid var(--td-border); line-height: 1.8; font-size: 14px; white-space: pre-line;">
                                {{ $consultation->context }}</div>
                        </div>
                    @endif

                    <div class="border-top pt-4">
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <div class="text-td-muted small mb-1">Waktu yang Kamu Inginkan</div>
                                <div class="fw-semibold small">
                                    {{ \Illuminate\Support\Carbon::parse($consultation->preferred_at)->format('d F Y, H:i') }}
                                    WIB
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="text-td-muted small mb-1">Permintaan Diajukan</div>
                                <div class="fw-semibold small">
                                    {{ $consultation->created_at->format('d F Y, H:i') }} WIB
                                </div>
                            </div>
                        </div>

                        @if ($consultation->scheduled_at)
                            <div class="td-schedule-box td-schedule-box--confirmed mt-3">
                                <span class="td-status-pill td-status-pill--success mb-2">
                                    Jadwal Final Ditetapkan
                                </span>

                                <div class="fw-bold text-td-primary mb-1">
                                    {{ $consultation->scheduled_at->format('d F Y, H:i') }} WIB
                                </div>

                                <p class="text-td-muted small mb-0">
                                    Jadwal sesi konsultasi telah dikonfirmasi oleh konselor.
                                </p>

                                @if ($consultation->meeting_link)
                                    <div class="border-top mt-3 pt-3">
                                        <div class="text-td-muted small mb-1">Akses / Lokasi Pertemuan</div>

                                        @if (filter_var($consultation->meeting_link, FILTER_VALIDATE_URL))
                                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                <span class="small text-td-primary text-break">
                                                    {{ $consultation->meeting_link }}
                                                </span>

                                                @if ($consultation->status === 'accepted')
                                                    <a href="{{ $consultation->meeting_link }}" target="_blank"
                                                        rel="noopener noreferrer" class="btn btn-td btn-sm">
                                                        Gabung Sesi &nearr;
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            <div class="fw-semibold small text-dark">
                                                {{ $consultation->meeting_link }}
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if ($consultation->meeting_notes)
                                    <div class="bg-white rounded-3 p-2 px-3 border mt-3">
                                        <div class="text-td-muted" style="font-size: 11px;">Catatan dari Konselor:</div>
                                        <p class="small mb-0 text-dark" style="line-height: 1.65;">
                                            {{ $consultation->meeting_notes }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="td-schedule-box mt-3">
                                <p class="text-td-muted small mb-0">
                                    Jadwal final belum ditetapkan. Konselor akan menentukan jadwal dan tautan pertemuan
                                    setelah menerima permintaanmu.
                                </p>
                            </div>
                        @endif

                        @if ($consultation->completed_at)
                            <div class="border-top pt-3 mt-3">
                                <div class="text-td-muted small mb-1">Konsultasi Diselesaikan</div>
                                <div class="fw-semibold small">
                                    {{ $consultation->completed_at->format('d F Y, H:i') }} WIB
                                </div>

                                @if (filled($consultation->counselor_notes))
                                    <div class="td-schedule-box td-schedule-box--confirmed mt-3">
                                        <span class="td-section-eyebrow d-block mb-1">CATATAN TINDAK LANJUT KONSELOR</span>
                                        <p class="small text-dark mb-0" style="line-height: 1.8; white-space: pre-line;">
                                            {{ $consultation->counselor_notes }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if ($consultation->cancelled_at)
                            <div class="border-top pt-3 mt-3">
                                <div class="text-td-muted small mb-1">Konsultasi Dibatalkan</div>
                                <div class="fw-semibold small text-danger">
                                    {{ $consultation->cancelled_at->format('d F Y, H:i') }} WIB
                                </div>

                                @if (filled($consultation->cancellation_reason))
                                    <div class="bg-light rounded-3 p-2 px-3 border mt-2">
                                        <div class="text-td-muted" style="font-size: 11px;">Alasan Pembatalan:</div>
                                        <p class="small mb-0 text-dark" style="line-height: 1.65;">
                                            {{ $consultation->cancellation_reason }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                </section>

                {{-- Card 2: Materi yang Dibagikan --}}
                <section class="td-card">

                    <span class="td-section-eyebrow mb-1">PERSETUJUAN PRIVASI</span>
                    <h5 class="fw-bold mb-1">Informasi yang Dibagikan</h5>
                    <p class="text-td-muted small mb-4">
                        Materi di bawah ini adalah informasi yang secara eksplisit kamu setujui untuk dibagikan.
                    </p>

                    {{-- Materi Jurnal --}}
                    @if ($consultation->share_journal && $consultation->journal)
                        <div class="td-shared-material-box td-shared-material-box--active mb-3">
                            <span class="td-status-pill td-status-pill--success mb-2">
                                Jurnal Disetujui
                            </span>

                            <h6 class="fw-bold mb-2">
                                {{ $consultation->journal->title ?: 'Catatan Pribadi' }}
                            </h6>

                            <div class="text-td-muted small" style=white-space: pre-wrap; line-height: 1.85;">
                                {{ $consultation->journal->content }}</div>
                        </div>
                    @else
                        <div class="td-shared-material-box mb-3">
                            <p class="small text-td-muted mb-0">
                                Jurnal tidak dibagikan pada sesi ini.
                            </p>
                        </div>
                    @endif

                    {{-- Materi AI Reflection --}}
                    @if ($consultation->share_ai_reflection && $consultation->aiReflection)
                        <div class="td-shared-material-box td-shared-material-box--active">
                            <span class="td-status-pill td-status-pill--success mb-3">
                                Refleksi AI Disetujui
                            </span>

                            @if ($consultation->aiReflection->summary)
                                <div class="mb-3">
                                    <h6 class="fw-bold small text-td-primary mb-1">Ringkasan Cerita</h6>
                                    <p class="small mb-0" style="line-height: 1.75;">
                                        {{ $consultation->aiReflection->summary }}
                                    </p>
                                </div>
                            @endif

                            @if ($consultation->aiReflection->emotional_insight)
                                <div>
                                    <h6 class="fw-bold small text-td-primary mb-1">Insight Emosional</h6>
                                    <p class="small mb-0" style="line-height: 1.75;">
                                        {{ $consultation->aiReflection->emotional_insight }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="td-shared-material-box">
                            <p class="small text-td-muted mb-0">
                                Refleksi AI tidak dibagikan pada sesi ini.
                            </p>
                        </div>
                    @endif

                    <p class="text-td-muted small mt-3 mb-0">
                        Persetujuan berbagi materi pada permintaan ini tidak mengubah pengaturan privasi jurnal aslimu.
                    </p>

                </section>

            </div>

            {{-- Kolom Kanan: Profil Konselor --}}
            <div class="col-lg-4 d-flex flex-column gap-4">
                <aside class="td-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @if ($consultation->counselor?->photo_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($consultation->counselor->photo_path) }}"
                                alt="Foto {{ $consultation->counselor->name }}" class="td-counselor-avatar"
                                style="width: 58px; height: 58px;">
                        @else
                            <div class="td-counselor-initials" style="width: 58px; height: 58px; font-size: 1.25rem;"
                                aria-hidden="true">
                                {{ mb_strtoupper(mb_substr($consultation->counselor?->name ?? 'K', 0, 1)) }}
                            </div>
                        @endif

                        <div>
                            <span class="td-section-eyebrow">KONSELOR PENDAMPING</span>
                            <h5 class="fw-bold mb-0">
                                {{ $consultation->counselor?->name ?? 'Konselor' }}
                            </h5>
                            <p class="text-td-muted small mb-0 text-capitalize">
                                {{ $consultation->counselor?->role ?? 'Konselor Sebaya' }}
                            </p>
                        </div>
                    </div>

                    @if ($consultation->counselor?->bio)
                        <p class="text-td-muted small mb-4" style="line-height: 1.75;">
                            {{ \Illuminate\Support\Str::limit($consultation->counselor->bio, 160) }}
                        </p>
                    @endif

                    @if ($consultation->counselor_id)
                        <a href="{{ route('counselors.show', $consultation->counselor_id) }}"
                            class="btn btn-outline-td w-100">
                            Lihat Profil Konselor
                        </a>
                    @endif
                </aside>
                @php
                    $canCancel =
                        in_array($consultation->status, ['pending_confirmation', 'accepted'], true) &&
                        (!$consultation->scheduled_at || $consultation->scheduled_at->isFuture());
                @endphp

                @if ($canCancel)
                    <aside class="td-card">
                        <span class="td-section-eyebrow mb-1">KENDALI SESI</span>
                        <h6 class="fw-bold mb-2">Batalkan Pengajuan?</h6>
                        <p class="text-td-muted small mb-3" style="line-height: 1.7;">
                            Jika rencana berubah atau kamu belum siap bercerita saat ini, kamu dapat membatalkan
                            permintaan konsultasi ini sebelum sesi dimulai.
                        </p>

                        <form action="{{ route('consultations.cancel', $consultation->id) }}" method="POST"
                            data-confirm="Apakah kamu yakin ingin membatalkan permintaan konsultasi ini? Tindakan ini tidak dapat diurungkan."
                            data-confirm-title="Batalkan Konsultasi?" data-confirm-button="Ya, Batalkan"
                            data-confirm-variant="danger">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="cancellation_reason" class="form-label small fw-semibold">
                                    Alasan Pembatalan <span class="text-td-muted fw-normal">(Opsional)</span>
                                </label>
                                <textarea id="cancellation_reason" name="cancellation_reason" rows="2" maxlength="500"
                                    class="form-control @error('cancellation_reason') is-invalid @enderror"
                                    placeholder="Contoh: Ada jadwal kuliah pengganti di jam tersebut...">{{ old('cancellation_reason') }}</textarea>
                                @error('cancellation_reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                Batalkan Konsultasi
                            </button>
                        </form>
                    </aside>
                @endif
            </div>

        </div>

    </div>

@endsection
