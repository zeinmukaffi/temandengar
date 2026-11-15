@extends('layouts.app')

@section('title', 'Journaling')

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
    @endphp

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <span class="td-section-eyebrow mb-1">RUANG CERITA</span>
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
    <section class="td-card td-mood-card mb-4">

        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="td-mood-heading-icon" aria-hidden="true">
                <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M8 14s1.5 2 4 2 4-2 4-2" />
                    <path d="M9 9h.01M15 9h.01" />
                </svg>
            </div>

            <div>
                <span class="td-section-eyebrow">MOOD CHECK-IN</span>
                <h5 class="fw-bold mb-1 mt-1">
                    Bagaimana perasaanmu hari ini?
                </h5>
                <p class="text-td-muted small mb-0">
                    Kenali perasaanmu, tanpa perlu menuliskan cerita panjang.
                </p>
            </div>
        </div>

        <form action="{{ route('mood-checkins.store') }}" method="POST">
            @csrf

            <label class="form-label small fw-semibold mb-3">
                Pilih mood yang paling menggambarkan perasaanmu.
            </label>

            <div class="td-mood-grid mb-3">
                @foreach (config('temandengar.moods') as $value => $label)
                    <div class="td-mood-grid-item">
                        <input type="radio" class="btn-check" name="mood" id="mood-{{ $value }}"
                            value="{{ $value }}" {{ old('mood') === $value ? 'checked' : '' }} required>

                        <label class="td-mood-option" for="mood-{{ $value }}">
                            <span class="td-mood-emoji" aria-hidden="true">
                                {{ $moodEmojis[$value] ?? '•' }}
                            </span>
                            <span class="td-mood-label">
                                {{ $label }}
                            </span>
                        </label>
                    </div>
                @endforeach
            </div>

            @error('mood')
                <div class="text-danger small mb-3">
                    {{ $message }}
                </div>
            @enderror

            <div class="row g-2 align-items-end">
                <div class="col-md-9">
                    <label for="mood-note" class="form-label small fw-medium">
                        Catatan singkat <span class="text-td-muted">(Opsional)</span>
                    </label>

                    <input type="text" name="note" id="mood-note" class="form-control"
                        placeholder="Apa yang membuatmu merasa seperti ini?" value="{{ old('note') }}" maxlength="500">

                    @error('note')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-td w-100">
                        Simpan Mood
                        <span aria-hidden="true" class="ms-1">&rarr;</span>
                    </button>
                </div>
            </div>

        </form>

    </section>

    {{-- Riwayat Mood Check-in --}}
    @if ($recentMoodCheckins->isNotEmpty())
        <section class="td-card mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <span class="td-section-eyebrow">CATATAN PERASAAN</span>
                    <h6 class="fw-bold mt-1 mb-0">Mood Terakhir</h6>
                </div>

                <span class="badge rounded-pill bg-td-soft text-td-primary px-3 py-2">
                    {{ $recentMoodCheckins->count() }} catatan
                </span>
            </div>

            <div class="td-mood-history">
                @foreach ($recentMoodCheckins as $checkin)
                    <div class="td-mood-history-item">
                        <span class="td-mood-history-emoji" aria-hidden="true">
                            {{ $moodEmojis[$checkin->mood] ?? '•' }}
                        </span>

                        <div>
                            <div class="fw-semibold small">
                                {{ config('temandengar.moods.' . $checkin->mood, ucfirst($checkin->mood)) }}
                            </div>
                            <div class="text-td-muted td-mood-history-date">
                                {{ $checkin->checked_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Riwayat Jurnal --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Riwayat Jurnal</h5>

        <span class="text-td-muted small">
            {{ $journals->total() }} jurnal
        </span>
    </div>

        {{-- Bar Pencarian & Filter Mood Jurnal --}}
    <form action="{{ route('journals.index') }}" method="GET" class="td-card p-3 mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-md-6">
                <input type="text" name="q" class="form-control form-control-sm"
                    placeholder="Cari judul atau isi cerita jurnal..."
                    value="{{ $keyword }}">
            </div>

            <div class="col-md-4">
                <select name="mood" class="form-select form-select-sm">
                    <option value="">Semua Mood</option>
                    @foreach ($moods as $moodKey =>$moodLabel)
                        <option value="{{ $moodKey }}" {{ $selectedMood ===$moodKey ? 'selected' : '' }}>
                            {{ $moodLabel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-td btn-sm w-100">Filter</button>
                @if ($keyword !== '' || $selectedMood !== '')
                    <a href="{{ route('journals.index') }}" class="btn btn-outline-td btn-sm" title="Reset Filter">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    @if ($journals->isEmpty())
        <div class="td-card td-journal-empty text-center py-5 px-4">
            <div class="td-empty-illustration mx-auto mb-4">
                <svg width="76" height="76" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <rect x="15" y="15" width="50" height="56" rx="8" fill="#EDF7F5" />
                    <path d="M25 15V10H60C64.4 10 68 13.6 68 18V61" stroke="#73B7AA" stroke-width="2.5"
                        stroke-linecap="round" />
                    <path d="M28 30H53M28 39H53M28 48H45" stroke="#365B78" stroke-width="2.5" stroke-linecap="round" />
                    <path d="M10 25L12 30L17 32L12 34L10 39L8 34L3 32L8 30Z" fill="#73B7AA" />
                    <circle cx="61" cy="11" r="4" fill="#365B78" />
                </svg>
            </div>

            <span class="td-section-eyebrow">RUANG CERITAMU</span>

            <h5 class="fw-bold mt-2 mb-2">
                Belum ada jurnal yang tersimpan
            </h5>

            <p class="text-td-muted small mx-auto mb-4" style="max-width: 430px; line-height: 1.8;">
                Kamu bisa mulai dengan menuliskan apa pun yang sedang
                kamu rasakan. Tidak perlu menyusun kata dengan sempurna.
                Mulailah dari hal kecil yang ingin kamu ceritakan.
            </p>

            <a href="{{ route('journals.create') }}" class="btn btn-td">
                + Tulis Jurnal Pertamamu
            </a>
        </div>
    @else
        <div class="row g-3">
            @foreach ($journals as $journal)
                @php
                    $journalPreview = $journal->content;

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
                            $paragraphs = [];
                            if ($answers['today'] !== '') {
                                $paragraphs[] = rtrim($answers['today'], '.!?…') . '.';
                            }
                            if ($answers['burden'] !== '') {
                                $paragraphs[] =
                                    'Hal lain yang cukup membebani pikiranku adalah ' .
                                    rtrim($answers['burden'], '.!?…') .
                                    '.';
                            }
                            if ($answers['feeling'] !== '') {
                                $paragraphs[] =
                                    'Saat menghadapi situasi tersebut, perasaan yang muncul adalah ' .
                                    rtrim($answers['feeling'], '.!?…') .
                                    '.';
                            }
                            if ($answers['support'] !== '') {
                                $paragraphs[] =
                                    'Saat ini, hal yang aku butuhkan adalah ' .
                                    rtrim($answers['support'], '.!?…') .
                                    '.';
                            }

                            $journalPreview = implode(' ', $paragraphs) ?: $journal->content;
                        }
                    }
                @endphp

                <div class="col-md-6">
                    <article class="td-card td-journal-card h-100">

                        <div class="td-journal-card-header">
                            <div class="d-flex flex-wrap gap-2">
                                <span class="td-entry-badge">
                                    {{ $journal->entry_type === 'guided' ? 'Guided Journaling' : 'Free Writing' }}
                                </span>

                                <span class="td-privacy-badge">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" aria-hidden="true">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>
                                    Pribadi
                                </span>
                            </div>

                            <span class="td-journal-date">
                                {{ $journal->journaled_at->format('d M Y') }}
                            </span>
                        </div>

                        <h6 class="td-journal-card-title">
                            {{ $journal->title ?: 'Catatan Pribadi' }}
                        </h6>

                        <p class="td-journal-card-preview">
                            {{ \Illuminate\Support\Str::limit($journalPreview, 150) }}
                        </p>

                        @if ($journal->moodCheckin)
                            <div class="td-journal-card-mood">
                                <span aria-hidden="true">
                                    {{ $moodEmojis[$journal->moodCheckin->mood] ?? '•' }}
                                </span>
                                <span>
                                    {{ config('temandengar.moods.' . $journal->moodCheckin->mood, ucfirst($journal->moodCheckin->mood)) }}
                                </span>
                            </div>
                        @endif

                        <div class="td-journal-card-footer">
                            <a href="{{ route('journals.show', $journal->id) }}" class="btn btn-outline-td btn-sm">
                                Lihat Jurnal
                                <span aria-hidden="true" class="ms-1">&rarr;</span>
                            </a>
                        </div>

                    </article>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $journals->links() }}
        </div>
    @endif

@endsection
