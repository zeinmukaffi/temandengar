@extends('layouts.app')

@section('title', 'Pilih Konselor')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <p class="text-td-muted small mb-2">TEMAN BERCERITA</p>
            <h3 class="fw-bold mb-1">Temukan Konselor Sebaya</h3>
            <p class="text-td-muted mb-0">
                Pilih pendengar yang paling sesuai dengan kebutuhan dan kenyamananmu.
            </p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Kembali ke Dashboard</a>
    </div>

    <div class="td-card mb-4">
        <form action="{{ route('counselors.index') }}" method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="q" class="form-label small fw-semibold">Cari konselor</label>
                    <input type="search" id="q" name="q" class="form-control"
                        value="{{ $keyword }}" placeholder="Cari nama atau deskripsi konselor...">
                </div>
                <div class="col-md-4">
                    <label for="specialty" class="form-label small fw-semibold">Bidang pendampingan</label>
                    <select id="specialty" name="specialty" class="form-select">
                        <option value="">Semua bidang</option>
                        @foreach ($specialties as $specialty)
                            <option value="{{ $specialty }}" {{ $selectedSpecialty === $specialty ? 'selected' : '' }}>
                                {{ $specialty }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-td flex-grow-1">Cari</button>
                    <a href="{{ route('counselors.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Konselor tersedia</h5>
        <span class="text-td-muted small">{{ $counselors->total() }} konselor</span>
    </div>

    @if ($counselors->isEmpty())
        <div class="td-card text-center py-5">
            <div class="feature-icon mx-auto mb-3">K</div>
            <h5 class="fw-bold">Belum ada konselor yang sesuai</h5>
            <p class="text-td-muted small mb-0">
                Coba ubah kata kunci atau bidang pendampingan yang dipilih.
            </p>
        </div>
    @else
        <div class="row g-3">
            @foreach ($counselors as $counselor)
                <div class="col-md-6 col-xl-4">
                    <article class="td-card h-100 d-flex flex-column">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            @if ($counselor->photo_path)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($counselor->photo_path) }}"
                                    alt="Foto {{ $counselor->name }}" class="rounded-circle flex-shrink-0"
                                    style="width:68px;height:68px;object-fit:cover;">
                            @else
                                <div class="feature-icon rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center"
                                    style="width:68px;height:68px;">{{ mb_strtoupper(mb_substr($counselor->name, 0, 1)) }}</div>
                            @endif
                            <div class="flex-grow-1 min-w-0">
                                <h6 class="fw-bold mb-1">{{ $counselor->name }}</h6>
                                <p class="text-td-muted small mb-2">{{ $counselor->role }}</p>
                                <span class="badge bg-success-subtle text-success">Tersedia</span>
                            </div>
                        </div>

                        <p class="text-td-muted small mb-3">
                            {{ \Illuminate\Support\Str::limit($counselor->bio ?: 'Konselor sebaya TemanDengar.', 125) }}
                        </p>

                        @if (!empty($counselor->specialties))
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @foreach (array_slice($counselor->specialties, 0, 3) as $specialty)
                                    <span class="badge bg-td-soft text-td-primary">{{ $specialty }}</span>
                                @endforeach
                                @if (count($counselor->specialties) > 3)
                                    <span class="badge bg-light text-secondary">+{{ count($counselor->specialties) - 3 }}</span>
                                @endif
                            </div>
                        @endif

                        <div class="mt-auto pt-2">
                            <a href="{{ route('counselors.show', $counselor->id) }}"
                                class="btn btn-outline-td w-100">Lihat Profil</a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $counselors->links() }}
        </div>
    @endif
@endsection
