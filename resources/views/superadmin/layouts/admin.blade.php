<!DOCTYPE html>
<html lang="es" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Super Admin')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

    <!-- CSS propio -->
    <link rel="stylesheet" href="{{ asset('../css/super-admin.css') }}">
</head>

<body class="sa-dash-body">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@stack('scripts')


<div class="sa-dash-layout">

    {{-- SIDEBAR --}}
    <aside class="sa-dash-sidebar">

        {{-- BRAND --}}
        <div class="sa-dash-brand">
            <span class="material-symbols-outlined sa-dash-brand-icon">
                admin_panel_settings
            </span>
            <div>
                <h1>Admin Panel</h1>
                <small>Public Transport System</small>
            </div>
        </div>

        {{-- NAV --}}
        <nav class="sa-dash-nav">

            <a class="sa-dash-nav-link" href="{{ route('superadmin.dashboard') }}">
    <span class="material-symbols-outlined">dashboard</span>
    Dashboard
</a>

<a class="sa-dash-nav-link" href="{{ route('superadmin.roles.index') }}">
    <span class="material-symbols-outlined">shield_person</span>
    Roles y permisos
</a>

<a class="sa-dash-nav-link" href="{{ route('superadmin.usuarios.index') }}">
    <span class="material-symbols-outlined">group</span>
    Usuarios
</a>

<a class="sa-dash-nav-link" href="{{ route('superadmin.empresas.index') }}">
    <span class="material-symbols-outlined">business</span>
    Empresas
</a>

<a class="sa-dash-nav-link" href="{{ route('superadmin.documentos.index') }}">
    <span class="material-symbols-outlined">description</span>
    Documentación
</a>

<a class="sa-dash-nav-link" href="{{ route('superadmin.tarjetas.index') }}">
    <span class="material-symbols-outlined">credit_card</span>
    Tarjetas
</a>

<a class="sa-dash-nav-link" href="{{ route('superadmin.licencias.index') }}">
    <span class="material-symbols-outlined">badge</span>
    Licencias
</a>

<a class="sa-dash-nav-link" href="{{ route('superadmin.reportes.index') }}">
    <span class="material-symbols-outlined">analytics</span>
    Reportes
</a>

<a class="sa-dash-nav-link" href="{{ route('superadmin.alertas.index') }}">
    <span class="material-symbols-outlined">notifications</span>
    Alertas
</a>

<a class="sa-dash-nav-link" href="{{ route('superadmin.configuracion.index') }}">
    <span class="material-symbols-outlined">settings</span>
    Configuración
</a>


        </nav>

        {{-- FOOTER --}}
        <div class="sa-dash-sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sa-dash-logout-btn">
                    <span class="material-symbols-outlined">logout</span>
                    Cerrar sesión
                </button>
            </form>
        </div>

    </aside>

    {{-- CONTENIDO --}}
    <main class="sa-main">
        @yield('content')
    </main>

</div>

</body>
</html>
