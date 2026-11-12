@extends('layouts.app')

@section('title', 'Detail Permintaan Konsultasi')

@section('content')

    @php
        $statusLabels = [
            'pending_confirmation' => [
                'label' => 'Menunggu Konfirmasi',
                'class' => 'warning',
                'description' => 'Permintaan ini menunggu keputusanmu.',
            ],
            'accepted' => [
                'label' => 'Diterima',
                'class' => 'success',
                'description' => 'Kamu sudah menerima permintaan konsultasi ini.',
            ],
            'rejected' => [
                'label' => 'Ditolak',
                'class' => 'danger',
                'description' => 'Permintaan konsultasi ini telah ditolak.',
            ],
            'completed' => [
                'label' => 'Selesai',
                'class' => 'primary',
                'description' => 'Konsultasi ini telah selesai.',
            ],
            'cancelled' => [
                'label' => 'Dibatalkan',
                'class' => 'secondary',
                'description' => 'Permintaan konsultasi ini telah dibatalkan.',
            ],
        ];

        $status = $statusLabels[$consultation->status] ?? [
            'label' => ucfirst($consultation->status),
            'class' => 'secondary',
            'description' => 'Status konsultasi sedang diperbarui.',
        ];
    @endphp

    {{-- Header --}}
    <div class="mb-4">

        <a href="{{ route('counselor.consultations.index') }}" class="text-td-primary text-decoration-none small">

            &larr; Kembali ke Permintaan Konsultasi

        </a>

        <h3 class="fw-bold mt-3 mb-1">Detail Permintaan</h3>

        <p class="text-td-muted mb-0">
            Baca informasi yang disampaikan Pencerita sebelum menentukan keputusan.
        </p>

    </div>

    @if ($errors->has('status'))
        <div class="alert alert-warning alert-dismissible fade show">
            {{ $errors->first('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4 consultation-detail-layout">

        {{-- Detail permintaan --}}
        <div class="col-lg-8">

            <div class="td-card consultation-compact-card mb-4">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">

                    <h5 class="fw-bold mb-0">
                        {{ $consultation->topic }}
                    </h5>

                    <span class="badge bg-{{ $status['class'] }}-subtle text-{{ $status['class'] }}">
                        {{ $status['label'] }}
                    </span>

                </div>

                <div class="bg-td-soft rounded-3 p-3 mb-4">
                    <p class="small mb-0">
                        {{ $status['description'] }}
                    </p>
                </div>

                @if (filled($consultation->context))
                    <div class="mb-4">
                        <h6 class="fw-bold mb-2">
                            Catatan Tambahan dari Pencerita
                        </h6>

                        <div class="bg-light rounded-3 p-3" style=" line-height: 1.7;">
                            {{ $consultation->context }}
                        </div>
                    </div>
                @endif

                <hr class="my-4">

                <div class="mb-3">
                    <div class="text-td-muted small mb-1">
                        Waktu konsultasi yang diinginkan
                    </div>

                    <div class="fw-semibold">
                        {{ \Illuminate\Support\Carbon::parse($consultation->preferred_at)->format('d F Y, H:i') }}
                    </div>

                    <small class="text-td-muted">
                        Waktu ini merupakan preferensi awal, belum merupakan jadwal final.
                    </small>
                </div>

                <div>
                    <div class="text-td-muted small mb-1">
                        Permintaan diajukan
                    </div>

                    <div class="fw-semibold">
                        {{ $consultation->created_at->format('d F Y, H:i') }}
                    </div>
                </div>

            </div>

            {{-- Jurnal yang disetujui --}}
            <div class="td-card consultation-compact-card mb-4">

                <h5 class="fw-bold mb-2">Materi yang Dibagikan</h5>

                <p class="text-td-muted small mb-4">
                    Bagian ini hanya menampilkan materi yang telah disetujui
                    secara eksplisit oleh Pencerita.
                </p>

                @if ($sharedJournal)
                    <div class="border rounded-3 p-3 mb-3">

                        <span class="badge bg-success-subtle text-success mb-2">
                            Jurnal Diizinkan untuk Dibagikan
                        </span>

                        <h6 class="fw-bold">
                            {{ $sharedJournal->title ?: 'Catatan Pribadi' }}
                        </h6>

                        <div style=" line-height: 1.8;">
                            {{ $sharedJournal->content }}
                        </div>

                    </div>
                @else
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <p class="text-td-muted small mb-0">
                            Pencerita tidak memberikan izin untuk membagikan isi jurnal.
                        </p>
                    </div>
                @endif

                {{-- AI Reflection yang disetujui --}}
                @if ($sharedReflection)

                    <div class="border rounded-3 p-3">

                        <span class="badge bg-success-subtle text-success mb-3">
                            AI Reflection Diizinkan untuk Dibagikan
                        </span>

                        @if ($sharedReflection->summary)
                            <section class="mb-3">

                                <h6 class="fw-bold small">
                                    Ringkasan Cerita
                                </h6>

                                <p class="small mb-0" style="line-height: 1.7;">
                                    {{ $sharedReflection->summary }}
                                </p>

                            </section>
                        @endif

                        @if ($sharedReflection->emotional_insight)
                            <section class="border-top pt-3 mb-3">

                                <h6 class="fw-bold small">
                                    Insight Emosional
                                </h6>

                                <p class="small mb-0" style="line-height: 1.7;">
                                    {{ $sharedReflection->emotional_insight }}
                                </p>

                            </section>
                        @endif

                        @if (!empty($sharedReflection->key_concerns))
                            <section class="border-top pt-3 mb-3">

                                <h6 class="fw-bold small">
                                    Hal yang Menjadi Perhatian
                                </h6>

                                <ul class="small mb-0">
                                    @foreach ($sharedReflection->key_concerns as $concern)
                                        <li>{{ $concern }}</li>
                                    @endforeach
                                </ul>

                            </section>
                        @endif

                        @if (!empty($sharedReflection->reflection_questions))
                            <section class="border-top pt-3 mb-3">

                                <h6 class="fw-bold small">
                                    Pertanyaan Refleksi
                                </h6>

                                <ol class="small mb-0">
                                    @foreach ($sharedReflection->reflection_questions as $question)
                                        <li class="mb-1">{{ $question }}</li>
                                    @endforeach
                                </ol>

                            </section>
                        @endif

                        @if (!empty($sharedReflection->suggested_steps))
                            <section class="border-top pt-3">

                                <h6 class="fw-bold small">
                                    Langkah yang Bisa Dicoba
                                </h6>

                                <ul class="small mb-0">
                                    @foreach ($sharedReflection->suggested_steps as $step)
                                        <li>{{ $step }}</li>
                                    @endforeach
                                </ul>

                            </section>
                        @endif

                    </div>
                @else
                    <div class="bg-light rounded-3 p-3">
                        <p class="text-td-muted small mb-0">
                            Pencerita tidak memberikan izin untuk membagikan AI Reflection.
                        </p>
                    </div>

                @endif

            </div>


            {{-- Ringkasan Persiapan Konsultasi dari Hermes --}}
            <div class="td-card consultation-compact-card">

                <h5 class="fw-bold mb-3">
                    Ringkasan Persiapan Konsultasi
                </h5>

                @if ($consultation->brief_status === 'completed' && filled($consultation->consultation_brief))

                    <div style="line-height: 1.8;">
                        {{ $consultation->consultation_brief }}
                    </div>

                    @if ($consultation->brief_generated_at)
                        <small class="text-td-muted d-block mt-3">
                            Dibuat pada
                            {{ $consultation->brief_generated_at->format('d F Y, H:i') }}
                        </small>
                    @endif
                @elseif (in_array($consultation->brief_status, ['pending', 'processing']))
                    <div class="bg-light rounded-3 p-3">
                        <p class="text-td-muted small mb-0">
                            Ringkasan persiapan konsultasi sedang dibuat.
                        </p>
                    </div>
                @elseif ($consultation->brief_status === 'failed')
                    <div class="bg-light rounded-3 p-3">
                        <p class="text-td-muted small mb-0">
                            Ringkasan persiapan belum berhasil dibuat.
                            Silakan gunakan informasi permintaan dan materi yang
                            telah dibagikan untuk mempersiapkan konsultasi.
                        </p>
                    </div>
                @else
                    <div class="bg-light rounded-3 p-3">
                        <p class="text-td-muted small mb-0">
                            Ringkasan persiapan belum tersedia.
                        </p>
                    </div>

                @endif

            </div>


        </div>

        {{-- Profil Pencerita dan keputusan --}}
        <div class="col-lg-4">

            <div class="td-card consultation-compact-card mb-4">

                <div class="feature-icon mb-3">P</div>

                <h5 class="fw-bold mb-1">
                    {{ $consultation->user?->name ?? 'Pencerita' }}
                </h5>

                <p class="text-td-muted small mb-0">
                    Pencerita TemanDengar
                </p>

            </div>

            @if ($consultation->status === 'pending_confirmation')
                <div class="td-card consultation-compact-card">

                    <h5 class="fw-bold mb-2">
                        Keputusan Konsultasi
                    </h5>

                    <p class="text-td-muted small mb-4">
                        Tinjau permintaan dan materi yang dibagikan
                        sebelum menerima atau menolaknya.
                    </p>

                    <form action="{{ route('counselor.consultations.accept', $consultation->id) }}" method="POST"
                        class="mb-3"
                        onsubmit="return confirm('Apakah kamu yakin ingin menerima permintaan konsultasi ini?')">

                        @csrf
                        @method('PATCH')

                        <button type="submit" class="btn btn-td w-100">
                            Terima Permintaan
                        </button>

                    </form>

                    <form action="{{ route('counselor.consultations.reject', $consultation->id) }}" method="POST"
                        onsubmit="return confirm('Apakah kamu yakin ingin menolak permintaan konsultasi ini?')">

                        @csrf
                        @method('PATCH')

                        <button type="submit" class="btn btn-outline-danger w-100">
                            Tolak Permintaan
                        </button>

                    </form>

                </div>
            @elseif ($consultation->status === 'accepted')
                <div class="td-card consultation-compact-card">

                    <h5 class="fw-bold mb-2">Jadwal Konsultasi</h5>

                    <p class="text-td-muted small mb-3">
                        Permintaan sudah diterima. Tetapkan waktu final
                        agar Pencerita dapat melihat jadwal yang disepakati.
                    </p>

                    @if ($consultation->scheduled_at)
                        <div class="bg-td-soft rounded-3 p-3 mb-3">

                            <div class="text-td-muted small mb-1">
                                Jadwal Final Saat Ini
                            </div>

                            <div class="fw-bold">
                                {{ $consultation->scheduled_at->format('d F Y, H:i') }}
                            </div>

                        </div>
                    @else
                        <div class="bg-light rounded-3 p-3 mb-3">
                            <p class="text-td-muted small mb-0">
                                Jadwal final belum ditetapkan.
                            </p>
                        </div>
                    @endif

                    <form action="{{ route('counselor.consultations.schedule', $consultation->id) }}" method="POST">

                        @csrf
                        @method('PATCH')

                        <label for="scheduled_at" class="form-label fw-semibold">
                            {{ $consultation->scheduled_at ? 'Perbarui Jadwal' : 'Tetapkan Jadwal' }}
                        </label>

                        <input type="datetime-local" id="scheduled_at" name="scheduled_at"
                            class="form-control @error('scheduled_at') is-invalid @enderror"
                            min="{{ now()->format('Y-m-d\TH:i') }}"
                            value="{{ old('scheduled_at', $consultation->scheduled_at?->format('Y-m-d\TH:i')) }}"
                            required>

                        @error('scheduled_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        @error('status')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror

                        <button type="submit" class="btn btn-td w-100 mt-3">
                            {{ $consultation->scheduled_at ? 'Simpan Perubahan Jadwal' : 'Tetapkan Jadwal Final' }}
                        </button>

                    </form>

                    @if ($consultation->scheduled_at && $consultation->scheduled_at->isPast())
                        <hr class="my-4">

                        <h6 class="fw-bold mb-2">Penyelesaian Konsultasi</h6>

                        <p class="text-td-muted small">
                            Tandai konsultasi selesai setelah sesi benar-benar terlaksana.
                        </p>

                        <form action="{{ route('counselor.consultations.complete', $consultation->id) }}" method="POST"
                            onsubmit="return confirm('Pastikan sesi konsultasi sudah selesai. Lanjutkan?')">

                            @csrf
                            @method('PATCH')

                            <button type="submit" class="btn btn-success w-100">
                                Tandai Selesai
                            </button>

                        </form>
                    @endif

                </div>
            @else
                <div class="td-card consultation-compact-card">

                    <h6 class="fw-bold mb-2">Keputusan Sudah Diproses</h6>

                    <p class="text-td-muted small mb-0">
                        Permintaan ini sudah tidak dapat diubah melalui halaman ini.
                    </p>

                </div>
            @endif

        </div>

    </div>


    <style>
        /*
                     * Memperbaiki card yang terlalu panjang.
                     * Hanya berlaku untuk halaman detail permintaan konselor.
                     */
        .consultation-detail-layout .consultation-compact-card {
            height: auto !important;
            min-height: 0 !important;
            align-self: flex-start;
        }

        .consultation-detail-layout>[class*="col-"] {
            align-self: flex-start;
        }

        /*
                     * Jarak antarkonten dibuat lebih ringkas.
                     */
        .consultation-detail-layout .consultation-compact-card {
            padding: 24px;
        }

        /*
                     * Membuat isi jurnal dan refleksi lebih nyaman dibaca.
                     */
        .consultation-detail-layout .consultation-compact-card p,
        .consultation-detail-layout .consultation-compact-card li {
            line-height: 1.7;
        }

        /*
                     * Sidebar keputusan tetap berada setelah profil konselor.
                     */
        @media (min-width: 992px) {
            .consultation-detail-layout .col-lg-4 {
                position: relative;
            }
        }
    </style>


@endsection
