
@extends('admin.layouts.app')

@section('title', 'Dashboard — SIGU')

@section('content')
<div class="admin-dashboard sigu-fade">

    <div class="sigu-page-hd">
        <div>
            <h1 class="sigu-page-title">Dashboard</h1>
            <p class="sigu-page-sub">Resumen del panel administrativo</p>
        </div>
    </div>

    <section class="sa-kpi-section">
        <div class="sa-kpi-card">
            <div class="kpi-left">
                <span class="kpi-title">Empresa</span>
                <span class="kpi-value" id="kpiEmpresa">—</span>
            </div>
            <div class="kpi-right">
                <div class="kpi-trend positive">+3% <span class="material-symbols-rounded" style="font-size:14px">trending_up</span></div>
                <svg class="kpi-sparkline" viewBox="0 0 100 28" preserveAspectRatio="none"><polyline points="0,20 20,14 40,10 60,8 80,12 100,6" fill="none" stroke="#9B84E3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></polyline></svg>
            </div>
        </div>
        <div class="sa-kpi-card">
            <div class="kpi-left">
                <span class="kpi-title">Total Usuarios</span>
                <span class="kpi-value" id="kpiUsuarios">—</span>
            </div>
            <div class="kpi-right">
                <div class="kpi-trend positive">+8% <span class="material-symbols-rounded" style="font-size:14px">trending_up</span></div>
                <svg class="kpi-sparkline" viewBox="0 0 100 28" preserveAspectRatio="none"><polyline points="0,22 20,18 40,14 60,12 80,10 100,8" fill="none" stroke="#6A4CC5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></polyline></svg>
            </div>
        </div>
        <div class="sa-kpi-card">
            <div class="kpi-left">
                <span class="kpi-title">Documentos</span>
                <span class="kpi-value" id="kpiDocumentos">—</span>
            </div>
            <div class="kpi-right">
                <div class="kpi-trend negative">-1% <span class="material-symbols-rounded" style="font-size:14px">trending_down</span></div>
                <svg class="kpi-sparkline" viewBox="0 0 100 28" preserveAspectRatio="none"><polyline points="0,12 20,16 40,20 60,22 80,20 100,24" fill="none" stroke="#ff6b6b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></polyline></svg>
            </div>
        </div>
    </section>

    <section style="margin-top:1rem;">
        <div class="sa-chart-card" style="max-width:600px;">
            <h4>
                <span class="material-symbols-rounded" style="font-size:1rem;color:var(--p);font-variation-settings:'FILL' 1">pie_chart</span>
                Distribución: Usuarios vs Documentos
            </h4>
            <canvas id="chartUsersDocs" width="600" height="300"></canvas>
        </div>
    </section>

</div>

@push('scripts')
<script>window.ADMIN_STATS_URL = "{{ route('admin.dashboard.stats') }}";</script>
<script src="{{ asset('js/dashboard/admin.js') }}" defer></script>
@endpush

@endsection
