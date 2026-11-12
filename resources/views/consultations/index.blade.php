<CodeBlock language="php" editable>
    @extends('layouts.app')

    @section('title', 'Konsultasi Saya')

    @section('content')

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <p class="text-td-muted small mb-2">RUANG KONSULTASI</p>
                <h3 class="fw-bold mb-1">Konsultasi Saya</h3>
                <p class="text-td-muted mb-0">
                    Pantau permintaan konsultasi yang sudah kamu ajukan.
                </p>
            </div>

            <a href="{{ route('counselors.index') }}" class="btn btn-td">
                Temukan Konselor
            </a>
        </div>

        @if ($consultations->isEmpty())

            <div class="td-card text-center py-5">
                <div class="feature-icon mx-auto mb-3">C</div>

                <h5 class="fw-bold">Belum Ada Konsultasi</h5>

                <p class="text-td-muted small mb-4">
                    Kamu belum memiliki permintaan konsultasi.
                    Temukan konselor sebaya yang sesuai dengan kebutuhanmu.
                </p>

                <a href="{{ route('counselors.index') }}" class="btn btn-td">
                    Cari Konselor
                </a>
            </div>
        @else
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

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Riwayat Permintaan</h5>
                <span class="text-td-muted small">
                    {{ $consultations->total() }} konsultasi
                </span>
            </div>

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
                                {{ $item->topic }}
                            </h5>

                            <p class="text-td-muted small mb-3">
                                Konselor: {{ $item->counselor?->name ?? 'Profil konselor tidak tersedia' }}
                            </p>

                            <p class="small mb-3">
                                {{ \Illuminate\Support\Str::limit($item->context ?: 'Tidak ada cerita tambahan.', 130) }}
                            </p>

                            <div class="text-td-muted small mb-1">
                                {{ $item->scheduled_at ? 'Jadwal Konsultasi Final' : 'Waktu yang Diinginkan' }}
                            </div>

                            <div class="fw-semibold small">
                                {{ ($item->scheduled_at ?? $item->preferred_at)->format('d M Y, H:i') }}
                            </div>

                            @if ($item->scheduled_at)
                                <span class="badge bg-success-subtle text-success mt-2">
                                    Jadwal Ditetapkan
                                </span>
                            @endif

                            <a href="{{ route('consultations.show', $item->id) }}" class="btn btn-outline-td btn-sm w-100">
                                Lihat Detail
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
