@extends('layouts.app')

@section('title', 'Journaling')

@section('content')

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-td-muted small mb-2">RUANG CERITA</p>
            <h3 class="fw-bold mb-1">Journaling</h3>
            <p class="text-td-muted mb-0">
                Ruang pribadi untuk menuangkan apa yang kamu rasakan.
            </p>
        </div>

        <a href="{{ route('journals.create') }}" class="btn btn-td">
            + Tulis Jurnal
        </a>
    </div>

    {{-- Mood Check-in --}}
    <div class="td-card mb-4">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="feature-icon">M</div>
            <div>
                <h5 class="fw-bold mb-1">Bagaimana perasaanmu hari ini?</h5>
                <p class="text-td-muted small mb-0">
                    Catat mood-mu tanpa harus menulis jurnal.
                </p>
            </div>
        </div>

        <form action="{{ route('mood-checkins.store') }}" method="POST">
            @csrf

            <div class="row g-2 mb-3">
                @foreach (config('temandengar.moods') as $value => $label)
                    <div class="col-6 col-sm-4 col-lg">
                        <input type="radio" class="btn-check" name="mood" id="mood-{{ $value }}"
                            value="{{ $value }}" {{ old('mood') === $value ? 'checked' : '' }} required>

                        <label class="btn btn-outline-secondary w-100 py-2" for="mood-{{ $value }}">
                            {{ $label }}
                        </label>
                    </div>
                @endforeach
            </div>

            @error('mood')
                <div class="text-danger small mb-2">{{ $message }}</div>
            @enderror

            <div class="row g-2 align-items-end">
                <div class="col-md-9">
                    <label for="mood-note" class="form-label small fw-medium">
                        Catatan singkat (opsional)
                    </label>

                    <input type="text" name="note" id="mood-note" class="form-control"
                        placeholder="Apa yang membuatmu merasa seperti ini?" value="{{ old('note') }}" maxlength="500">

                    @error('note')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-td w-100">
                        Simpan Mood
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Riwayat Mood Check-in --}}
    @if ($recentMoodCheckins->isNotEmpty())
        <div class="td-card mb-4">
            <h6 class="fw-bold mb-3">Mood Terakhir</h6>

            <div class="d-flex flex-wrap gap-2">
                @foreach ($recentMoodCheckins as $checkin)
                    <div class="border rounded-3 px-3 py-2">
                        <div class="fw-semibold small">
                            {{ config('temandengar.moods.' . $checkin->mood, ucfirst($checkin->mood)) }}
                        </div>

                        <div class="text-td-muted" style="font-size: 11px;">
                            {{ $checkin->checked_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Riwayat Jurnal --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Riwayat Jurnal</h5>

        <span class="text-td-muted small">
            {{ $journals->total() }} jurnal
        </span>
    </div>

    @if ($journals->isEmpty())

        <div class="td-card text-center py-5">
            <div class="feature-icon mx-auto mb-3">J</div>

            <h5 class="fw-bold">Belum ada jurnal</h5>

            <p class="text-td-muted small mb-4">
                Kamu bisa mulai dengan menuliskan apa pun yang sedang kamu pikirkan.
            </p>

            <a href="{{ route('journals.create') }}" class="btn btn-td">
                Tulis Jurnal Pertamamu
            </a>
        </div>
    @else
        <div class="row g-3">
            @foreach ($journals as $journal)
                <div class="col-md-6">
                    <div class="td-card">

                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                            <div>
                                <span class="badge bg-light text-secondary">
                                    {{ $journal->entry_type === 'guided' ? 'Guided' : 'Free Writing' }}
                                </span>

                                <span class="badge bg-td-soft text-td-primary">
                                    Pribadi
                                </span>
                            </div>

                            <small class="text-td-muted">
                                {{ $journal->journaled_at->format('d M Y') }}
                            </small>
                        </div>

                        <h6 class="fw-bold mb-2">
                            {{ $journal->title ?: 'Catatan Pribadi' }}
                        </h6>

                        <p class="text-td-muted small mb-3">
                            {{ \Illuminate\Support\Str::limit($journal->content, 150) }}
                        </p>

                        @if ($journal->moodCheckin)
                            <p class="small mb-3">
                                Mood:
                                <strong>
                                    {{ config('temandengar.moods.' . $journal->moodCheckin->mood, ucfirst($journal->moodCheckin->mood)) }}
                                </strong>
                            </p>
                        @endif

                        <a href="{{ route('journals.show', $journal->id) }}" class="btn btn-outline-td btn-sm">
                            Lihat Jurnal
                        </a>

                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $journals->links() }}
        </div>

    @endif

@endsection
