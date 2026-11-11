@extends('layouts.app')

@section('title', 'Tulis Jurnal')

@section('content')

    <div class="mb-4">
        <a href="{{ route('journals.index') }}" class="text-td-primary text-decoration-none small">
            &larr; Kembali ke Journaling
        </a>

        <h3 class="fw-bold mt-3 mb-1">Tulis Ceritamu</h3>

        <p class="text-td-muted">
            Tidak perlu memikirkan susunan kata yang sempurna.
            Tulis saja apa yang ingin kamu ceritakan.
        </p>
    </div>

    <div class="row g-4">

        {{-- Form jurnal --}}
        <div class="col-lg-8">

            <div class="td-card">

                <form action="{{ route('journals.store') }}" method="POST">
                    @csrf

                    {{-- Judul --}}
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

                    {{-- Jenis jurnal --}}
                    <div class="mb-3">
                        <label for="entry_type" class="form-label fw-medium">
                            Metode Menulis
                        </label>

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

                    {{-- Mood --}}
                    <div class="mb-3">
                        <label for="mood" class="form-label fw-medium">
                            Bagaimana perasaanmu saat ini?
                            <span class="text-td-muted">(Opsional)</span>
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

                    {{-- Catatan mood --}}
                    <div class="mb-3">
                        <label for="mood_note" class="form-label fw-medium">
                            Catatan Mood <span class="text-td-muted">(Opsional)</span>
                        </label>

                        <input type="text" name="mood_note" id="mood_note" class="form-control"
                            placeholder="Apa yang kamu rasakan?" value="{{ old('mood_note') }}" maxlength="500">

                        @error('mood_note')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Isi jurnal --}}
                    <div class="mb-4">
                        <label for="content" class="form-label fw-medium">
                            Ceritamu
                        </label>

                        <textarea name="content" id="content" rows="12" maxlength="20000"
                            class="form-control @error('content') is-invalid @enderror"
                            placeholder="Mulailah menulis dari mana pun yang kamu mau..." required>{{ old('content') }}</textarea>

                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="text-end text-td-muted small mt-1">
                            Maksimal 20.000 karakter
                        </div>
                    </div>

                    {{-- Privasi --}}
                    <div class="bg-light rounded-3 p-3 mb-4">
                        <div class="d-flex gap-2">
                            <span class="text-td-primary fw-bold">P</span>

                            <div>
                                <h6 class="fw-semibold small mb-1">
                                    Jurnal ini bersifat pribadi
                                </h6>

                                <p class="text-td-muted small mb-0">
                                    Jurnal hanya dapat dilihat oleh akunmu.
                                    Kamu dapat menentukan apakah ingin membagikannya
                                    saat mengajukan konsultasi.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('journals.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-td">
                            Simpan Jurnal
                        </button>
                    </div>

                </form>

            </div>
        </div>

        {{-- Panduan menulis --}}
        <div class="col-lg-4">
            <div class="td-card">
                <div class="feature-icon mb-3">?</div>

                <h6 class="fw-bold mb-3">Butuh inspirasi untuk mulai?</h6>

                <p class="text-td-muted small">
                    Coba gunakan beberapa pertanyaan ini sebagai panduan:
                </p>

                <div class="border-start border-3 ps-3 mb-3" style="border-color: #73B7AA !important;">
                    <p class="small mb-0">
                        Apa yang terjadi hari ini?
                    </p>
                </div>

                <div class="border-start border-3 ps-3 mb-3" style="border-color: #73B7AA !important;">
                    <p class="small mb-0">
                        Apa hal yang paling membebani pikiranmu?
                    </p>
                </div>

                <div class="border-start border-3 ps-3 mb-3" style="border-color: #73B7AA !important;">
                    <p class="small mb-0">
                        Apa yang sebenarnya ingin kamu sampaikan kepada seseorang?
                    </p>
                </div>

                <div class="border-start border-3 ps-3" style="border-color: #73B7AA !important;">
                    <p class="small mb-0">
                        Dukungan seperti apa yang sedang kamu butuhkan?
                    </p>
                </div>

                <hr>

                <p class="text-td-muted small mb-0">
                    Kamu tidak harus menjawab semuanya. Pilih pertanyaan
                    yang paling sesuai dengan keadaanmu.
                </p>
            </div>
        </div>

    </div>

@endsection
