@extends('layouts.guest')

@section('title', 'Daftar Akun')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">

                <div class="auth-card p-4 p-md-5">

                    <div class="text-center mb-4">
                        <div class="brand-mark mx-auto mb-3">T</div>

                        <h3 class="fw-bold text-td-primary">
                            Buat Akun TemanDengar
                        </h3>

                        <p class="text-td-muted small">
                            Mulai perjalanan untuk memahami ceritamu.
                        </p>
                    </div>

                    <form action="{{ route('register.store') }}" method="POST">
                        @csrf

                        {{-- Nama lengkap --}}
                        <div class="mb-3">
                            <label for="name" class="form-label fw-medium">
                                Nama Lengkap
                            </label>

                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror" placeholder="Masukkan nama lengkap"
                                value="{{ old('name') }}" maxlength="100" autocomplete="name" required>

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">
                                Email
                            </label>

                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror" placeholder="nama@email.com"
                                value="{{ old('email') }}" autocomplete="email" required>

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
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Minimal 8 karakter" autocomplete="new-password" required>

                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Konfirmasi password --}}
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-medium">
                                Konfirmasi Password
                            </label>

                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control" placeholder="Masukkan ulang password" autocomplete="new-password"
                                required>
                        </div>

                        <button type="submit" class="btn btn-td w-100">
                            Buat Akun
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <span class="small text-td-muted">
                            Sudah memiliki akun?
                        </span>

                        <a href="{{ route('login') }}" class="small fw-semibold text-td-primary text-decoration-none">
                            Masuk di sini
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection
