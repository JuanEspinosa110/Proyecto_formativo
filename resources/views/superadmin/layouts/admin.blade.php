<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Super Admin')</title>
    <title>@yield('title', 'SIGU') — Sistema Integral de Seguimiento Urbano</title>

    <!-- Tipografías: Sora (display) + Inter Tight (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Inter+Tight:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">

    <title>@yield('title', 'SIGU') — Sistema Integral de Seguimiento Urbano</title>

    <!-- Tipografías: Sora (display) + Inter Tight (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Inter+Tight:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/superadmin_congif.css') }}">


    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Font Awesome (compatibilidad con módulos existentes) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- SIGU — Sistema de estilos unificado -->
    <link rel="stylesheet" href="{{ asset('css/sigu-core.css') }}">
    <link rel="stylesheet" href="{{ asset('css/empresas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/perfil-seguridad.css') }}">

    @stack('styles')
</head>

<body class="sigu-body">

    <!-- ╔══════════════════════════════════════════════════════╗
     ║  NAVBAR SIGU                                         ║
     ╚══════════════════════════════════════════════════════╝ -->
    <header class="sigu-navbar" id="sigu-navbar">
        <div class="sigu-navbar-inner">

            <!-- ▸ BRAND -->
            <a href="{{ route('superadmin.dashboard') }}" class="sigu-brand" aria-label="SIGU inicio">
                <div class="sigu-brand-mark" aria-hidden="true">
                    <span class="material-symbols-rounded">route</span>
                </div>
                <div class="sigu-brand-text">
                    <span class="sigu-brand-name">SIGU</span>
                    <span class="sigu-brand-sub">Seguimiento Urbano</span>
                </div>
            </a>

            <!-- ▸ NAV LINKS desktop -->
            <nav class="sigu-nav" aria-label="Principal">
                <a href="{{ route('superadmin.dashboard') }}"
                    class="sigu-nl {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">dashboard</span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('superadmin.empresas.index') }}"
                    class="sigu-nl {{ request()->routeIs('superadmin.empresas.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">business</span>
                    <span>Empresas</span>
                </a>
                <a href="{{ route('superadmin.licencias.index') }}"
                    class="sigu-nl {{ request()->routeIs('superadmin.licencias.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">verified</span>
                    <span>Licencias</span>
                </a>
                <a href="{{ route('superadmin.planes.index') }}"
                    class="sigu-nl {{ request()->routeIs('superadmin.planes.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">layers</span>
                    <span>Planes</span>
                </a>
                <a href="{{ route('superadmin.rutas.index') }}"
                    class="sigu-nl {{ request()->routeIs('superadmin.rutas.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">map</span>
                    <span>Rutas</span>
                </a>
            </nav>
            <div class="dropdown">
            <a href="#"
            class="sigu-nl dropdown-toggle 
            {{ request()->routeIs('superadmin.configuracion.*') ? 'active' : '' }}"
            data-bs-toggle="dropdown"
            aria-expanded="false">

                <span class="material-symbols-rounded">settings</span>
                <span>Configuración</span>
            </a>

            <ul class="dropdown-menu">

                {{-- CIUDADES --}}
                <li>
                    <a class="dropdown-item"
                    href="{{ route('superadmin.configuracion.ciudades.index') }}">
                        <i class="bi bi-geo-alt"></i> Ciudades
                    </a>
                </li>

                {{-- TIPOS DE EMPRESA --}}
                <li>
                    <a class="dropdown-item"
                    href="{{ route('superadmin.configuracion.tipo-empresa.index') }}">
                        <i class="bi bi-building"></i> Tipos de Empresa
                    </a>
                </li>

                {{-- TIPOS DE USUARIO --}}
                <li>
                    <a class="dropdown-item"
                    href="{{ route('superadmin.configuracion.tipo-usuario.index') }}">
                        <i class="bi bi-people"></i> Tipos de Usuario
                    </a>
                </li>

                {{-- ESTADOS --}}
                <li>
                    <a class="dropdown-item"
                    href="{{ route('superadmin.configuracion.estados.index') }}">
                        <i class="bi bi-toggle-on"></i> Estados
                    </a>
                </li>
                <li>
                    <a class="dropdown-item"
                    href="{{ route('superadmin.configuracion.tipo-mantenimiento.index') }}">
                        <i class="bi bi-tools"></i> Tipo de Mantenimiento
                    </a>
                </li>
                <li>
                    <a class="dropdown-item"
                    href="{{ route('superadmin.configuracion.tipo-documento.index') }}">
                        <i class="bi bi-file-earmark-text"></i> Tipo de Documento
                    </a>
                </li>
                <li>
                    <a class="dropdown-item"
                    href="{{ route('superadmin.configuracion.tipo-asignacion.index') }}">
                        <i class="bi bi-journal-check"></i> Tipo de Asignación
                    </a>
                </li>
            </ul>
        </div>

            {{-- FOOTER --}}
            <div class="sa-dash-sidebar-footer">
            <!-- ▸ ACCIONES DERECHA -->
            <div class="sigu-nb-end">

                <!-- Dropdown usuario -->
                <div class="dropdown">
                    <button class="sigu-user-pill dropdown-toggle"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <div class="sigu-user-ava">
                            <span class="material-symbols-rounded">person</span>
                        </div>
                        <div class="sigu-user-info d-none d-md-flex">
                            <span class="sigu-user-name">Super Admin</span>
                            <span class="sigu-user-role">Administrador</span>
                        </div>
                        <span class="material-symbols-rounded sigu-caret d-none d-md-inline">expand_more</span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end sigu-drop">
                        <li class="sigu-drop-head">
                            <span class="material-symbols-rounded">manage_accounts</span>
                            Mi cuenta
                        </li>
                        <li>
                            <a class="dropdown-item sigu-di" href="{{ route('superadmin.perfil.index') }}">
                                <span class="material-symbols-rounded">badge</span>
                                Perfil y Seguridad
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider sigu-drop-sep">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item sigu-di sigu-di-danger">
                                    <span class="material-symbols-rounded">logout</span>
                                    Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

                <!-- Hamburger mobile -->
                <button class="sigu-burger d-lg-none" id="sigu-burger" aria-expanded="false" aria-label="Menú">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>

        <!-- Mobile drawer -->
        <div class="sigu-drawer" id="sigu-drawer">
            <a href="{{ route('superadmin.dashboard') }}" class="sigu-dl {{ request()->routeIs('superadmin.dashboard')    ? 'active' : '' }}"><span class="material-symbols-rounded">dashboard</span>Dashboard</a>
            <a href="{{ route('superadmin.empresas.index') }}" class="sigu-dl {{ request()->routeIs('superadmin.empresas.*') ? 'active' : '' }}"><span class="material-symbols-rounded">business</span>Empresas</a>
            <a href="{{ route('superadmin.licencias.index') }}" class="sigu-dl {{ request()->routeIs('superadmin.licencias.*') ? 'active' : '' }}"><span class="material-symbols-rounded">verified</span>Licencias</a>
            <a href="{{ route('superadmin.planes.index') }}" class="sigu-dl {{ request()->routeIs('superadmin.planes.*')   ? 'active' : '' }}"><span class="material-symbols-rounded">layers</span>Planes</a>
            <a href="{{ route('superadmin.rutas.index') }}" class="sigu-dl {{ request()->routeIs('superadmin.rutas.*')   ? 'active' : '' }}"><span class="material-symbols-rounded">map</span>Rutas</a>
            <a href="{{ route('superadmin.perfil.index') }}" class="sigu-dl {{ request()->routeIs('superadmin.perfil.*')   ? 'active' : '' }}"><span class="material-symbols-rounded">badge</span>Perfil</a>
            <div class="sigu-drawer-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sigu-drawer-logout">
                        <span class="material-symbols-rounded">logout</span>Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- ╔══════════════════════════════════════════════════════╗
     ║  CONTENIDO PRINCIPAL                                 ║
     ╚══════════════════════════════════════════════════════╝ -->
    <main class="sigu-main">
        @yield('content')
    </main>

    <!-- ╔══════════════════════════════════════════════════════╗
     ║  FOOTER                                              ║
     ╚══════════════════════════════════════════════════════╝ -->
    <footer class="sigu-footer">
        <span class="sigu-footer-brand">SIGU</span>
        <span class="sigu-footer-full">Sistema Integral de Seguimiento Urbano</span>
        <span class="sigu-footer-sep">·</span>
        <span>© {{ date('Y') }}</span>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        (function() {
            // Burger / drawer toggle
            const burger = document.getElementById('sigu-burger');
            const drawer = document.getElementById('sigu-drawer');
            if (burger && drawer) {
                burger.addEventListener('click', () => {
                    const isOpen = drawer.classList.toggle('open');
                    burger.classList.toggle('open', isOpen);
                    burger.setAttribute('aria-expanded', isOpen);
                });
                document.addEventListener('click', e => {
                    if (!burger.contains(e.target) && !drawer.contains(e.target)) {
                        drawer.classList.remove('open');
                        burger.classList.remove('open');
                        burger.setAttribute('aria-expanded', false);
                    }
                });
            }

            // Scroll shadow
            const navbar = document.getElementById('sigu-navbar');
            if (navbar) {
                const update = () => navbar.classList.toggle('scrolled', window.scrollY > 4);
                window.addEventListener('scroll', update, {
                    passive: true
                });
                update();
            }

            // Auto-dismiss alerts
            setTimeout(() => {
                document.querySelectorAll('.alert-dismissible').forEach(el => {
                    bootstrap.Alert.getOrCreateInstance(el)?.close();
                });
            }, 5000);
        })();
    </script>

    @stack('scripts')
</body>

</html>