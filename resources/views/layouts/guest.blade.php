<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'TemanDengar') — Ruang Cerita & Dukungan Mahasiswa</title>

    {{-- Google Fonts: Poppins --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap 5.3.8 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    {{-- Unified TemanDengar CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body>

    {{-- Navbar Publik (Sticky & Responsive) --}}
    <nav class="navbar navbar-expand-lg td-navbar sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <span class="brand-mark">T</span>
                TemanDengar
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#guestNavbar" aria-controls="guestNavbar" aria-expanded="false"
                aria-label="Toggle navigasi">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="guestNavbar">
                {{-- Menu Navigasi Section (tampil optimal di halaman utama) --}}
                <ul class="navbar-nav mx-auto my-3 my-lg-0 gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#tentang">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#fitur">Fitur Utama</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#cara-kerja">Cara Kerja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#privasi">Privasi & Aman</a>
                    </li>
                </ul>

                {{-- Tombol Aksi --}}
                <div class="d-flex flex-column flex-lg-row gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-td btn-sm">
                            Buka Dashboard &rarr;
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-td btn-sm">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-td btn-sm">
                            Daftar Gratis
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main>
        {{-- Notifikasi Global --}}
        @if (session('success') || session('error'))
            <div class="container mt-3">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2"
                        role="alert">
                        <svg width="18" height="18" fill="currentColor" class="flex-shrink-0"
                            viewBox="0 0 16 16">
                            <path
                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                        </svg>
                        <div class="flex-grow-1">{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2"
                        role="alert">
                        <svg width="18" height="18" fill="currentColor" class="flex-shrink-0"
                            viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                            <path d="M7.002 11a1 1 0 1 0 2 0 1 1 0 0 0-2 0zm.93-6.412l-.81 4.542h1.76l-.81-4.542z" />
                        </svg>
                        <div class="flex-grow-1">{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                @endif
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Footer Publik --}}
    <footer class="td-footer">
        <div class="container">
            <div class="row g-4 pb-4">
                <div class="col-lg-5">
                    <a class="navbar-brand d-inline-flex align-items-center mb-3" href="{{ route('home') }}">
                        <span class="brand-mark">T</span>
                        TemanDengar
                    </a>
                    <p class="text-td-muted small mb-0" style="max-width: 380px; line-height: 1.7;">
                        Ruang aman bagi mahasiswa untuk menulis jurnal harian, memahami emosi lewat AI Reflection,
                        dan terhubung dengan konselor sebaya yang terlatih.
                    </p>
                </div>

                <div class="col-6 col-lg-3">
                    <h6 class="fw-bold text-td-primary mb-3">Navigasi</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 small mb-0">
                        <li><a href="{{ route('home') }}#tentang" class="td-footer-link">Tentang Platform</a></li>
                        <li><a href="{{ route('home') }}#fitur" class="td-footer-link">Fitur Utama</a></li>
                        <li><a href="{{ route('home') }}#cara-kerja" class="td-footer-link">Cara Kerja</a></li>
                        <li><a href="{{ route('home') }}#privasi" class="td-footer-link">Privasi & Keamanan</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-4">
                    <h6 class="fw-bold text-td-primary mb-3">Catatan Penting</h6>
                    <p class="text-td-muted small mb-0" style="line-height: 1.7;">
                        TemanDengar menyediakan dukungan sebaya dan refleksi mandiri, bukan pengganti diagnosis klinis
                        atau penanganan gawat darurat medis/psikiatri.
                    </p>
                </div>
            </div>

            <div
                class="border-top pt-3 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 text-td-muted small">
                <span>&copy; {{ date('Y') }} TemanDengar. Seluruh hak cipta dilindungi.</span>
                <span>Didesain untuk ketenangan &amp; dukungan mahasiswa.</span>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>

    @stack('scripts')
</body>

</html>
