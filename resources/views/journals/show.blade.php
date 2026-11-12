@extends('layouts.app')

@section('title', 'Detail Jurnal')

@section('content')

    @php
        // Ambil refleksi terbaru untuk jurnal ini.
        $reflection = $journal->aiReflections->sortByDesc('created_at')->first();

        // Konversi tampilan jurnal Guided versi lama (format pertanyaan-jawaban)
        // menjadi cerita yang lebih natural. Konten database tidak diubah.
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
        <a href="{{ route('journals.index') }}" class="text-td-primary text-decoration-none small">
            &larr; Kembali ke Journaling
        </a>
    </div>

    <div class="row g-4">

        {{-- Detail jurnal --}}
        <div class="col-lg-8">

            <div class="td-card">

                <div class="d-flex justify-content-between align-items-start gap-3 mb-4">

                    <div>
                        <span class="badge bg-light text-secondary mb-2">
                            {{ $journal->entry_type === 'guided' ? 'Guided Journaling' : 'Free Writing' }}
                        </span>

                        <h3 class="fw-bold mb-2">
                            {{ $journal->title ?: 'Catatan Pribadi' }}
                        </h3>

                        <p class="text-td-muted small mb-0">
                            {{ $journal->journaled_at->format('d F Y, H:i') }}
                        </p>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" type="button">
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
                                    onsubmit="return confirm('Apakah kamu yakin ingin menghapus jurnal ini?')">
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

                {{-- Mood --}}
                @if ($journal->moodCheckin)
                    <div class="bg-td-soft rounded-3 p-3 mb-4">
                        <span class="text-td-muted small">Mood saat menulis</span>

                        <h6 class="fw-bold text-td-primary mt-1 mb-1">
                            {{ config('temandengar.moods.' . $journal->moodCheckin->mood, ucfirst($journal->moodCheckin->mood)) }}
                        </h6>

                        @if ($journal->moodCheckin->note)
                            <p class="small mb-0">
                                {{ $journal->moodCheckin->note }}
                            </p>
                        @endif
                    </div>
                @endif

                {{-- Isi jurnal --}}
                <div style="white-space: pre-wrap; line-height: 1.9;">{{ $displayContent }}</div>

                <hr class="my-4">

                <div class="d-flex align-items-center gap-2 text-td-muted small">
                    <span class="fw-bold text-td-primary">P</span>
                    Jurnal ini bersifat pribadi.
                </div>

            </div>

        </div>

        {{-- Panel AI Reflection --}}
        <div class="col-lg-4">

            <div class="td-card">
                <div class="feature-icon mb-3">AI</div>

                <h5 class="fw-bold mb-2">AI Reflection</h5>

                <p class="text-td-muted small mb-3">
                    Refleksi ini membantu merangkum cerita dan membuka ruang untuk memahami pengalamanmu.
                    Hasilnya bukan diagnosis atau pengganti dukungan dari konselor.
                </p>

                @if ($reflection)
                    <div class="border rounded-3 p-3">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                            <span class="fw-semibold">Refleksi Hermes</span>

                            @if ($reflection->status === 'completed')
                                <span class="badge bg-success-subtle text-success">Completed</span>
                            @elseif ($reflection->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning">Pending</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">Failed</span>
                            @endif
                        </div>

                        @if ($reflection->status === 'completed')

                            {{-- Ringkasan cerita --}}
                            @if ($reflection->summary)
                                <section class="mb-3">
                                    <h6 class="fw-bold small mb-2">Ringkasan Cerita</h6>
                                    <p class="small mb-0" style="line-height: 1.7;">
                                        {{ $reflection->summary }}
                                    </p>
                                </section>
                            @endif

                            {{-- Insight emosional --}}
                            @if ($reflection->emotional_insight)
                                <section class="border-top pt-3 mb-3">
                                    <h6 class="fw-bold small mb-2">Insight Emosional</h6>
                                    <p class="small mb-0" style="line-height: 1.7;">
                                        {{ $reflection->emotional_insight }}
                                    </p>
                                </section>
                            @endif

                            {{-- Hal yang menjadi perhatian --}}
                            @if (!empty($reflection->key_concerns))
                                <section class="border-top pt-3 mb-3">
                                    <h6 class="fw-bold small mb-2">Hal yang Menjadi Perhatian</h6>
                                    <ul class="small ps-3 mb-0" style="line-height: 1.7;">
                                        @foreach ($reflection->key_concerns as $concern)
                                            <li class="mb-1">{{ $concern }}</li>
                                        @endforeach
                                    </ul>
                                </section>
                            @endif

                            {{-- Pertanyaan refleksi --}}
                            @if (!empty($reflection->reflection_questions))
                                <section class="border-top pt-3 mb-3">
                                    <h6 class="fw-bold small mb-2">Pertanyaan Refleksi</h6>
                                    <ol class="small ps-3 mb-0" style="line-height: 1.7;">
                                        @foreach ($reflection->reflection_questions as $question)
                                            <li class="mb-2">{{ $question }}</li>
                                        @endforeach
                                    </ol>
                                </section>
                            @endif

                            {{-- Langkah yang bisa dicoba --}}
                            @if (!empty($reflection->suggested_steps))
                                <section class="border-top pt-3">
                                    <h6 class="fw-bold small mb-2">Langkah yang Bisa Dicoba</h6>
                                    <ul class="small ps-3 mb-0" style="line-height: 1.7;">
                                        @foreach ($reflection->suggested_steps as $step)
                                            <li class="mb-1">{{ $step }}</li>
                                        @endforeach
                                    </ul>
                                </section>
                            @endif
                        @elseif ($reflection->status === 'pending')
                            <div class="bg-light rounded-3 p-3">
                                <p class="small text-td-muted mb-0">
                                    Refleksi sedang diproses. Silakan buka kembali halaman ini beberapa saat lagi.
                                </p>
                            </div>
                        @else
                            <div class="bg-light rounded-3 p-3">
                                <p class="small text-td-muted mb-0">
                                    Refleksi belum berhasil dibuat. Ceritamu tetap tersimpan dan bersifat pribadi.
                                </p>
                            </div>
                        @endif

                        <div class="text-td-muted mt-3" style="font-size: 11px;">
                            Dibuat {{ $reflection->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                @else
                    <div class="bg-light rounded-3 p-3">
                        <p class="small text-td-muted mb-0">
                            Belum ada refleksi AI untuk jurnal ini.
                        </p>
                    </div>
                @endif
            </div>

        </div>

    </div>

@endsection
