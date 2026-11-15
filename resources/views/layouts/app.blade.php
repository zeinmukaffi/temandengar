<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Dashboard') | TemanDengar</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <style>
        :root {
            /* --- Brand Core --- */
            --td-primary: #365B78;
            --td-primary-dark: #28465F;
            --td-primary-subtle: #EBF1F5;
            --td-accent: #73B7AA;
            --td-accent-dark: #5A998D;
            --td-accent-subtle: #EDF7F5;

            /* --- Surfaces & Canvas --- */
            --td-bg: #F5F8FA;
            --td-surface: #FFFFFF;
            --td-surface-muted: #F8FAFB;
            --td-border: #E8EEF1;
            --td-border-subtle: #F0F4F7;

            /* --- Typography Colors --- */
            --td-text: #263746;
            --td-text-muted: #657786;
            --td-text-subtle: #98A6B2;
            --td-muted: var(--td-text-muted); /* Backward-compatibility alias */

            /* --- Semantic Status Tokens (Accessible WCAG AA) --- */
            --td-status-warning-text: #8F5B0F;
            --td-status-warning-bg: #FEF7EC;
            --td-status-warning-border: #F7E0BA;

            --td-status-success-text: #1B735A;
            --td-status-success-bg: #EAF6F2;
            --td-status-success-border: #BCE3D6;

            --td-status-danger-text: #A83232;
            --td-status-danger-bg: #FDF2F2;
            --td-status-danger-border: #F5C6C6;

            --td-status-info-text: #265B82;
            --td-status-info-bg: #ECF4F9;
            --td-status-info-border: #C8E0EF;

            /* --- Elevation & Shadows --- */
            --td-shadow-xs: 0 1px 3px rgba(38, 55, 70, 0.05);
            --td-shadow-sm: 0 2px 8px rgba(38, 55, 70, 0.06);
            --td-shadow-md: 0 8px 24px rgba(38, 55, 70, 0.08);

            /* --- Border Radii --- */
            --td-radius-sm: 8px;
            --td-radius-md: 12px;
            --td-radius-lg: 16px;
            --td-radius-xl: 20px;
            --td-radius-pill: 9999px;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--td-bg);
            color: var(--td-text);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .app-navbar {
            background: var(--td-surface);
            border-bottom: 1px solid var(--td-border);
            box-shadow: var(--td-shadow-xs);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--td-primary) !important;
            letter-spacing: -0.3px;
        }

        .brand-mark {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            background: linear-gradient(135deg, var(--td-primary), var(--td-primary-dark));
            border-radius: var(--td-radius-md);
            font-weight: 700;
            margin-right: 9px;
            box-shadow: 0 2px 6px rgba(54, 91, 120, 0.25);
        }

        .nav-link {
            color: var(--td-text-muted);
            font-size: 14px;
            font-weight: 500;
            padding: 8px 14px;
            border-radius: var(--td-radius-sm);
            transition: color 0.15s ease, background-color 0.15s ease;
        }

        .nav-link:hover {
            color: var(--td-primary);
            background-color: var(--td-primary-subtle);
        }

        .nav-link.active {
            color: var(--td-primary);
            font-weight: 600;
            background-color: var(--td-primary-subtle);
        }

        .user-profile-btn {
            background: var(--td-surface);
            border: 1px solid var(--td-border);
            border-radius: var(--td-radius-pill);
            padding: 6px 14px 6px 8px;
            font-size: 14px;
            font-weight: 500;
            color: var(--td-text);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            max-width: 220px;
            transition: all 0.15s ease;
        }

        .user-profile-btn:hover,
        .user-profile-btn:focus {
            background: var(--td-primary-subtle);
            border-color: rgba(54, 91, 120, 0.2);
            color: var(--td-primary);
        }

        .user-name-text {
            max-width: 140px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .user-avatar-badge {
            width: 28px;
            height: 28px;
            border-radius: var(--td-radius-pill);
            background: var(--td-accent);
            color: white;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                padding-top: 12px;
                padding-bottom: 12px;
            }

            .user-profile-btn {
                max-width: 100%;
                width: 100%;
                justify-content: flex-start;
                margin-top: 12px;
            }

            .user-name-text {
                max-width: calc(100% - 50px);
            }
        }

        .page-container {
            max-width: 1180px;
            margin: auto;
            padding: 36px 24px;
        }

        /* Flexible card styles: height: auto by default; add .h-100 when grid row equality is needed */
        .td-card {
            background: var(--td-surface);
            border: 1px solid var(--td-border);
            border-radius: var(--td-radius-lg);
            padding: 24px;
            box-shadow: var(--td-shadow-xs);
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .td-card--interactive:hover {
            border-color: var(--td-accent);
            box-shadow: var(--td-shadow-sm);
        }

        .btn-td {
            background: var(--td-primary);
            border: 1px solid var(--td-primary);
            color: white;
            border-radius: var(--td-radius-sm);
            padding: 10px 18px;
            font-weight: 500;
            box-shadow: 0 1px 2px rgba(54, 91, 120, 0.15);
            transition: all 0.15s ease;
        }

        .btn-td:hover {
            background: var(--td-primary-dark);
            border-color: var(--td-primary-dark);
            color: white;
            box-shadow: 0 3px 8px rgba(54, 91, 120, 0.25);
        }

        .btn-outline-td {
            border: 1px solid var(--td-primary);
            color: var(--td-primary);
            background: transparent;
            border-radius: var(--td-radius-sm);
            padding: 10px 18px;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .btn-outline-td:hover {
            background: var(--td-primary);
            color: white;
        }

        .form-control,
        .form-select {
            border-radius: var(--td-radius-sm);
            border-color: var(--td-border);
            color: var(--td-text);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--td-accent);
            box-shadow: 0 0 0 0.2rem rgba(115, 183, 170, 0.2);
        }

        .text-td-primary {
            color: var(--td-primary) !important;
        }

        .text-td-muted {
            color: var(--td-text-muted) !important;
        }

        .bg-td-soft {
            background-color: var(--td-accent-subtle) !important;
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--td-radius-md);
            background: var(--td-accent-subtle);
            color: var(--td-primary);
            font-size: 20px;
            font-weight: 600;
        }

        .dropdown-menu {
            border: 1px solid var(--td-border);
            border-radius: var(--td-radius-md);
            box-shadow: var(--td-shadow-md);
            padding: 8px;
        }

        .dropdown-item {
            border-radius: var(--td-radius-sm);
            padding: 9px 12px;
            font-size: 14px;
            color: var(--td-text);
        }

        .dropdown-item:hover {
            background: var(--td-primary-subtle);
            color: var(--td-primary);
        }

        .alert {
            border-radius: var(--td-radius-md);
            border-width: 1px;
            font-size: 14px;
        }

        .alert-success {
            background-color: var(--td-status-success-bg);
            border-color: var(--td-status-success-border);
            color: var(--td-status-success-text);
        }

        .alert-danger {
            background-color: var(--td-status-danger-bg);
            border-color: var(--td-status-danger-border);
            color: var(--td-status-danger-text);
        }

        .alert-warning {
            background-color: var(--td-status-warning-bg);
            border-color: var(--td-status-warning-border);
            color: var(--td-status-warning-text);
        }

        .alert-info {
            background-color: var(--td-status-info-bg);
            border-color: var(--td-status-info-border);
            color: var(--td-status-info-text);
        }
    </style>

    @stack('styles')
</head>

<body>

    {{-- Navbar utama --}}
    <nav class="navbar navbar-expand-lg app-navbar py-3">
        <div class="container-fluid px-lg-5">

            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <span class="brand-mark">T</span>
                TemanDengar
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">

                <ul class="navbar-nav ms-lg-5 me-auto gap-lg-3">
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
                                class="nav-link {{ request()->routeIs('counselors.*', 'consultations.create') ? 'active' : '' }}">
                                Konselor
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->role === 'pencerita')
                        <li class="nav-item">
                            <a href="{{ route('consultations.index') }}"
                                class="nav-link {{ request()->routeIs('consultations.index', 'consultations.show') ? 'active' : '' }}">
                                Konsultasi Saya
                            </a>
                        </li>
                    @endif

                    {{-- Navigasi Konselor Sebaya --}}
                    @if (auth()->user()->role === 'konselor')
                        <li class="nav-item">
                            <a href="{{ route('counselor.consultations.index') }}"
                                class="nav-link {{ request()->routeIs('counselor.*') ? 'active' : '' }}">
                                Permintaan Konsultasi
                            </a>
                        </li>
                    @endif

                </ul>

                {{-- Profil pengguna --}}
                <div class="dropdown">
                    <button class="user-profile-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ auth()->user()->name }}">
                        <span class="user-avatar-badge flex-shrink-0">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="user-name-text text-truncate">{{ auth()->user()->name }}</span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <span class="dropdown-item-text text-secondary small">
                                {{ auth()->user()->email }}
                            </span>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
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
    </nav>

    <main class="page-container">

        {{-- Notifikasi Global --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 d-flex align-items-center gap-2" role="alert">
                <svg width="18" height="18" fill="currentColor" class="flex-shrink-0" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
                <div class="flex-grow-1">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4 d-flex align-items-center gap-2" role="alert">
                <svg width="18" height="18" fill="currentColor" class="flex-shrink-0" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M7.002 11a1 1 0 1 0 2 0 1 1 0 0 0-2 0zm.93-6.412l-.81 4.542h1.76l-.81-4.542z"/>
                </svg>
                <div class="flex-grow-1">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show mb-4 d-flex align-items-center gap-2" role="alert">
                <svg width="18" height="18" fill="currentColor" class="flex-shrink-0" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
                <div class="flex-grow-1">{{ session('warning') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show mb-4 d-flex align-items-center gap-2" role="alert">
                <svg width="18" height="18" fill="currentColor" class="flex-shrink-0" viewBox="0 0 16 16">
                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                </svg>
                <div class="flex-grow-1">{{ session('info') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @yield('content')

    </main>

    {{-- Global Confirmation Modal --}}
    <div class="modal fade" id="tdConfirmModal" tabindex="-1" aria-labelledby="tdConfirmModalTitle" aria-hidden="true">
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

            // Capture-phase listener to intercept submission before local listeners fire
            document.addEventListener('submit', (event) => {
                const form = event.target;
                if (!(form instanceof HTMLFormElement)) return;

                const submitter = event.submitter;
                const confirmMsg = submitter?.dataset?.confirm ?? form.dataset.confirm;

                // Form does not require confirmation; allow normal submission
                if (confirmMsg === undefined) return;

                // Form has already been confirmed by the user; allow execution to proceed to local listeners
                if (form.dataset.tdConfirmed === 'true') {
                    delete form.dataset.tdConfirmed;
                    return;
                }

                // Respect form.noValidate and submitter.formNoValidate
                const isValidationDisabled = form.noValidate || Boolean(submitter?.formNoValidate);
                if (!isValidationDisabled && !form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                // Intercept and prevent local submit handlers from prematurely triggering loading states
                event.preventDefault();
                event.stopImmediatePropagation();

                // Extract configuration from submitter (takes precedence) or form
                const title = submitter?.dataset?.confirmTitle || form.dataset.confirmTitle || 'Konfirmasi Tindakan';
                const message = (typeof confirmMsg === 'string' && confirmMsg.trim() !== '' && confirmMsg !== 'true')
                    ? confirmMsg
                    : 'Apakah kamu yakin ingin melanjutkan tindakan ini?';
                const buttonText = submitter?.dataset?.confirmButton || form.dataset.confirmButton || 'Ya, Lanjutkan';
                const buttonVariant = submitter?.dataset?.confirmVariant || form.dataset.confirmVariant || 'primary';
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

            // Modal confirm button handler
            confirmBtn?.addEventListener('click', () => {
                if (!pendingForm) return;

                // Prevent duplicate clicks on the modal confirmation button
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

            // Reset modal state when dismissed via cancel button, 'X', backdrop, or Escape
            modalElement.addEventListener('hidden.bs.modal', () => {
                resetModal();
            });

            // Handle browser bfcache navigation
            window.addEventListener('pageshow', () => {
                bsModal.hide();
                resetModal();
            });
        })();
    </script>

    @stack('scripts')
</body>

</html>
