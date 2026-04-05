<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Control de Tiempo') — SIGU</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Inter+Tight:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sigu-core.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    <style>
        /* ── Controlador de Tiempo — acento morado SIGU ── */
        :root {
            --ct-accent:      #5e548e;
            --ct-accent-dark: #4a4070;
            --ct-accent-light:#ede9f8;
            --ct-accent-mid:  #9b89c4;
        }

        /* Sidebar brand */
        .sigu-brand-mark { background: var(--ct-accent) !important; }

        /* Link activo */
        .sigu-sb-link.active,
        .sigu-sb-link:hover {
            background: var(--ct-accent-light) !important;
            color: var(--ct-accent) !important;
        }
        .sigu-sb-link.active .material-symbols-rounded,
        .sigu-sb-link:hover  .material-symbols-rounded {
            color: var(--ct-accent) !important;
        }

        /* Botones primarios de la UI */
        .btn-ct {
            background: var(--ct-accent);
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
            font-weight: 600;
            transition: background .2s;
        }
        .btn-ct:hover { background: var(--ct-accent-dark); color: #fff; }

        /* Badge de estado activo */
        .badge-ct {
            background: var(--ct-accent-light);
            color: var(--ct-accent);
            border: 1px solid var(--ct-accent-mid);
            font-weight: 600;
            border-radius: 999px;
            padding: 0.2em 0.8em;
        }

        /* KPI cards */
        .ct-kpi { border-left: 4px solid var(--ct-accent); }
        .ct-kpi-icon { color: var(--ct-accent); }

        /* Timeline Styles */
        .ct-timeline {
            border-left: 2px dashed #eee;
            margin-left: 10px;
            padding-left: 20px;
        }
    </style>

    @stack('styles')
</head>

<body class="sigu-body sigu-admin-layout">

    <aside class="sigu-sidebar" id="sigu-sidebar">
        <div class="sigu-sidebar-inner">
            <a href="{{ route('controlador-tiempo.dashboard') }}" class="sigu-brand">
                <div class="sigu-brand-mark" aria-hidden="true">
                    <span class="material-symbols-rounded">timer</span>
                </div>
                <div class="sigu-brand-text">
                    <span class="sigu-brand-name">SIGU</span>
                    <span class="sigu-brand-sub">Control Tiempo</span>
                </div>
            </a>

            <nav class="sigu-sidebar-nav" aria-label="Controlador de Tiempo">

                <a href="{{ route('controlador-tiempo.dashboard') }}"
                   class="sigu-sb-link {{ request()->routeIs('controlador-tiempo.dashboard') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">dashboard</span></span>
                    <span>Inicio</span>
                </a>

                <div class="sigu-sb-divider"></div>
                <p class="sigu-sb-section">Operaciones</p>

                <a href="{{ route('controlador-tiempo.despacho.index') }}"
                   class="sigu-sb-link {{ request()->routeIs('controlador-tiempo.despacho*') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">directions_bus</span></span>
                    <span>Despacho</span>
                </a>

                <a href="{{ route('controlador-tiempo.monitoreo.index') }}"
                   class="sigu-sb-link {{ request()->routeIs('controlador-tiempo.monitoreo*') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">radar</span></span>
                    <span>Monitoreo en Vivo</span>
                </a>

                <a href="{{ route('controlador-tiempo.verificacion.scanner') }}"
                   class="sigu-sb-link {{ request()->routeIs('controlador-tiempo.verificacion*') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">qr_code_scanner</span></span>
                    <span>Escaneo de Bus</span>
                </a>

                <a href="{{ route('controlador-tiempo.planillas.index') }}"
                   class="sigu-sb-link {{ request()->routeIs('controlador-tiempo.planillas*') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">assignment</span></span>
                    <span>Planillas</span>
                </a>
                <div class="sigu-sb-divider"></div>
                <a href="{{ route('pasajero.dashboard') }}"
                   class="sigu-sb-link {{ request()->routeIs('pasajero.*') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">credit_card</span></span>
                    <span style="font-weight:700;">Mi Tarjeta</span>
                </a>
            </nav>

            <div class="sigu-sidebar-footer">
                <div class="px-3 pb-2 small text-muted">
                    <span class="material-symbols-rounded align-middle me-1" style="font-size:1rem;">person</span>
                    {{ Auth::user()->primer_nombre ?? '' }} {{ Auth::user()->primer_apellido ?? '' }}
                </div>
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
        <span class="sigu-footer-full">Sistema Integral de Seguimiento Urbano — Control de Tiempo</span>
        <span class="sigu-footer-sep">·</span>
        <span>© {{ date('Y') }}</span>
    </footer>

    <!-- Toast notifications -->
    <div class="toast-container position-fixed bottom-0 end-0 p-4" style="z-index: 1085;">
        <div id="siguToast" class="toast align-items-center text-white border-0 shadow-lg rounded-4" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-3 py-3 px-4">
                    <div id="toastIconWrap" class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px; height:32px; background: rgba(255,255,255,0.2)">
                        <span id="toastIcon" class="material-symbols-rounded fs-5"></span>
                    </div>
                    <div id="toastMessage" class="fw-medium"></div>
                </div>
                <button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function showToast(message, type = 'success') {
            const toastEl   = document.getElementById('siguToast');
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
            new bootstrap.Toast(toastEl).show();
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
