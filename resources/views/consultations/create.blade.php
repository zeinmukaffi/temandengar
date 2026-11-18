@extends('layouts.app')

@section('title', 'Ajukan Konsultasi')

@section('content')

    <div class="mb-4">
        <a href="{{ route('counselors.show', $counselor->id) }}" class="td-back-link text-decoration-none">
            <span aria-hidden="true">&larr;</span>
            Kembali ke Profil Konselor
        </a>

        <div class="mt-3">
            <span class="td-section-eyebrow">PENGAJUAN SESI</span>
            <h3 class="fw-bold mt-1 mb-1">Ajukan Konsultasi</h3>
            <p class="text-td-muted mb-0">
                Ceritakan kebutuhanmu dan tentukan informasi yang ingin kamu bagikan.
            </p>
        </div>
    </div>

    <div class="row g-4">

        {{-- Kolom Kiri: Form Pengajuan --}}
        <div class="col-lg-8">
            <div class="td-card td-writing-card">

                {{-- Banner Konselor Terpilih --}}
                <div class="td-counselor-mini-banner mb-4">
                    @if ($counselor->photo_path)
                        <img src="{{ asset('storage/' . $counselor->photo_path) }}" alt="{{ $counselor->name }}"
                            class="td-counselor-avatar" style="width: 58px; height: 58px;">
                    @else
                        <div class="td-counselor-initials" style="width: 58px; height: 58px; font-size: 1.25rem;"
                            aria-hidden="true">
                            {{ strtoupper(substr($counselor->name, 0, 1)) }}
                        </div>
                    @endif

                    <div class="flex-grow-1">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <span class="fw-bold text-td-primary">{{ $counselor->name }}</span>
                            <span class="td-status-pill td-status-pill--success" style="padding: 2px 9px;">
                                Tersedia
                            </span>
                        </div>
                        <div class="text-td-muted small text-capitalize">
                            Konselor Sebaya • {{ $counselor->role }}
                        </div>
                    </div>
                </div>

                <form id="consultationForm" action="{{ route('consultations.store', $counselor->id) }}" method="POST">
                    @csrf

                    {{-- Topik Konsultasi --}}
                    <div class="mb-4">
                        <label for="topic" class="form-label fw-semibold">
                            Topik Konsultasi
                        </label>

                        <select name="topic" id="topic" class="form-select @error('topic') is-invalid @enderror"
                            required>
                            <option value="">Pilih topik yang ingin dibahas</option>

                            @foreach (['Akademik', 'Manajemen Waktu', 'Relasi Pertemanan', 'Keluarga', 'Pengembangan Diri', 'Karier', 'Lainnya'] as $topic)
                                <option value="{{ $topic }}" {{ old('topic') === $topic ? 'selected' : '' }}>
                                    {{ $topic }}
                                </option>
                            @endforeach
                        </select>

                        @error('topic')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Konteks Konsultasi --}}
                    <div class="mb-4">
                        <label for="context" class="form-label fw-semibold">
                            Apa yang ingin kamu ceritakan?
                            <span class="text-td-muted fw-normal">(Opsional)</span>
                        </label>

                        <textarea name="context" id="context" rows="5" maxlength="10000"
                            class="form-control @error('context') is-invalid @enderror"
                            placeholder="Ceritakan secara singkat hal yang ingin kamu bahas bersama konselor...">{{ old('context') }}</textarea>

                        @error('context')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="text-end text-td-muted small mt-1">
                            Maksimal 10.000 karakter.
                        </div>
                    </div>

                    {{-- Waktu Konsultasi --}}
                    <div class="mb-4">
                        <label for="preferred_at" class="form-label fw-semibold">
                            Waktu Konsultasi yang Diinginkan
                        </label>

                        <input type="datetime-local" name="preferred_at" id="preferred_at"
                            class="form-control @error('preferred_at') is-invalid @enderror"
                            min="{{ now()->format('Y-m-d\TH:i') }}" value="{{ old('preferred_at') }}" required>

                        @error('preferred_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="text-td-muted small mt-1">
                            Waktu ini merupakan preferensi awal dan akan dikonfirmasi kembali oleh konselor.
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- Persetujuan Berbagi Materi --}}
                    <div class="mb-3">
                        <span class="td-section-eyebrow mb-1">KENDALI PRIVASI</span>
                        <h5 class="fw-bold mb-1">Informasi yang Ingin Dibagikan</h5>
                        <p class="text-td-muted small mb-0">
                            Pilih jurnal sebagai sumber materi konsultasi. Persetujuan berbagi jurnal
                            dan refleksi AI dapat ditentukan secara terpisah.
                        </p>
                    </div>

                    {{-- Izin Berbagi Jurnal --}}
                    <div class="td-consent-box mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="share_journal" value="1" id="shareJournal"
                                class="form-check-input" {{ old('share_journal') ? 'checked' : '' }}>

                            <label for="shareJournal" class="form-check-label fw-semibold">
                                Bagikan Jurnal Pribadi
                            </label>
                        </div>

                        <p class="text-td-muted small mt-1 mb-0 ps-4">
                            Izinkan konselor membaca isi jurnal yang kamu pilih di bawah ini.
                        </p>
                    </div>

                    {{-- Pilihan Jurnal Acuan --}}
                    <div class="td-method-section mb-3">
                        <label for="journalSelect" class="form-label fw-semibold small">
                            Pilih Jurnal Acuan
                        </label>

                        <select name="journal_id" id="journalSelect"
                            class="form-select @error('journal_id') is-invalid @enderror" disabled>

                            <option value="">Pilih jurnal</option>

                            @foreach ($journals as $journal)
                                @php
                                    $latestReflection = $journal->aiReflections->first();
                                @endphp

                                <option value="{{ $journal->id }}"
                                    data-reflection-id="{{ $latestReflection?->id ?? '' }}"
                                    data-reflection-summary="{{ $latestReflection?->summary ?? '' }}"
                                    data-reflection-date="{{ $latestReflection?->created_at?->format('d M Y, H:i') ?? '' }}"
                                    {{ (string) old('journal_id') === (string) $journal->id ? 'selected' : '' }}>
                                    {{ $journal->title ?: 'Catatan Pribadi' }} —
                                    {{ $journal->journaled_at->format('d M Y') }}
                                </option>
                            @endforeach
                        </select>

                        @error('journal_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                        @if ($journals->isEmpty())
                            <p class="text-td-muted small mt-2 mb-0">
                                Kamu belum memiliki jurnal tersimpan.
                            </p>
                        @endif
                    </div>

                    {{-- Izin Berbagi AI Reflection --}}
                    <div class="td-consent-box mb-4">
                        <div class="form-check">
                            <input type="checkbox" name="share_ai_reflection" value="1" id="shareAiReflection"
                                class="form-check-input" {{ old('share_ai_reflection') ? 'checked' : '' }}>

                            <label for="shareAiReflection" class="form-check-label fw-semibold">
                                Bagikan Refleksi AI (Nala AI)
                            </label>
                        </div>

                        <p class="text-td-muted small mt-1 mb-3 ps-4">
                            Refleksi AI otomatis mengikuti jurnal yang kamu pilih. Persetujuan berbagi tetap berada di
                            tanganmu.
                        </p>

                        <div id="reflectionMatchInfo" class="bg-white border rounded-3 p-3 ms-4" aria-live="polite">
                            <div class="fw-semibold small text-td-primary mb-1">
                                Ringkasan Cerita AI
                            </div>

                            <p id="reflectionSummary" class="text-td-muted small mb-1" style="line-height: 1.65;">
                                Pilih jurnal untuk melihat ringkasan refleksi AI.
                            </p>

                            <small id="reflectionDate" class="text-td-muted" style="font-size: 11px;"></small>
                        </div>

                        @error('share_ai_reflection')
                            <div class="text-danger small mt-2 ps-4">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Privacy Notice --}}
                    <div class="td-writing-privacy mb-4">
                        <div class="td-writing-privacy-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z" />
                                <path d="m9 12 2 2 4-4" />
                            </svg>
                        </div>

                        <div>
                            <h6 class="fw-semibold small mb-1">Kendali tetap berada padamu</h6>
                            <p class="text-td-muted small mb-0">
                                Informasi yang tidak kamu pilih tidak akan disertakan sebagai materi jurnal
                                atau refleksi untuk konsultasi ini.
                            </p>
                        </div>
                    </div>

                    <div class="p-3 rounded-3 mb-3 small"
                        style="background: var(--td-surface-muted); border: 1px solid var(--td-border); line-height: 1.7;">
                        <strong class="text-dark d-block mb-1">Catatan Layanan Pendampingan Sebaya</strong>
                        <span class="text-td-muted">
                            Konselor sebaya siap mendengarkan dan menemanimu berdiskusi, namun tidak memberikan diagnosis
                            maupun terapi
                            klinis. Untuk kondisi darurat, segera hubungi layanan darurat <strong>119 (ekstensi 8)</strong>.
                        </span>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('counselors.show', $counselor->id) }}" class="btn btn-outline-secondary">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-td" id="submitConsultation">
                            Kirim Permintaan
                        </button>
                    </div>

                </form>

            </div>
        </div>

        {{-- Kolom Kanan: Panduan Singkat --}}
        <div class="col-lg-4 td-writing-sidebar">
            <aside class="td-card td-writing-tip-card">
                <div class="td-tip-icon mb-3">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 16v-4" />
                        <path d="M12 8h.01" />
                    </svg>
                </div>

                <span class="td-section-eyebrow">INFORMASI SESI</span>
                <h6 class="fw-bold mt-1 mb-3">Sebelum Mengirim Permintaan</h6>

                <div class="td-writing-tip-quote mb-3">
                    <p class="small mb-0">
                        Pilih topik yang paling sesuai dengan hal yang ingin kamu bahas.
                    </p>
                </div>

                <div class="td-writing-tip-quote mb-3">
                    <p class="small mb-0">
                        Waktu yang kamu pilih merupakan preferensi awal sebelum dikonfirmasi konselor.
                    </p>
                </div>

                <div class="td-writing-tip-quote">
                    <p class="small mb-0">
                        Kamu tidak wajib membagikan jurnal maupun refleksi AI untuk mengajukan sesi.
                    </p>
                </div>
            </aside>
        </div>

    </div>

    {{-- Loading overlay --}}
    <div id="consultationLoading" class="td-journal-loading" aria-hidden="true">
        <div class="td-journal-loading__dialog" role="status" aria-live="polite">
            <div class="td-journal-loading__spinner" aria-hidden="true"></div>
            <h5 class="fw-bold mb-2">Mengirim Permintaan Konsultasi</h5>
            <p class="text-td-muted small mb-0">
                Permintaanmu sedang disimpan dan Nala AI sedang menyiapkan ringkasan untuk konselor.
                Proses ini bisa memerlukan beberapa saat. Jangan tutup atau refresh halaman ini.
            </p>
        </div>
    </div>

    @push('scripts')
        <script>
            (() => {
                const form = document.getElementById('consultationForm');
                const overlay = document.getElementById('consultationLoading');
                const shareJournal = document.getElementById('shareJournal');
                const shareAi = document.getElementById('shareAiReflection');
                const journalSelect = document.getElementById('journalSelect');
                const reflectionInfo = document.getElementById('reflectionMatchInfo');
                const submitButton = document.getElementById('submitConsultation');

                if (!form || !overlay || !shareJournal || !shareAi || !journalSelect || !reflectionInfo) return;

                let isSubmitting = false;

                function updateSharingForm() {
                    const needsJournal = shareJournal.checked || shareAi.checked;
                    journalSelect.disabled = !needsJournal;
                    journalSelect.required = needsJournal;
                    updateReflectionPreview();
                }

                function updateReflectionPreview() {
                    const option = journalSelect.selectedOptions[0];
                    const summaryElement = document.getElementById('reflectionSummary');
                    const dateElement = document.getElementById('reflectionDate');

                    const hasJournal = Boolean(option?.value);
                    const reflectionId = option?.dataset.reflectionId || '';
                    const summary = option?.dataset.reflectionSummary || '';
                    const date = option?.dataset.reflectionDate || '';
                    const hasReflection = Boolean(reflectionId && summary.trim());

                    shareAi.disabled = !hasReflection;

                    if (!hasReflection) {
                        shareAi.checked = false;
                        summaryElement.textContent = hasJournal ?
                            'Jurnal ini belum memiliki refleksi AI yang selesai dibuat.' :
                            'Pilih jurnal untuk melihat ringkasan refleksi AI.';
                        dateElement.textContent = '';
                        return;
                    }

                    summaryElement.textContent = summary;
                    dateElement.textContent = date ? `Dibuat ${date}` : '';
                }

                shareJournal.addEventListener('change', updateSharingForm);
                shareAi.addEventListener('change', updateSharingForm);
                journalSelect.addEventListener('change', updateReflectionPreview);

                updateSharingForm();

                form.addEventListener('submit', (event) => {
                    if (isSubmitting) {
                        event.preventDefault();
                        return;
                    }

                    if ((shareJournal.checked || shareAi.checked) && !journalSelect.value) {
                        event.preventDefault();
                        alert('Pilih jurnal acuan terlebih dahulu.');
                        journalSelect.focus();
                        return;
                    }

                    if (shareAi.checked) {
                        const option = journalSelect.selectedOptions[0];
                        if (!option?.dataset.reflectionId) {
                            event.preventDefault();
                            alert('Jurnal yang dipilih belum memiliki refleksi AI yang selesai.');
                            journalSelect.focus();
                            return;
                        }
                    }

                    isSubmitting = true;
                    overlay.classList.add('is-visible');
                    overlay.setAttribute('aria-hidden', 'false');

                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.setAttribute('aria-disabled', 'true');
                        submitButton.textContent = 'Mengirim...';
                    }
                });

                window.addEventListener('pageshow', () => {
                    isSubmitting = false;
                    overlay.classList.remove('is-visible');
                    overlay.setAttribute('aria-hidden', 'true');

                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.removeAttribute('aria-disabled');
                        submitButton.textContent = 'Kirim Permintaan';
                    }
                });
            })();
        </script>
    @endpush

@endsection
