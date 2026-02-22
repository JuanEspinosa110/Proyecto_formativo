<!DOCTYPE html>
<html lang="es" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Super Admin')</title>

    <title>@yield('title', 'SIGU') — Sistema Integral de Seguimiento Urbano</title>

    <!-- Tipografías: Sora (display) + Inter Tight (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Inter+Tight:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/superadmin_congif.css') }}">


    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

     <!-- Font -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- CSS propio -->
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/super-admin-roles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/perfil-seguridad.css') }}">
    <link rel="stylesheet" href="{{ asset('css/super-admin-licencia.css') }}">
    <link rel="stylesheet" href="{{ asset('css/empresas.css') }}">

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


                <a class="sa-dash-nav-link" href="{{ route('superadmin.empresas.index') }}">
                    <span class="material-symbols-outlined">business</span>
                    Empresas
                </a>


                <a class="sa-dash-nav-link" href="{{ route('superadmin.licencias.index') }}">
                    <span class="material-symbols-outlined">badge</span>
                    Licencias
                </a>


                <a class="sa-dash-nav-link" href="{{ route('superadmin.perfil.index') }}">
                    <span class="material-symbols-outlined">security</span>
                    Perfil y seguridad
                </a>


            </nav>
            <div class="dropdown">
                <a href="#" 
                class="sigu-nl dropdown-toggle {{ request()->routeIs('superadmin.departamentos.*') ? 'active' : '' }}"
                data-bs-toggle="dropdown"
                aria-expanded="false">
                    <span class="material-symbols-rounded">settings</span>
                    <span>Configuración</span>
                </a>

                <ul class="dropdown-menu">
                    
                   <li class="nav-item">
                        <a class="nav-link" href="{{ route('superadmin.ciudades.index') }}">
                            <i class="bi bi-geo-alt"></i> Ciudades
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                        href="{{ route('superadmin.tipo-empresa.index') }}">
                            <i class="bi bi-building"></i> Tipos de Empresa
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            Tipos Usuario
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
            <a href="{{ route('superadmin.perfil.index') }}" class="sigu-dl {{ request()->routeIs('superadmin.perfil.*')   ? 'active' : '' }}"><span class="material-symbols-rounded">badge</span>Perfil</a>
            <div class="sigu-drawer-footer">
                
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