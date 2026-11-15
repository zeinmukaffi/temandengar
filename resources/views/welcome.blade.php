@extends('layouts.guest')

@section('title', 'Ruang Cerita dan Dukungan Mahasiswa')

@section('content')

    {{-- 1. HERO SECTION --}}
    <section id="tentang" class="td-landing-hero">
        <div class="container">
            <div class="row align-items-center g-5">

                <div class="col-lg-7">
                    <div class="td-pill-badge mb-4">
                        <span class="td-pill-dot"></span>
                        Ruang Cerita &amp; Dukungan Sebaya Mahasiswa
                    </div>

                    <h1 class="display-5 fw-bold mb-4" style="line-height: 1.25; letter-spacing: -0.8px;">
                        Setiap cerita <span class="text-td-primary">layak didengar,</span> tanpa perlu dihakimi.
                    </h1>

                    <p class="text-td-muted fs-6 mb-4" style="max-width: 580px; line-height: 1.8;">
                        TemanDengar hadir sebagai ruang aman bagi mahasiswa untuk menuliskan apa yang sedang dirasakan,
                        mendapatkan refleksi diri berbantuan AI, dan terhubung dengan konselor sebaya yang siap menemani.
                    </p>

                    <div class="d-flex flex-wrap gap-3 mb-5">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-td btn-lg">
                                Lanjutkan ke Dashboard &rarr;
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-td btn-lg">
                                Mulai Bercerita Sekarang
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-td btn-lg">
                                Saya Sudah Punya Akun
                            </a>
                        @endauth
                    </div>

                    {{-- Highlight Pilar --}}
                    <div class="row g-3 pt-2 border-top" style="max-width: 560px;">
                        <div class="col-4">
                            <div class="fw-bold text-td-primary">100% Privat</div>
                            <small class="text-td-muted">Jurnal terkunci default</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-td-primary">AI Reflection</div>
                            <small class="text-td-muted">Bantu urai pikiran</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-td-primary">Peer Support</div>
                            <small class="text-td-muted">Teman sebaya terlatih</small>
                        </div>
                    </div>
                </div>

                {{-- Preview Card Sisi Kanan --}}
                <div class="col-lg-5">
                    <div class="auth-card p-4 p-lg-5 position-relative">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge rounded-pill bg-td-soft text-td-primary px-3 py-2">
                                Ruang Refleksi Harian
                            </span>
                            <span class="text-td-muted small">Hari ini</span>
                        </div>

                        <h5 class="fw-bold text-td-primary mb-2">
                            "Tidak harus selalu punya semua jawaban hari ini."
                        </h5>

                        <p class="text-td-muted small mb-4" style="line-height: 1.75;">
                            Terkadang, langkah pertama menuju lega hanyalah memberi izin pada diri sendiri
                            untuk mengakui rasa lelah, cemas, atau bingung yang sedang kamu alami.
                        </p>

                        <div class="p-3 rounded-4 mb-3"
                            style="background: var(--td-bg); border: 1px solid var(--td-border);">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="td-pill-dot"></span>
                                <span class="fw-semibold small text-td-primary">AI Reflection Insight</span>
                            </div>
                            <p class="text-td-muted small mb-0" style="line-height: 1.65;">
                                Kamu sedang menanggung beban akademik yang cukup padat minggu ini.
                                Mengambil jeda 15 menit bisa membantu menjernihkan pikiranmu.
                            </p>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-2">
                            <div class="small text-td-muted">Butuh teman diskusi?</div>
                            <a href="{{ route('register') }}"
                                class="text-decoration-none fw-semibold small text-td-primary">
                                Hubungi Konselor &rarr;
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- 2. FITUR UTAMA --}}
    <section id="fitur" class="td-section td-section-white">
        <div class="container">
            <div class="text-center mx-auto mb-5" style="max-width: 640px;">
                <span class="td-eyebrow mb-2">Ekosistem Dukungan</span>
                <h2 class="fw-bold text-td-primary mb-3">Tiga Langkah Menjaga Ketenangan Diri</h2>
                <p class="text-td-muted mb-0" style="line-height: 1.75;">
                    Dirancang khusus mengikuti kebutuhan emosional mahasiswa—mulai dari ruang refleksi mandiri
                    hingga dukungan percakapan dua arah.
                </p>
            </div>

            <div class="row g-4">
                {{-- Fitur 1: Journaling --}}
                <div class="col-md-4">
                    <div class="td-feature-box">
                        <div class="feature-icon mb-4">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20h9" />
                                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z" />
                                <path d="m15 5 3 3" />
                            </svg>
                        </div>
                        <h5 class="fw-bold mb-2">Private Journaling</h5>
                        <p class="text-td-muted small mb-0" style="line-height: 1.8;">
                            Catat mood harian dan tuangkan isi pikiranmu kapan saja tanpa takut dinilai.
                            Semua tulisan tersimpan aman di ruang pribadimu.
                        </p>
                    </div>
                </div>

                {{-- Fitur 2: AI Reflection --}}
                <div class="col-md-4">
                    <div class="td-feature-box">
                        <div class="feature-icon mb-4" style="background: var(--td-primary-subtle);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
                                <path d="M19 3v4" />
                                <path d="M21 5h-4" />
                            </svg>
                        </div>
                        <h5 class="fw-bold mb-2">AI Reflection</h5>
                        <p class="text-td-muted small mb-0" style="line-height: 1.8;">
                            Dapatkan rangkuman empatik dan pertanyaan reflektif dari tulisan jurnalmu untuk
                            membantu melihat situasi dari sudut pandang yang lebih jernih.
                        </p>
                    </div>
                </div>

                {{-- Fitur 3: Peer Counseling --}}
                <div class="col-md-4">
                    <div class="td-feature-box">
                        <div class="feature-icon mb-4">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8Z" />
                            </svg>
                        </div>
                        <h5 class="fw-bold mb-2">Peer Counseling</h5>
                        <p class="text-td-muted small mb-0" style="line-height: 1.8;">
                            Pilih konselor sebaya yang sesuai dengan topik masalahmu dan ajukan sesi konsultasi
                            secara terstruktur dan nyaman.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. CARA KERJA --}}
    <section id="cara-kerja" class="td-section">
        <div class="container">
            <div class="text-center mx-auto mb-5" style="max-width: 620px;">
                <span class="td-eyebrow mb-2">Alur Penggunaan</span>
                <h2 class="fw-bold text-td-primary mb-3">Mulai dalam Empat Langkah Sederhana</h2>
                <p class="text-td-muted mb-0" style="line-height: 1.75;">
                    Kamu bebas berhenti di tahap journaling mandiri, atau melanjutkan ke sesi konsultasi
                    ketika merasa butuh teman bicara.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="td-step-card bg-white">
                        <div class="td-step-number">1</div>
                        <h6 class="fw-bold mb-2">Buat Akun Mahasiswa</h6>
                        <p class="text-td-muted small mb-0" style="line-height: 1.75;">
                            Daftar dalam hitungan detik untuk membuka ruang personalmu di TemanDengar.
                        </p>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="td-step-card bg-white">
                        <div class="td-step-number">2</div>
                        <h6 class="fw-bold mb-2">Tulis Jurnal Harian</h6>
                        <p class="text-td-muted small mb-0" style="line-height: 1.75;">
                            Pilih kondisi perasaanmu hari ini dan ceritakan hal yang sedang membebani pikiranmu.
                        </p>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="td-step-card bg-white">
                        <div class="td-step-number">3</div>
                        <h6 class="fw-bold mb-2">Baca AI Reflection</h6>
                        <p class="text-td-muted small mb-0" style="line-height: 1.75;">
                            Pahami pola emosi dan temukan langkah kecil yang bisa kamu lakukan hari ini.
                        </p>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="td-step-card bg-white">
                        <div class="td-step-number">4</div>
                        <h6 class="fw-bold mb-2">Terhubung ke Konselor</h6>
                        <p class="text-td-muted small mb-0" style="line-height: 1.75;">
                            Ajukan konsultasi dengan konselor sebaya dan tentukan sendiri jurnal mana yang ingin dibagikan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. PRIVASI & KENDALI --}}
    <section id="privasi" class="td-section td-section-white">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="td-eyebrow mb-2">Privasi &amp; Keamanan</span>
                    <h2 class="fw-bold text-td-primary mb-3">Ceritamu Tetap Sepenuhnya Milikmu</h2>
                    <p class="text-td-muted mb-4" style="line-height: 1.8;">
                        Kami memahami bahwa bercerita membutuhkan rasa aman. Oleh karena itu, TemanDengar
                        menerapkan kendali privasi berbasis persetujuan (*consent-first*) di setiap fitur.
                    </p>

                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="feature-icon flex-shrink-0" style="width: 40px; height: 40px; font-size: 16px;">
                                &#10003;
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Jurnal Bersifat Pribadi Secara Default</h6>
                                <p class="text-td-muted small mb-0">
                                    Tidak ada konselor atau pengguna lain yang dapat membaca jurnalmu tanpa izinmu.
                                </p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start gap-3">
                            <div class="feature-icon flex-shrink-0" style="width: 40px; height: 40px; font-size: 16px;">
                                &#10003;
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Kendali Berbagi Saat Konsultasi</h6>
                                <p class="text-td-muted small mb-0">
                                    Saat mengajukan konsultasi, kamu bebas memilih untuk melampirkan jurnal/AI Reflection
                                    atau bercerita dari awal.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="td-cta-banner">
                        <span class="badge rounded-pill px-3 py-2 mb-3"
                            style="background: rgba(255,255,255,0.15); color: #fff;">
                            Siap untuk mulai?
                        </span>
                        <h3 class="fw-bold mb-3" style="line-height: 1.35;">
                            Beri ruang bagi dirimu untuk bernapas dan didengarkan.
                        </h3>
                        <p class="mb-4" style="color: rgba(255,255,255,0.85); line-height: 1.75;">
                            Gratis untuk mahasiswa. Mulai tulis jurnal pertamamu hari ini atau temukan konselor sebaya yang
                            tepat.
                        </p>
                        <div class="d-flex flex-wrap gap-3">
                            @auth
                                <a href="{{ route('dashboard') }}"
                                    class="btn btn-light fw-semibold text-td-primary px-4 py-2 rounded-3">
                                    Ke Dashboard Saya
                                </a>
                            @else
                                <a href="{{ route('register') }}"
                                    class="btn btn-light fw-semibold text-td-primary px-4 py-2 rounded-3">
                                    Daftar Sekarang
                                </a>
                                <a href="{{ route('login') }}" class="btn btn-outline-light px-4 py-2 rounded-3">
                                    Masuk Akun
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
