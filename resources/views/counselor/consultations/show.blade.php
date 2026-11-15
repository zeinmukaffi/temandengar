@extends('layouts.app')

@section('title', 'Detail Permintaan Konsultasi')

@section('content')

    @php
        $statusLabels = [
            'pending_confirmation' => [
                'label' => 'Menunggu Konfirmasi',
                'pill' => 'warning',
                'description' => 'Permintaan ini menunggu keputusanmu untuk diterima atau ditolak.',
            ],
            'accepted' => [
                'label' => 'Diterima',
                'pill' => 'success',
                'description' =>
                    'Kamu sudah menerima permintaan konsultasi ini. Pastikan jadwal final telah ditetapkan.',
            ],
            'rejected' => [
                'label' => 'Ditolak',
                'pill' => 'danger',
                'description' => 'Permintaan konsultasi ini telah ditolak.',
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

    {{-- Header --}}
    <div class="mb-4">
        <a href="{{ route('counselor.consultations.index') }}" class="td-back-link text-decoration-none">
            <span aria-hidden="true">&larr;</span>
            Kembali ke Permintaan Konsultasi
        </a>

        <div class="mt-3">
            <span class="td-section-eyebrow">TINJAUAN SESI</span>
            <h3 class="fw-bold mt-1 mb-1">Detail Permintaan</h3>
            <p class="text-td-muted mb-0">
                Baca informasi yang disampaikan Pencerita sebelum menentukan keputusan.
            </p>
        </div>
    </div>

    @if ($errors->has('status'))
        <div class="alert alert-warning alert-dismissible fade show">
            {{ $errors->first('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->has('brief'))
        <div class="alert alert-warning alert-dismissible fade show">
            {{ $errors->first('brief') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4 consultation-detail-layout align-items-start">

        {{-- Kolom Kiri: Detail Permintaan, Materi & Hermes Brief --}}
        <div class="col-lg-8 d-flex flex-column gap-4">

            {{-- Card 1: Informasi Permintaan --}}
            <section class="td-card consultation-compact-card">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
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
                        <span class="td-section-eyebrow mb-2">CATATAN TAMBAHAN DARI PENCERITA</span>
                        <div class="p-3 rounded-3"
                            style="background: var(--td-surface-muted); border: 1px solid var(--td-border); line-height: 1.8; font-size: 14px; white-space: pre-line;">
                            {{ $consultation->context }}</div>
                    </div>
                @endif

                <div class="border-top pt-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="text-td-muted small mb-1">Waktu konsultasi yang diinginkan</div>
                            <div class="fw-semibold small">
                                {{ \Illuminate\Support\Carbon::parse($consultation->preferred_at)->format('d F Y, H:i') }}
                                WIB
                            </div>
                            <small class="text-td-muted" style="font-size: 11px;">
                                Preferensi awal pencerita sebelum jadwal final ditetapkan.
                            </small>
                        </div>

                        <div class="col-sm-6">
                            <div class="text-td-muted small mb-1">Permintaan diajukan</div>
                            <div class="fw-semibold small">
                                {{ $consultation->created_at->format('d F Y, H:i') }} WIB
                            </div>
                        </div>

                        @if ($consultation->cancelled_at)
                            <div class="col-12 border-top pt-3 mt-3">
                                <div class="text-td-muted small mb-1">Dibatalkan oleh Pencerita pada</div>
                                <div class="fw-semibold small text-danger">
                                    {{ $consultation->cancelled_at->format('d F Y, H:i') }} WIB
                                </div>

                                @if (filled($consultation->cancellation_reason))
                                    <div class="p-3 rounded-3 mt-2"
                                        style="background: var(--td-surface-muted); border: 1px solid var(--td-border);">
                                        <div class="text-td-muted mb-1" style="font-size: 11px;">
                                            Alasan Pembatalan dari Pencerita:
                                        </div>
                                        <p class="small mb-0 text-dark" style="line-height: 1.65;">
                                            {{ $consultation->cancellation_reason }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            {{-- Card 2: Materi yang Dibagikan --}}
            <section class="td-card consultation-compact-card">
                <span class="td-section-eyebrow mb-1">PERSETUJUAN PENCERITA</span>
                <h5 class="fw-bold mb-1">Materi yang Dibagikan</h5>
                <p class="text-td-muted small mb-4">
                    Bagian ini hanya menampilkan materi yang telah disetujui secara eksplisit oleh Pencerita.
                </p>

                {{-- Jurnal yang disetujui --}}
                @if ($sharedJournal)
                    <div class="td-shared-material-box td-shared-material-box--active mb-3">
                        <span class="td-status-pill td-status-pill--success mb-2">
                            Jurnal Diizinkan untuk Dibagikan
                        </span>

                        <h6 class="fw-bold mb-2">
                            {{ $sharedJournal['title'] ?? 'Catatan Pribadi' }}
                        </h6>

                        <div class="text-td-muted small" style="line-height: 1.85;">
                            {{ $sharedJournal['content'] ?? '' }}</div>
                    </div>
                @else
                    <div class="td-shared-material-box mb-3">
                        <p class="text-td-muted small mb-0">
                            @if ($consultation->share_journal)
                                Snapshot jurnal tidak tersedia untuk permintaan ini.
                            @else
                                Pencerita tidak memberikan izin untuk membagikan isi jurnal.
                            @endif
                        </p>
                    </div>
                @endif

                {{-- AI Reflection yang disetujui --}}
                @if ($sharedReflection)
                    <div class="td-shared-material-box td-shared-material-box--active">
                        <span class="td-status-pill td-status-pill--success mb-3">
                            AI Reflection Diizinkan untuk Dibagikan
                        </span>

                        @if ($sharedReflection['summary'] ?? '')
                            <section class="mb-3">
                                <h6 class="fw-bold small text-td-primary mb-1">Ringkasan Cerita</h6>
                                <p class="small mb-0" style="line-height: 1.75;">
                                    {{ $sharedReflection['summary'] ?? '' }}
                                </p>
                            </section>
                        @endif

                        @if ($sharedReflection['emotional_insight'] ?? '')
                            <section class="border-top pt-3 mb-3">
                                <h6 class="fw-bold small text-td-primary mb-1">Insight Emosional</h6>
                                <p class="small mb-0" style="line-height: 1.75;">
                                    {{ $sharedReflection['emotional_insight'] ?? '' }}
                                </p>
                            </section>
                        @endif

                        @if (!empty($sharedReflection['key_concerns'] ?? []))
                            <section class="border-top pt-3 mb-3">
                                <h6 class="fw-bold small text-td-primary mb-1">Hal yang Menjadi Perhatian</h6>
                                <ul class="small ps-3 mb-0" style="line-height: 1.75;">
                                    @foreach ($sharedReflection['key_concerns'] ?? [] as $concern)
                                        <li class="mb-1">{{ $concern }}</li>
                                    @endforeach
                                </ul>
                            </section>
                        @endif

                        @if (!empty($sharedReflection['reflection_questions'] ?? []))
                            <section class="border-top pt-3 mb-3">
                                <h6 class="fw-bold small text-td-primary mb-1">Pertanyaan Refleksi</h6>
                                <ol class="small ps-3 mb-0" style="line-height: 1.75;">
                                    @foreach ($sharedReflection['reflection_questions'] ?? [] as $question)
                                        <li class="mb-1">{{ $question }}</li>
                                    @endforeach
                                </ol>
                            </section>
                        @endif

                        @if (!empty($sharedReflection['suggested_steps'] ?? []))
                            <section class="border-top pt-3">
                                <h6 class="fw-bold small text-td-primary mb-1">Langkah yang Bisa Dicoba</h6>
                                <ul class="small ps-3 mb-0" style="line-height: 1.75;">
                                    @foreach ($sharedReflection['suggested_steps'] ?? [] as $step)
                                        <li class="mb-1">{{ $step }}</li>
                                    @endforeach
                                </ul>
                            </section>
                        @endif
                    </div>
                @else
                    <div class="td-shared-material-box">
                        <p class="text-td-muted small mb-0">
                            @if ($consultation->share_ai_reflection)
                                Snapshot AI Reflection tidak tersedia untuk permintaan ini.
                            @else
                                Pencerita tidak memberikan izin untuk membagikan AI Reflection.
                            @endif
                        </p>
                    </div>
                @endif
            </section>

            {{-- Card 3: Ringkasan Persiapan Konsultasi dari Hermes --}}
            <section class="td-card consultation-compact-card td-brief-card">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="feature-icon flex-shrink-0" style="width: 42px; height: 42px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
                            <path d="M19 3v4" />
                            <path d="M21 5h-4" />
                        </svg>
                    </div>
                    <div>
                        <span class="td-section-eyebrow">HERMES BRIEF</span>
                        <h5 class="fw-bold mb-0">Ringkasan Persiapan Konsultasi</h5>
                    </div>
                </div>

                @if ($consultation->brief_status === 'completed' && filled($consultation->consultation_brief))
                    <div class="bg-white border rounded-3 p-3"
                        style="line-height: 1.85; font-size: 14px; white-space: pre-line;">
                        {{ $consultation->consultation_brief }}</div>

                    @if ($consultation->brief_generated_at)
                        <small class="text-td-muted d-block mt-2" style="font-size: 11px;">
                            Dibuat pada {{ $consultation->brief_generated_at->format('d F Y, H:i') }} WIB
                        </small>
                    @endif
                @elseif (in_array($consultation->brief_status, ['pending', 'processing']))
                    <div class="bg-white border rounded-3 p-3">
                        <p class="text-td-muted small mb-0">
                            Ringkasan persiapan konsultasi sedang dibuat oleh Hermes. Silakan muat ulang beberapa saat lagi.
                        </p>
                    </div>
                @elseif ($consultation->brief_status === 'failed')
                    <div class="bg-white border rounded-3 p-3">
                        <p class="text-td-muted small mb-3">
                            Ringkasan persiapan belum berhasil dibuat. Kamu masih dapat menggunakan informasi permintaan
                            dan materi yang telah dibagikan untuk mempersiapkan konsultasi.
                        </p>

                        <form id="briefRetryForm"
                            action="{{ route('counselor.consultations.brief.retry', $consultation->id) }}" method="POST"
                            data-confirm="Apakah kamu ingin mencoba membuat ulang ringkasan konsultasi?"
                            data-confirm-title="Buat Ulang Ringkasan?" data-confirm-button="Ya, Buat Ulang"
                            data-confirm-variant="primary">
                            @csrf
                            @method('PATCH')

                            <button type="submit" id="retryBriefButton" class="btn btn-outline-td btn-sm w-100">
                                Coba Buat Ulang Ringkasan
                            </button>
                        </form>
                    </div>
                @else
                    <div class="bg-white border rounded-3 p-3">
                        <p class="text-td-muted small mb-0">
                            Ringkasan persiapan belum tersedia.
                        </p>
                    </div>
                @endif
            </section>

        </div>

        {{-- Kolom Kanan: Profil Pencerita & Panel Keputusan --}}
        <div class="col-lg-4 d-flex flex-column gap-4">

            {{-- Card Profil Pencerita --}}
            <aside class="td-card consultation-compact-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="td-counselor-initials" style="width: 54px; height: 54px; font-size: 1.2rem;"
                        aria-hidden="true">
                        {{ mb_strtoupper(mb_substr($consultation->user?->name ?? 'P', 0, 1)) }}
                    </div>

                    <div>
                        <span class="td-section-eyebrow">PENCERITA</span>
                        <h5 class="fw-bold mb-0">
                            {{ $consultation->user?->name ?? 'Pencerita' }}
                        </h5>
                        <p class="text-td-muted small mb-0">
                            Mahasiswa TemanDengar
                        </p>
                    </div>
                </div>
            </aside>

            {{-- Card Keputusan / Jadwal --}}
            @if ($consultation->status === 'pending_confirmation')
                <aside class="td-card consultation-compact-card">
                    <span class="td-section-eyebrow mb-1">TINDAKAN KONSELOR</span>
                    <h5 class="fw-bold mb-2">Keputusan Konsultasi</h5>

                    <p class="text-td-muted small mb-4" style="line-height: 1.75;">
                        Tinjau permintaan dan materi yang dibagikan sebelum menerima atau menolaknya.
                    </p>

                    <form action="{{ route('counselor.consultations.accept', $consultation->id) }}" method="POST"
                        class="mb-2" data-confirm="Apakah kamu yakin ingin menerima permintaan konsultasi ini?"
                        data-confirm-title="Terima Permintaan?" data-confirm-button="Ya, Terima"
                        data-confirm-variant="success">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="btn btn-td w-100">
                            Terima Permintaan
                        </button>
                    </form>

                    <form action="{{ route('counselor.consultations.reject', $consultation->id) }}" method="POST"
                        data-confirm="Apakah kamu yakin ingin menolak permintaan konsultasi ini?"
                        data-confirm-title="Tolak Permintaan?" data-confirm-button="Ya, Tolak"
                        data-confirm-variant="danger">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="btn btn-outline-danger w-100">
                            Tolak Permintaan
                        </button>
                    </form>
                </aside>
            @elseif ($consultation->status === 'accepted')
                <aside class="td-card consultation-compact-card">
                    <span class="td-section-eyebrow mb-1">PENJADWALAN</span>
                    <h5 class="fw-bold mb-2">Jadwal &amp; Ruang Konsultasi</h5>

                    <p class="text-td-muted small mb-3" style="line-height: 1.75;">
                        Tetapkan waktu final serta tautan meeting (Google Meet/Zoom) atau lokasi pertemuan agar
                        Pencerita dapat bergabung sesuai jadwal.
                    </p>

                    @if ($consultation->scheduled_at)
                        <div class="td-schedule-box td-schedule-box--confirmed mb-3">
                            <div class="text-td-muted mb-1" style="font-size: 11px;">
                                Jadwal Final Saat Ini
                            </div>
                            <div class="fw-bold text-td-primary mb-2">
                                {{ $consultation->scheduled_at->format('d F Y, H:i') }} WIB
                            </div>

                            @if ($consultation->meeting_link)
                                <div class="border-top pt-2 mt-2">
                                    <div class="text-td-muted" style="font-size: 11px;">Media / Lokasi:</div>
                                    @if (filter_var($consultation->meeting_link, FILTER_VALIDATE_URL))
                                        <a href="{{ $consultation->meeting_link }}" target="_blank"
                                            rel="noopener noreferrer"
                                            class="small fw-semibold text-td-primary text-break">
                                            {{ $consultation->meeting_link }} &nearr;
                                        </a>
                                    @else
                                        <div class="small fw-semibold text-dark text-break">
                                            {{ $consultation->meeting_link }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @if ($consultation->meeting_notes)
                                <div class="text-td-muted small mt-2 pt-2 border-top" style="font-size: 12px;">
                                    Catatan: {{ $consultation->meeting_notes }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="td-schedule-box mb-3">
                            <p class="text-td-muted small mb-0">
                                Jadwal final dan akses pertemuan belum ditetapkan.
                            </p>
                        </div>
                    @endif

                    <form action="{{ route('counselor.consultations.schedule', $consultation->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="scheduled_at" class="form-label small fw-semibold">
                                {{ $consultation->scheduled_at ? 'Perbarui Jadwal Final' : 'Tetapkan Jadwal Final' }}
                            </label>

                            <input type="datetime-local" id="scheduled_at" name="scheduled_at"
                                class="form-control @error('scheduled_at') is-invalid @enderror"
                                min="{{ now()->format('Y-m-d\TH:i') }}"
                                value="{{ old('scheduled_at', $consultation->scheduled_at?->format('Y-m-d\TH:i')) }}"
                                required>

                            @error('scheduled_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="meeting_link" class="form-label small fw-semibold">
                                Tautan Meeting atau Lokasi Pertemuan
                            </label>

                            <input type="text" id="meeting_link" name="meeting_link"
                                class="form-control @error('meeting_link') is-invalid @enderror"
                                placeholder="https://meet.google.com/xxx-xxxx-xxx atau Ruang Konseling Gedung B"
                                value="{{ old('meeting_link', $consultation->meeting_link) }}" required>

                            @error('meeting_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="meeting_notes" class="form-label small fw-semibold">
                                Catatan Pertemuan <span class="text-td-muted fw-normal">(Opsional)</span>
                            </label>

                            <textarea id="meeting_notes" name="meeting_notes" rows="2" maxlength="1000"
                                class="form-control @error('meeting_notes') is-invalid @enderror"
                                placeholder="Contoh: Silakan bergabung 5 menit lebih awal atau hubungi jika terkendala...">{{ old('meeting_notes', $consultation->meeting_notes) }}</textarea>

                            @error('meeting_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @error('status')
                            <div class="text-danger small mb-2">{{ $message }}</div>
                        @enderror

                        <button type="submit" class="btn btn-td w-100">
                            {{ $consultation->scheduled_at ? 'Simpan Perubahan Jadwal' : 'Tetapkan Jadwal & Akses Sesi' }}
                        </button>
                    </form>

                    @if ($consultation->scheduled_at && $consultation->scheduled_at->isPast())
                        <hr class="my-4">

                        <h6 class="fw-bold mb-2">Penyelesaian Konsultasi</h6>
                        <p class="text-td-muted small mb-3">
                            Tandai konsultasi selesai setelah sesi benar-benar terlaksana. Kamu juga dapat
                            meninggalkan catatan tindak lanjut untuk Pencerita.
                        </p>

                        <form action="{{ route('counselor.consultations.complete', $consultation->id) }}" method="POST"
                            data-confirm="Pastikan sesi konsultasi sudah benar-benar selesai dan catatanmu sudah sesuai. Lanjutkan?"
                            data-confirm-title="Selesaikan Konsultasi?" data-confirm-button="Ya, Selesaikan"
                            data-confirm-variant="success">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="counselor_notes" class="form-label small fw-semibold">
                                    Catatan Tindak Lanjut <span class="text-td-muted fw-normal">(Opsional)</span>
                                </label>
                                <textarea id="counselor_notes" name="counselor_notes" rows="3" maxlength="2000"
                                    class="form-control @error('counselor_notes') is-invalid @enderror"
                                    placeholder="Tuliskan poin diskusi penting atau langkah kecil yang bisa dicoba Pencerita setelah sesi ini...">{{ old('counselor_notes') }}</textarea>
                                @error('counselor_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-success w-100">
                                Tandai Selesai
                            </button>
                        </form>
                    @endif
                </aside>
            @else
                <aside class="td-card consultation-compact-card">
                    <span class="td-section-eyebrow mb-1">STATUS AKHIR</span>
                    <h6 class="fw-bold mb-2">Keputusan Sudah Diproses</h6>
                    <p class="text-td-muted small mb-0">
                        Permintaan ini sudah berstatus final dan tidak dapat diubah melalui halaman ini.
                    </p>

                    @if ($consultation->completed_at)
                        <div class="border-top pt-3 mt-3">
                            <div class="text-td-muted small mb-1">Diselesaikan pada</div>
                            <div class="fw-semibold small text-td-primary">
                                {{ $consultation->completed_at->format('d F Y, H:i') }} WIB
                            </div>
                        </div>
                    @endif

                    @if (filled($consultation->counselor_notes))
                        <div class="border-top pt-3 mt-3">
                            <div class="text-td-muted small mb-1">Catatan Tindak Lanjut yang Dikirim</div>
                            <div class="p-3 rounded-3 small"
                                style="background: var(--td-surface-muted); border: 1px solid var(--td-border); line-height: 1.75; white-space: pre-line;">
                                {{ $consultation->counselor_notes }}</div>
                        </div>
                    @endif
                </aside>
            @endif

        </div>

    </div>

    {{-- Loading overlay saat Hermes membuat ulang ringkasan --}}
    <div id="briefRetryLoading" class="td-journal-loading" aria-hidden="true">
        <div class="td-journal-loading__dialog" role="status" aria-live="polite" aria-atomic="true">
            <div class="td-journal-loading__spinner" aria-hidden="true"></div>
            <h5 class="fw-bold mb-2">Membuat Ulang Ringkasan</h5>
            <p class="text-td-muted small mb-0">
                Hermes sedang menyiapkan ringkasan konsultasi.
                Proses ini mungkin memerlukan beberapa saat. Jangan tutup halaman ini.
            </p>
        </div>
    </div>

    @push('scripts')
        <script>
            (() => {
                const form = document.getElementById('briefRetryForm');
                const overlay = document.getElementById('briefRetryLoading');
                const button = document.getElementById('retryBriefButton');

                if (!form || !overlay || !button) return;

                let isSubmitting = false;
                const originalButtonText = button.textContent.trim();

                form.addEventListener('submit', (event) => {
                    if (isSubmitting) {
                        event.preventDefault();
                        return;
                    }

                    isSubmitting = true;
                    overlay.classList.add('is-visible');
                    overlay.setAttribute('aria-hidden', 'false');

                    button.disabled = true;
                    button.setAttribute('aria-disabled', 'true');
                    button.textContent = 'Sedang Membuat Ringkasan...';
                });

                window.addEventListener('pageshow', () => {
                    isSubmitting = false;
                    overlay.classList.remove('is-visible');
                    overlay.setAttribute('aria-hidden', 'true');

                    button.disabled = false;
                    button.removeAttribute('aria-disabled');
                    button.textContent = originalButtonText;
                });
            })();
        </script>

        @if ($consultation->brief_status === 'processing')
            @push('scripts')
                <script>
                    (() => {
                        const statusUrl = @json(route('counselor.consultations.brief.status', $consultation->id));
                        const poller = setInterval(async () => {
                            try {
                                const res = await fetch(statusUrl, {
                                    headers: {
                                        'Accept': 'application/json'
                                    }
                                });
                                if (!res.ok) return;
                                const data = await res.json();
                                if (data.brief_status && data.brief_status !== 'processing') {
                                    clearInterval(poller);
                                    window.location.reload();
                                }
                            } catch (e) {}
                        }, 4000);
                    })();
                </script>
            @endpush
        @endif
    @endpush

@endsection
