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
            background-color: var(--td-bg);
            color: var(--td-text);
            min-height: 100vh;
        }

        .navbar-brand {
            color: var(--td-primary) !important;
            font-weight: 700;
            letter-spacing: -0.5px;
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

        .btn-td {
            background-color: var(--td-primary);
            color: white;
            border: 1px solid var(--td-primary);
            border-radius: 10px;
            padding: 11px 20px;
            font-weight: 500;
        }

        .btn-td:hover {
            background-color: var(--td-primary-dark);
            border-color: var(--td-primary-dark);
            color: white;
        }

        .btn-outline-td {
            border: 1px solid var(--td-primary);
            color: var(--td-primary);
            border-radius: 10px;
            padding: 11px 20px;
            font-weight: 500;
        }

        .btn-outline-td:hover {
            background-color: var(--td-primary);
            color: white;
        }

        .form-control {
            padding: 12px 14px;
            border-radius: 10px;
            border-color: #DCE5EA;
        }

        .form-control:focus {
            border-color: var(--td-accent);
            box-shadow: 0 0 0 0.2rem rgba(115, 183, 170, 0.15);
        }

        .auth-card {
            background: white;
            border: 1px solid #EAF0F3;
            border-radius: 20px;
            box-shadow: 0 12px 35px rgba(37, 63, 82, 0.06);
        }

        .text-td-primary {
            color: var(--td-primary);
        }

        .text-td-muted {
            color: var(--td-muted);
        }

        .bg-td-soft {
            background-color: #EAF3F2;
        }

        .alert {
            border-radius: 10px;
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
        {{-- Flash message --}}
        <div class="container mt-3">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
