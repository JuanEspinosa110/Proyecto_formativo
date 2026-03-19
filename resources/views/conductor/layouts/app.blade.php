<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Conductor') — SIGU</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Inter+Tight:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sigu-core.css') }}">
    <link rel="stylesheet" href="{{ asset('css/conductor-layout.css') }}">
    
    @stack('styles')
</head>
<body class="sigu-body sigu-conductor-body">
    <header class="sigu-navbar shadow-sm">
        <div class="sigu-navbar-inner">
            <div class="d-flex align-items-center">
                <a href="{{ route('conductor.dashboard') }}" class="text-decoration-none d-flex align-items-center gap-2">
                    <div class="sigu-logo-icon">
                        <span class="material-symbols-rounded">directions_bus</span>
                    </div>
                    <div>
                        <span class="fw-bold text-dark fs-5 d-block sigu-logo-text">SIGU</span>
                        <span class="text-muted small">Panel Conductor</span>
                    </div>
                </a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <div class="sigu-user-pill dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="sigu-user-ava">
                            <span class="material-symbols-rounded">person</span>
                        </div>
                        <div class="d-none d-md-flex flex-column justify-content-center text-start">
                            <span class="sigu-user-name">{{ auth()->user()->primer_nombre }} {{ auth()->user()->primer_apellido }}</span>
                            <span class="sigu-user-role">Conductor • {{ auth()->user()->doc_usuario }}</span>
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger fw-medium d-flex align-items-center gap-2">
                                    <span class="material-symbols-rounded">logout</span> Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <main class="sigu-main">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 p-3 d-flex align-items-center gap-3 mb-4">
                <span class="material-symbols-rounded">check_circle</span>
                <span class="fw-medium">{{ session('success') }}</span>
            </div>
        @endif
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
