@extends('layouts.guest')

@section('title', 'Ruang Cerita dan Dukungan')

@section('content')
    <div class="container py-5">

        <div class="row align-items-center min-vh-75 py-5">

            <div class="col-lg-7 mb-5 mb-lg-0">

                <span class="badge rounded-pill bg-td-soft text-td-primary px-3 py-2 mb-3">
                    Ruang Cerita dan Dukungan
                </span>

                <h1 class="display-5 fw-bold lh-base mb-4">
                    Setiap cerita
                    <span class="text-td-primary">layak didengar.</span>
                </h1>

                <p class="text-td-muted fs-6 mb-4" style="max-width: 580px;">
                    TemanDengar adalah ruang bagi mahasiswa untuk menuangkan cerita,
                    merefleksikan perasaan, dan terhubung dengan konselor sebaya.
                </p>

                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="btn btn-td">
                        Mulai Bercerita
                    </a>

                    <a href="{{ route('login') }}" class="btn btn-outline-td">
                        Saya Sudah Punya Akun
                    </a>
                </div>

                <div class="mt-5 d-flex flex-wrap gap-4">
                    <div>
                        <h6 class="fw-bold mb-1 text-td-primary">Journaling</h6>
                        <small class="text-td-muted">Tuangkan ceritamu</small>
                    </div>

                    <div>
                        <h6 class="fw-bold mb-1 text-td-primary">AI Reflection</h6>
                        <small class="text-td-muted">Pahami ceritamu</small>
                    </div>

                    <div>
                        <h6 class="fw-bold mb-1 text-td-primary">Peer Counseling</h6>
                        <small class="text-td-muted">Temukan teman bicara</small>
                    </div>
                </div>

            </div>

            <div class="col-lg-5">
                <div class="auth-card p-4 p-lg-5">
                    <div class="feature-icon mb-4">T</div>

                    <h4 class="fw-bold text-td-primary mb-3">
                        Tidak harus selalu punya jawaban.
                    </h4>

                    <p class="text-td-muted mb-0">
                        Terkadang, langkah pertama cukup dengan memberi ruang
                        untuk memahami apa yang sedang kamu rasakan.
                    </p>

                    <hr class="my-4">

                    <div class="d-flex align-items-start gap-3">
                        <div class="feature-icon flex-shrink-0">1</div>
                        <div>
                            <h6 class="fw-semibold mb-1">Ceritakan</h6>
                            <p class="text-td-muted small mb-0">
                                Tulis apa yang sedang kamu pikirkan.
                            </p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mt-3">
                        <div class="feature-icon flex-shrink-0">2</div>
                        <div>
                            <h6 class="fw-semibold mb-1">Refleksikan</h6>
                            <p class="text-td-muted small mb-0">
                                Dapatkan refleksi berbantuan AI.
                            </p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mt-3">
                        <div class="feature-icon flex-shrink-0">3</div>
                        <div>
                            <h6 class="fw-semibold mb-1">Terhubung</h6>
                            <p class="text-td-muted small mb-0">
                                Pilih konselor sebaya ketika membutuhkan dukungan.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
