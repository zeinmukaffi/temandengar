@extends('layouts.app')

@section('title', 'Pilih Konselor')

@section('content')

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <span class="td-section-eyebrow mb-1">TEMAN BERCERITA</span>
            <h3 class="fw-bold mb-1">Temukan Konselor Sebaya</h3>
            <p class="text-td-muted mb-0">
                Pilih pendengar yang paling sesuai dengan kebutuhan dan kenyamananmu.
            </p>
        </div>

        <a href="{{ route('dashboard') }}" class="btn btn-outline-td btn-sm">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    {{-- Filter & Pencarian --}}
    <section class="td-card mb-4">
        <form action="{{ route('counselors.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-5 col-lg-6">
                    <label for="q" class="form-label small fw-semibold">Cari konselor</label>
                    <input type="search" id="q" name="q" class="form-control"
                        value="{{ $keyword }}" placeholder="Cari nama atau deskripsi konselor...">
                </div>

                <div class="col-md-4 col-lg-4">
                    <label for="specialty" class="form-label small fw-semibold">Bidang pendampingan</label>
                    <select id="specialty" name="specialty" class="form-select">
                        <option value="">Semua bidang</option>
                        @foreach ($specialties as $specialty)
                            <option value="{{ $specialty }}" {{ $selectedSpecialty === $specialty ? 'selected' : '' }}>
                                {{ $specialty }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-td flex-grow-1">Cari</button>
                    @if ($keyword !== '' || $selectedSpecialty !== '')
                        <a href="{{ route('counselors.index') }}" class="btn btn-outline-secondary"
                            title="Reset filter">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </section>

    {{-- Result Counter --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Konselor Tersedia</h5>
        <span class="text-td-muted small">{{ $counselors->total() }} konselor</span>
    </div>

    @if ($counselors->isEmpty())
        <div class="td-card td-journal-empty text-center py-5 px-4">
            <div class="td-empty-illustration mx-auto mb-3">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--td-primary)"
                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.3-4.3" />
                </svg>
            </div>
            <h5 class="fw-bold mb-2">Belum ada konselor yang sesuai</h5>
            <p class="text-td-muted small mx-auto mb-3" style="max-width: 400px;">
                Coba ubah kata kunci pencarian atau pilih "Semua bidang" untuk melihat seluruh konselor sebaya yang tersedia.
            </p>
            <a href="{{ route('counselors.index') }}" class="btn btn-outline-td btn-sm">
                Tampilkan Semua Konselor
            </a>
        </div>
    @else
        <div class="row g-3">
            @foreach ($counselors as $counselor)
                <div class="col-md-6 col-xl-4">
                    <article class="td-card td-counselor-card h-100">

                        <div class="d-flex align-items-start gap-3 mb-3">
                            @if ($counselor->photo_path)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($counselor->photo_path) }}"
                                    alt="Foto {{ $counselor->name }}" class="td-counselor-avatar">
                            @else
                                <div class="td-counselor-initials" aria-hidden="true">
                                    {{ mb_strtoupper(mb_substr($counselor->name, 0, 1)) }}
                                </div>
                            @endif

                            <div class="flex-grow-1 min-w-0">
                                <span class="td-status-pill td-status-pill--success mb-2">
                                    <span class="td-pill-dot"></span>
                                    Tersedia
                                </span>
                                <h6 class="fw-bold mb-1 text-truncate">{{ $counselor->name }}</h6>
                                <p class="text-td-muted small mb-0 text-capitalize">{{ $counselor->role }}</p>
                            </div>
                        </div>

                        <p class="text-td-muted small mb-3" style="line-height: 1.75;">
                            {{ \Illuminate\Support\Str::limit($counselor->bio ?: 'Konselor sebaya TemanDengar yang siap mendengarkan ceritamu.', 125) }}
                        </p>

                        @if (!empty($counselor->specialties))
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                @foreach (array_slice($counselor->specialties, 0, 3) as $specialty)
                                    <span class="td-specialty-tag td-specialty-tag--sm">{{ $specialty }}</span>
                                @endforeach
                                @if (count($counselor->specialties) > 3)
                                    <span class="td-specialty-tag td-specialty-tag--sm bg-light text-secondary">
                                        +{{ count($counselor->specialties) - 3 }} lainnya
                                    </span>
                                @endif
                            </div>
                        @endif

                        <div class="mt-auto pt-2">
                            <a href="{{ route('counselors.show', $counselor->id) }}"
                                class="btn btn-outline-td w-100">
                                Lihat Profil &amp; Jadwal
                                <span aria-hidden="true" class="ms-1">&rarr;</span>
                            </a>
                        </div>

                    </article>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $counselors->links() }}
        </div>
    @endif

@endsection