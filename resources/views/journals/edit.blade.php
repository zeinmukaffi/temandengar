@extends('layouts.app')

@section('title', 'Edit Jurnal')

@php
    $currentEntryType = old('entry_type', $journal->entry_type);

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

    $currentContent = old('content', $displayContent);
@endphp

@section('content')

    <div class="mb-4">
        <a href="{{ route('journals.show', $journal->id) }}" class="td-back-link text-decoration-none">
            <span aria-hidden="true">&larr;</span>
            Kembali ke Detail Jurnal
        </a>

        <div class="mt-3">
            <span class="td-section-eyebrow">SUNTING CATATAN</span>
            <h3 class="fw-bold mt-1 mb-1">Edit Jurnal</h3>
            <p class="text-td-muted mb-0">
                Perbarui cerita yang ingin kamu simpan. Cerita Guided Journaling juga bisa diedit langsung.
            </p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="td-card td-writing-card">
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

                    <div class="td-method-section mb-4">
                        <label for="entry_type" class="form-label fw-semibold mb-2">Metode Menulis</label>
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

                    <div id="guidedEditNote" class="alert alert-info py-2 px-3 small mb-4" hidden>
                        Ini adalah cerita yang sebelumnya disusun dari jawaban Guided Journaling.
                        Kamu bisa menyunting seluruh cerita secara langsung di bawah.
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                            <label for="content" id="contentLabel" class="form-label fw-semibold mb-0">
                                {{ $currentEntryType === 'guided' ? 'Cerita yang tersusun' : 'Isi Jurnal' }}
                            </label>

                            <span class="td-content-mode-label" id="editContentModeBadge">
                                {{ $currentEntryType === 'guided' ? 'Cerita tersusun' : 'Free Writing' }}
                            </span>
                        </div>

                        <textarea name="content" id="content" rows="13" maxlength="20000"
                            class="form-control td-writing-textarea @error('content') is-invalid @enderror"
                            placeholder="Mulailah menulis dari mana pun yang kamu mau..." required>{{ $currentContent }}</textarea>

                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-2">
                            <small id="contentHelp" class="text-td-muted">
                                {{ $currentEntryType === 'guided'
                                    ? 'Edit cerita final yang akan disimpan dan diproses ulang oleh Nala AI.'
                                    : 'Tulis atau perbarui ceritamu dengan bebas.' }}
                            </small>

                            <small id="contentCounterWrapper" class="td-character-counter">
                                <span id="contentCharCount" aria-live="polite">0</span> / 20.000 karakter
                            </small>
                        </div>
                    </div>

                    <div class="td-writing-privacy mb-4">
                        <div class="td-writing-privacy-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z" />
                                <rect x="9" y="10" width="6" height="5" rx="1" />
                                <path d="M10 10V8a2 2 0 0 1 4 0v2" />
                            </svg>
                        </div>

                        <div>
                            <h6 class="fw-semibold mb-1">Jurnal ini bersifat pribadi</h6>
                            <p class="text-td-muted small mb-0">
                                Jurnal hanya dapat dilihat oleh akunmu. Kamu dapat menentukan apakah ingin
                                membagikannya saat mengajukan konsultasi.
                            </p>
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

        <div class="col-lg-4 td-writing-sidebar">
            <div class="td-card td-writing-tip-card">
                <div class="td-tip-icon mb-3" id="writingTipIconBox">
                    <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 20h9" />
                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z" />
                        <path d="m15 5 3 3" />
                    </svg>
                </div>

                <span class="td-section-eyebrow">PANDUAN SUNTING</span>

                <h6 class="fw-bold mt-2 mb-3" id="writingTipTitle">
                    {{ $currentEntryType === 'guided' ? 'Cerita dari panduan' : 'Menulis bebas' }}
                </h6>

                <p class="text-td-muted small mb-0" id="writingTipText" style="line-height: 1.75;">
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

    {{-- Loading overlay --}}
    <div id="journalLoadingOverlay" class="td-journal-loading" aria-hidden="true">
        <div class="td-journal-loading__dialog" role="status" aria-live="polite" aria-atomic="true">
            <div class="td-journal-loading__spinner" aria-hidden="true"></div>
            <h5 class="fw-bold mb-2">Menyimpan perubahan jurnal</h5>
            <p class="text-td-muted small mb-0">
                Perubahanmu sedang disimpan. Jika isi atau metode menulis berubah,
                Nala AI juga akan memperbarui refleksinya. Jangan tutup halaman ini.
            </p>
        </div>
    </div>

    @push('scripts')
        <script>
            (() => {
                const form = document.getElementById('journalEditForm');
                const overlay = document.getElementById('journalLoadingOverlay');
                const entryType = document.getElementById('entry_type');
                const contentField = document.getElementById('content');
                const contentLabel = document.getElementById('contentLabel');
                const contentHelp = document.getElementById('contentHelp');
                const guidedNote = document.getElementById('guidedEditNote');
                const modeBadge = document.getElementById('editContentModeBadge');
                const tipTitle = document.getElementById('writingTipTitle');
                const tipText = document.getElementById('writingTipText');
                const contentCharCount = document.getElementById('contentCharCount');
                const contentCounterWrapper = document.getElementById('contentCounterWrapper');

                if (!form || !overlay || !entryType || !contentField) return;

                let isSubmitting = false;
                const submitButtons = form.querySelectorAll('[data-journal-submit]');

                function updateContentCounter() {
                    if (!contentCharCount || !contentCounterWrapper) return;
                    const totalCharacters = contentField.value.length;
                    contentCharCount.textContent = totalCharacters.toLocaleString('id-ID');

                    const isNearLimit = totalCharacters >= 19000;
                    const isAtLimit = totalCharacters >= 20000;

                    contentCounterWrapper.classList.toggle('is-near-limit', isNearLimit && !isAtLimit);
                    contentCounterWrapper.classList.toggle('is-at-limit', isAtLimit);
                }

                function updateWritingMode() {
                    const isGuided = entryType.value === 'guided';

                    if (guidedNote) guidedNote.hidden = !isGuided;
                    if (modeBadge) modeBadge.textContent = isGuided ? 'Cerita tersusun' : 'Free Writing';

                    contentLabel.textContent = isGuided ? 'Cerita yang tersusun' : 'Isi Jurnal';
                    contentHelp.textContent = isGuided ?
                        'Edit cerita final yang akan disimpan dan diproses ulang oleh Nala AI.' :
                        'Tulis atau perbarui ceritamu dengan bebas.';

                    if (tipTitle) tipTitle.textContent = isGuided ? 'Cerita dari panduan' : 'Menulis bebas';
                    if (tipText) {
                        tipText.textContent = isGuided ?
                            'Cerita ini sebelumnya disusun dari jawaban panduan. Kamu bisa menyuntingnya langsung sebagai satu kesatuan.' :
                            'Kamu bebas mengubah cerita tanpa mengikuti urutan tertentu. Tulis kejadian, pikiran, atau perasaan yang ingin kamu simpan.';
                    }
                }

                contentField.addEventListener('input', updateContentCounter);
                entryType.addEventListener('change', updateWritingMode);
                updateWritingMode();
                updateContentCounter();

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
    @endpush

@endsection
