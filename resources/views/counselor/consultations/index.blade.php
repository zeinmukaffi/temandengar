@extends('layouts.app')

@section('title', 'Permintaan Konsultasi')

@section('content')

    @php
        $statusLabels = [
            'pending_confirmation' => [
                'label' => 'Menunggu Konfirmasi',
                'class' => 'warning',
            ],
            'accepted' => [
                'label' => 'Diterima',
                'class' => 'success',
            ],
            'rejected' => [
                'label' => 'Ditolak',
                'class' => 'danger',
            ],
            'completed' => [
                'label' => 'Selesai',
                'class' => 'primary',
            ],
            'cancelled' => [
                'label' => 'Dibatalkan',
                'class' => 'secondary',
            ],
        ];
    @endphp

    {{-- Header --}}
    <div class="mb-4">
        <p class="text-td-muted small mb-2">RUANG KONSELOR</p>

        <h3 class="fw-bold mb-1">
            Selamat datang, {{ $counselor->name }}
        </h3>

        <p class="text-td-muted mb-0">
            Kelola permintaan konsultasi dari mahasiswa yang ingin bercerita.
        </p>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-lg-3">
            <div class="td-card h-100">
                <p class="text-td-muted small mb-2">Total Permintaan</p>
                <h3 class="fw-bold mb-0">{{ $totalRequests }}</h3>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="td-card h-100">
                <p class="text-td-muted small mb-2">Menunggu</p>
                <h3 class="fw-bold text-warning mb-0">{{ $pendingRequests }}</h3>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="td-card h-100">
                <p class="text-td-muted small mb-2">Diterima</p>
                <h3 class="fw-bold text-success mb-0">{{ $acceptedRequests }}</h3>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="td-card h-100">
                <p class="text-td-muted small mb-2">Selesai</p>
                <h3 class="fw-bold text-primary mb-0">{{ $completedRequests }}</h3>
            </div>
        </div>

    </div>

    {{-- Daftar permintaan --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Permintaan Konsultasi</h5>

        <span class="text-td-muted small">
            {{ $consultations->total() }} data
        </span>
    </div>

    @if ($consultations->isEmpty())

        <div class="td-card text-center py-5">

            <div class="feature-icon mx-auto mb-3">C</div>

            <h5 class="fw-bold">Belum Ada Permintaan</h5>

            <p class="text-td-muted small mb-0">
                Permintaan konsultasi baru akan muncul di halaman ini.
            </p>

        </div>
    @else
        <div class="row g-3">

            @foreach ($consultations as $item)
                @php
                    $status = $statusLabels[$item->status] ?? [
                        'label' => ucfirst($item->status),
                        'class' => 'secondary',
                    ];
                @endphp

                <div class="col-md-6">

                    <div class="td-card h-100">

                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">

                            <span class="badge bg-{{ $status['class'] }}-subtle text-{{ $status['class'] }}">
                                {{ $status['label'] }}
                            </span>

                            <small class="text-td-muted">
                                {{ $item->created_at->format('d M Y') }}
                            </small>

                        </div>

                        <h5 class="fw-bold mb-1">
                            {{ $item->user?->name ?? 'Pengguna' }}
                        </h5>

                        <p class="text-td-primary small fw-semibold mb-3">
                            {{ $item->topic }}
                        </p>

                        <p class="text-td-muted small mb-3">
                            {{ \Illuminate\Support\Str::limit($item->context ?: 'Tidak ada cerita tambahan.', 130) }}
                        </p>

                        <div class="border-top pt-3 mb-3">

                            <div class="text-td-muted small mb-1">
                                Waktu yang diinginkan
                            </div>

                            <div class="small fw-semibold">
                                {{ \Illuminate\Support\Carbon::parse($item->preferred_at)->format('d M Y, H:i') }}
                            </div>

                        </div>

                        <a href="{{ route('counselor.consultations.show', $item->id) }}"
                            class="btn btn-outline-td btn-sm w-100">

                            Lihat Permintaan

                        </a>

                    </div>

                </div>
            @endforeach

        </div>

        <div class="mt-4">
            {{ $consultations->links() }}
        </div>

    @endif

@endsection
