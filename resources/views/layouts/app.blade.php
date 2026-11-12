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
            --td-primary: #365B78;
            --td-primary-dark: #28465F;
            --td-accent: #73B7AA;
            --td-bg: #F5F8FA;
            --td-text: #263746;
            --td-muted: #7C8B97;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--td-bg);
            color: var(--td-text);
            min-height: 100vh;
        }

        .app-navbar {
            background: white;
            border-bottom: 1px solid #E8EEF1;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--td-primary) !important;
        }

        .brand-mark {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            background: var(--td-primary);
            border-radius: 12px;
            font-weight: 700;
            margin-right: 9px;
        }

        .nav-link {
            color: #657786;
            font-size: 14px;
            font-weight: 500;
        }

        .nav-link.active,
        .nav-link:hover {
            color: var(--td-primary);
        }

        .page-container {
            max-width: 1180px;
            margin: auto;
            padding: 36px 24px;
        }

        .td-card {
            background: white;
            border: 1px solid #E8EEF1;
            border-radius: 18px;
            padding: 24px;
            height: 100%;
        }

        .btn-td {
            background: var(--td-primary);
            border: 1px solid var(--td-primary);
            color: white;
            border-radius: 10px;
            padding: 10px 18px;
            font-weight: 500;
        }

        .btn-td:hover {
            background: var(--td-primary-dark);
            color: white;
        }

        .text-td-primary {
            color: var(--td-primary);
        }

        .text-td-muted {
            color: var(--td-muted);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: #EAF3F2;
            color: var(--td-primary);
            font-size: 21px;
            font-weight: 600;
        }

        .dropdown-menu {
            border: 1px solid #E8EEF1;
            border-radius: 12px;
            padding: 8px;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 14px;
        }

        .alert {
            border-radius: 10px;
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
                                class="nav-link {{ request()->routeIs('counselors.*') || request()->routeIs('consultations.*') ? 'active fw-semibold' : '' }}">
                                Konselor
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->role === 'pencerita')
                        <li class="nav-item">
                            <a href="{{ route('consultations.index') }}"
                                class="nav-link {{ request()->routeIs('consultations.*') ? 'active fw-semibold' : '' }}">
                                Konsultasi Saya
                            </a>
                        </li>
                    @endif

                </ul>

                {{-- Profil pengguna --}}
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        {{ auth()->user()->name }}
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

        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>

    @stack('scripts')
</body>

</html>
