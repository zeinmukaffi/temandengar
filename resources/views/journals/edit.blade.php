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

                <form action="{{ route('journals.update', $journal->id) }}" method="POST">
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

                        <button type="submit" class="btn btn-td">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

@endsection
