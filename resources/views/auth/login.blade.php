@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">

                <div class="auth-card p-4 p-md-5">

                    <div class="text-center mb-4">
                        <div class="brand-mark mx-auto mb-3">T</div>

                        <h3 class="fw-bold text-td-primary">
                            Selamat Datang Kembali
                        </h3>

                        <p class="text-td-muted small">
                            Masuk untuk melanjutkan perjalananmu di TemanDengar.
                        </p>
                    </div>

                    <form action="{{ route('login.store') }}" method="POST">
                        @csrf

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">
                                Email
                            </label>

                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror" placeholder="nama@email.com"
                                value="{{ old('email') }}" autocomplete="username" required autofocus>

                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label fw-medium">
                                Password
                            </label>

                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan password"
                                autocomplete="current-password" required>

                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Remember me --}}
                        <div class="mb-4 form-check">
                            <input type="checkbox" name="remember" id="remember" class="form-check-input" value="1">

                            <label for="remember" class="form-check-label small text-td-muted">
                                Ingat saya
                            </label>
                        </div>

                        <button type="submit" class="btn btn-td w-100">
                            Masuk
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <span class="small text-td-muted">
                            Belum memiliki akun?
                        </span>

                        <a href="{{ route('register') }}" class="small fw-semibold text-td-primary text-decoration-none">
                            Daftar sekarang
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection
