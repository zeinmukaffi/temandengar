@extends('layouts.app')

@section('title', 'Profil & Ketersediaan Konselor')

@section('content')

    @php
        $currentSpecialties = old('specialties', $counselor->specialties ?? []);
        $extraSpecialties = collect($currentSpecialties)
            ->reject(fn($item) => in_array($item, $suggestedSpecialties, true))
            ->implode(', ');
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <span class="td-section-eyebrow mb-1">PENGATURAN KONSELOR</span>
            <h3 class="fw-bold mb-1">Profil &amp; Status Ketersediaan</h3>
            <p class="text-td-muted mb-0">
                Atur informasi publik yang dilihat Pencerita di direktori konselor sebaya.
            </p>
        </div>

        <a href="{{ route('counselor.consultations.index') }}" class="btn btn-outline-td btn-sm">
            &larr; Kembali ke Permintaan Konsultasi
        </a>
    </div>

    <form action="{{ route('counselor.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4 align-items-start">

            {{-- Kolom Kiri: Form Utama Profil --}}
            <div class="col-lg-8">
                <div class="td-card p-4">

                    {{-- Foto Profil --}}
                    <div class="d-flex flex-wrap align-items-center gap-4 pb-4 mb-4 border-bottom">
                        @if ($counselor->photo_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($counselor->photo_path) }}"
                                alt="Foto {{ $counselor->name }}" class="td-counselor-avatar td-counselor-avatar--lg">
                        @else
                            <div class="td-counselor-initials td-counselor-initials--lg" aria-hidden="true">
                                {{ mb_strtoupper(mb_substr($counselor->name, 0, 1)) }}
                            </div>
                        @endif

                        <div class="flex-grow-1">
                            <label for="photo" class="form-label fw-semibold mb-1">Foto Profil</label>
                            <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp"
                                class="form-control form-control-sm @error('photo') is-invalid @enderror">

                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="text-td-muted mt-1" style="font-size: 12px;">
                                Format JPG, PNG, atau WebP. Ukuran maksimal 2 MB.
                            </div>

                            @if ($counselor->photo_path)
                                <div class="form-check mt-2">
                                    <input type="checkbox" id="remove_photo" name="remove_photo" value="1"
                                        class="form-check-input">
                                    <label for="remove_photo" class="form-check-label small text-danger">
                                        Hapus foto profil saat ini
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Nama & Peran --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-7">
                            <label for="name" class="form-label fw-semibold">Nama Tampilan</label>
                            <input type="text" id="name" name="name" maxlength="120"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $counselor->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-5">
                            <label for="role" class="form-label fw-semibold">Peran / Keterangan Singkat</label>
                            <input type="text" id="role" name="role" maxlength="100"
                                class="form-control @error('role') is-invalid @enderror"
                                placeholder="Contoh: Konselor Sebaya • Mahasiswa Psikologi"
                                value="{{ old('role', $counselor->role) }}" required>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Bio / Tentang Konselor --}}
                    <div class="mb-4">
                        <label for="bio" class="form-label fw-semibold">Tentang Konselor (Bio)</label>
                        <textarea id="bio" name="bio" rows="4" maxlength="2000"
                            class="form-control @error('bio') is-invalid @enderror"
                            placeholder="Perkenalkan dirimu secara hangat, pendekatan mendengarkanmu, dan hal yang membuatmu senang menemani teman bercerita...">{{ old('bio', $counselor->bio) }}</textarea>
                        @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="text-end text-td-muted small mt-1">Maksimal 2.000 karakter.</div>
                    </div>

                    {{-- Bidang Pendampingan --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold d-block mb-2">Bidang Pendampingan</label>
                        <p class="text-td-muted small mb-3">
                            Pilih topik diskusi yang paling nyaman kamu dampingi (maksimal 10 topik).
                        </p>

                        <div class="row g-2 mb-3">
                            @foreach ($suggestedSpecialties as $item)
                                <div class="col-sm-6 col-md-4">
                                    <div class="form-check border rounded-3 px-3 py-2 bg-light">
                                        <input type="checkbox" name="specialties[]" value="{{ $item }}"
                                            id="spec_{{ \Illuminate\Support\Str::slug($item) }}"
                                            class="form-check-input ms-0 me-2"
                                            {{ in_array($item, $currentSpecialties, true) ? 'checked' : '' }}>
                                        <label for="spec_{{ \Illuminate\Support\Str::slug($item) }}"
                                            class="form-check-label small">
                                            {{ $item }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <label for="custom_specialties" class="form-label small fw-semibold">
                            Bidang Tambahan <span class="text-td-muted fw-normal">(Opsional, pisahkan dengan koma)</span>
                        </label>
                        <input type="text" id="custom_specialties" name="custom_specialties" maxlength="250"
                            class="form-control @error('custom_specialties') is-invalid @enderror"
                            placeholder="Contoh: Manajemen Stres, Organisasi Kampus"
                            value="{{ old('custom_specialties', $extraSpecialties) }}">
                        @error('custom_specialties')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Deskripsi Ketersediaan --}}
                    <div class="mb-4">
                        <label for="availability_description" class="form-label fw-semibold">
                            Informasi Ketersediaan Jadwal
                        </label>
                        <textarea id="availability_description" name="availability_description" rows="2" maxlength="1000"
                            class="form-control @error('availability_description') is-invalid @enderror"
                            placeholder="Contoh: Tersedia Senin–Jumat pukul 16.00–20.00 WIB via Google Meet, atau Sabtu pagi di kampus.">{{ old('availability_description', $counselor->availability_description) }}</textarea>
                        @error('availability_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <button type="submit" class="btn btn-td px-4">
                            Simpan Perubahan Profil
                        </button>
                    </div>

                </div>
            </div>

            {{-- Kolom Kanan: Status Ketersediaan --}}
            <div class="col-lg-4">
                <aside class="td-card p-4">
                    <span class="td-section-eyebrow mb-1">VISIBILITAS DIREKTORI</span>
                    <h5 class="fw-bold mb-2">Status Ketersediaan</h5>

                    <p class="text-td-muted small mb-3" style="line-height: 1.75;">
                        Aktifkan status tersedia agar profilmu muncul di direktori dan dapat menerima pengajuan
                        konsultasi baru dari Pencerita.
                    </p>

                    <div class="td-consent-box mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" role="switch" id="is_available" name="is_available" value="1"
                                class="form-check-input"
                                {{ old('is_available', $counselor->is_available) ? 'checked' : '' }}>
                            <label for="is_available" class="form-check-label fw-semibold">
                                Tersedia untuk Konsultasi
                            </label>
                        </div>
                        <p class="text-td-muted small mt-2 mb-0" style="font-size: 12px;">
                            Jika dinonaktifkan (misalnya saat masa ujian), profilmu disembunyikan sementara dari
                            direktori, namun sesi yang sudah berjalan tetap dapat dikelola.
                        </p>
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-2 border-top small">
                        <span class="text-td-muted">Status Saat Ini:</span>
                        @if ($counselor->is_available)
                            <span class="td-status-pill td-status-pill--success">
                                <span class="td-pill-dot"></span>
                                Aktif Menerima Sesi
                            </span>
                        @else
                            <span class="td-status-pill td-status-pill--secondary">
                                Sedang Tidak Tersedia
                            </span>
                        @endif
                    </div>
                </aside>
            </div>

        </div>
    </form>

@endsection
