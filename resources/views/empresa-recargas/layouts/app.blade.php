<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Empresa de Recargas — SIGU')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Inter+Tight:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sigu-core.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/btn-sigu.css') }}">

    @stack('styles')
</head>

<body class="sigu-body sigu-admin-layout">

    <aside class="sigu-sidebar" id="sigu-sidebar">
        <div class="sigu-sidebar-inner">
            <a href="{{ route('gestor-recargas.dashboard') }}" class="sigu-brand">
                <div class="sigu-brand-mark" aria-hidden="true"><span class="material-symbols-rounded">point_of_sale</span></div>
                <div class="sigu-brand-text">
                    <span class="sigu-brand-name">SIGU</span>
                    <span class="sigu-brand-sub">Empresa Recargas</span>
                </div>
            </a>

            <nav class="sigu-sidebar-nav" aria-label="Gestor Recargas">

                @php $rol = Auth::user() ? Auth::user()->id_tipo_usuario : null; @endphp

                @if($rol == 10)
                    <a href="{{ route('gestor-recargas.dashboard') }}" class="sigu-sb-link {{ request()->routeIs('gestor-recargas.dashboard') ? 'active' : '' }}">
                        <span class="sb-ico"><span class="material-symbols-rounded">dashboard</span></span>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('gestor-recargas.historial') }}" class="sigu-sb-link {{ request()->routeIs('gestor-recargas.historial') ? 'active' : '' }}">
                        <span class="sb-ico"><span class="material-symbols-rounded">history</span></span>
                        <span>Historial</span>
                    </a>
                    <a href="{{ route('gestor-recargas.usuarios.index') }}" class="sigu-sb-link {{ request()->routeIs('gestor-recargas.usuarios.*') ? 'active' : '' }}">
                        <span class="sb-ico"><span class="material-symbols-rounded">people</span></span>
                        <span>Usuarios</span>
                    </a>
                @elseif($rol == 8)
                    <a href="{{ route('gestor-recargas.recargar') }}" class="sigu-sb-link {{ request()->routeIs('gestor-recargas.recargar') ? 'active' : '' }}">
                        <span class="sb-ico"><span class="material-symbols-rounded">payments</span></span>
                        <span>Recargar Tarjeta</span>
                    </a>
                    <a href="{{ route('gestor-recargas.titularidad') }}" class="sigu-sb-link {{ request()->routeIs('gestor-recargas.titularidad') ? 'active' : '' }}">
                        <span class="sb-ico"><span class="material-symbols-rounded">swap_horiz</span></span>
                        <span>Titularidad Tarjeta</span>
                    </a>
                    <a href="{{ route('gestor-recargas.historial') }}" class="sigu-sb-link {{ request()->routeIs('gestor-recargas.historial') ? 'active' : '' }}">
                        <span class="sb-ico"><span class="material-symbols-rounded">history</span></span>
                        <span>Historial</span>
                    </a>
                @endif

                <div class="sigu-sb-divider" style="height:1px; background:rgba(255,255,255,0.1); margin:1rem 0;"></div>
                <a href="{{ route('pasajero.dashboard') }}" class="sigu-sb-link {{ request()->routeIs('pasajero.*') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">credit_card</span></span>
                    <span>Mi Tarjeta</span>
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

    <!-- Toasts (misma lógica que admin) -->
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
