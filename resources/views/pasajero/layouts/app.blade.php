<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mi cuenta') — SIGU Pasajero</title>

    <!-- Google Fonts: Sora + Inter Tight -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter+Tight:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Material Symbols Rounded -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SIGU Core + Módulo Pasajero -->
    <link rel="stylesheet" href="{{ asset('css/sigu-core.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pasajero.css') }}">

    @stack('styles')
</head>

<body>

    <!-- ── Navbar ──────────────────────────────────────────────── -->
    <nav class="sigu-nav">
        <div class="sigu-nav-inner">

            <!-- Brand -->
            <a href="{{ route('pasajero.saldo') }}" class="sigu-brand">
                <div class="sigu-brand-mark">
                    <span class="material-symbols-rounded"
                        style="font-variation-settings:var(--ms-on)">directions_transit</span>
                </div>
                <span class="sigu-brand-name">SIGU</span>
            </a>

            <!-- Links desktop -->
            <div class="sigu-nav-links" id="navLinks">
                <a href="{{ route('pasajero.saldo') }}"
                    class="sigu-nl {{ request()->routeIs('pasajero.saldo') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">credit_card</span> Inicio / Mi tarjeta
                </a>
                <a href="{{ route('pasajero.rutas.index') }}"
                    class="sigu-nl {{ request()->routeIs('pasajero.rutas.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">alt_route</span> Rutas
                </a>
                <a href="{{ route('pasajero.recargas.index') }}"
                    class="sigu-nl {{ request()->routeIs('pasajero.recargas.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">store</span> Recargas
                </a>
                <a href="{{ route('pasajero.historial.index') }}"
                    class="sigu-nl {{ request()->routeIs('pasajero.historial.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">history</span> Historial
                </a>
                <a href="{{ route('pasajero.mapa') }}"
                    class="sigu-nl {{ request()->routeIs('pasajero.mapa') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">map</span> Mapa
                </a>
            </div>

            <!-- Perfil dropdown -->
            <div class="sigu-nav-end">
                <!-- Hamburger Menu Mobile Toggle -->
                <button class="sigu-hamburger d-md-none" id="navToggle">
                    <span class="material-symbols-rounded">menu</span>
                </button>

                <div class="dropdown">
                    <button class="sigu-user-btn dropdown-toggle d-flex align-items-center gap-2" type="button"
                        id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">

                        @if (auth()->user()->foto_usuario)
                            <img src="{{ asset('storage/' . auth()->user()->foto_usuario) }}" class="sigu-avatar">
                        @else
                            <div class="sigu-avatar-init">
                                {{ strtoupper(substr(auth()->user()->primer_nombre ?? 'P', 0, 1)) }}
                            </div>
                        @endif

                        <span class="sigu-user-name d-none d-md-inline mb-0">
                            {{ auth()->user()->primer_nombre }}
                        </span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end m-0 p-0" aria-labelledby="userMenu">
                        <li>
                            <a class="dropdown-item gap-2" href="{{ route('pasajero.perfil.edit') }}">
                                <span class="material-symbols-rounded" style="font-size:1.1rem">manage_accounts</span>
                                <span>Mi perfil</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                                @csrf
                                <button type="submit" class="dropdown-item gap-2 text-danger">
                                    <span class="material-symbols-rounded" style="font-size:1.1rem">logout</span>
                                    <span>Cerrar sesión</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </nav>

    <!-- ── Drawer mobile ────────────────────────────────────────── -->
    <div class="sigu-drawer-overlay" id="drawerOverlay"></div>
    <div class="sigu-drawer" id="drawer">
        <div class="sigu-drawer-header">
            <span class="sigu-brand-name">SIGU Pasajero</span>
            <button class="sigu-drawer-close" id="drawerClose">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <div class="sigu-drawer-body">
            <a href="{{ route('pasajero.saldo') }}"
                class="sigu-drawer-link {{ request()->routeIs('pasajero.saldo') ? 'active' : '' }}">
                <span class="material-symbols-rounded">credit_card</span> Inicio / Mi tarjeta
            </a>
            <a href="{{ route('pasajero.rutas.index') }}"
                class="sigu-drawer-link {{ request()->routeIs('pasajero.rutas.*') ? 'active' : '' }}">
                <span class="material-symbols-rounded">alt_route</span> Rutas
            </a>
            <a href="{{ route('pasajero.recargas.index') }}"
                class="sigu-drawer-link {{ request()->routeIs('pasajero.recargas.*') ? 'active' : '' }}">
                <span class="material-symbols-rounded">store</span> Puntos de recarga
            </a>
            <a href="{{ route('pasajero.historial.index') }}"
                class="sigu-drawer-link {{ request()->routeIs('pasajero.historial.*') ? 'active' : '' }}">
                <span class="material-symbols-rounded">history</span> Historial
            </a>
            <a href="{{ route('pasajero.mapa') }}"
                class="sigu-drawer-link {{ request()->routeIs('pasajero.mapa') ? 'active' : '' }}">
                <span class="material-symbols-rounded">map</span> Mapa de paradas
            </a>
            <hr style="border-color:var(--border);margin:.5rem 0">
            <a href="{{ route('pasajero.perfil.edit') }}"
                class="sigu-drawer-link {{ request()->routeIs('pasajero.perfil.*') ? 'active' : '' }}">
                <span class="material-symbols-rounded">manage_accounts</span> Mi perfil
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sigu-drawer-link w-100 text-start border-0 bg-transparent"
                    style="color:var(--err)">
                    <span class="material-symbols-rounded">logout</span> Cerrar sesión
                </button>
            </form>
        </div>
    </div>

    <!-- ── Contenido principal ──────────────────────────────────── -->
    <main class="sigu-main">
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Drawer mobile toggle -->
    <script>
        const toggle = document.getElementById('navToggle');
        const drawer = document.getElementById('drawer');
        const overlay = document.getElementById('drawerOverlay');
        const closeBtn = document.getElementById('drawerClose');

        function openDrawer() {
            drawer.classList.add('open');
            overlay.classList.add('show');
        }

        function closeDrawer() {
            drawer.classList.remove('open');
            overlay.classList.remove('show');
        }

        toggle?.addEventListener('click', openDrawer);
        closeBtn?.addEventListener('click', closeDrawer);
        overlay?.addEventListener('click', closeDrawer);
    </script>

    @stack('scripts')
</body>

</html>
