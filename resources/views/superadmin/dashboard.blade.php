@extends('superadmin.layouts.admin')

@section('title', 'Dashboard — SIGU')

@section('content')
<div class="sa-dashboard sigu-fade">

    <!-- Page header -->
    <div class="sigu-page-hd">
        <div>
            <h1 class="sigu-page-title">Dashboard</h1>
            <p class="sigu-page-sub">Resumen general del sistema de transporte urbano</p>
        </div>
    </div>

    <!-- KPIs -->
    <section class="sa-kpi-section">
        <div class="sa-kpi-card">
            <span>Total Empresas</span>
            <strong id="kpiEmpresas">—</strong>
        </div>
        <div class="sa-kpi-card">
            <span>Total Licencias</span>
            <strong id="kpiLicencias">—</strong>
        </div>
        <div class="sa-kpi-card">
            <span>Planes Activos</span>
            <strong id="kpiPlanes">—</strong>
        </div>
    </section>

    <!-- Gráficas -->
    <section class="sa-chart-grid">

        <div class="sa-chart-card">
            <h4>
                <span class="material-symbols-rounded" style="font-size:1rem;color:var(--p);font-variation-settings:'FILL' 1">pie_chart</span>
                Estado de Empresas
            </h4>
            <canvas id="chartEmpresasEstado"></canvas>
        </div>

        <div class="sa-chart-card">
            <h4>
                <span class="material-symbols-rounded" style="font-size:1rem;color:var(--p);font-variation-settings:'FILL' 1">donut_large</span>
                Estado de Licencias
            </h4>
            <canvas id="chartLicenciasEstado"></canvas>
        </div>

        <div class="sa-chart-card">
            <h4>
                <span class="material-symbols-rounded" style="font-size:1rem;color:var(--p);font-variation-settings:'FILL' 1">bar_chart</span>
                Planes más utilizados
            </h4>
            <canvas id="chartPlanes"></canvas>
        </div>

        <div class="sa-chart-card full">
            <h4>
                <span class="material-symbols-rounded" style="font-size:1rem;color:var(--p);font-variation-settings:'FILL' 1">trending_up</span>
                Crecimiento mensual de Empresas
            </h4>
            <canvas id="chartEmpresasMensual"></canvas>
        </div>

        <div class="sa-chart-card full">
            <h4>
                <span class="material-symbols-rounded" style="font-size:1rem;color:var(--p);font-variation-settings:'FILL' 1">area_chart</span>
                Licencias emitidas por mes
            </h4>
            <canvas id="chartLicenciasMensual"></canvas>
        </div>

    </section>
</div>

@push('scripts')
<script>
    // Paleta SIGU para Chart.js
    const SIGU = {
        purple: '#5E548E',
        indigo: '#4A4E69',
        ok: '#2A9E6A',
        warn: '#C97B0C',
        err: '#C43B3B',
        info: '#2576BB',
        pLight: '#ECE9F5',
        border: '#DADDE1',
    };

    // Defaults Chart.js con tipografía SIGU
    Chart.defaults.font.family = "'Inter Tight', system-ui, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6B6F88';
    Chart.defaults.plugins.legend.labels.padding = 16;
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.pointStyleWidth = 10;
    Chart.defaults.plugins.tooltip.backgroundColor = '#2F2F3A';
    Chart.defaults.plugins.tooltip.titleColor = '#ffffff';
    Chart.defaults.plugins.tooltip.bodyColor = '#DADDE1';
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
    Chart.defaults.plugins.tooltip.padding = 10;

    const DASHBOARD_STATS_URL = "{{ route('superadmin.dashboard.stats') }}";
</script>

<script src="{{ asset('js/dashboard/superadmin.js') }}"></script>

<script>
    cargarDashboard(DASHBOARD_STATS_URL);
    setInterval(() => cargarDashboard(DASHBOARD_STATS_URL), 15000);
</script>
@endpush

@endsection
