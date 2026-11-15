<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'TemanDengar')</title>

    {{-- Google Fonts: Poppins --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap 5.3.8 --}}
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

            /* --- Semantic Status Tokens --- */
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
            background-color: var(--td-bg);
            color: var(--td-text);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .navbar-brand {
            color: var(--td-primary) !important;
            font-weight: 700;
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

        .btn-td {
            background-color: var(--td-primary);
            color: white;
            border: 1px solid var(--td-primary);
            border-radius: var(--td-radius-sm);
            padding: 10px 18px;
            font-weight: 500;
            box-shadow: 0 1px 2px rgba(54, 91, 120, 0.15);
            transition: all 0.15s ease;
        }

        .btn-td:hover {
            background-color: var(--td-primary-dark);
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
            background-color: var(--td-primary);
            color: white;
        }

        .form-control,
        .form-select {
            padding: 12px 14px;
            border-radius: var(--td-radius-sm);
            border-color: var(--td-border);
            color: var(--td-text);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--td-accent);
            box-shadow: 0 0 0 0.2rem rgba(115, 183, 170, 0.2);
        }

        .auth-card {
            background: var(--td-surface);
            border: 1px solid var(--td-border);
            border-radius: var(--td-radius-xl);
            box-shadow: var(--td-shadow-md);
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

    {{-- Navbar publik --}}
    <nav class="navbar navbar-expand-lg bg-white border-bottom py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <span class="brand-mark">T</span>
                TemanDengar
            </a>

            <div class="d-flex gap-2">
                <a href="{{ route('login') }}" class="btn btn-outline-td btn-sm">
                    Masuk
                </a>

                <a href="{{ route('register') }}" class="btn btn-td btn-sm">
                    Daftar
                </a>
            </div>
        </div>
    </nav>

    <main>
        {{-- Notifikasi Global --}}
        <div class="container mt-3">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <svg width="18" height="18" fill="currentColor" class="flex-shrink-0" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>
                    <div class="flex-grow-1">{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <svg width="18" height="18" fill="currentColor" class="flex-shrink-0" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path d="M7.002 11a1 1 0 1 0 2 0 1 1 0 0 0-2 0zm.93-6.412l-.81 4.542h1.76l-.81-4.542z"/>
                    </svg>
                    <div class="flex-grow-1">{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>

    @stack('scripts')
</body>

</html>
