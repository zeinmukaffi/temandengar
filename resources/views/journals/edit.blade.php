@extends('layouts.app')

@section('title', 'Edit Jurnal')

@section('content')

    <div class="mb-4">
        <a href="{{ route('journals.show', $journal->id) }}" class="text-td-primary text-decoration-none small">
            &larr; Kembali ke Detail Jurnal
        </a>

        <h3 class="fw-bold mt-3 mb-1">Edit Jurnal</h3>

        <p class="text-td-muted">
            Perbarui cerita yang ingin kamu simpan.
        </p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="td-card">

                <form id="journalEditForm" action="{{ route('journals.update', $journal->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="title" class="form-label fw-medium">
                            Judul Jurnal
                        </label>

                        <input type="text" name="title" id="title"
                            class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title', $journal->title) }}" maxlength="160">

                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="entry_type" class="form-label fw-medium">
                            Metode Menulis
                        </label>

                        <select name="entry_type" id="entry_type" class="form-select" required>
                            <option value="free"
                                {{ old('entry_type', $journal->entry_type) === 'free' ? 'selected' : '' }}>
                                Free Writing
                            </option>

                            <option value="guided"
                                {{ old('entry_type', $journal->entry_type) === 'guided' ? 'selected' : '' }}>
                                Guided Journaling
                            </option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="content" class="form-label fw-medium">
                            Isi Jurnal
                        </label>

                        <textarea name="content" id="content" rows="14" maxlength="20000"
                            class="form-control @error('content') is-invalid @enderror" required>{{ old('content', $journal->content) }}</textarea>

                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
    </div>


    {{-- Loading overlay saat perubahan jurnal disimpan --}}
    <div id="journalLoadingOverlay" class="td-journal-loading" aria-hidden="true">
        <div class="td-journal-loading__dialog" role="status" aria-live="polite" aria-atomic="true">
            <div class="td-journal-loading__spinner" aria-hidden="true"></div>
            <h5 class="fw-bold mb-2">Menyimpan perubahan jurnal</h5>
            <p class="text-td-muted small mb-0">
                Perubahanmu sedang disimpan. Jika judul, isi, atau metode menulis berubah,
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

            if (!form || !overlay) return;

            let isSubmitting = false;
            const submitButtons = form.querySelectorAll('[data-journal-submit]');

            form.addEventListener('submit', (event) => {
                // Cegah pengiriman ganda akibat klik berulang.
                if (isSubmitting) {
                    event.preventDefault();
                    return;
                }

                // Event submit hanya berjalan setelah validasi HTML form lolos.
                isSubmitting = true;
                overlay.classList.add('is-visible');
                overlay.setAttribute('aria-hidden', 'false');

                submitButtons.forEach((button) => {
                    button.disabled = true;
                    button.setAttribute('aria-disabled', 'true');
                });
            });

            // Pulihkan tampilan bila halaman dikembalikan dari browser cache.
            window.addEventListener('pageshow', () => {
                isSubmitting = false;
                overlay.classList.remove('is-visible');
                overlay.setAttribute('aria-hidden', 'true');
                submitButtons.forEach((button) => {
                    button.disabled = false;
                    button.removeAttribute('aria-disabled');
                });
            });
        })();
    </script>

@endsection
