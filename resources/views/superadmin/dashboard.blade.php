@extends('superadmin.layouts.admin')

@section('title', 'Dashboard Super Admin')

@section('content')

<div class="sa-dash-container">

<header class="sa-dash-header">
    <h2 class="sa-dash-title">Dashboard Administrativo</h2>
    <p class="sa-dash-subtitle">Resumen general del sistema</p>
</header>

<div class="sa-divider"></div>

<section class="sa-dash-actions">
    <h4>Acciones rápidas</h4>

    <div class="sa-dash-actions-buttons">
        <a href="#" class="btn btn-primary">Crear Usuario</a>
        <a href="#" class="btn btn-outline-primary">Registrar Empresa</a>
    </div>
</section>

<div class="sa-divider"></div>

<section class="sa-dash-charts">

    <div class="sa-dash-chart-card">
        <h4>Usuarios</h4>
        <canvas id="chartUsuarios"></canvas>
    </div>

    <div class="sa-dash-chart-card">
        <h4>Empresas</h4>
        <canvas id="chartEmpresas"></canvas>
    </div>

    <div class="sa-dash-chart-card">
        <h4>Documentos</h4>
        <canvas id="chartDocumentos"></canvas>
    </div>

    <div class="sa-dash-chart-card">
        <h4>Tarjetas</h4>
        <canvas id="chartTarjetas"></canvas>
    </div>

</section>

</div>


<canvas id="dashboardChart"></canvas>

</section>



@push('scripts')
<script>
    const DASHBOARD_STATS_URL = "{{ route('superadmin.dashboard.stats') }}";
</script>

<script src="{{ asset('js/dashboard/superadmin.js') }}"></script>

<script>
    cargarDashboard(DASHBOARD_STATS_URL);
    setInterval(() => cargarDashboard(DASHBOARD_STATS_URL), 10000);
</script>
@endpush


@endsection
