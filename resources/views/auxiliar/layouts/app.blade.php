<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIGU') — Auxiliar Administrativo</title>

    <!-- Tipografías y utilidades compartidas -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Inter+Tight:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sigu-core.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}"> <!-- Reusando base css -->

    @stack('styles')
</head>

<body class="sigu-body sigu-admin-layout">

    <aside class="sigu-sidebar" id="sigu-sidebar">
        <div class="sigu-sidebar-inner">
            <a href="{{ route('auxiliar.dashboard') }}" class="sigu-brand">
                <div class="sigu-brand-mark" aria-hidden="true"><span class="material-symbols-rounded">support_agent</span></div>
                <div class="sigu-brand-text">
                    <span class="sigu-brand-name">SIGU</span>
                    <span class="sigu-brand-sub">Auxiliar Admin</span>
                </div>
            </a>

            <nav class="sigu-sidebar-nav" aria-label="Auxiliar">
                <a href="{{ route('auxiliar.dashboard') }}" class="sigu-sb-link {{ request()->routeIs('auxiliar.dashboard') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">dashboard</span></span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('auxiliar.usuarios.index') }}" class="sigu-sb-link {{ request()->routeIs('auxiliar.usuarios.*') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">group</span></span>
                    <span>Gestión Usuarios</span>
                </a>

                <a href="{{ route('auxiliar.asignaciones.index') }}" class="sigu-sb-link {{ request()->routeIs('auxiliar.asignaciones.*') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">assignment_turned_in</span></span>
                    <span>Asignaciones</span>
                </a>

                <a href="{{ route('auxiliar.documentos.index') }}" class="sigu-sb-link {{ request()->routeIs('auxiliar.documentos.*') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">folder_open</span></span>
                    <span>Documentos</span>
                </a>

                <a href="{{ route('auxiliar.reportes.index') }}" class="sigu-sb-link {{ request()->routeIs('auxiliar.reportes.*') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">analytics</span></span>
                    <span>Reportes</span>
                </a>

            </nav>

            <div class="sigu-sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sigu-btn sigu-btn-ghost">
                        <span class="material-symbols-rounded">logout</span>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <main class="sigu-main sigu-main-with-sidebar">
        @yield('content')
    </main>

    <footer class="sigu-footer">
        <span class="sigu-footer-brand">SIGU</span>
        <span class="sigu-footer-full">Sistema Integral de Seguimiento Urbano</span>
        <span class="sigu-footer-sep">·</span>
        <span>© {{ date('Y') }}</span>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

</body>
</html>
