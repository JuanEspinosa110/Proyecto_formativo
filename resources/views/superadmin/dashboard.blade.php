@extends('superadmin.layouts.admin')

@section('title', 'Dashboard Super Admin')

@section('content')

<div class="sa-dashboard">

    <!-- HEADER SUPERIOR -->
    <header class="sa-topbar">
        <div class="sa-topbar-left">
            <h1>Sistema de Gestión de Transporte</h1>
            <span>Panel Super Administrador</span>
        </div>

        <nav class="sa-topbar-nav">
            <a href="{{ route('superadmin.dashboard') }}" class="active">Dashboard</a>
            <a href="{{ route('superadmin.empresas.index') }}">Empresas</a>
            <a href="{{ route('superadmin.licencias.index') }}">Licencias</a>
            <a href="{{ route('superadmin.planes.index') }}">Planes</a>
            <a href="{{ route('superadmin.perfil.index') }}">Perfil</a>
        </nav>
    </header>

    <!-- KPIs -->
    <section class="sa-kpi-section">
        <div class="sa-kpi-card">
            <span>Total Empresas</span>
            <strong id="kpiEmpresas">0</strong>
        </div>

        <div class="sa-kpi-card">
            <span>Total Licencias</span>
            <strong id="kpiLicencias">0</strong>
        </div>

        <div class="sa-kpi-card">
            <span>Planes Activos</span>
            <strong id="kpiPlanes">0</strong>
        </div>
    </section>

    <!-- GRÁFICAS -->
<section class="sa-chart-grid">

    <!-- 1️⃣ Empresas por estado -->
    <div class="sa-chart-card">
        <h4>Estado de Empresas</h4>
        <canvas id="chartEmpresasEstado"></canvas>
    </div>

    <!-- 2️⃣ Licencias por estado -->
    <div class="sa-chart-card">
        <h4>Estado de Licencias</h4>
        <canvas id="chartLicenciasEstado"></canvas>
    </div>

    <!-- 3️⃣ Planes más usados -->
    <div class="sa-chart-card">
        <h4>Planes más utilizados</h4>
        <canvas id="chartPlanes"></canvas>
    </div>

    <!-- 4️⃣ Crecimiento mensual empresas -->
    <div class="sa-chart-card full">
        <h4>Crecimiento mensual de Empresas</h4>
        <canvas id="chartEmpresasMensual"></canvas>
    </div>

    <!-- 5️⃣ Licencias emitidas por mes -->
    <div class="sa-chart-card full">
        <h4>Licencias emitidas por mes</h4>
        <canvas id="chartLicenciasMensual"></canvas>
    </div>

</section>


</div>

@push('scripts')
<script>
    const DASHBOARD_STATS_URL = "{{ route('superadmin.dashboard.stats') }}";
</script>

<script src="{{ asset('js/dashboard/superadmin.js') }}"></script>

<script>
    cargarDashboard(DASHBOARD_STATS_URL);
    setInterval(() => cargarDashboard(DASHBOARD_STATS_URL), 15000);
</script>
@endpush

@endsection
