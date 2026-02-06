<!DOCTYPE html>
<html lang="es" class="light">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Panel Administrador')</title>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
</head>

<body class="sa-body">
<div class="sa-layout">

  <!-- Sidebar -->
  <aside class="sa-sidebar">
    <div class="sa-sidebar-brand">
      <div class="sa-brand-icon">🚍</div>
      <div>
        <h1 class="sa-brand-title">Transport Global</h1>
        <p class="sa-brand-role">Super Admin</p>
      </div>
    </div>

    <nav class="sa-sidebar-nav">
      <div class="sa-nav-group">
        <p class="sa-nav-label">General</p>
        <a class="sa-nav-link sa-nav-link-active">
          <span class="material-symbols-outlined">dashboard</span> Dashboard
        </a>
      </div>

      <div class="sa-nav-group">
        <p class="sa-nav-label">Administración</p>
        <a class="sa-nav-link"><span class="material-symbols-outlined">shield_person</span> Roles</a>
        <a class="sa-nav-link"><span class="material-symbols-outlined">group</span> Usuarios</a>
        <a class="sa-nav-link"><span class="material-symbols-outlined">apartment</span> Empresas</a>
        <a class="sa-nav-link"><span class="material-symbols-outlined">link</span> Afiliaciones</a>
      </div>

      <div class="sa-nav-group">
        <p class="sa-nav-label">Operación</p>
        <a class="sa-nav-link"><span class="material-symbols-outlined">directions_bus</span> Buses</a>
        <a class="sa-nav-link"><span class="material-symbols-outlined">map</span> Rutas</a>
        <a class="sa-nav-link"><span class="material-symbols-outlined">route</span> Viajes</a>
        <a class="sa-nav-link"><span class="material-symbols-outlined">confirmation_number</span> Pasajes</a>
        <a class="sa-nav-link"><span class="material-symbols-outlined">credit_card</span> Tarjetas</a>
      </div>

      <div class="sa-nav-group">
        <p class="sa-nav-label">Control</p>
        <a class="sa-nav-link"><span class="material-symbols-outlined">folder_open</span> Documentación</a>
        <a class="sa-nav-link"><span class="material-symbols-outlined">build</span> Mantenimiento</a>
      </div>

      <div class="sa-nav-group">
        <p class="sa-nav-label">Análisis</p>
        <a class="sa-nav-link"><span class="material-symbols-outlined">bar_chart</span> Reportes</a>
      </div>
    </nav>

    <div class="sa-sidebar-footer">
      <a class="sa-nav-link"><span class="material-symbols-outlined">settings</span> Configuración</a>
    </div>
  </aside>

  <!-- Main -->
  <div class="sa-main">

    <!-- Header -->
    <header class="sa-header">
      <h2 class="sa-header-title">@yield('page-title', 'Dashboard')</h2>

      <div class="sa-header-actions">
        <input class="sa-search" placeholder="Búsqueda global..." />
        <button class="sa-icon-btn">
          <span class="material-symbols-outlined">notifications</span>
        </button>
        <button class="sa-icon-btn">
          <span class="material-symbols-outlined">dark_mode</span>
        </button>
      </div>
    </header>

    <!-- Dynamic Content -->
    <main class="sa-content">
        @yield('content')
    </main>

  </div>
</div>
</body>
</html>
