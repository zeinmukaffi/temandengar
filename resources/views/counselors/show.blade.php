@extends('layouts.app')

@section('title', 'Profil Konselor')

@section('content')

    <div class="mb-4">
        <a href="{{ route('counselors.index') }}" class="td-back-link text-decoration-none">
            <span aria-hidden="true">&larr;</span>
            Kembali ke Direktori Konselor
        </a>
    </div>

    <div class="row g-4">

        {{-- Kolom Kiri: Detail Profil Konselor --}}
        <div class="col-lg-8">
            <article class="td-card td-counselor-profile-card">

                {{-- Identitas Konselor --}}
                <div class="d-flex flex-wrap align-items-center gap-4 mb-4">
                    @if ($counselor->photo_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($counselor->photo_path) }}"
                            alt="Foto {{ $counselor->name }}" class="td-counselor-avatar td-counselor-avatar--lg">
                    @else
                        <div class="td-counselor-initials td-counselor-initials--lg" aria-hidden="true">
                            {{ mb_strtoupper(mb_substr($counselor->name, 0, 1)) }}
                        </div>
                    @endif

                    <div class="flex-grow-1">
                        <span class="td-status-pill td-status-pill--success mb-2">
                            <span class="td-pill-dot"></span>
                            Tersedia untuk konsultasi
                        </span>
                        <h3 class="fw-bold mb-1">{{ $counselor->name }}</h3>
                        <p class="text-td-muted mb-0 text-capitalize">Konselor Sebaya • {{ $counselor->role }}</p>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Tentang Konselor --}}
                <section class="mb-4">
                    <span class="td-section-eyebrow mb-2">PROFIL SINGKAT</span>
                    <h5 class="fw-bold mb-2">Tentang Konselor</h5>
                    <p class="text-td-muted mb-0" style="white-space: pre-line; line-height: 1.85; font-size: 14.5px;">
                        {{ $counselor->bio ?: 'Belum ada deskripsi profil.' }}
                    </p>
                </section>

                {{-- Bidang Pendampingan --}}
                <section class="mb-4">
                    <span class="td-section-eyebrow mb-2">TOPIK DISKUSI</span>
                    <h5 class="fw-bold mb-3">Bidang Pendampingan</h5>
                    @if (!empty($counselor->specialties))
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($counselor->specialties as $specialty)
                                <span class="td-specialty-tag">{{ $specialty }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-td-muted small mb-0">Belum ada bidang pendampingan yang dicantumkan.</p>
                    @endif
                </section>

                {{-- Informasi Ketersediaan --}}
                <section>
                    <span class="td-section-eyebrow mb-2">JADWAL &amp; KETERSEDIAAN</span>
                    <h5 class="fw-bold mb-3">Informasi Ketersediaan</h5>
                    <div class="td-availability-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--td-primary)"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 mt-1"
                            aria-hidden="true">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        <p class="text-td-muted small mb-0" style="line-height: 1.75;">
                            {{ $counselor->availability_description ?: 'Konselor tersedia. Waktu konsultasi akan dikonfirmasi setelah pengajuan dikirim.' }}
                        </p>
                    </div>
                </section>

            </article>
        </div>

        {{-- Kolom Kanan: Kartu Ajukan Konsultasi --}}
        <div class="col-lg-4 td-writing-sidebar">
            <aside class="td-card p-4">

                <div class="feature-icon mb-3">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path
                            d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8Z" />
                        <path d="M8 11h.01M12 11h.01M16 11h.01" />
                    </svg>
                </div>

                <span class="td-section-eyebrow">MULAI SESI</span>
                <h5 class="fw-bold mt-1 mb-2">Merasa cocok untuk bercerita?</h5>

                <p class="text-td-muted small mb-3" style="line-height: 1.75;">
                    Kamu dapat memilih {{ $counselor->name }} sebagai pendamping. Sebelum pengajuan dikirim,
                    kamu akan menentukan informasi apa yang ingin dibagikan.
                </p>

                <div class="td-writing-privacy mb-4">
                    <div class="td-writing-privacy-icon" style="width: 34px; height: 34px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>
                    </div>
                    <p class="small text-td-primary mb-0" style="line-height: 1.65;">
                        Jurnal dan refleksi AI tetap pribadi sampai kamu memberikan persetujuan untuk membagikannya.
                    </p>
                </div>

                <a href="{{ route('consultations.create', $counselor->id) }}" class="btn btn-td w-100 py-2">
                    Ajukan Konsultasi &rarr;
                </a>

            </aside>
        </div>

    </div>

@endsection
