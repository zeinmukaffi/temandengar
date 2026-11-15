<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Dashboard') | TemanDengar</title>

    {{-- Google Fonts: Poppins --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap 5.3.8 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    {{-- Unified TemanDengar CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body>

    {{-- Navbar Utama (Authenticated) --}}
    <nav class="navbar navbar-expand-lg td-navbar sticky-top py-3">
        <div class="container">

            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <span class="brand-mark">T</span>
                TemanDengar
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false"
                aria-label="Toggle navigasi">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">

                <ul class="navbar-nav ms-lg-4 me-auto my-3 my-lg-0 gap-lg-2">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('journals.index') }}"
                            class="nav-link {{ request()->routeIs('journals.*') ? 'active' : '' }}">
                            Journaling
                        </a>
                    </li>

                    @if (auth()->user()->role === 'pencerita')
                        <li class="nav-item">
                            <a href="{{ route('counselors.index') }}"
                                class="nav-link {{ request()->routeIs('counselors.*') ? 'active' : '' }}">
                                Konselor Sebaya
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('consultations.index') }}"
                                class="nav-link {{ request()->routeIs('consultations.*') ? 'active' : '' }}">
                                Konsultasi Saya
                            </a>
                        </li>
                    @endif
                    {{-- Navigasi Khusus Konselor Sebaya --}}
                    @if (auth()->user()->role === 'konselor')
                        <li class="nav-item">
                            <a href="{{ route('counselor.consultations.index') }}"
                                class="nav-link {{ request()->routeIs('counselor.consultations.*') ? 'active' : '' }}">
                                Permintaan Konsultasi
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('counselor.profile.edit') }}"
                                class="nav-link {{ request()->routeIs('counselor.profile.*') ? 'active' : '' }}">
                                Profil Konselor
                            </a>
                        </li>
                    @endif
                </ul>

                @php
                    $unreadCount = auth()->user()->unreadNotifications()->count();
                    $recentNotifications = auth()->user()->notifications()->latest()->take(6)->get();
                @endphp

                <div class="d-flex align-items-center gap-2">
                    {{-- Dropdown Notifikasi --}}
                    <div class="dropdown">
                        <button class="btn btn-light border rounded-3 position-relative px-2 py-2" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifikasi">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                            </svg>

                            @if ($unreadCount > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="font-size: 10px;">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </button>

                        <div class="dropdown-menu dropdown-menu-end mt-2 p-0 shadow-sm border"
                            style="width: 330px; max-width: 90vw;">
                            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                                <span class="fw-semibold small">Notifikasi</span>

                                @if ($unreadCount > 0)
                                    <form action="{{ route('notifications.read-all') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-link btn-sm p-0 text-decoration-none text-td-primary"
                                            style="font-size: 12px;">
                                            Tandai semua dibaca
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div style="max-height: 320px; overflow-y: auto;">
                                @forelse ($recentNotifications as $notif)
                                    <a href="{{ route('notifications.read', $notif->id) }}"
                                        class="dropdown-item py-2 px-3 border-bottom text-wrap {{ $notif->read_at === null ? 'bg-td-soft' : '' }}">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                            <span class="fw-semibold small text-dark">
                                                {{ data_get($notif->data, 'title', 'Pembaruan Konsultasi') }}
                                            </span>
                                            @if ($notif->read_at === null)
                                                <span class="badge rounded-pill bg-td-primary"
                                                    style="font-size: 9px;">Baru</span>
                                            @endif
                                        </div>
                                        <div class="text-td-muted" style="font-size: 12px; line-height: 1.5;">
                                            {{ data_get($notif->data, 'message') }}
                                        </div>
                                        <div class="text-td-muted mt-1" style="font-size: 11px;">
                                            {{ $notif->created_at->diffForHumans() }}
                                        </div>
                                    </a>
                                @empty
                                    <div class="px-3 py-4 text-center text-td-muted small">
                                        Belum ada notifikasi saat ini.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Profil Pengguna --}}
                    <div class="dropdown">
                        <button
                            class="btn btn-light border rounded-3 d-flex align-items-center gap-2 px-3 py-2 dropdown-toggle"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span
                                class="d-inline-flex align-items-center justify-content-center rounded-circle bg-td-primary-soft text-td-primary fw-bold"
                                style="width: 28px; height: 28px; font-size: 12px;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="small fw-medium">{{ auth()->user()->name }}</span>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end mt-2" style="min-width: 230px;">
                            <li class="px-3 py-2">
                                <div class="fw-semibold small text-dark">{{ auth()->user()->name }}</div>
                                <div class="text-td-muted" style="font-size: 12px;">{{ auth()->user()->email }}</div>
                                <span class="badge rounded-pill bg-td-soft text-td-primary mt-2"
                                    style="font-size: 11px;">
                                    Peran: {{ ucfirst(auth()->user()->role) }}
                                </span>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <a href="{{ route('home') }}" class="dropdown-item">
                                    Halaman Beranda Publik
                                </a>
                            </li>

                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="page-container">

        {{-- Notifikasi Global --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4"
                role="alert">
                <svg width="18" height="18" fill="currentColor" class="flex-shrink-0" viewBox="0 0 16 16">
                    <path
                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                </svg>
                <div class="flex-grow-1">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4"
                role="alert">
                <svg width="18" height="18" fill="currentColor" class="flex-shrink-0" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                    <path d="M7.002 11a1 1 0 1 0 2 0 1 1 0 0 0-2 0zm.93-6.412l-.81 4.542h1.76l-.81-4.542z" />
                </svg>
                <div class="flex-grow-1">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @yield('content')

    </main>

    {{-- Footer Ringkas Authenticated + Jaring Pengaman Krisis --}}
    <footer class="border-top bg-white py-4 mt-auto">
        <div class="container">
            <div class="p-3 rounded-3 mb-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2"
                style="background: var(--td-surface-muted); border: 1px solid var(--td-border);">
                <div class="small">
                    <span class="fw-semibold text-dark">Butuh bantuan profesional segera?</span>
                    <span class="text-td-muted">
                        TemanDengar merupakan ruang dukungan sebaya dan bukan layanan darurat medis/psikologis.
                        Jika kamu atau temanmu berada dalam kondisi krisis atau bahaya segera, hubungi
                        <strong class="text-td-primary">119 (ekstensi 8)</strong> atau layanan konseling kampus
                        terdekat.
                    </span>
                </div>
            </div>

            <div
                class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 text-td-muted small">
                <span>&copy; {{ date('Y') }} TemanDengar — Ruang Cerita &amp; Dukungan</span>
                <span>Privasi ceritamu adalah prioritas utama.</span>
            </div>
        </div>
    </footer>

    {{-- Global Confirmation Modal --}}
    <div class="modal fade" id="tdConfirmModal" tabindex="-1" aria-labelledby="tdConfirmModalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="tdConfirmModalTitle">
                        Konfirmasi Tindakan
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body py-3">
                    <p class="text-td-muted mb-0" id="tdConfirmModalMessage">
                        Apakah kamu yakin ingin melanjutkan tindakan ini?
                    </p>
                </div>

                <div class="modal-footer border-0 pt-0">

                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal"
                        id="tdConfirmModalCancelButton">
                        Batal
                    </button>

                    <button type="button" class="btn btn-primary rounded-3" id="tdConfirmModalButton">
                        Ya, Lanjutkan
                    </button>

                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>

    {{-- Global Confirmation Modal Engine --}}
    <script>
        (() => {
            const modalElement = document.getElementById('tdConfirmModal');
            if (!modalElement || typeof bootstrap === 'undefined') return;

            const bsModal = bootstrap.Modal.getOrCreateInstance(modalElement);
            const titleEl = document.getElementById('tdConfirmModalTitle');
            const messageEl = document.getElementById('tdConfirmModalMessage');
            const confirmBtn = document.getElementById('tdConfirmModalButton');
            const cancelBtn = document.getElementById('tdConfirmModalCancelButton');

            let pendingForm = null;
            let pendingSubmitter = null;

            const VARIANT_MAP = {
                primary: 'btn-primary',
                secondary: 'btn-secondary',
                success: 'btn-success',
                danger: 'btn-danger',
                warning: 'btn-warning'
            };

            const DEFAULT_VARIANT_CLASS = 'btn-primary';
            const ALL_VARIANT_CLASSES = Object.values(VARIANT_MAP);

            function resolveVariantClass(variantName) {
                if (typeof variantName !== 'string') return DEFAULT_VARIANT_CLASS;
                const key = variantName.toLowerCase().trim();
                return VARIANT_MAP[key] || DEFAULT_VARIANT_CLASS;
            }

            function setConfirmButtonVariant(variantName) {
                if (!confirmBtn) return;
                confirmBtn.classList.remove(...ALL_VARIANT_CLASSES);
                confirmBtn.classList.add(resolveVariantClass(variantName));
            }

            function resetModal() {
                pendingForm = null;
                pendingSubmitter = null;
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    setConfirmButtonVariant('primary');
                    confirmBtn.textContent = 'Ya, Lanjutkan';
                }
                if (cancelBtn) {
                    cancelBtn.disabled = false;
                    cancelBtn.textContent = 'Batal';
                }
                if (titleEl) titleEl.textContent = 'Konfirmasi Tindakan';
                if (messageEl) messageEl.textContent = 'Apakah kamu yakin ingin melanjutkan tindakan ini?';
            }

            document.addEventListener('submit', (event) => {
                const form = event.target;
                if (!(form instanceof HTMLFormElement)) return;

                const submitter = event.submitter;
                const confirmMsg = submitter?.dataset?.confirm ?? form.dataset.confirm;

                if (confirmMsg === undefined) return;

                if (form.dataset.tdConfirmed === 'true') {
                    delete form.dataset.tdConfirmed;
                    return;
                }

                const isValidationDisabled = form.noValidate || Boolean(submitter?.formNoValidate);
                if (!isValidationDisabled && !form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                event.preventDefault();
                event.stopImmediatePropagation();

                const title = submitter?.dataset?.confirmTitle || form.dataset.confirmTitle ||
                    'Konfirmasi Tindakan';
                const message = (typeof confirmMsg === 'string' && confirmMsg.trim() !== '' && confirmMsg !==
                        'true') ?
                    confirmMsg :
                    'Apakah kamu yakin ingin melanjutkan tindakan ini?';
                const buttonText = submitter?.dataset?.confirmButton || form.dataset.confirmButton ||
                    'Ya, Lanjutkan';
                const buttonVariant = submitter?.dataset?.confirmVariant || form.dataset.confirmVariant ||
                    'primary';
                const cancelText = submitter?.dataset?.confirmCancel || form.dataset.confirmCancel || 'Batal';

                pendingForm = form;
                pendingSubmitter = submitter;

                if (titleEl) titleEl.textContent = title;
                if (messageEl) messageEl.textContent = message;
                if (confirmBtn) {
                    confirmBtn.textContent = buttonText;
                    setConfirmButtonVariant(buttonVariant);
                }
                if (cancelBtn) cancelBtn.textContent = cancelText;

                bsModal.show();
            }, true);

            confirmBtn?.addEventListener('click', () => {
                if (!pendingForm) return;

                confirmBtn.disabled = true;
                if (cancelBtn) cancelBtn.disabled = true;

                const formToSubmit = pendingForm;
                const submitterToUse = pendingSubmitter;

                pendingForm = null;
                pendingSubmitter = null;

                bsModal.hide();

                formToSubmit.dataset.tdConfirmed = 'true';
                if (submitterToUse) {
                    formToSubmit.requestSubmit(submitterToUse);
                } else {
                    formToSubmit.requestSubmit();
                }
            });

            modalElement.addEventListener('hidden.bs.modal', () => {
                resetModal();
            });

            window.addEventListener('pageshow', () => {
                bsModal.hide();
                resetModal();
            });
        })();
    </script>

    @stack('scripts')
</body>

</html>
