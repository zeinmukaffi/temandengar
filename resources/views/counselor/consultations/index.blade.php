@extends('layouts.app')

@section('title', 'Permintaan Konsultasi')

@section('content')

    @php
        $statusLabels = [
            'pending_confirmation' => [
                'label' => 'Menunggu Konfirmasi',
                'pill' => 'warning',
            ],
            'accepted' => [
                'label' => 'Diterima',
                'pill' => 'success',
            ],
            'rejected' => [
                'label' => 'Ditolak',
                'pill' => 'danger',
            ],
            'completed' => [
                'label' => 'Selesai',
                'pill' => 'primary',
            ],
            'cancelled' => [
                'label' => 'Dibatalkan',
                'pill' => 'secondary',
            ],
        ];
    @endphp

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <span class="td-section-eyebrow mb-1">RUANG KONSELOR</span>
            <h3 class="fw-bold mb-1">
                Selamat datang, {{ $counselor->name }}
            </h3>
            <p class="text-td-muted mb-0">
                Kelola permintaan konsultasi dari mahasiswa yang ingin bercerita.
            </p>
        </div>

        <a href="{{ route('dashboard') }}" class="btn btn-outline-td btn-sm">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    {{-- Kartu Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="td-card td-stat-card td-stat-card--primary h-100">
                <span class="td-section-eyebrow d-block mb-2">Total Permintaan</span>
                <h2 class="fw-bold text-td-primary mb-0">{{ $totalRequests }}</h2>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="td-card td-stat-card td-stat-card--warning h-100">
                <span class="td-section-eyebrow d-block mb-2">Menunggu</span>
                <h2 class="fw-bold mb-0" style="color: var(--td-status-warning-text);">{{ $pendingRequests }}</h2>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="td-card td-stat-card td-stat-card--success h-100">
                <span class="td-section-eyebrow d-block mb-2">Diterima</span>
                <h2 class="fw-bold mb-0" style="color: var(--td-status-success-text);">{{ $acceptedRequests }}</h2>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="td-card td-stat-card td-stat-card--info h-100">
                <span class="td-section-eyebrow d-block mb-2">Selesai</span>
                <h2 class="fw-bold text-td-primary mb-0">{{ $completedRequests }}</h2>
            </div>
        </div>
    </div>

    @php
        $counselorTabs = [
            '' => ['label' => 'Semua', 'count' => $totalRequests],
            'pending_confirmation' => ['label' => 'Menunggu', 'count' => $pendingRequests],
            'accepted' => ['label' => 'Diterima', 'count' => $acceptedRequests],
            'completed' => ['label' => 'Selesai', 'count' => $completedRequests],
            'cancelled' => ['label' => 'Dibatalkan', 'count' => $cancelledRequests],
            'rejected' => ['label' => 'Ditolak', 'count' => $rejectedRequests],
        ];
    @endphp

    <div class="td-card p-3 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            {{-- Pill Filter Status --}}
            <div class="d-flex flex-wrap gap-2">
                @foreach ($counselorTabs as $statusKey => $tab)
                    <a href="{{ route('counselor.consultations.index', array_filter(['status' => $statusKey, 'q' => $keyword])) }}"
                        class="btn btn-sm rounded-pill px-3 {{ $selectedStatus === $statusKey ? 'btn-td' : 'btn-outline-td' }}">
                        {{ $tab['label'] }}
                        <span class="ms-1 opacity-75">({{ $tab['count'] }})</span>
                    </a>
                @endforeach
            </div>

            {{-- Form Pencarian Topik / Pencerita --}}
            <form action="{{ route('counselor.consultations.index') }}" method="GET"
                class="d-flex align-items-center gap-2">
                @if ($selectedStatus !== '')
                    <input type="hidden" name="status" value="{{ $selectedStatus }}">
                @endif

                <input type="text" name="q" class="form-control form-control-sm" style="min-width: 220px;"
                    placeholder="Cari topik atau nama Pencerita..." value="{{ $keyword }}">

                <button type="submit" class="btn btn-td btn-sm">Cari</button>

                @if ($keyword !== '' || $selectedStatus !== '')
                    <a href="{{ route('counselor.consultations.index') }}" class="btn btn-outline-td btn-sm">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Daftar Permintaan --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Daftar Permintaan Masuk</h5>
        <span class="text-td-muted small">{{ $consultations->total() }} data</span>
    </div>

    @if ($consultations->isEmpty())
        <div class="td-card td-journal-empty text-center py-5 px-4">
            <div class="td-empty-illustration mx-auto mb-3">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--td-primary)"
                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                </svg>
            </div>

            <span class="td-section-eyebrow">RUANG PENDAMPINGAN</span>
            <h5 class="fw-bold mt-2 mb-2">Belum Ada Permintaan</h5>
            <p class="text-td-muted small mx-auto mb-0" style="max-width: 400px;">
                Permintaan konsultasi baru dari mahasiswa akan muncul di halaman ini.
            </p>
        </div>
    @else
        <div class="row g-3">
            @foreach ($consultations as $item)
                @php
                    $status = $statusLabels[$item->status] ?? [
                        'label' => ucfirst($item->status),
                        'pill' => 'secondary',
                    ];
                @endphp

                <div class="col-md-6">
                    <article class="td-card td-consultation-card h-100">

                        {{-- Status & Tanggal Masuk --}}
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                            <span class="td-status-pill td-status-pill--{{ $status['pill'] }}">
                                {{ $status['label'] }}
                            </span>

                            <small class="text-td-muted">
                                {{ $item->created_at->format('d M Y') }}
                            </small>
                        </div>

                        {{-- Identitas Pencerita & Topik --}}
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="td-counselor-initials" style="width: 44px; height: 44px; font-size: 1rem;"
                                aria-hidden="true">
                                {{ mb_strtoupper(mb_substr($item->user?->name ?? 'P', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h5 class="fw-bold mb-0 text-truncate">
                                    {{ $item->user?->name ?? 'Pengguna' }}
                                </h5>
                                <span class="text-td-primary small fw-semibold">
                                    Topik: {{ $item->topic }}
                                </span>
                            </div>
                        </div>

                        {{-- Konteks Singkat --}}
                        <p class="text-td-muted small mb-3" style="line-height: 1.75;">
                            {{ \Illuminate\Support\Str::limit($item->context ?: 'Tidak ada catatan tambahan dari pencerita.', 130) }}
                        </p>

                        {{-- Jadwal --}}
                        <div class="td-schedule-box {{ $item->scheduled_at ? 'td-schedule-box--confirmed' : '' }} mb-4">
                            <div class="text-td-muted mb-1" style="font-size: 11px;">
                                {{ $item->scheduled_at ? 'Jadwal Konsultasi Final' : 'Waktu yang Diinginkan Pencerita' }}
                            </div>

                            <div class="small fw-semibold text-td-primary">
                                {{ ($item->scheduled_at ?? $item->preferred_at)->format('d F Y, H:i') }} WIB
                            </div>
                        </div>

                        <div class="mt-auto">
                            <a href="{{ route('counselor.consultations.show', $item->id) }}"
                                class="btn btn-outline-td btn-sm w-100">
                                Tinjau Permintaan
                                <span aria-hidden="true" class="ms-1">&rarr;</span>
                            </a>
                        </div>

                    </article>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $consultations->links() }}
        </div>
    @endif

@endsection
