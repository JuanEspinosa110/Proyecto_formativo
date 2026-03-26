<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIGU') — Sistema Integral de Seguimiento Urbano</title>

    <!-- Tipografías y utilidades compartidas -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Inter+Tight:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sigu-core.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/validacion.css') }}">

    @stack('styles')
</head>

<body class="sigu-body sigu-admin-layout">

    <aside class="sigu-sidebar" id="sigu-sidebar">
        <div class="sigu-sidebar-inner">
            <a href="{{ route('admin.dashboard') }}" class="sigu-brand">
                <div class="sigu-brand-mark" aria-hidden="true"><span class="material-symbols-rounded">route</span></div>
                <div class="sigu-brand-text">
                    <span class="sigu-brand-name">SIGU</span>
                    <span class="sigu-brand-sub">{{ Auth::user()->id_tipo_usuario == 1 ? 'Administración' : 'Auxiliar' }}</span>
                </div>
            </a>

            @php
            $routePrefix = Auth::user()->id_tipo_usuario == 1 ? 'admin' : 'empresa';
            @endphp

            <nav class="sigu-sidebar-nav" aria-label="Administrador">
                <a href="{{ route($routePrefix . '.dashboard') }}" class="sigu-sb-link {{ request()->routeIs($routePrefix . '.dashboard') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">dashboard</span></span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route($routePrefix . '.usuarios.index') }}" class="sigu-sb-link {{ request()->routeIs($routePrefix . '.usuarios.*') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">people</span></span>
                    <span>Usuarios</span>
                </a>

                <a href="{{ route($routePrefix . '.documentos.index') }}" class="sigu-sb-link {{ request()->routeIs($routePrefix . '.documentos.*') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">description</span></span>
                    <span>Docs. Vehículos</span>
                </a>

                <a href="{{ route($routePrefix . '.buses.index') }}" class="sigu-sb-link {{ request()->routeIs($routePrefix . '.buses.*') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">directions_bus</span></span>
                    <span>Buses</span>
                </a>

                <a href="{{ route($routePrefix . '.asignaciones.index') }}" class="sigu-sb-link {{ request()->routeIs($routePrefix . '.asignaciones.*') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">assignment</span></span>
                    <span>Asignaciones</span>
                </a>

                <a href="{{ route('empresa.reportes.index') }}" class="sigu-sb-link {{ request()->routeIs('empresa.reportes.*') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">analytics</span></span>
                    <span>Reportes</span>
                </a>

                @if(Auth::user()->id_tipo_usuario == 1)
                <a href="{{ route('admin.rutas.index') }}" class="sigu-sb-link {{ request()->routeIs('admin.rutas.*') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">map</span></span>
                    <span>Rutas</span>
                </a>

                <div class="sigu-sb-divider" style="height:1px; background:rgba(255,255,255,0.1); margin:1rem 0;"></div>
                <p class="small text-uppercase px-4 mb-2" style="font-size:10px; opacity:0.6; letter-spacing:1px;">Mantenimiento</p>

                <a href="{{ route('admin.mantenimiento.reportes') }}" class="sigu-sb-link {{ request()->routeIs('admin.mantenimiento.reportes') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">notification_important</span></span>
                    <span>Reportes de Fallas</span>
                </a>

                <a href="{{ route('admin.mantenimiento.index') }}" class="sigu-sb-link {{ request()->routeIs('admin.mantenimiento.*') && !request()->routeIs('admin.mantenimiento.reportes') ? 'active' : '' }}">
                    <span class="sb-ico"><span class="material-symbols-rounded">build</span></span>
                    <span>Mantenimiento</span>
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
            @endif
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

    <div class="toast-container position-fixed bottom-0 end-0 p-4" style="z-index: 1085;">
        <div id="siguToast" class="toast align-items-center text-white border-0 shadow-lg rounded-4" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="8000">
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

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


    <!-- ─── Modal de confirmación personalizado ─────────────────────── -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-body text-center p-4">
                    <span class="material-symbols-rounded mb-3 d-block" id="confirmIcon"
                          style="font-size:3rem; color:#f6820c;">help</span>
                    <h6 class="fw-bold mb-1" id="confirmTitle">¿Estás seguro?</h6>
                    <p class="text-muted mb-0 small" id="confirmMessage">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2 pb-4 pt-0">
                    <button type="button" class="btn btn-sm"
                            style="border:1.5px solid #cbd5e0; color:#4a5568; border-radius:0.5rem; padding:0.35rem 1.2rem;"
                            data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="confirmOkBtn" class="btn btn-sm"
                            style="background:var(--p); color:#fff; border:none; border-radius:0.5rem; padding:0.35rem 1.2rem;">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Delegación global para botones con data-confirm-form
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-confirm-form]');
            if (!btn) return;
            e.preventDefault();
            const formId = btn.dataset.confirmForm;
            const title   = btn.dataset.confirmTitle  || '¿Estás seguro?';
            const msg     = btn.dataset.confirmMsg    || 'Esta acción no se puede deshacer.';
            document.getElementById('confirmTitle').textContent   = title;
            document.getElementById('confirmMessage').textContent = msg;
            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            document.getElementById('confirmOkBtn').onclick = function() {
                modal.hide();
                document.getElementById(formId).submit();
            };
            modal.show();
        });
    </script>

    @stack('scripts')
</body>

</html>
