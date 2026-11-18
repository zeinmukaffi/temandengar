@extends('layouts.app')

@section('title', 'Detail Jurnal')

@section('content')

    @php
        $moodEmojis = [
            'senang' => '😊',
            'tenang' => '😌',
            'sedih' => '😢',
            'cemas' => '😰',
            'marah' => '😠',
            'lelah' => '🥱',
            'bingung' => '🤔',
        ];

        $reflection = $journal->aiReflections->sortByDesc('created_at')->first();

        $displayContent = $journal->content;

        $guidedQuestionLabels = [
            'today' => 'Apa yang terjadi hari ini?',
            'burden' => 'Apa hal yang paling membebani pikiranmu?',
            'feeling' => 'Bagaimana perasaanmu menghadapi situasi tersebut?',
            'support' => 'Dukungan atau perubahan apa yang kamu butuhkan sekarang?',
        ];

        if ($journal->entry_type === 'guided') {
            $lines = preg_split('/\R/u', $journal->content) ?: [];
            $answers = array_fill_keys(array_keys($guidedQuestionLabels), '');
            $currentKey = null;
            $currentAnswerLines = [];
            $matchedAnyQuestion = false;

            foreach ($lines as $line) {
                $matchedKey = array_search(trim($line), $guidedQuestionLabels, true);

                if ($matchedKey !== false) {
                    if ($currentKey !== null) {
                        $answers[$currentKey] = trim(implode("\n", $currentAnswerLines));
                    }
                    $currentKey = $matchedKey;
                    $currentAnswerLines = [];
                    $matchedAnyQuestion = true;
                    continue;
                }

                if ($currentKey !== null) {
                    $currentAnswerLines[] = $line;
                }
            }

            if ($currentKey !== null) {
                $answers[$currentKey] = trim(implode("\n", $currentAnswerLines));
            }

            if ($matchedAnyQuestion) {
                $addPunctuation = static function (string $value): string {
                    $value = trim($value);
                    if ($value === '' || preg_match('/[.!?…]$/u', $value)) {
                        return $value;
                    }
                    return $value . '.';
                };

                $paragraphs = [];
                if ($answers['today'] !== '') {
                    $paragraphs[] = $addPunctuation($answers['today']);
                }
                if ($answers['burden'] !== '') {
                    $paragraphs[] =
                        'Hal lain yang cukup membebani pikiranku adalah ' . $addPunctuation($answers['burden']);
                }
                if ($answers['feeling'] !== '') {
                    $paragraphs[] =
                        'Saat menghadapi situasi tersebut, perasaan yang muncul adalah ' .
                        $addPunctuation($answers['feeling']);
                }
                if ($answers['support'] !== '') {
                    $paragraphs[] = 'Saat ini, hal yang aku butuhkan adalah ' . $addPunctuation($answers['support']);
                }

                $displayContent = implode("\n\n", $paragraphs) ?: $journal->content;
            }
        }
    @endphp

    <div class="mb-4">
        <a href="{{ route('journals.index') }}" class="td-back-link text-decoration-none">
            <span aria-hidden="true">&larr;</span>
            Kembali ke Journaling
        </a>
    </div>

    <div class="row g-4">

        {{-- Kolom Kiri: Detail Jurnal --}}
        <div class="col-lg-8">
            <article class="td-card td-journal-article">

                {{-- Header Artikel --}}
                <div class="d-flex justify-content-between align-items-start gap-3 mb-4 pb-3 border-bottom">
                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="td-entry-badge">
                                {{ $journal->entry_type === 'guided' ? 'Guided Journaling' : 'Free Writing' }}
                            </span>

                            <span class="td-privacy-badge">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="3" y="11" width="18" height="11" rx="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                                Pribadi
                            </span>
                        </div>

                        <h3 class="fw-bold mb-1">
                            {{ $journal->title ?: 'Catatan Pribadi' }}
                        </h3>

                        <p class="text-td-muted small mb-0">
                            Ditulis pada {{ $journal->journaled_at->format('d F Y, H:i') }}
                        </p>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-light border rounded-3 dropdown-toggle btn-sm px-3 py-2"
                            data-bs-toggle="dropdown" type="button">
                            Aksi
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('journals.edit', $journal->id) }}">
                                    Edit Jurnal
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <form action="{{ route('journals.destroy', $journal->id) }}" method="POST"
                                    data-confirm="Apakah kamu yakin ingin menghapus jurnal ini? Jurnal yang dihapus tidak dapat dikembalikan."
                                    data-confirm-title="Hapus Jurnal?" data-confirm-button="Ya, Hapus"
                                    data-confirm-variant="danger">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="dropdown-item text-danger">
                                        Hapus Jurnal
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Mood Callout --}}
                @if ($journal->moodCheckin)
                    <div class="td-mood-callout mb-4">
                        <div class="mt-2" style="font-size: 28px; line-height: 1.2;" aria-hidden="true">
                            {{ $moodEmojis[$journal->moodCheckin->mood] ?? '•' }}
                        </div>
                        <div>
                            <span class="td-section-eyebrow">MOOD SAAT MENULIS</span>
                            <h6 class="fw-bold text-td-primary mt-1 mb-1">
                                {{ config('temandengar.moods.' . $journal->moodCheckin->mood, ucfirst($journal->moodCheckin->mood)) }}
                            </h6>

                            @if ($journal->moodCheckin->note)
                                <p class="text-td-muted small mb-0">
                                    "{{ $journal->moodCheckin->note }}"
                                </p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Isi Cerita Jurnal --}}
                <div class="td-journal-body-text mb-4">{{ $displayContent }}</div>

                {{-- Catatan Privasi --}}
                <div class="d-flex align-items-center gap-2 text-td-muted small pt-3 border-top">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--td-primary)"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z" />
                        <path d="m9 12 2 2 4-4" />
                    </svg>
                    <span>Jurnal ini bersifat pribadi dan hanya terlihat olehmu.</span>
                </div>

                {{-- Ajakan Konsultasi Sebaya --}}
                <div class="mt-4 p-4 rounded-4" style="background: var(--td-bg); border: 1px solid var(--td-border);">
                    <span class="td-section-eyebrow mb-1">DUKUNGAN SEBAYA</span>
                    <h6 class="fw-bold text-td-primary mb-2">
                        Ingin membahas cerita ini bersama seseorang?
                    </h6>

                    <p class="text-td-muted small mb-3" style="line-height: 1.75;">
                        Temukan konselor sebaya yang bisa menemanimu. Kamu dapat menentukan sendiri
                        apakah jurnal ini ingin dibagikan saat mengajukan konsultasi.
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('counselors.index') }}" class="btn btn-td btn-sm">
                            Cari Konselor untuk Konsultasi &rarr;
                        </a>

                        <a href="{{ route('consultations.index') }}" class="btn btn-outline-td btn-sm">
                            Lihat Konsultasi Saya
                        </a>
                    </div>
                </div>

            </article>
        </div>

        {{-- Kolom Kanan: Panel AI Reflection --}}
        <div class="col-lg-4">
            <aside class="td-card">

                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="feature-icon flex-shrink-0">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
                            <path d="M19 3v4" />
                            <path d="M21 5h-4" />
                        </svg>
                    </div>
                    <div>
                        <span class="td-section-eyebrow">Nala AI INSIGHT</span>
                        <h5 class="fw-bold mb-0">AI Reflection</h5>
                    </div>
                </div>

                <p class="text-td-muted small mb-3" style="line-height: 1.7;">
                    Refleksi ini membantu merangkum cerita dan membuka ruang untuk memahami pengalamanmu.
                    Hasilnya bukan diagnosis atau pengganti dukungan dari konselor.
                </p>

                @if ($reflection)
                    <div class="td-reflection-box">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                            <span class="fw-semibold small text-td-primary">Hasil Refleksi</span>

                            @if ($reflection->status === 'completed')
                                <span class="td-status-pill td-status-pill--success">Selesai</span>
                            @elseif ($reflection->status === 'pending')
                                <span class="td-status-pill td-status-pill--warning">Diproses</span>
                            @else
                                <span class="td-status-pill td-status-pill--danger">Gagal</span>
                            @endif
                        </div>

                        @if ($reflection->status === 'completed')

                            @if ($reflection->summary)
                                <section class="td-reflection-section">
                                    <h6 class="fw-bold small text-td-primary mb-2">Ringkasan Cerita</h6>
                                    <p class="small mb-0" style="line-height: 1.75;">
                                        {{ $reflection->summary }}
                                    </p>
                                </section>
                            @endif

                            @if ($reflection->emotional_insight)
                                <section class="td-reflection-section">
                                    <h6 class="fw-bold small text-td-primary mb-2">Insight Emosional</h6>
                                    <p class="small mb-0" style="line-height: 1.75;">
                                        {{ $reflection->emotional_insight }}
                                    </p>
                                </section>
                            @endif

                            @if (!empty($reflection->key_concerns))
                                <section class="td-reflection-section">
                                    <h6 class="fw-bold small text-td-primary mb-2">Hal yang Menjadi Perhatian</h6>
                                    <ul class="small ps-3 mb-0" style="line-height: 1.75;">
                                        @foreach ($reflection->key_concerns as $concern)
                                            <li class="mb-1">{{ $concern }}</li>
                                        @endforeach
                                    </ul>
                                </section>
                            @endif

                            @if (!empty($reflection->reflection_questions))
                                <section class="td-reflection-section">
                                    <h6 class="fw-bold small text-td-primary mb-2">Pertanyaan Refleksi</h6>
                                    <ol class="small ps-3 mb-0" style="line-height: 1.75;">
                                        @foreach ($reflection->reflection_questions as $question)
                                            <li class="mb-2">{{ $question }}</li>
                                        @endforeach
                                    </ol>
                                </section>
                            @endif

                            @if (!empty($reflection->suggested_steps))
                                <section class="td-reflection-section">
                                    <h6 class="fw-bold small text-td-primary mb-2">Langkah yang Bisa Dicoba</h6>
                                    <ul class="small ps-3 mb-0" style="line-height: 1.75;">
                                        @foreach ($reflection->suggested_steps as $step)
                                            <li class="mb-1">{{ $step }}</li>
                                        @endforeach
                                    </ul>
                                </section>
                            @endif
                        @elseif ($reflection && $reflection->status === 'failed')
                            <div class="td-shared-material-box">
                                <p class="text-td-muted small mb-3" style="line-height: 1.75;">
                                    Refleksi AI belum berhasil dibuat saat jurnal disimpan. Kamu dapat mencoba memprosesnya
                                    kembali sekarang tanpa mengubah isi jurnalmu.
                                </p>

                                <form id="retryJournalReflectionForm"
                                    action="{{ route('journals.reflection.retry', $journal->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" id="retryJournalReflectionBtn"
                                        class="btn btn-outline-td btn-sm">
                                        Coba Buat Ulang Refleksi AI
                                    </button>
                                </form>
                            </div>
                        @elseif ($reflection->status === 'pending')
                            <div class="bg-white border rounded-3 p-3">
                                <p class="small text-td-muted mb-0">
                                    Refleksi sedang diproses. Silakan muat ulang halaman ini beberapa saat lagi.
                                </p>
                            </div>
                        @else
                            <div class="bg-white border rounded-3 p-3">
                                <p class="small text-td-muted mb-0">
                                    Refleksi belum berhasil dibuat. Ceritamu tetap tersimpan dan bersifat pribadi.
                                </p>
                            </div>
                        @endif

                        <div class="text-td-muted mt-3 pt-2 border-top" style="font-size: 11px;">
                            Dibuat pada {{ $reflection->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                @else
                    <div class="td-reflection-box">
                        <p class="small text-td-muted mb-0">
                            Belum ada refleksi AI untuk jurnal ini.
                        </p>
                    </div>
                @endif
                <div class="border-top pt-3 mt-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span class="text-td-muted small">
                        Apakah refleksi dari Nala AI ini terasa membantu?
                    </span>

                    <div class="d-flex align-items-center gap-2">
                        <form action="{{ route('journals.reflection.feedback', $journal->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="feedback" value="helpful">
                            <button type="submit"
                                class="btn btn-sm {{ $reflection->user_feedback === 'helpful' ? 'btn-td' : 'btn-outline-td' }}">
                                Membantu
                            </button>
                        </form>

                        <form action="{{ route('journals.reflection.feedback', $journal->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="feedback" value="not_helpful">
                            <button type="submit"
                                class="btn btn-sm {{ $reflection->user_feedback === 'not_helpful' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                                Kurang Sesuai
                            </button>
                        </form>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    {{-- Loading overlay saat membuat ulang refleksi --}}
    <div id="journalRetryLoading" class="td-journal-loading" aria-hidden="true">
        <div class="td-journal-loading__dialog" role="status" aria-live="polite">
            <div class="td-journal-loading__spinner" aria-hidden="true"></div>
            <h5 class="fw-bold mb-2">Membuat Ulang Refleksi AI</h5>
            <p class="text-td-muted small mb-0">
                Nala AI sedang membaca kembali ceritamu. Proses ini memerlukan beberapa saat, mohon jangan tutup halaman ini.
            </p>
        </div>
    </div>

    @push('scripts')
        <script>
            (() => {
                const form = document.getElementById('retryJournalReflectionForm');
                const overlay = document.getElementById('journalRetryLoading');
                const button = document.getElementById('retryJournalReflectionBtn');

                if (!form || !overlay || !button) return;

                let isSubmitting = false;

                form.addEventListener('submit', (event) => {
                    if (isSubmitting) {
                        event.preventDefault();
                        return;
                    }

                    isSubmitting = true;
                    overlay.classList.add('is-visible');
                    overlay.setAttribute('aria-hidden', 'false');
                    button.disabled = true;
                    button.textContent = 'Sedang Memproses...';
                });

                window.addEventListener('pageshow', () => {
                    isSubmitting = false;
                    overlay.classList.remove('is-visible');
                    overlay.setAttribute('aria-hidden', 'true');
                    button.disabled = false;
                    button.textContent = 'Coba Buat Ulang Refleksi AI';
                });
            })();
        </script>

        @if ($reflection && $reflection->status === 'pending')
            <script>
                (() => {
                    const statusUrl = @json(route('journals.reflection.status', $journal->id));
                    const poller = setInterval(async () => {
                        try {
                            const res = await fetch(statusUrl, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });
                            if (!res.ok) return;
                            const data = await res.json();
                            if (data.status && data.status !== 'pending') {
                                clearInterval(poller);
                                window.location.reload();
                            }
                        } catch (e) {}
                    }, 4000);
                })();
            </script>
        @endif
    @endpush


@endsection
