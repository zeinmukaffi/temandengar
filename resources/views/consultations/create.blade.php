@extends('layouts.app')

@section('title', 'Ajukan Konsultasi')

@section('content')

    <div class="mb-4">
        <a href="{{ route('counselors.show', $counselor->id) }}" class="text-td-primary text-decoration-none small">
            &larr; Kembali ke Profil Konselor
        </a>

        <h3 class="fw-bold mt-3 mb-1">Ajukan Konsultasi</h3>

        <p class="text-td-muted mb-0">
            Ceritakan kebutuhanmu dan tentukan informasi yang ingin kamu bagikan.
        </p>
    </div>

    <div class="row g-4">

        {{-- Form pengajuan --}}
        <div class="col-lg-8">

            <div class="td-card">

                {{-- Profil konselor yang dipilih --}}
                <div class="bg-td-soft rounded-3 p-3 mb-4">
                    <div class="d-flex align-items-center gap-3">

                        @if ($counselor->photo_path)
                            <img src="{{ asset('storage/' . $counselor->photo_path) }}" alt="{{ $counselor->name }}"
                                class="rounded-circle" width="64" height="64" style="object-fit: cover;">
                        @else
                            <div class="feature-icon rounded-circle">
                                {{ strtoupper(substr($counselor->name, 0, 1)) }}
                            </div>
                        @endif

                        <div>
                            <div class="fw-bold">{{ $counselor->name }}</div>
                            <div class="text-td-muted small">{{ $counselor->role }}</div>
                            <span class="badge bg-success-subtle text-success mt-1">
                                Tersedia
                            </span>
                        </div>

                    </div>
                </div>

                <form id="consultationForm" action="{{ route('consultations.store', $counselor->id) }}" method="POST">

                    @csrf

                    {{-- Topik konsultasi --}}
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

                    {{-- Konteks konsultasi --}}
                    <div class="mb-4">
                        <label for="context" class="form-label fw-semibold">
                            Apa yang ingin kamu ceritakan?
                            <span class="text-td-muted fw-normal">(Opsional)</span>
                        </label>

                        <textarea name="context" id="context" rows="6" maxlength="10000"
                            class="form-control @error('context') is-invalid @enderror"
                            placeholder="Ceritakan secara singkat hal yang ingin kamu bahas bersama konselor...">{{ old('context') }}</textarea>

                        @error('context')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="text-end text-td-muted small mt-1">
                            Maksimal 10.000 karakter.
                        </div>
                    </div>

                    {{-- Waktu konsultasi --}}
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
                            Waktu ini merupakan preferensi awal dan belum menjadi jadwal final.
                        </div>
                    </div>

                    <hr class="my-4">


                    {{-- Persetujuan berbagi materi --}}
                    <h5 class="fw-bold mb-2">Informasi yang Ingin Dibagikan</h5>

                    <p class="text-td-muted small mb-3">
                        Pilih jurnal sebagai sumber materi konsultasi.
                        Persetujuan berbagi jurnal dan refleksi AI dapat ditentukan secara terpisah.
                    </p>

                    {{-- Izin berbagi jurnal --}}
                    <div class="border rounded-3 p-3 mb-3">

                        <div class="form-check">
                            <input type="checkbox" name="share_journal" value="1" id="shareJournal"
                                class="form-check-input" {{ old('share_journal') ? 'checked' : '' }}>

                            <label for="shareJournal" class="form-check-label fw-semibold">
                                Bagikan Jurnal
                            </label>
                        </div>

                        <p class="text-td-muted small mt-1 mb-0">
                            Izinkan konselor membaca isi jurnal yang kamu pilih.
                        </p>

                    </div>

                    <div class="border rounded-3 p-3 mb-3">

                        <label for="journalSelect" class="form-label fw-semibold">
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

                                    {{ $journal->title ?: 'Catatan Pribadi' }}
                                    — {{ $journal->journaled_at->format('d M Y') }}

                                </option>
                            @endforeach

                        </select>

                        @error('journal_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                        @if ($journals->isEmpty())
                            <p class="text-td-muted small mt-2 mb-0">
                                Kamu belum memiliki jurnal.
                            </p>
                        @endif

                    </div>

                    {{-- Izin berbagi AI Reflection --}}
                    <div class="border rounded-3 p-3 mb-4">

                        <div class="form-check">
                            <input type="checkbox" name="share_ai_reflection" value="1" id="shareAiReflection"
                                class="form-check-input" {{ old('share_ai_reflection') ? 'checked' : '' }}>

                            <label for="shareAiReflection" class="form-check-label fw-semibold">
                                Bagikan Refleksi AI
                            </label>
                        </div>

                        <p class="text-td-muted small mt-1 mb-2">
                            Refleksi AI akan otomatis mengikuti jurnal yang kamu pilih.
                            Persetujuan berbagi tetap berada di tanganmu.
                        </p>


                        <div id="reflectionMatchInfo" class="bg-light rounded-3 p-3 mt-2" aria-live="polite">

                            <div class="fw-semibold small mb-2">
                                Ringkasan Cerita AI
                            </div>

                            <p id="reflectionSummary" class="small mb-2">
                                Pilih jurnal untuk melihat ringkasan refleksi AI.
                            </p>

                            <small id="reflectionDate" class="text-td-muted">
                            </small>
                        </div>


                        @error('share_ai_reflection')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror

                    </div>

                    {{-- Privacy information --}}
                    <div class="bg-light rounded-3 p-3 mb-4">
                        <div class="d-flex gap-2">
                            <span class="text-td-primary fw-bold">P</span>

                            <div>
                                <h6 class="fw-semibold small mb-1">
                                    Kendali tetap berada padamu
                                </h6>

                                <p class="text-td-muted small mb-0">
                                    Informasi yang tidak kamu pilih tidak akan disertakan
                                    sebagai materi jurnal atau refleksi untuk konsultasi ini.
                                </p>
                            </div>
                        </div>
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

        {{-- Informasi tambahan --}}
        <div class="col-lg-4">
            <div class="td-card">

                <div class="feature-icon mb-3">C</div>

                <h6 class="fw-bold mb-3">Sebelum Mengirim Permintaan</h6>

                <div class="border-start border-3 ps-3 mb-3" style="border-color: #73B7AA !important;">
                    <p class="small mb-0">
                        Pilih topik yang paling sesuai dengan hal yang ingin kamu bahas.
                    </p>
                </div>

                <div class="border-start border-3 ps-3 mb-3" style="border-color: #73B7AA !important;">
                    <p class="small mb-0">
                        Waktu yang kamu pilih merupakan preferensi, bukan jadwal yang sudah dikonfirmasi.
                    </p>
                </div>

                <div class="border-start border-3 ps-3" style="border-color: #73B7AA !important;">
                    <p class="small mb-0">
                        Kamu tidak wajib membagikan jurnal maupun refleksi AI.
                    </p>
                </div>

            </div>
        </div>

    </div>

    {{-- Loading overlay --}}
    <div id="consultationLoading" class="td-journal-loading" aria-hidden="true">
        <div class="td-journal-loading__dialog" role="status" aria-live="polite">
            <div class="td-journal-loading__spinner"></div>
            <h5 class="fw-bold mb-2">Mengirim Permintaan</h5>
            <p class="text-td-muted small mb-0">
                Permintaan konsultasimu sedang disimpan.
                Jangan tutup halaman ini.
            </p>
        </div>
    </div>

    <style>
        .td-journal-loading {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(23, 39, 55, .48);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity .2s ease, visibility .2s ease;
        }

        .td-journal-loading.is-visible {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .td-journal-loading__dialog {
            width: min(100%, 390px);
            padding: 32px 28px;
            border-radius: 20px;
            background: #fff;
            text-align: center;
            box-shadow: 0 20px 60px rgba(15, 35, 55, .18);
        }

        .td-journal-loading__spinner {
            width: 44px;
            height: 44px;
            margin: 0 auto 20px;
            border: 4px solid #e7edf7;
            border-top-color: #2347a8;
            border-radius: 50%;
            animation: consultation-spin .8s linear infinite;
        }

        @keyframes consultation-spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <script>
        (() => {
            const form = document.getElementById('consultationForm');

            const shareJournal = document.getElementById('shareJournal');
            const shareAi = document.getElementById('shareAiReflection');

            const journalSelect = document.getElementById('journalSelect');
            const reflectionInfo = document.getElementById('reflectionMatchInfo');

            if (!form || !journalSelect || !reflectionInfo) return;

            function updateSharingForm() {
                // Jurnal acuan diperlukan jika salah satu materi ingin dibagikan.
                const needsJournal =
                    shareJournal.checked || shareAi.checked;

                journalSelect.disabled = !needsJournal;
                journalSelect.required = needsJournal;

                updateReflectionPreview();
            }


            function updateReflectionPreview() {
                const option = journalSelect.selectedOptions[0];

                const summaryElement =
                    document.getElementById('reflectionSummary');

                const dateElement =
                    document.getElementById('reflectionDate');

                const hasJournal = Boolean(option?.value);
                const reflectionId = option?.dataset.reflectionId || '';
                const summary = option?.dataset.reflectionSummary || '';
                const date = option?.dataset.reflectionDate || '';

                const hasReflection = Boolean(
                    reflectionId && summary.trim()
                );

                // Refleksi hanya bisa dibagikan jika jurnal terpilih
                // memiliki refleksi AI yang sudah completed.
                shareAi.disabled = !hasReflection;

                if (!hasReflection) {
                    shareAi.checked = false;

                    summaryElement.textContent = hasJournal ?
                        'Jurnal ini belum memiliki refleksi AI yang selesai dibuat.' :
                        'Pilih jurnal untuk melihat ringkasan refleksi AI.';

                    dateElement.textContent = '';
                    return;
                }

                // Tampilkan ringkasan AI dari jurnal yang dipilih.
                // textContent digunakan agar isi AI tidak diperlakukan sebagai HTML.
                summaryElement.textContent = summary;
                dateElement.textContent = date ?
                    `Dibuat ${date}` :
                    '';
            }


            shareJournal.addEventListener('change', updateSharingForm);
            shareAi.addEventListener('change', updateSharingForm);

            journalSelect.addEventListener('change', updateReflectionPreview);

            updateSharingForm();

            // Validasi terakhir sebelum submit.
            form.addEventListener('submit', (event) => {
                if (
                    (shareJournal.checked || shareAi.checked) &&
                    !journalSelect.value
                ) {
                    event.preventDefault();

                    alert('Pilih jurnal acuan terlebih dahulu.');
                    journalSelect.focus();

                    return;
                }

                if (shareAi.checked) {
                    const option = journalSelect.selectedOptions[0];

                    if (!option?.dataset.reflectionId) {
                        event.preventDefault();

                        alert(
                            'Jurnal yang dipilih belum memiliki refleksi AI yang selesai.'
                        );

                        return;
                    }
                }
            });
        })();
    </script>

@endsection
