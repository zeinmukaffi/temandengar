@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <div class="td-auth-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">

                    <div class="td-auth-shell">
                        <div class="row g-0">

                            {{-- Sisi Kiri: Brand & Empathy Panel (Desktop) --}}
                            <div class="col-lg-5 d-none d-lg-flex">
                                <div class="td-auth-aside w-100">
                                    <div>
                                        <span class="badge rounded-pill px-3 py-2 mb-4"
                                            style="background: rgba(255,255,255,0.14); color: #fff; font-weight: 500;">
                                            Ruang Cerita Mahasiswa
                                        </span>

                                        <h3 class="fw-bold mb-3" style="line-height: 1.35;">
                                            Senang melihatmu kembali di TemanDengar.
                                        </h3>

                                        <p class="small mb-0" style="color: rgba(255,255,255,0.82); line-height: 1.8;">
                                            Ambil jeda sejenak dari padatnya aktivitas kuliah. Jurnal pribadimu
                                            dan konselor sebaya selalu siap menemani kapan pun kamu butuh.
                                        </p>
                                    </div>

                                    <div class="td-auth-quote-box mt-4">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                stroke="#73B7AA" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z" />
                                                <path d="m9 12 2 2 4-4" />
                                            </svg>
                                            <span class="small fw-semibold">Ruang Aman &amp; Terjaga</span>
                                        </div>
                                        <p class="mb-0"
                                            style="font-size: 12px; color: rgba(255,255,255,0.8); line-height: 1.65;">
                                            Seluruh catatan jurnalmu bersifat pribadi secara default dan hanya dapat
                                            diakses olehmu kecuali kamu memilih untuk membagikannya.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Sisi Kanan: Form Login --}}
                            <div class="col-lg-7">
                                <div class="td-auth-form-area">

                                    <div class="mb-4">
                                        <div class="d-flex align-items-center gap-2 mb-3 d-lg-none">
                                            <span class="brand-mark"
                                                style="width: 32px; height: 32px; font-size: 14px;">T</span>
                                            <span class="fw-bold text-td-primary">TemanDengar</span>
                                        </div>

                                        <h3 class="fw-bold text-td-primary mb-1">
                                            Masuk ke Akunmu
                                        </h3>
                                        <p class="text-td-muted small mb-0">
                                            Lanjutkan jurnal harian dan sesi refleksimu hari ini.
                                        </p>
                                    </div>

                                    <form action="{{ route('login.store') }}" method="POST" id="tdLoginForm">
                                        @csrf

                                        {{-- Email --}}
                                        <div class="mb-3">
                                            <label for="email" class="form-label small fw-semibold">
                                                Alamat Email
                                            </label>

                                            <input type="email" name="email" id="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                placeholder="nama@email.com" value="{{ old('email') }}"
                                                autocomplete="username" required autofocus>

                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Password dengan Toggle Visibility --}}
                                        <div class="mb-3">
                                            <label for="password" class="form-label small fw-semibold">
                                                Password
                                            </label>

                                            <div class="td-input-group-password">
                                                <input type="password" name="password" id="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    placeholder="Masukkan password" autocomplete="current-password"
                                                    required>

                                                <button type="button" class="td-password-toggle"
                                                    data-toggle-password="password" aria-label="Tampilkan password">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                                        <circle cx="12" cy="12" r="3" />
                                                    </svg>
                                                </button>

                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Remember me --}}
                                        <div class="mb-4 d-flex align-items-center justify-content-between">
                                            <div class="form-check mb-0">
                                                <input type="checkbox" name="remember" id="remember"
                                                    class="form-check-input" value="1"
                                                    {{ old('remember') ? 'checked' : '' }}>
                                                <label for="remember" class="form-check-label small text-td-muted">
                                                    Ingat sesi saya
                                                </label>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-td w-100 py-2 fw-semibold" id="tdLoginSubmit">
                                            Masuk ke TemanDengar
                                        </button>
                                    </form>

                                    <div class="text-center mt-4 pt-3 border-top">
                                        <span class="small text-td-muted">
                                            Belum memiliki akun?
                                        </span>
                                        <a href="{{ route('register') }}"
                                            class="small fw-semibold text-td-primary text-decoration-none ms-1">
                                            Daftar sekarang &rarr;
                                        </a>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const input = document.getElementById(btn.dataset.togglePassword);
                if (!input) return;
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                btn.style.color = isPassword ? 'var(--td-primary)' : 'var(--td-text-muted)';
            });
        });

        document.getElementById('tdLoginForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('tdLoginSubmit');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Memproses...';
            }
        });
    </script>
@endpush
