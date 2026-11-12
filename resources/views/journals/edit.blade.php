@extends('layouts.app')

@section('title', 'Edit Jurnal')

@php
    $currentEntryType = old('entry_type', $journal->entry_type);

    // Mendukung jurnal Guided versi lama yang masih tersimpan dalam format tanya-jawab.
    // Saat diedit, format lama ditampilkan sebagai cerita yang bisa disunting langsung.
    $guidedQuestionLabels = [
        'today' => 'Apa yang terjadi hari ini?',
        'burden' => 'Apa hal yang paling membebani pikiranmu?',
        'feeling' => 'Bagaimana perasaanmu menghadapi situasi tersebut?',
        'support' => 'Dukungan atau perubahan apa yang kamu butuhkan sekarang?',
    ];

    $displayContent = $journal->content;
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
                $paragraphs[] = 'Hal lain yang cukup membebani pikiranku adalah ' . $addPunctuation($answers['burden']);
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

    // Isi lama dari input pengguna memiliki prioritas ketika validasi sebelumnya gagal.
    $currentContent = old('content', $displayContent);
@endphp

@section('content')

    <div class="mb-4">
        <a href="{{ route('journals.show', $journal->id) }}" class="text-td-primary text-decoration-none small">
            &larr; Kembali ke Detail Jurnal
        </a>

        <h3 class="fw-bold mt-3 mb-1">Edit Jurnal</h3>
        <p class="text-td-muted mb-0">
            Perbarui cerita yang ingin kamu simpan. Cerita Guided Journaling juga bisa diedit langsung.
        </p>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="td-card">
                <form id="journalEditForm" action="{{ route('journals.update', $journal->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="title" class="form-label fw-medium">
                            Judul Jurnal <span class="text-td-muted">(Opsional)</span>
                        </label>
                        <input type="text" name="title" id="title"
                            class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title', $journal->title) }}" maxlength="160">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="entry_type" class="form-label fw-medium">Metode Menulis</label>
                        <select name="entry_type" id="entry_type"
                            class="form-select @error('entry_type') is-invalid @enderror" required>
                            <option value="free" {{ $currentEntryType === 'free' ? 'selected' : '' }}>
                                Free Writing - Menulis bebas
                            </option>
                            <option value="guided" {{ $currentEntryType === 'guided' ? 'selected' : '' }}>
                                Guided Journaling - Menggunakan panduan
                            </option>
                        </select>
                        @error('entry_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="guidedEditNote" class="alert alert-info py-2 small" hidden>
                        Ini adalah cerita yang sebelumnya disusun dari jawaban Guided Journaling.
                        Kamu bisa menyunting seluruh cerita secara langsung di bawah.
                    </div>

                    <div class="mb-4">
                        <label for="content" id="contentLabel" class="form-label fw-medium">
                            {{ $currentEntryType === 'guided' ? 'Cerita yang tersusun' : 'Isi Jurnal' }}
                        </label>
                        <textarea name="content" id="content" rows="14" maxlength="20000"
                            class="form-control @error('content') is-invalid @enderror"
                            placeholder="Mulailah menulis dari mana pun yang kamu mau..." required>{{ $currentContent }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-between align-items-center gap-2 mt-1">
                            <small id="contentHelp" class="text-td-muted">
                                {{ $currentEntryType === 'guided'
                                    ? 'Edit cerita final yang akan disimpan dan diproses ulang oleh Hermes.'
                                    : 'Tulis atau perbarui ceritamu dengan bebas.' }}
                            </small>
                            <small class="text-td-muted text-nowrap">Maks. 20.000 karakter</small>
                        </div>
                    </div>

                    <div class="bg-light rounded-3 p-3 mb-4">
                        <div class="d-flex gap-2">
                            <span class="text-td-primary fw-bold">P</span>
                            <div>
                                <h6 class="fw-semibold small mb-1">Jurnal ini bersifat pribadi</h6>
                                <p class="text-td-muted small mb-0">
                                    Jurnal hanya dapat dilihat oleh akunmu. Kamu dapat menentukan apakah ingin
                                    membagikannya saat mengajukan konsultasi.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('journals.show', $journal->id) }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-td" data-journal-submit>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="td-card">
                <div class="feature-icon mb-3" id="writingTipIcon">{{ $currentEntryType === 'guided' ? 'G' : 'F' }}</div>
                <h6 class="fw-bold mb-3" id="writingTipTitle">
                    {{ $currentEntryType === 'guided' ? 'Cerita dari panduan' : 'Menulis bebas' }}
                </h6>
                <p class="text-td-muted small mb-0" id="writingTipText">
                    @if ($currentEntryType === 'guided')
                        Jawaban dari panduan sudah disusun menjadi satu cerita. Kamu bisa memperbaiki kata,
                        menambahkan detail, atau menghapus bagian yang tidak ingin disimpan.
                    @else
                        Kamu bebas mengubah cerita tanpa mengikuti urutan tertentu. Tulis kejadian,
                        pikiran, atau perasaan yang ingin kamu simpan.
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Loading overlay saat jurnal disimpan dan refleksi diproses ulang --}}
    <div id="journalLoadingOverlay" class="td-journal-loading" aria-hidden="true">
        <div class="td-journal-loading__dialog" role="status" aria-live="polite" aria-atomic="true">
            <div class="td-journal-loading__spinner" aria-hidden="true"></div>
            <h5 class="fw-bold mb-2">Menyimpan perubahan jurnal</h5>
            <p class="text-td-muted small mb-0">
                Perubahanmu sedang disimpan. Jika isi atau metode menulis berubah,
                Hermes juga akan memperbarui refleksinya. Jangan tutup halaman ini.
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
            border: 1px solid rgba(35, 71, 168, .08);
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
            animation: td-journal-spin .8s linear infinite;
        }

        @keyframes td-journal-spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .td-journal-loading__spinner {
                animation-duration: 1.8s;
            }

            .td-journal-loading {
                transition: none;
            }
        }
    </style>

    <script>
        (() => {
            const form = document.getElementById('journalEditForm');
            const overlay = document.getElementById('journalLoadingOverlay');
            const entryType = document.getElementById('entry_type');
            const contentField = document.getElementById('content');
            const contentLabel = document.getElementById('contentLabel');
            const contentHelp = document.getElementById('contentHelp');
            const guidedNote = document.getElementById('guidedEditNote');
            const tipIcon = document.getElementById('writingTipIcon');
            const tipTitle = document.getElementById('writingTipTitle');
            const tipText = document.getElementById('writingTipText');

            if (!form || !overlay || !entryType || !contentField) return;

            let isSubmitting = false;
            const submitButtons = form.querySelectorAll('[data-journal-submit]');

            function updateWritingMode() {
                const isGuided = entryType.value === 'guided';

                if (guidedNote) guidedNote.hidden = !isGuided;
                contentLabel.textContent = isGuided ? 'Cerita yang tersusun' : 'Isi Jurnal';
                contentHelp.textContent = isGuided ?
                    'Edit cerita final yang akan disimpan dan diproses ulang oleh Hermes.' :
                    'Tulis atau perbarui ceritamu dengan bebas.';

                if (tipIcon) tipIcon.textContent = isGuided ? 'G' : 'F';
                if (tipTitle) tipTitle.textContent = isGuided ? 'Cerita dari panduan' : 'Menulis bebas';
                if (tipText) {
                    tipText.textContent = isGuided ?
                        'Cerita ini sebelumnya disusun dari jawaban panduan. Kamu bisa menyuntingnya langsung sebagai satu kesatuan.' :
                        'Kamu bebas mengubah cerita tanpa mengikuti urutan tertentu. Tulis kejadian, pikiran, atau perasaan yang ingin kamu simpan.';
                }
            }

            entryType.addEventListener('change', updateWritingMode);
            updateWritingMode();

            form.addEventListener('submit', (event) => {
                if (isSubmitting) {
                    event.preventDefault();
                    return;
                }

                if (!contentField.value.trim()) {
                    event.preventDefault();
                    contentField.focus();
                    return;
                }

                isSubmitting = true;
                overlay.classList.add('is-visible');
                overlay.setAttribute('aria-hidden', 'false');

                submitButtons.forEach(button => {
                    button.disabled = true;
                    button.setAttribute('aria-disabled', 'true');
                });
            });

            window.addEventListener('pageshow', () => {
                isSubmitting = false;
                overlay.classList.remove('is-visible');
                overlay.setAttribute('aria-hidden', 'true');
                submitButtons.forEach(button => {
                    button.disabled = false;
                    button.removeAttribute('aria-disabled');
                });
            });
        })();
    </script>

@endsection
