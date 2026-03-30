<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Propietario') — SIGU</title>

    <!-- Tipografías: Sora (display) + Inter Tight (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Inter+Tight:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- SIGU — Sistema de estilos unificado -->
    <link rel="stylesheet" href="{{ asset('css/sigu-core.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/validaciones.css') }}">

    <link rel="stylesheet" href="{{ asset('css/propietario.css') }}">

    @stack('styles')
</head>

<body class="sigu-body">

    <header class="sigu-navbar">
        <div class="sigu-navbar-inner">
            
            <!-- Izquierda: Logo -->
            <div class="d-flex align-items-center sigu-brand-section">
                <a href="{{ route('propietario.dashboard') }}" class="sigu-brand text-decoration-none d-flex align-items-center gap-2">
                    <div class="sigu-brand-mark">
                        <span class="material-symbols-rounded">person_pin</span>
                    </div>
                    <div class="sigu-brand-text">
                        <span class="sigu-brand-name fw-bold text-dark fs-5 mb-0 d-block">SIGU</span>
                        <span class="sigu-brand-sub text-muted small">Propietario</span>
                    </div>
                </a>
            </div>

            <!-- Centro: Menú de Navegación -->
            <nav class="sigu-nav d-none d-lg-flex m-0 justify-content-center flex-grow-1">
                <a href="{{ route('propietario.dashboard') }}" class="sigu-nl {{ request()->routeIs('propietario.dashboard') && !request()->has('section') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">dashboard</span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('propietario.dashboard', ['section' => 'vehiculo']) }}" class="sigu-nl {{ request()->get('section') == 'vehiculo' ? 'active' : '' }}">
                    <span class="material-symbols-rounded">directions_bus</span>
                    <span>Mi Vehículo</span>
                </a>
                <a href="{{ route('propietario.dashboard', ['section' => 'asignaciones']) }}" class="sigu-nl {{ request()->get('section') == 'asignaciones' ? 'active' : '' }}">
                    <span class="material-symbols-rounded">assignment</span>
                    <span>Asignaciones</span>
                </a>
                <a href="{{ route('propietario.dashboard', ['section' => 'documentos']) }}" class="sigu-nl {{ request()->get('section') == 'documentos' ? 'active' : '' }}">
                    <span class="material-symbols-rounded">description</span>
                    <span>Documentos</span>
                </a>
                <a href="{{ route('propietario.dashboard', ['section' => 'historial']) }}" class="sigu-nl {{ request()->get('section') == 'historial' ? 'active' : '' }}">
                    <span class="material-symbols-rounded">history</span>
                    <span>Historial</span>
                </a>
                <a href="{{ route('propietario.dashboard', ['section' => 'ganancias']) }}" class="sigu-nl {{ request()->get('section') == 'ganancias' ? 'active' : '' }}">
                    <span class="material-symbols-rounded">payments</span>
                    <span>Ganancias</span>
                </a>
            </nav>

            <!-- Derecha: Perfil Usuario -->
            <div class="sigu-nb-end d-flex align-items-center justify-content-end gap-3">
                <div class="dropdown">
                    <div class="sigu-user-pill dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="sigu-user-ava">
                            <span class="material-symbols-rounded">person</span>
                        </div>
                        <div class="sigu-user-info d-none d-md-block text-start">
                            <span class="sigu-user-name d-block m-0 pb-1">{{ auth()->user()->primer_nombre }} {{ auth()->user()->primer_apellido }}</span>
                            <span class="sigu-user-role d-block m-0">Documento: {{ auth()->user()->doc_usuario }}</span>
                        </div>
                        <span class="material-symbols-rounded text-muted expand-icon">expand_more</span>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2 px-2">
                        <li>
                            <a href="{{ route('pasajero.dashboard') }}" class="dropdown-item d-flex align-items-center gap-2 py-2 rounded-2 mb-1 fw-medium">
                                <span class="material-symbols-rounded fs-5">credit_card</span>
                                Mi Tarjeta
                            </a>
                        </li>
                        <li><hr class="dropdown-divider opacity-10"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger fw-medium">
                                    <span class="material-symbols-rounded fs-5">logout</span>
                                    Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            
        </div>
    </header>

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
    <div class="toast-container position-fixed bottom-0 end-0 p-4 sigu-toast-container">
        <div id="siguToast" class="toast align-items-center text-white border-0 shadow-lg rounded-4" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="8000">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-3 py-3 px-4">
                    <div id="toastIconWrap" class="rounded-circle d-flex align-items-center justify-content-center sigu-toast-icon-wrap">
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
