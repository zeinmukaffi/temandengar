@extends('layouts.app')

@section('title', 'Detail Konsultasi')

@section('content')

    @php
        $statusLabels = [
            'pending_confirmation' => [
                'label' => 'Menunggu Konfirmasi',
                'class' => 'warning',
                'description' => 'Permintaan konsultasimu sedang menunggu tanggapan konselor.',
            ],
            'accepted' => [
                'label' => 'Diterima',
                'class' => 'success',
                'description' => 'Konselor telah menerima permintaan konsultasimu.',
            ],
            'rejected' => [
                'label' => 'Ditolak',
                'class' => 'danger',
                'description' => 'Permintaan konsultasi ini belum dapat diterima.',
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

    <div class="mb-4">
        <a href="{{ route('consultations.index') }}" class="text-td-primary text-decoration-none small">
            &larr; Kembali ke Konsultasi Saya
        </a>

        <h3 class="fw-bold mt-3 mb-1">Detail Konsultasi</h3>

        <p class="text-td-muted small mb-0">
            Informasi permintaan konsultasimu.
        </p>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- Informasi permintaan --}}
        <div class="col-lg-8">

            <div class="td-card mb-4">

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">

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

                <div class="mb-4">
                    <div class="text-td-muted small mb-1">
                        Cerita atau alasan konsultasi
                    </div>

                    <div style="white-space: pre-wrap; line-height: 1.8;">
                        {{ $consultation->context ?: 'Tidak ada cerita tambahan.' }}</div>
                </div>

                <div class="border-top pt-3">
                    <div class="text-td-muted small mb-1">
                        Waktu yang diinginkan
                    </div>

                    <div class="fw-semibold">
                        {{ \Illuminate\Support\Carbon::parse($consultation->preferred_at)->format('d F Y, H:i') }}
                    </div>

                    <small class="text-td-muted">
                        Waktu tersebut merupakan preferensi awal, bukan jadwal yang telah dikonfirmasi.
                    </small>
                </div>

                <div class="border-top pt-3 mt-3">
                    <div class="text-td-muted small mb-1">
                        Permintaan dibuat
                    </div>

                    <div class="fw-semibold">
                        {{ $consultation->created_at->format('d F Y, H:i') }}
                    </div>
                </div>

            </div>

            {{-- Informasi materi yang dibagikan --}}
            <div class="td-card mb-4">

                <h5 class="fw-bold mb-2">Informasi yang Dibagikan</h5>

                <p class="text-td-muted small mb-4">
                    Materi yang tercantum di sini adalah informasi
                    yang secara eksplisit kamu setujui untuk dibagikan.
                </p>

                @if ($consultation->share_journal && $consultation->journal)
                    <div class="border rounded-3 p-3 mb-3">

                        <span class="badge bg-success-subtle text-success mb-2">
                            Jurnal Disetujui
                        </span>

                        <h6 class="fw-bold">
                            {{ $consultation->journal->title ?: 'Catatan Pribadi' }}
                        </h6>

                        <div style="white-space: pre-wrap; line-height: 1.8;">{{ $consultation->journal->content }}</div>

                    </div>
                @else
                    <div class="border rounded-3 p-3 mb-3">
                        <p class="small text-td-muted mb-0">
                            Jurnal tidak dibagikan.
                        </p>
                    </div>
                @endif

                @if ($consultation->share_ai_reflection && $consultation->aiReflection)

                    <div class="border rounded-3 p-3">

                        <span class="badge bg-success-subtle text-success mb-3">
                            Refleksi AI Disetujui
                        </span>

                        @if ($consultation->aiReflection->summary)
                            <h6 class="fw-bold small">Ringkasan Cerita</h6>
                            <p class="small">
                                {{ $consultation->aiReflection->summary }}
                            </p>
                        @endif

                        @if ($consultation->aiReflection->emotional_insight)
                            <h6 class="fw-bold small">Insight Emosional</h6>
                            <p class="small mb-0">
                                {{ $consultation->aiReflection->emotional_insight }}
                            </p>
                        @endif

                    </div>
                @else
                    <div class="border rounded-3 p-3">
                        <p class="small text-td-muted mb-0">
                            Refleksi AI tidak dibagikan.
                        </p>
                    </div>

                @endif

                <p class="text-td-muted small mt-3 mb-0">
                    Persetujuan berbagi materi pada permintaan ini tidak
                    mengubah pengaturan privasi jurnal aslimu.
                </p>

            </div>

            {{-- Ringkasan konsultasi dari Hermes --}}
            <div class="td-card">

                <h5 class="fw-bold mb-2">Ringkasan Persiapan Konsultasi</h5>

                @if ($consultation->consultation_brief)
                    <div style="white-space: pre-wrap; line-height: 1.8;">
                        {{ $consultation->consultation_brief }}
                    </div>
                @else
                    <p class="text-td-muted small mb-0">
                        Ringkasan persiapan konsultasi belum dibuat.
                        Hasil ini akan tersedia setelah integrasi
                        Consultation Summary dengan Hermes diaktifkan.
                    </p>
                @endif

            </div>

        </div>

        {{-- Profil konselor --}}
        <div class="col-lg-4">

            <div class="td-card">

                <div class="feature-icon mb-3">K</div>

                <h5 class="fw-bold mb-1">
                    {{ $consultation->counselor?->name ?? 'Konselor' }}
                </h5>

                <p class="text-td-muted small">
                    {{ $consultation->counselor?->role ?? 'Konselor Sebaya' }}
                </p>

                @if ($consultation->counselor?->bio)
                    <p class="small">
                        {{ $consultation->counselor->bio }}
                    </p>
                @endif

                <a href="{{ route('counselors.show', $consultation->counselor_id) }}" class="btn btn-outline-td w-100">
                    Lihat Profil Konselor
                </a>

            </div>

        </div>

    </div>

@endsection
