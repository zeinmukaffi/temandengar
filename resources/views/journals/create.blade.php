@extends('layouts.app')

@section('title', 'Tulis Jurnal')

@section('content')

    <div class="mb-4">
        <a href="{{ route('journals.index') }}" class="td-back-link text-decoration-none">
            <span aria-hidden="true">&larr;</span>
            Kembali ke Journaling
        </a>

        <div class="mt-3">
            <span class="td-section-eyebrow">RUANG PRIBADI</span>

            <h3 class="fw-bold mt-1 mb-1">
                Tulis Ceritamu
            </h3>

            <p class="text-td-muted mb-0">
                Pilih cara menulis yang paling nyaman untukmu. Tidak ada jawaban yang salah.
            </p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="td-card td-writing-card">
                <form id="journalCreateForm" action="{{ route('journals.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="title" class="form-label fw-medium">
                            Judul Jurnal <span class="text-td-muted">(Opsional)</span>
                        </label>
                        <input type="text" id="title" name="title"
                            class="form-control @error('title') is-invalid @enderror"
                            placeholder="Beri judul untuk ceritamu" value="{{ old('title') }}" maxlength="160">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="td-method-section mb-4">
                        <label for="entry_type" class="form-label fw-semibold mb-1">
                            Bagaimana kamu ingin menulis?
                        </label>

                        <p class="text-td-muted small mb-3">
                            Kamu bisa menulis bebas atau mengikuti beberapa pertanyaan panduan.
                        </p>

                        <select name="entry_type" id="entry_type"
                            class="form-select @error('entry_type') is-invalid @enderror" required>
                            <option value="free" {{ old('entry_type', 'free') === 'free' ? 'selected' : '' }}>
                                Free Writing - Menulis bebas
                            </option>
                            <option value="guided" {{ old('entry_type') === 'guided' ? 'selected' : '' }}>
                                Guided Journaling - Menggunakan panduan
                            </option>
                        </select>

                        @error('entry_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-5">
                            <label for="mood" class="form-label fw-medium">
                                Perasaan saat ini <span class="text-td-muted">(Opsional)</span>
                            </label>
                            <select name="mood" id="mood" class="form-select">
                                <option value="">Tidak memilih mood</option>
                                @foreach ($moods as $value => $label)
                                    <option value="{{ $value }}" {{ old('mood') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mood')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-7">
                            <label for="mood_note" class="form-label fw-medium">
                                Catatan Mood <span class="text-td-muted">(Opsional)</span>
                            </label>
                            <input type="text" name="mood_note" id="mood_note" class="form-control"
                                placeholder="Apa yang membuatmu merasa demikian?" value="{{ old('mood_note') }}"
                                maxlength="500">
                            @error('mood_note')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Guided Journaling --}}
                    <div id="guidedWritingSection" class="td-guided-section mb-4" hidden>

                        <div class="mb-4">
                            <div class="d-flex align-items-start gap-3">
                                <div class="td-guided-intro-icon">
                                    <svg width="23" height="23" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                        stroke-linejoin="round" aria-hidden="true">
                                        <path d="M12 3v3M12 18v3M3 12h3M18 12h3" />
                                        <circle cx="12" cy="12" r="6" />
                                        <path d="m10 12 1.5 1.5L14.5 10" />
                                    </svg>
                                </div>

                                <div>
                                    <h6 class="fw-bold mb-1">Mari susun ceritamu perlahan.</h6>
                                    <p class="text-td-muted small mb-0">
                                        Jawab satu atau beberapa pertanyaan berikut. Jawabanmu akan disusun
                                        menjadi cerita yang bisa kamu edit sebelum disimpan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="td-guided-question-card mb-3">
                            <div class="td-question-heading">
                                <span class="td-question-number">01</span>
                                <label for="guided_today" class="form-label fw-semibold mb-0">
                                    Apa yang terjadi hari ini?
                                </label>
                            </div>
                            <textarea id="guided_today" name="guided_answers[today]" rows="3" maxlength="4500" class="form-control"
                                data-guided-answer placeholder="Ceritakan kejadian yang paling berkesan...">{{ old('guided_answers.today') }}</textarea>
                        </div>

                        <div class="td-guided-question-card mb-3">
                            <div class="td-question-heading">
                                <span class="td-question-number">02</span>
                                <label for="guided_burden" class="form-label fw-semibold mb-0">
                                    Apa hal yang paling membebani pikiranmu?
                                </label>
                            </div>
                            <textarea id="guided_burden" name="guided_answers[burden]" rows="3" maxlength="4500" class="form-control"
                                data-guided-answer placeholder="Contoh: tugas menumpuk dan deadline berdekatan...">{{ old('guided_answers.burden') }}</textarea>
                        </div>

                        <div class="td-guided-question-card mb-3">
                            <div class="td-question-heading">
                                <span class="td-question-number">03</span>
                                <label for="guided_feeling" class="form-label fw-semibold mb-0">
                                    Bagaimana perasaanmu menghadapi situasi tersebut?
                                </label>
                            </div>
                            <textarea id="guided_feeling" name="guided_answers[feeling]" rows="3" maxlength="4500" class="form-control"
                                data-guided-answer placeholder="Contoh: lelah, cemas, dan sulit fokus...">{{ old('guided_answers.feeling') }}</textarea>
                        </div>

                        <div class="td-guided-question-card mb-3">
                            <div class="td-question-heading">
                                <span class="td-question-number">04</span>
                                <label for="guided_support" class="form-label fw-semibold mb-0">
                                    Dukungan atau perubahan apa yang kamu butuhkan sekarang?
                                </label>
                            </div>
                            <textarea id="guided_support" name="guided_answers[support]" rows="3" maxlength="4500" class="form-control"
                                data-guided-answer placeholder="Contoh: waktu istirahat dan teman untuk bercerita...">{{ old('guided_answers.support') }}</textarea>
                        </div>

                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                            <small class="text-td-muted">
                                Minimal satu jawaban. Setiap jawaban maksimal 4.500 karakter.
                            </small>

                            <button type="button" id="rebuildGuidedStory" class="btn btn-outline-td btn-sm">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="me-1" aria-hidden="true">
                                    <path d="M20 7v5h-5" />
                                    <path d="M4 17v-5h5" />
                                    <path d="M5.6 9a7 7 0 0 1 11.5-2L20 12" />
                                    <path d="m4 12 2.9 5a7 7 0 0 0 11.5-2" />
                                </svg>
                                Susun ulang cerita
                            </button>
                        </div>

                        <div id="guidedValidationMessage" class="td-guided-validation mt-3" role="alert" hidden>
                            Isi setidaknya satu jawaban sebelum menyimpan jurnal.
                        </div>
                    </div>

                    {{-- Final Journal Content --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                            <label for="content" id="contentLabel" class="form-label fw-semibold mb-0">
                                Ceritamu
                            </label>

                            <span class="td-content-mode-label">
                                {{ old('entry_type', 'free') === 'guided' ? 'Cerita tersusun' : 'Free Writing' }}
                            </span>
                        </div>

                        <textarea name="content" id="content" rows="12" maxlength="20000"
                            class="form-control td-writing-textarea @error('content') is-invalid @enderror"
                            placeholder="Mulailah menulis dari mana pun yang kamu mau..." required>{{ old('content') }}</textarea>

                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-2">
                            <small id="contentHelp" class="text-td-muted">
                                Tulis dengan bebas. Ceritamu hanya dapat dilihat oleh akunmu.
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
                                Jurnal hanya dapat dilihat oleh akunmu. Kamu tetap memiliki kendali
                                untuk menentukan apakah ingin membagikannya saat mengajukan konsultasi.
                            </p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('journals.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-td" data-journal-submit>Simpan Jurnal</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar Tips --}}
        <div class="col-lg-4 td-writing-sidebar">

            <div class="td-card td-writing-tip-card mb-4" id="freeWritingTips">
                <div class="td-tip-icon mb-3">
                    <svg id="freeWritingTipIcon" width="26" height="26" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <path d="M12 20h9" />
                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z" />
                        <path d="m15 5 3 3" />
                    </svg>
                </div>

                <span class="td-section-eyebrow">TIPS MENULIS</span>
                <h6 class="fw-bold mt-2 mb-3">Menulis bebas</h6>

                <p class="text-td-muted small mb-3">
                    Mulai dari mana pun yang terasa nyaman. Kamu bisa menulis
                    kejadian, pikiran, atau perasaan tanpa mengikuti urutan tertentu.
                </p>

                <div class="td-writing-tip-quote mb-3">
                    <p class="small mb-0">
                        Apa hal yang paling ingin kamu keluarkan dari pikiranmu?
                    </p>
                </div>

                <div class="td-writing-tip-quote">
                    <p class="small mb-0">
                        Tidak perlu menyusun kata dengan sempurna. Tulis saja dengan jujur.
                    </p>
                </div>
            </div>

            <div class="td-card td-writing-tip-card mb-4" id="guidedWritingTips" hidden>
                <div class="td-tip-icon td-tip-icon-guided mb-3">
                    <svg id="guidedWritingTipIcon" width="26" height="26" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <circle cx="12" cy="12" r="9" />
                        <path d="m12 7 1.7 3.5L17 12l-3.3 1.5L12 17l-1.7-3.5L7 12l3.3-1.5Z" />
                    </svg>
                </div>

                <span class="td-section-eyebrow">TIPS MENULIS</span>
                <h6 class="fw-bold mt-2 mb-3">Menulis dengan panduan</h6>

                <p class="text-td-muted small mb-3">
                    Jawab pertanyaan secara bertahap. Setelah itu, TemanDengar menyusun
                    jawabanmu menjadi satu cerita. Kamu tetap bisa mengedit hasil susunannya.
                </p>

                <div class="td-writing-tip-note">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
                        class="flex-shrink-0 mt-1">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 11v5M12 8h.01" />
                    </svg>

                    <p class="small mb-0">
                        Jawaban yang kosong tidak akan dimasukkan ke cerita.
                    </p>
                </div>
            </div>

        </div>
    </div>

    {{-- Loading overlay --}}
    <div id="journalLoadingOverlay" class="td-journal-loading" aria-hidden="true">
        <div class="td-journal-loading__dialog" role="status" aria-live="polite" aria-atomic="true">
            <div class="td-journal-loading__spinner" aria-hidden="true"></div>
            <h5 class="fw-bold mb-2">Jurnal sedang disimpan</h5>
            <p class="text-td-muted small mb-0">
                Ceritamu sedang disimpan dan Nala AI sedang menyiapkan refleksi.
                Proses ini bisa memerlukan beberapa saat. Jangan tutup halaman ini.
            </p>
        </div>
    </div>

    @push('scripts')
        <script>
            (() => {
                const form = document.getElementById('journalCreateForm');
                const overlay = document.getElementById('journalLoadingOverlay');
                const entryType = document.getElementById('entry_type');
                const guidedSection = document.getElementById('guidedWritingSection');
                const contentField = document.getElementById('content');
                const contentLabel = document.getElementById('contentLabel');
                const contentHelp = document.getElementById('contentHelp');
                const guidedTips = document.getElementById('guidedWritingTips');
                const freeTips = document.getElementById('freeWritingTips');
                const guidedMessage = document.getElementById('guidedValidationMessage');
                const rebuildButton = document.getElementById('rebuildGuidedStory');
                const contentCharCount = document.getElementById('contentCharCount');
                const contentCounterWrapper = document.getElementById('contentCounterWrapper');
                const contentModeLabel = document.querySelector('.td-content-mode-label');

                if (!form || !overlay || !entryType || !contentField) return;

                let isSubmitting = false;
                let contentManuallyEdited = contentField.value.trim().length > 0;

                function updateContentCounter() {
                    if (!contentCharCount || !contentCounterWrapper) return;
                    const totalCharacters = contentField.value.length;
                    contentCharCount.textContent = totalCharacters.toLocaleString('id-ID');

                    const isNearLimit = totalCharacters >= 19000;
                    const isAtLimit = totalCharacters >= 20000;

                    contentCounterWrapper.classList.toggle('is-near-limit', isNearLimit && !isAtLimit);
                    contentCounterWrapper.classList.toggle('is-at-limit', isAtLimit);
                }

                const submitButtons = form.querySelectorAll('[data-journal-submit]');
                const guidedFields = Array.from(form.querySelectorAll('[data-guided-answer]'));

                function addFinalPunctuation(value) {
                    const text = value.trim();
                    if (!text) return '';
                    return /[.!?…]$/.test(text) ? text : `${text}.`;
                }

                function buildGuidedStory() {
                    const [today, burden, feeling, support] = guidedFields.map(field => field.value.trim());
                    const paragraphs = [];

                    if (today) paragraphs.push(addFinalPunctuation(today));
                    if (burden) paragraphs.push(
                        `Hal lain yang cukup membebani pikiranku adalah ${addFinalPunctuation(burden)}`);
                    if (feeling) paragraphs.push(
                        `Saat menghadapi situasi tersebut, perasaan yang muncul adalah ${addFinalPunctuation(feeling)}`);
                    if (support) paragraphs.push(`Saat ini, hal yang aku butuhkan adalah ${addFinalPunctuation(support)}`);

                    return paragraphs.join('\n\n');
                }

                function refreshGuidedStory(force = false) {
                    if (entryType.value !== 'guided') return;

                    const hasAnswer = guidedFields.some(field => field.value.trim() !== '');
                    if (!hasAnswer) {
                        if (guidedMessage) {
                            guidedMessage.textContent = 'Isi setidaknya satu jawaban sebelum menyimpan jurnal.';
                            guidedMessage.hidden = false;
                        }
                        return false;
                    }

                    if (!contentManuallyEdited || force) {
                        const story = buildGuidedStory();
                        if (story.length > 20000) {
                            if (guidedMessage) {
                                guidedMessage.textContent =
                                    'Cerita hasil susunan melebihi 20.000 karakter. Ringkas jawabanmu.';
                                guidedMessage.hidden = false;
                            }
                            return false;
                        }

                        contentField.value = story;
                        contentManuallyEdited = false;
                        updateContentCounter();
                    }

                    if (guidedMessage) guidedMessage.hidden = true;
                    return true;
                }

                function toggleWritingMode() {
                    const isGuided = entryType.value === 'guided';

                    guidedSection.hidden = !isGuided;
                    guidedTips.hidden = !isGuided;
                    freeTips.hidden = isGuided;

                    if (contentModeLabel) {
                        contentModeLabel.textContent = isGuided ? 'Cerita tersusun' : 'Free Writing';
                    }

                    contentLabel.textContent = isGuided ? 'Cerita yang tersusun (bisa diedit)' : 'Ceritamu';
                    contentField.placeholder = isGuided ?
                        'Jawab pertanyaan di atas untuk menyusun cerita. Kamu juga bisa mengedit teks ini.' :
                        'Mulailah menulis dari mana pun yang kamu mau...';
                    contentHelp.textContent = isGuided ?
                        'Ini adalah cerita final yang akan disimpan dan dikirim ke Nala AI.' :
                        'Tulis dengan bebas. Ceritamu hanya dapat dilihat oleh akunmu.';

                    if (isGuided && guidedFields.some(field => field.value.trim() !== '') && !contentField.value.trim()) {
                        refreshGuidedStory();
                    }

                    if (guidedMessage) guidedMessage.hidden = true;
                }

                guidedFields.forEach(field => {
                    field.addEventListener('input', () => {
                        if (guidedMessage) guidedMessage.hidden = true;
                        if (!contentManuallyEdited) refreshGuidedStory();
                    });
                });

                contentField.addEventListener('input', () => {
                    if (entryType.value === 'guided') {
                        contentManuallyEdited = true;
                    }
                    updateContentCounter();
                });

                rebuildButton?.addEventListener('click', () => {
                    contentManuallyEdited = false;
                    refreshGuidedStory(true);
                });

                entryType.addEventListener('change', toggleWritingMode);
                toggleWritingMode();
                updateContentCounter();

                form.addEventListener('submit', (event) => {
                    if (isSubmitting) {
                        event.preventDefault();
                        return;
                    }

                    if (entryType.value === 'guided') {
                        const hasAnswer = guidedFields.some(field => field.value.trim() !== '');
                        if (!hasAnswer) {
                            event.preventDefault();
                            if (guidedMessage) {
                                guidedMessage.textContent = 'Isi setidaknya satu jawaban sebelum menyimpan jurnal.';
                                guidedMessage.hidden = false;
                            }
                            guidedFields[0]?.focus();
                            return;
                        }

                        if (!contentManuallyEdited && !refreshGuidedStory()) {
                            event.preventDefault();
                            return;
                        }
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
