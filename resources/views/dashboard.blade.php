@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    {{-- Header dashboard --}}
    <div class="mb-4">
        <p class="text-td-muted small mb-2">
            DASHBOARD
        </p>

        <h3 class="fw-bold mb-2">
            Halo, {{ $user->name }}.
        </h3>

        <p class="text-td-muted mb-0">
            Selamat datang kembali di ruang TemanDengar.
        </p>
    </div>


    {{-- Welcome section --}}
    <div class="td-card mb-4" style="background: linear-gradient(120deg, #EAF3F2, #F5F8FA);">

        <div class="row align-items-center">
            <div class="col-lg-8">

                <span class="badge bg-white text-td-primary mb-3">
                    Ruang Cerita dan Dukungan
                </span>

                <h4 class="fw-bold text-td-primary mb-3">
                    Mulai dari cerita kecilmu hari ini.
                </h4>

                <p class="text-td-muted mb-0">
                    Tidak apa-apa untuk berhenti sejenak dan memahami
                    apa yang sedang kamu rasakan.
                </p>

            </div>

            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <span class="display-3 fw-bold text-td-primary">T</span>
            </div>
        </div>
    </div>


    <div class="row g-4">

        {{-- Journaling --}}
        <div class="col-md-6">
            <div class="td-card">
                <div class="feature-icon mb-3">J</div>

                <h5 class="fw-bold mb-2">Journaling</h5>

                <p class="text-td-muted small mb-3">
                    Tuangkan cerita dan pikiranmu melalui jurnal pribadi.
                    Kamu juga bisa mencatat mood harian.
                </p>

                <a href="{{ route('journals.index') }}" class="btn btn-td">
                    Buka Journaling
                </a>
            </div>
        </div>

        {{-- Konsultasi --}}
        <div class="col-md-6">
            <div class="td-card">
                <div class="feature-icon mb-3">K</div>

                <h5 class="fw-bold mb-2">Konsultasi Sebaya</h5>

                <p class="text-td-muted small mb-3">
                    Temukan konselor sebaya dan ajukan konsultasi
                    sesuai kebutuhanmu.
                </p>

                <span class="badge bg-light text-secondary">
                    Segera Hadir
                </span>
            </div>
        </div>

    </div>


    {{-- Privacy reminder --}}
    <div class="td-card mt-4">
        <div class="d-flex align-items-start gap-3">

            <div class="feature-icon flex-shrink-0">
                P
            </div>

            <div>
                <h6 class="fw-bold mb-2">
                    Ceritamu tetap milikmu.
                </h6>

                <p class="text-td-muted small mb-0">
                    Jurnal yang kamu tulis bersifat pribadi secara default.
                    Kamu memiliki kendali untuk menentukan materi yang ingin
                    dibagikan saat mengajukan konsultasi.
                </p>
            </div>

        </div>
    </div>

@endsection
