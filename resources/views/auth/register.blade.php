@extends('layouts.guest')

@section('title', 'Daftar Akun')

@section('content')
    <div class="td-auth-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">

                    <div class="td-auth-shell">
                        <div class="row g-0">

                            {{-- Sisi Kiri: Value Proposition Panel (Desktop) --}}
                            <div class="col-lg-5 d-none d-lg-flex">
                                <div class="td-auth-aside w-100">
                                    <div>
                                        <span class="badge rounded-pill px-3 py-2 mb-4"
                                            style="background: rgba(255,255,255,0.14); color: #fff; font-weight: 500;">
                                            Mulai Perjalananmu
                                        </span>

                                        <h3 class="fw-bold mb-3" style="line-height: 1.35;">
                                            Beri ruang aman untuk ceritamu sendiri.
                                        </h3>

                                        <p class="small mb-4" style="color: rgba(255,255,255,0.82); line-height: 1.8;">
                                            Bergabung dengan TemanDengar untuk mengelola jurnal emosi, memahami
                                            perasaan lewat AI Reflection, dan terhubung dengan konselor sebaya.
                                        </p>

                                        <ul class="list-unstyled d-flex flex-column gap-3 small mb-0"
                                            style="color: rgba(255,255,255,0.9);">
                                            <li class="d-flex align-items-center gap-2">
                                                <span class="td-pill-dot"></span>
                                                <span>Jurnal pribadi harian &amp; pelacak mood</span>
                                            </li>
                                            <li class="d-flex align-items-center gap-2">
                                                <span class="td-pill-dot"></span>
                                                <span>AI Reflection untuk menjernihkan pikiran</span>
                                            </li>
                                            <li class="d-flex align-items-center gap-2">
                                                <span class="td-pill-dot"></span>
                                                <span>Konsultasi sebaya dengan kendali privasi penuh</span>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="td-auth-quote-box mt-4">
                                        <p class="mb-0"
                                            style="font-size: 12px; color: rgba(255,255,255,0.85); line-height: 1.65;">
                                            "Tidak harus selalu punya jawaban hari ini. Langkah pertama cukup dengan
                                            memberi ruang untuk memahami apa yang sedang kamu rasakan."
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Sisi Kanan: Form Registrasi --}}
                            <div class="col-lg-7">
                                <div class="td-auth-form-area">

                                    <div class="mb-4">
                                        <div class="d-flex align-items-center gap-2 mb-3 d-lg-none">
                                            <span class="brand-mark"
                                                style="width: 32px; height: 32px; font-size: 14px;">T</span>
                                            <span class="fw-bold text-td-primary">TemanDengar</span>
                                        </div>

                                        <h3 class="fw-bold text-td-primary mb-1">
                                            Buat Akun TemanDengar
                                        </h3>
                                        <p class="text-td-muted small mb-0">
                                            Lengkapi data singkat di bawah untuk memulai.
                                        </p>
                                    </div>

                                    <form action="{{ route('register.store') }}" method="POST" id="tdRegisterForm">
                                        @csrf

                                        {{-- Nama lengkap --}}
                                        <div class="mb-3">
                                            <label for="name" class="form-label small fw-semibold">
                                                Nama Lengkap
                                            </label>

                                            <input type="text" name="name" id="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                placeholder="Masukkan nama lengkap atau panggilan"
                                                value="{{ old('name') }}" maxlength="100" autocomplete="name" required
                                                autofocus>

                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Email --}}
                                        <div class="mb-3">
                                            <label for="email" class="form-label small fw-semibold">
                                                Alamat Email
                                            </label>

                                            <input type="email" name="email" id="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                placeholder="nama@email.com" value="{{ old('email') }}"
                                                autocomplete="email" required>

                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Password --}}
                                        <div class="mb-3">
                                            <label for="password" class="form-label small fw-semibold">
                                                Password
                                            </label>

                                            <div class="td-input-group-password">
                                                <input type="password" name="password" id="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    placeholder="Minimal 8 karakter" autocomplete="new-password" required>

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

                                        {{-- Konfirmasi password --}}
                                        <div class="mb-4">
                                            <label for="password_confirmation" class="form-label small fw-semibold">
                                                Konfirmasi Password
                                            </label>

                                            <div class="td-input-group-password">
                                                <input type="password" name="password_confirmation"
                                                    id="password_confirmation" class="form-control"
                                                    placeholder="Masukkan ulang password" autocomplete="new-password"
                                                    required>

                                                <button type="button" class="td-password-toggle"
                                                    data-toggle-password="password_confirmation"
                                                    aria-label="Tampilkan konfirmasi password">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                                        <circle cx="12" cy="12" r="3" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-td w-100 py-2 fw-semibold"
                                            id="tdRegisterSubmit">
                                            Buat Akun Sekarang
                                        </button>
                                    </form>

                                    <div class="text-center mt-4 pt-3 border-top">
                                        <span class="small text-td-muted">
                                            Sudah memiliki akun?
                                        </span>
                                        <a href="{{ route('login') }}"
                                            class="small fw-semibold text-td-primary text-decoration-none ms-1">
                                            Masuk di sini &rarr;
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

        document.getElementById('tdRegisterForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('tdRegisterSubmit');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Mendaftarkan akun...';
            }
        });
    </script>
@endpush
