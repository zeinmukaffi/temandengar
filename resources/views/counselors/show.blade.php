@extends('layouts.app')

@section('title', 'Profil Konselor')

@section('content')
    <div class="mb-4">
        <a href="{{ route('counselors.index') }}" class="text-td-primary text-decoration-none small">
            &larr; Kembali ke Direktori Konselor
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="td-card">
                <div class="d-flex flex-wrap align-items-start gap-3 mb-4">
                    @if ($counselor->photo_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($counselor->photo_path) }}"
                            alt="Foto {{ $counselor->name }}" class="rounded-circle"
                            style="width:104px;height:104px;object-fit:cover;">
                    @else
                        <div class="feature-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="width:104px;height:104px;font-size:2rem;">
                            {{ mb_strtoupper(mb_substr($counselor->name, 0, 1)) }}
                        </div>
                    @endif

                    <div class="flex-grow-1">
                        <span class="badge bg-success-subtle text-success mb-2">Tersedia untuk konsultasi</span>
                        <h3 class="fw-bold mb-1">{{ $counselor->name }}</h3>
                        <p class="text-td-muted mb-0">{{ $counselor->role }}</p>
                    </div>
                </div>

                <hr class="my-4">

                <section class="mb-4">
                    <h5 class="fw-bold mb-2">Tentang Konselor</h5>
                    <p class="text-td-muted mb-0" style="white-space:pre-line;line-height:1.8;">
                        {{ $counselor->bio ?: 'Belum ada deskripsi profil.' }}
                    </p>
                </section>

                <section class="mb-4">
                    <h5 class="fw-bold mb-2">Bidang Pendampingan</h5>
                    @if (!empty($counselor->specialties))
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($counselor->specialties as $specialty)
                                <span class="badge bg-td-soft text-td-primary px-3 py-2">{{ $specialty }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-td-muted small mb-0">Belum ada bidang pendampingan yang dicantumkan.</p>
                    @endif
                </section>

                <section>
                    <h5 class="fw-bold mb-2">Informasi Ketersediaan</h5>
                    <p class="text-td-muted mb-0">
                        {{ $counselor->availability_description ?: 'Konselor tersedia. Waktu konsultasi akan dikonfirmasi setelah pengajuan.' }}
                    </p>
                </section>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="td-card">
                <div class="feature-icon mb-3">K</div>
                <h5 class="fw-bold mb-2">Merasa cocok untuk bercerita?</h5>
                <p class="text-td-muted small mb-3">
                    Kamu dapat memilih konselor ini sebagai pendamping. Sebelum pengajuan dikirim,
                    kamu akan menentukan informasi apa yang ingin dibagikan.
                </p>
                <div class="bg-td-soft rounded-3 p-3 mb-3">
                    <p class="small text-td-primary mb-0">
                        Jurnal dan refleksi AI tetap pribadi sampai kamu memberikan persetujuan untuk membagikannya.
                    </p>
                </div>
                <a href="{{ route('consultations.create', $counselor->id) }}" class="btn btn-td w-100">
                    Ajukan Konsultasi
                </a>
                <p class="text-td-muted text-center mt-2 mb-0" style="font-size:11px;">
                    Form pengajuan sedang dalam tahap pengembangan.
                </p>
            </div>
        </div>
    </div>
@endsection
