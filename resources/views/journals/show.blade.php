@extends('layouts.app')

@section('title', 'Detail Jurnal')

@section('content')

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
                <div style="white-space: pre-wrap; line-height: 1.9;">{{ $journal->content }}</div>

                <hr class="my-4">

                <div class="d-flex align-items-center gap-2 text-td-muted small">
                    <span class="fw-bold text-td-primary">P</span>
                    Jurnal ini bersifat pribadi.
                </div>

            </div>

        </div>

        {{-- Panel AI --}}
        <div class="col-lg-4">

            <div class="td-card mb-4">
                <div class="feature-icon mb-3">AI</div>

                <h5 class="fw-bold mb-2">AI Reflection</h5>

                <p class="text-td-muted small">
                    Hermes nantinya akan membantu merangkum cerita,
                    mengidentifikasi kemungkinan emosi, dan memberikan
                    pertanyaan refleksi.
                </p>

                @if ($journal->aiReflections->isNotEmpty())

                    @foreach ($journal->aiReflections->sortByDesc('created_at') as $reflection)
                        <div class="border rounded-3 p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-semibold">Refleksi AI</span>

                                <span class="badge bg-light text-secondary">
                                    {{ ucfirst($reflection->status) }}
                                </span>
                            </div>

                            <p class="small mb-2">
                                {{ \Illuminate\Support\Str::limit($reflection->summary, 180) }}
                            </p>

                            <span class="text-td-muted" style="font-size: 11px;">
                                {{ $reflection->created_at->format('d M Y, H:i') }}
                            </span>
                        </div>
                    @endforeach
                @else
                    <div class="bg-light rounded-3 p-3">
                        <p class="small text-td-muted mb-0">
                            Refleksi AI belum tersedia. Integrasi Hermes
                            akan ditambahkan pada tahap berikutnya.
                        </p>
                    </div>

                @endif
            </div>

        </div>

    </div>

@endsection
