@extends('layouts.app')

@section('title', 'Konsultasi Saya')

@section('content')

    @php
        $statusTabs = [
            '' => ['label' => 'Semua', 'count' => $statusCounts['all']],
            'pending_confirmation' => ['label' => 'Menunggu', 'count' => $statusCounts['pending_confirmation']],
            'accepted' => ['label' => 'Diterima', 'count' => $statusCounts['accepted']],
            'completed' => ['label' => 'Selesai', 'count' => $statusCounts['completed']],
            'cancelled' => ['label' => 'Dibatalkan', 'count' => $statusCounts['cancelled']],
            'rejected' => ['label' => 'Ditolak', 'count' => $statusCounts['rejected']],
        ];
    @endphp

    <div class="d-flex flex-wrap gap-2 mb-4">
        @foreach ($statusTabs as $statusKey => $tab)
            <a href="{{ $statusKey === '' ? route('consultations.index') : route('consultations.index', ['status' => $statusKey]) }}"
                class="btn btn-sm rounded-pill px-3 {{ $selectedStatus === $statusKey ? 'btn-td' : 'btn-outline-td' }}">
                {{ $tab['label'] }}
                <span class="ms-1 opacity-75">({{ $tab['count'] }})</span>
            </a>
        @endforeach
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <span class="td-section-eyebrow mb-1">RUANG KONSULTASI</span>
            <h3 class="fw-bold mb-1">Konsultasi Saya</h3>
            <p class="text-td-muted mb-0">
                Pantau status dan jadwal permintaan konsultasi yang sudah kamu ajukan.
            </p>
        </div>

        <a href="{{ route('counselors.index') }}" class="btn btn-td">
            + Temukan Konselor
        </a>
    </div>

    @if ($consultations->isEmpty())
        <div class="td-card td-journal-empty text-center py-5 px-4">
            <div class="td-empty-illustration mx-auto mb-3">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--td-primary)"
                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path
                        d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8Z" />
                    <path d="M8 11h.01M12 11h.01M16 11h.01" />
                </svg>
            </div>

            <span class="td-section-eyebrow">DUKUNGAN SEBAYA</span>
            <h5 class="fw-bold mt-2 mb-2">Belum Ada Konsultasi</h5>

            <p class="text-td-muted small mx-auto mb-4" style="max-width: 420px; line-height: 1.8;">
                Kamu belum memiliki permintaan konsultasi. Temukan konselor sebaya yang sesuai
                dengan kebutuhanmu untuk mulai berdiskusi.
            </p>

            <a href="{{ route('counselors.index') }}" class="btn btn-td">
                Cari Konselor Sebaya
            </a>
        </div>
    @else
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

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Riwayat Permintaan</h5>
            <span class="text-td-muted small">{{ $consultations->total() }} konsultasi</span>
        </div>

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

                        {{-- Header Status & Tanggal Pengajuan --}}
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                            <span class="td-status-pill td-status-pill--{{ $status['pill'] }}">
                                {{ $status['label'] }}
                            </span>

                            <small class="text-td-muted">
                                Diajukan {{ $item->created_at->format('d M Y') }}
                            </small>
                        </div>

                        {{-- Topik & Nama Konselor --}}
                        <h5 class="fw-bold mb-1">
                            {{ $item->topic }}
                        </h5>

                        <p class="text-td-muted small mb-3">
                            Konselor: <span
                                class="fw-medium text-dark">{{ $item->counselor?->name ?? 'Profil konselor tidak tersedia' }}</span>
                        </p>

                        {{-- Ringkasan Konteks --}}
                        <p class="text-td-muted small mb-3" style="line-height: 1.75;">
                            {{ \Illuminate\Support\Str::limit($item->context ?: 'Tidak ada catatan tambahan yang disertakan.', 130) }}
                        </p>

                        {{-- Informasi Jadwal --}}
                        <div class="td-schedule-box {{ $item->scheduled_at ? 'td-schedule-box--confirmed' : '' }} mb-4">
                            <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                                <span class="text-td-muted" style="font-size: 11px;">
                                    {{ $item->scheduled_at ? 'Jadwal Konsultasi Final' : 'Waktu yang Diinginkan' }}
                                </span>

                                @if ($item->scheduled_at)
                                    <span class="badge bg-success-subtle text-success" style="font-size: 10px;">
                                        Terkonfirmasi
                                    </span>
                                @endif
                            </div>

                            <div class="fw-semibold small text-td-primary">
                                {{ ($item->scheduled_at ?? $item->preferred_at)->format('d F Y, H:i') }} WIB
                            </div>
                        </div>

                        {{-- Tombol Detail --}}
                        <div class="mt-auto">
                            <a href="{{ route('consultations.show', $item->id) }}" class="btn btn-outline-td btn-sm w-100">
                                Lihat Detail Konsultasi
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
