@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="td-dashboard">

        {{-- Page Heading --}}
        <div class="td-dashboard-heading mb-4">
            <span class="td-dashboard-eyebrow">RUANG TEMANDENGAR</span>

            <h3 class="fw-bold mb-2 mt-2">
                Halo, {{ $user->name }}.
            </h3>

            <p class="text-td-muted mb-0">
                Senang melihatmu kembali. Apa yang ingin kamu ceritakan hari ini?
            </p>
        </div>

        {{-- Welcome Hero --}}
        <section class="td-dashboard-hero td-card mb-4">
            <div class="row align-items-center g-4">

                <div class="col-lg-8">
                    <span class="td-hero-label mb-3">
                        <span class="td-hero-label__dot"></span>
                        Ruang cerita dan dukungan
                    </span>

                    <h2 class="td-hero-title fw-bold mb-3">
                        Mulai dari cerita kecilmu hari ini.
                    </h2>

                    <p class="td-hero-description mb-0">
                        Tidak apa-apa untuk berhenti sejenak dan memahami
                        apa yang sedang kamu rasakan. Setiap cerita
                        layak untuk didengarkan.
                    </p>
                </div>

                <div class="col-lg-4">
                    <div class="td-hero-illustration">
                        <svg viewBox="0 0 260 190" xmlns="http://www.w3.org/2000/svg" role="img"
                            aria-label="Ilustrasi gelembung percakapan" focusable="false">
                            <defs>
                                <linearGradient id="tdHeroGradient" x1="0" y1="0" x2="1"
                                    y2="1">
                                    <stop offset="0%" stop-color="#73B7AA" />
                                    <stop offset="100%" stop-color="#365B78" />
                                </linearGradient>
                            </defs>

                            <circle cx="130" cy="95" r="82" fill="#EDF7F5" />
                            <circle cx="205" cy="35" r="13" fill="#D8EDE8" />
                            <circle cx="48" cy="145" r="9" fill="#D8EDE8" />

                            <path
                                d="M45 43 Q45 30 59 30 H150 Q164 30 164 44 V96 Q164 110 150 110 H94 L70 129 V110 H59 Q45 110 45 96 Z"
                                fill="#FFFFFF" stroke="#B9DCD4" stroke-width="2" />

                            <circle cx="79" cy="69" r="5" fill="#73B7AA" />
                            <circle cx="103" cy="69" r="5" fill="#73B7AA" />
                            <circle cx="127" cy="69" r="5" fill="#73B7AA" />

                            <path
                                d="M106 83 Q106 70 120 70 H205 Q219 70 219 84 V130 Q219 144 205 144 H189 V162 L166 144 H120 Q106 144 106 130 Z"
                                fill="url(#tdHeroGradient)" />

                            <path
                                d="M150 106 C150 96 164 93 169 103 C174 93 188 96 188 106 C188 117 169 129 169 129 S150 117 150 106 Z"
                                fill="#FFFFFF" />

                            <path d="M207 21 L211 32 L222 36 L211 40 L207 51 L203 40 L192 36 L203 32 Z" fill="#73B7AA" />
                            <path d="M61 26 L64 33 L71 36 L64 39 L61 46 L58 39 L51 36 L58 33 Z" fill="#365B78" />
                        </svg>
                    </div>
                </div>

            </div>
        </section>

        {{-- Main Feature Cards --}}
        <div class="row g-4">

            {{-- Journaling Card --}}
            <div class="col-md-6">
                <section class="td-card td-dashboard-feature-card h-100">

                    <div class="td-dashboard-feature-icon td-icon-sage mb-4">
                        <svg width="27" height="27" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 20h9" />
                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z" />
                            <path d="m15 5 3 3" />
                        </svg>
                    </div>

                    <div class="td-dashboard-feature-content">
                        <span class="td-feature-eyebrow">RUANG PRIBADI</span>

                        <h5 class="fw-bold mt-2 mb-2">
                            Journaling
                        </h5>

                        <p class="text-td-muted mb-4">
                            Tuangkan cerita, pikiran, dan perasaanmu
                            melalui jurnal pribadi. Kamu juga bisa
                            mencatat mood harian dan merefleksikan
                            pengalamanmu.
                        </p>
                    </div>

                    <div class="td-dashboard-feature-actions mt-auto">
                        <a href="{{ route('journals.index') }}" class="btn btn-td">
                            Buka Journaling
                            <span aria-hidden="true" class="ms-1">&rarr;</span>
                        </a>

                        <a href="{{ route('journals.create') }}" class="btn btn-outline-td">
                            + Tulis Jurnal Baru
                        </a>
                    </div>

                </section>
            </div>

            {{-- Role-Specific Second Card --}}
            <div class="col-md-6">
                @if ($user->role === 'konselor')
                    <section class="td-card td-dashboard-feature-card h-100">

                        <div class="td-dashboard-feature-icon td-icon-blue mb-4">
                            <svg width="27" height="27" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </div>

                        <div class="td-dashboard-feature-content">
                            <span class="td-feature-eyebrow">PERAN KONSELOR SEBAYA</span>

                            <h5 class="fw-bold mt-2 mb-2">
                                Dampingi Teman Bercerita
                            </h5>

                            <p class="text-td-muted mb-4">
                                Tinjau pengajuan konsultasi masuk dari mahasiswa, baca konteks yang
                                dibagikan dengan penuh empati, dan berikan tanggapan yang suportif.
                            </p>
                        </div>

                        <div class="td-dashboard-feature-actions mt-auto">
                            <a href="{{ route('counselor.consultations.index') }}" class="btn btn-td">
                                Permintaan Konsultasi
                                <span aria-hidden="true" class="ms-1">&rarr;</span>
                            </a>
                        </div>

                    </section>
                @else
                    <section class="td-card td-dashboard-feature-card h-100">

                        <div class="td-dashboard-feature-icon td-icon-blue mb-4">
                            <svg width="27" height="27" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path
                                    d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8Z" />
                                <path d="M8 11h.01M12 11h.01M16 11h.01" />
                            </svg>
                        </div>

                        <div class="td-dashboard-feature-content">
                            <span class="td-feature-eyebrow">DUKUNGAN SEBAYA</span>

                            <h5 class="fw-bold mt-2 mb-2">
                                Butuh teman untuk bercerita?
                            </h5>

                            <p class="text-td-muted mb-4">
                                Ada konselor sebaya yang siap mendengarkan
                                dan menemanimu membahas hal yang sedang
                                kamu hadapi. Kamu tidak harus menghadapi
                                semuanya sendirian.
                            </p>
                        </div>

                        <div class="td-dashboard-feature-actions mt-auto">
                            <a href="{{ route('counselors.index') }}" class="btn btn-td">
                                Temukan Konselor
                                <span aria-hidden="true" class="ms-1">&rarr;</span>
                            </a>

                            <a href="{{ route('consultations.index') }}" class="btn btn-outline-td">
                                Konsultasi Saya
                            </a>
                        </div>

                    </section>
                @endif
            </div>

        </div>

        {{-- Privacy Reminder --}}
        <section class="td-privacy-card mt-4">

            <div class="td-privacy-icon">
                <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z" />
                    <path d="m9 12 2 2 4-4" />
                </svg>
            </div>

            <div class="td-privacy-content">
                <span class="td-feature-eyebrow">PRIVASI DAN KENDALI</span>

                <h6 class="fw-bold mt-2 mb-2">
                    Ceritamu tetap milikmu.
                </h6>

                <p class="text-td-muted small mb-0">
                    Jurnal yang kamu tulis bersifat pribadi secara default.
                    Saat mengajukan konsultasi, kamu memiliki kendali
                    untuk menentukan apakah ingin membagikan jurnal
                    atau AI Reflection kepada konselor.
                </p>
            </div>

        </section>

    </div>

@endsection
