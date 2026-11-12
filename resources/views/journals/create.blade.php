@extends('layouts.app')

@section('title', 'Tulis Jurnal')

@section('content')

    <div class="mb-4">
        <a href="{{ route('journals.index') }}" class="text-td-primary text-decoration-none small">
            &larr; Kembali ke Journaling
        </a>

        <h3 class="fw-bold mt-3 mb-1">Tulis Ceritamu</h3>

        <p class="text-td-muted">
            Pilih cara menulis yang paling nyaman untukmu. Tidak ada jawaban yang salah.
        </p>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="td-card">
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

                    <div class="mb-3">
                        <label for="entry_type" class="form-label fw-medium">Metode Menulis</label>
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

                    <div class="mb-3">
                        <label for="mood" class="form-label fw-medium">
                            Bagaimana perasaanmu saat ini? <span class="text-td-muted">(Opsional)</span>
                        </label>
                        <select name="mood" id="mood" class="form-select">
                            <option value="">Tidak ingin memilih mood</option>
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

                    <div class="mb-4">
                        <label for="mood_note" class="form-label fw-medium">
                            Catatan Mood <span class="text-td-muted">(Opsional)</span>
                        </label>
                        <input type="text" name="mood_note" id="mood_note" class="form-control"
                            placeholder="Apa yang kamu rasakan?" value="{{ old('mood_note') }}" maxlength="500">
                        @error('mood_note')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Guided Journaling: jawaban menjadi bahan untuk menyusun draf cerita. --}}
                    <div id="guidedWritingSection" class="mb-4" hidden>
                        <div class="alert alert-info py-2 small">
                            Jawab satu atau beberapa pertanyaan. Jawabanmu akan disusun menjadi cerita yang bisa kamu edit
                            sebelum disimpan.
                        </div>

                        <div class="mb-3">
                            <label for="guided_today" class="form-label fw-semibold">
                                1. Apa yang terjadi hari ini?
                            </label>
                            <textarea id="guided_today" name="guided_answers[today]" rows="3" maxlength="4500" class="form-control"
                                data-guided-answer placeholder="Ceritakan kejadian yang paling berkesan...">{{ old('guided_answers.today') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="guided_burden" class="form-label fw-semibold">
                                2. Apa hal yang paling membebani pikiranmu?
                            </label>
                            <textarea id="guided_burden" name="guided_answers[burden]" rows="3" maxlength="4500" class="form-control"
                                data-guided-answer placeholder="Contoh: tugas menumpuk dan deadline berdekatan...">{{ old('guided_answers.burden') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="guided_feeling" class="form-label fw-semibold">
                                3. Bagaimana perasaanmu menghadapi situasi tersebut?
                            </label>
                            <textarea id="guided_feeling" name="guided_answers[feeling]" rows="3" maxlength="4500" class="form-control"
                                data-guided-answer placeholder="Contoh: lelah, cemas, dan sulit fokus...">{{ old('guided_answers.feeling') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="guided_support" class="form-label fw-semibold">
                                4. Dukungan atau perubahan apa yang kamu butuhkan sekarang?
                            </label>
                            <textarea id="guided_support" name="guided_answers[support]" rows="3" maxlength="4500" class="form-control"
                                data-guided-answer placeholder="Contoh: waktu istirahat dan teman untuk bercerita...">{{ old('guided_answers.support') }}</textarea>
                        </div>

                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div class="text-td-muted small">
                                Minimal satu jawaban. Setiap jawaban maksimal 4.500 karakter.
                            </div>
                            <button type="button" id="rebuildGuidedStory" class="btn btn-outline-td btn-sm">
                                Susun ulang cerita
                            </button>
                        </div>

                        <div id="guidedValidationMessage" class="text-danger small mt-2" role="alert" hidden>
                            Isi setidaknya satu jawaban sebelum menyimpan jurnal.
                        </div>
                    </div>

                    {{-- Satu field content dipakai untuk Free Writing dan cerita final Guided. --}}
                    <div class="mb-4">
                        <label for="content" id="contentLabel" class="form-label fw-medium">Ceritamu</label>
                        <textarea name="content" id="content" rows="12" maxlength="20000"
                            class="form-control @error('content') is-invalid @enderror"
                            placeholder="Mulailah menulis dari mana pun yang kamu mau..." required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-between align-items-center gap-2 mt-1">
                            <small id="contentHelp" class="text-td-muted">
                                Tulis dengan bebas. Ceritamu hanya dapat dilihat oleh akunmu.
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
                        <a href="{{ route('journals.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-td" data-journal-submit>Simpan Jurnal</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="td-card" id="freeWritingTips">
                <div class="feature-icon mb-3">F</div>
                <h6 class="fw-bold mb-3">Menulis bebas</h6>
                <p class="text-td-muted small">
                    Mulai dari mana pun yang terasa nyaman. Kamu bisa menulis kejadian, pikiran,
                    atau perasaan tanpa mengikuti urutan tertentu.
                </p>
                <div class="border-start border-3 ps-3 mb-3" style="border-color: #73B7AA !important;">
                    <p class="small mb-0">Apa hal yang paling ingin kamu keluarkan dari pikiranmu?</p>
                </div>
                <div class="border-start border-3 ps-3" style="border-color: #73B7AA !important;">
                    <p class="small mb-0">Tidak perlu menyusun kata dengan sempurna. Tulis saja dengan jujur.</p>
                </div>
            </div>

            <div class="td-card" id="guidedWritingTips" hidden>
                <div class="feature-icon mb-3">G</div>
                <h6 class="fw-bold mb-3">Menulis dengan panduan</h6>
                <p class="text-td-muted small">
                    Jawab pertanyaan secara bertahap. Setelah itu, TemanDengar menyusun jawabanmu
                    menjadi satu cerita. Kamu tetap bisa mengedit hasil susunannya.
                </p>
                <div class="bg-td-soft rounded-3 p-3">
                    <p class="small mb-0 text-td-primary">
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
                Ceritamu sedang disimpan dan Hermes sedang menyiapkan refleksi.
                Proses ini bisa memerlukan beberapa saat. Jangan tutup halaman ini.
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

            if (!form || !overlay || !entryType || !contentField) return;

            let isSubmitting = false;
            let contentManuallyEdited = contentField.value.trim().length > 0;
            const submitButtons = form.querySelectorAll('[data-journal-submit]');
            const guidedFields = Array.from(form.querySelectorAll('[data-guided-answer]'));

            function addFinalPunctuation(value) {
                const text = value.trim();
                if (!text) return '';
                return /[.!?…]$/.test(text) ? text : `${text}.`;
            }

            function buildGuidedStory() {
                const answers = guidedFields.map(field => field.value.trim());
                const [today, burden, feeling, support] = answers;
                const paragraphs = [];

                if (today) paragraphs.push(addFinalPunctuation(today));

                if (burden) {
                    paragraphs.push(
                        `Hal lain yang cukup membebani pikiranku adalah ${addFinalPunctuation(burden)}`
                    );
                }

                if (feeling) {
                    paragraphs.push(
                        `Saat menghadapi situasi tersebut, perasaan yang muncul adalah ${addFinalPunctuation(feeling)}`
                    );
                }

                if (support) {
                    paragraphs.push(
                        `Saat ini, hal yang aku butuhkan adalah ${addFinalPunctuation(support)}`
                    );
                }

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
                }

                if (guidedMessage) guidedMessage.hidden = true;
                return true;
            }

            function toggleWritingMode() {
                const isGuided = entryType.value === 'guided';
                guidedSection.hidden = !isGuided;
                guidedTips.hidden = !isGuided;
                freeTips.hidden = isGuided;
                // Mode Guided membuat content melalui jawaban, jadi validasi kosong
                // ditangani di submit setelah cerita disusun.
                contentField.required = !isGuided;

                contentLabel.textContent = isGuided ?
                    'Cerita yang tersusun (bisa diedit)' :
                    'Ceritamu';

                contentField.placeholder = isGuided ?
                    'Jawab pertanyaan di atas untuk menyusun cerita. Kamu juga bisa mengedit teks ini.' :
                    'Mulailah menulis dari mana pun yang kamu mau...';

                contentHelp.textContent = isGuided ?
                    'Ini adalah cerita final yang akan disimpan dan dikirim ke Hermes.' :
                    'Tulis dengan bebas. Ceritamu hanya dapat dilihat oleh akunmu.';

                if (isGuided && guidedFields.some(field => field.value.trim() !== '') &&
                    !contentField.value.trim()) {
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
                if (entryType.value === 'guided') contentManuallyEdited = true;
            });

            rebuildButton?.addEventListener('click', () => {
                contentManuallyEdited = false;
                refreshGuidedStory(true);
            });

            entryType.addEventListener('change', toggleWritingMode);
            toggleWritingMode();

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

                    // Jika draft belum pernah diedit, susun ulang dari jawaban terbaru.
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

@endsection
