<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Propietario') — SIGU</title>

    <!-- Tipografías: Sora (display) + Inter Tight (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Inter+Tight:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&display=swap"
        rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- SIGU — Sistema de estilos unificado -->
    <link rel="stylesheet" href="{{ asset('css/sigu-core.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/validacion.css') }}">

    <style>
        /* Ajustes específicos para el layout horizontal del propietario */
        .sigu-body {
            background: linear-gradient(150deg, #e5dcf8ff 0%, #8271a6ff 100%) !important;
            background-attachment: fixed !important;
            min-height: 100vh;
        }

        .sigu-navbar {
            position: sticky;
            top: 0;
            z-index: 1050;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(106, 81, 160, 0.1);
            padding: 0;
            height: 72px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.03);
        }

        .sigu-navbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            padding: 0 1.5rem;
            height: 100%;
        }

        .sigu-nav {
            display: flex;
            gap: 0.5rem;
            margin-left: 2.5rem;
            height: 100%;
        }

        .sigu-nl {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0 0.85rem;
            transition: all 0.25s ease;
            position: relative;
            height: 100%;
            letter-spacing: 0.2px;
        }

        .sigu-nl:hover {
            color: #5d548e;
        }

        .sigu-nl.active {
            color: #5d548e;
            background: rgba(93, 84, 142, 0.04);
        }

        .sigu-nl.active:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 15%;
            right: 15%;
            height: 3px;
            background: #5d548e;
            border-radius: 3px 3px 0 0;
        }

        .sigu-nl .material-symbols-rounded {
            font-size: 1.25rem;
        }

        .sigu-main {
            padding: 2rem 1.5rem;
            max-width: 1400px;
            margin: 0 auto;
            min-height: calc(100vh - 140px);
        }

        .sigu-user-pill {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 0.4rem 1rem;
            border-radius: 2rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .sigu-user-pill:hover {
            background: #f1f5f9;
        }

        .sigu-user-ava {
            width: 32px;
            height: 32px;
            background: #5d548e;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sigu-user-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: #1e293b;
        }

        .sigu-user-role {
            font-size: 0.7rem;
            color: #64748b;
            display: block;
        }

        /* Estilos móviles y Offcanvas */
        @media (max-width: 991.98px) {
            .sigu-navbar {
                height: auto;
                min-height: 64px;
                padding: 0.75rem 0;
            }
            .sigu-navbar-inner {
                padding: 0 1rem;
            }
            .sigu-main {
                padding: 1.5rem 1rem;
            }
            .sigu-user-info {
                display: none;
            }
            .sigu-user-pill {
                padding: 0.35rem;
                gap: 0;
                border: none;
                background: transparent;
            }
            .sigu-user-pill:hover {
                background: rgba(0,0,0,0.03);
            }
            .sigu-user-pill .material-symbols-rounded:last-child {
                display: none;
            }
        }

        .sigu-offcanvas {
            border-left: none;
            width: 280px !important;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
        }

        .sigu-offcanvas-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .sigu-offcanvas-body {
            padding: 1.5rem 1rem;
        }

        .sigu-mobile-nav {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .sigu-mobile-nl {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: #475569;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.85rem 1.25rem;
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .sigu-mobile-nl:hover, .sigu-mobile-nl.active {
            background: #5d548e;
            color: white;
        }

        .sigu-mobile-nl .material-symbols-rounded {
            font-size: 1.5rem;
        }
    </style>

    @stack('styles')
</head>

<body class="sigu-body">

    <header class="sigu-navbar">
        <div class="sigu-navbar-inner">
            <div class="d-flex align-items-center">
                <!-- Hamburger Toggle (Mobile Only) -->
                <button class="btn btn-link link-dark d-lg-none p-0 me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNav">
                    <span class="material-symbols-rounded fs-1">menu</span>
                </button>

                <a href="{{ route('propietario.dashboard') }}"
                    class="sigu-brand text-decoration-none d-flex align-items-center gap-2">
                    <div class="sigu-brand-mark"
                        style="background: #5d548e; color: white; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <span class="material-symbols-rounded">person_pin</span>
                    </div>
                    <div class="sigu-brand-text">
                        <span class="sigu-brand-name fw-bold text-dark fs-5 mb-0 d-block"
                            style="line-height: 1;">SIGU</span>
                        <span class="sigu-brand-sub text-muted small">Propietario</span>
                    </div>
                </a>

                <nav class="sigu-nav d-none d-lg-flex">
                    <a href="{{ route('propietario.dashboard') }}"
                        class="sigu-nl {{ request()->routeIs('propietario.dashboard') && !request()->has('section') ? 'active' : '' }}">
                        <span class="material-symbols-rounded">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('propietario.dashboard', ['section' => 'vehiculo']) }}"
                        class="sigu-nl {{ request()->get('section') == 'vehiculo' ? 'active' : '' }}">
                        <span class="material-symbols-rounded">directions_bus</span>
                        <span>Mi Vehículo</span>
                    </a>
                    <a href="{{ route('propietario.dashboard', ['section' => 'asignaciones']) }}"
                        class="sigu-nl {{ request()->get('section') == 'asignaciones' ? 'active' : '' }}">
                        <span class="material-symbols-rounded">assignment</span>
                        <span>Asignaciones</span>
                    </a>
                    <a href="{{ route('propietario.dashboard', ['section' => 'documentos']) }}"
                        class="sigu-nl {{ request()->get('section') == 'documentos' ? 'active' : '' }}">
                        <span class="material-symbols-rounded">description</span>
                        <span>Documentos</span>
                    </a>
                    <a href="{{ route('propietario.dashboard', ['section' => 'historial']) }}"
                        class="sigu-nl {{ request()->get('section') == 'historial' ? 'active' : '' }}">
                        <span class="material-symbols-rounded">history</span>
                        <span>Historial</span>
                    </a>
                    <a href="{{ route('propietario.dashboard', ['section' => 'ganancias']) }}"
                        class="sigu-nl {{ request()->get('section') == 'ganancias' ? 'active' : '' }}">
                        <span class="material-symbols-rounded">payments</span>
                        <span>Ganancias</span>
                    </a>
                </nav>
            </div>

            <div class="sigu-nb-end d-flex align-items-center gap-3">
                <div class="dropdown">
                    <div class="sigu-user-pill dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="sigu-user-ava">
                            <span class="material-symbols-rounded">person</span>
                        </div>
                        <div class="sigu-user-info d-none d-md-block">
                            <span class="sigu-user-name">{{ auth()->user()->primer_nombre }}
                                {{ auth()->user()->primer_apellido }}</span>
                            <span class="sigu-user-role">Documento: {{ auth()->user()->doc_usuario }}</span>
                        </div>
                        <span class="material-symbols-rounded fs-5 text-muted">expand_more</span>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-3 py-2 px-2"
                        style="min-width: 240px;">
                        <li>
                            <div class="px-3 py-2 mb-1 border-bottom d-md-none">
                                <span class="fw-bold d-block small">{{ auth()->user()->primer_nombre }}
                                    {{ auth()->user()->primer_apellido }}</span>
                                <span class="text-muted" style="font-size: 0.65rem;">Propietario</span>
                            </div>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-3 py-2 rounded-3"
                                href="{{ route('pasajero.dashboard') }}">
                                <span class="material-symbols-rounded text-primary fs-5">credit_card</span>
                                <div>
                                    <span class="d-block fw-semibold small">Mi Tarjeta de Pasajero</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider opacity-50">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="dropdown-item d-flex align-items-center gap-3 py-2 rounded-3 text-danger fw-medium">
                                    <span class="material-symbols-rounded fs-5">logout</span>
                                    <span>Cerrar sesión</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- Offcanvas Mobile Navigation -->
    <div class="offcanvas offcanvas-start sigu-offcanvas" tabindex="-1" id="offcanvasNav" aria-labelledby="offcanvasNavLabel">
        <div class="sigu-offcanvas-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div style="background: #5d548e; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <span class="material-symbols-rounded fs-5">person_pin</span>
                </div>
                <span class="fw-bold text-dark fs-5">SIGU</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="sigu-offcanvas-body">
            <nav class="sigu-mobile-nav">
                <a href="{{ route('propietario.dashboard') }}"
                    class="sigu-mobile-nl {{ request()->routeIs('propietario.dashboard') && !request()->has('section') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">dashboard</span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('propietario.dashboard', ['section' => 'vehiculo']) }}"
                    class="sigu-mobile-nl {{ request()->get('section') == 'vehiculo' ? 'active' : '' }}">
                    <span class="material-symbols-rounded">directions_bus</span>
                    <span>Mi Vehículo</span>
                </a>
                <a href="{{ route('propietario.dashboard', ['section' => 'asignaciones']) }}"
                    class="sigu-mobile-nl {{ request()->get('section') == 'asignaciones' ? 'active' : '' }}">
                    <span class="material-symbols-rounded">assignment</span>
                    <span>Asignaciones</span>
                </a>
                <a href="{{ route('propietario.dashboard', ['section' => 'documentos']) }}"
                    class="sigu-mobile-nl {{ request()->get('section') == 'documentos' ? 'active' : '' }}">
                    <span class="material-symbols-rounded">description</span>
                    <span>Documentos</span>
                </a>
                <a href="{{ route('propietario.dashboard', ['section' => 'historial']) }}"
                    class="sigu-mobile-nl {{ request()->get('section') == 'historial' ? 'active' : '' }}">
                    <span class="material-symbols-rounded">history</span>
                    <span>Historial</span>
                </a>
                <a href="{{ route('propietario.dashboard', ['section' => 'ganancias']) }}"
                    class="sigu-mobile-nl {{ request()->get('section') == 'ganancias' ? 'active' : '' }}">
                    <span class="material-symbols-rounded">payments</span>
                    <span>Ganancias</span>
                </a>
            </nav>

            <div class="mt-5 pt-4 border-top">
                <div class="d-flex align-items-center gap-3 px-3 mb-4">
                    <div class="sigu-user-ava" style="width: 44px; height: 44px;">
                        <span class="material-symbols-rounded fs-4">person</span>
                    </div>
                    <div>
                        <span class="fw-bold d-block text-dark">{{ auth()->user()->primer_nombre }}</span>
                        <span class="text-muted small">Propietario</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100 rounded-pill py-2 fw-bold d-flex align-items-center justify-content-center gap-2">
                        <span class="material-symbols-rounded fs-5">logout</span>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>

    <main class="sigu-main">
        @yield('content')
    </main>

    <footer class="sigu-footer border-top py-4 bg-white">
        <div class="container-fluid max-width-1400 px-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    <span class="sigu-footer-brand fw-bold text-dark">SIGU</span>
                    <span class="sigu-footer-full text-muted ms-2">Sistema Integral de Seguimiento Urbano</span>
                </div>
                <div class="text-muted small">
                    <span>© {{ date('Y') }} — Todos los derechos reservados.</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Toast UI -->
    <div class="toast-container position-fixed bottom-0 end-0 p-4" style="z-index: 1085;">
        <div id="siguToast" class="toast align-items-center text-white border-0 shadow-lg rounded-4" role="alert"
            aria-live="assertive" aria-atomic="true" data-bs-delay="8000">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-3 py-3 px-4">
                    <div id="toastIconWrap" class="rounded-circle d-flex align-items-center justify-content-center"
                        style="width:32px; height:32px; background: rgba(255,255,255,0.2)">
                        <span id="toastIcon" class="material-symbols-rounded fs-5"></span>
                    </div>
                    <div id="toastMessage" class="fw-medium"></div>
                </div>
                <button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function showToast(message, type = 'success') {
            const toastEl = document.getElementById('siguToast');
            const toastBody = document.getElementById('toastMessage');
            const toastIcon = document.getElementById('toastIcon');

            toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info');

            if (type === 'success') {
                toastEl.classList.add('bg-success');
                toastIcon.textContent = 'check_circle';
            } else if (type === 'error') {
                toastEl.classList.add('bg-danger');
                toastIcon.textContent = 'error';
            } else {
                toastEl.classList.add('bg-info');
                toastIcon.textContent = 'info';
            }

            toastBody.textContent = message;
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }

        @if(session('success'))
            document.addEventListener('DOMContentLoaded', () => showToast("{{ session('success') }}", 'success'));
        @endif
        @if(session('error'))
            document.addEventListener('DOMContentLoaded', () => showToast("{{ session('error') }}", 'error'));
        @endif
    </script>

    @stack('scripts')
</body>

</html>