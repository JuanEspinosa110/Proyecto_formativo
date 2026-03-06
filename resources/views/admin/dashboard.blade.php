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

    <section class="sa-charts-grid">
        <div class="sa-chart-card" style="max-width:600px;">
            <h4>
                <span class="material-symbols-rounded" style="font-size:1rem;color:var(--p);font-variation-settings:'FILL' 1">pie_chart</span>
                Distribución: Usuarios vs Documentos
            </h4>
            <canvas id="chartUsersDocs" width="600" height="300"></canvas>
        </div>
        <div class="sa-chart-card" style="max-width:600px;">
            <h4>
                <span class="material-symbols-rounded" style="font-size:1rem;color:var(--p);font-variation-settings:'FILL' 1">bar_chart</span>
                Buses por Estado
            </h4>
            <canvas id="chartBusesEstado" width="600" height="300"></canvas>
        </div>
        <div class="sa-chart-card" style="max-width:600px;">
            <h4>
                <span class="material-symbols-rounded" style="font-size:1rem;color:var(--p);font-variation-settings:'FILL' 1">timeline</span>
                Viajes por Ruta
            </h4>
            <canvas id="chartViajesRuta" width="600" height="300"></canvas>
        </div>
    </section>

</div>

@push('scripts')
<script>window.ADMIN_STATS_URL = "{{ route('admin.dashboard.stats') }}";</script>
<script src="{{ asset('js/dashboard/admin.js') }}" defer></script>
@endpush

@endsection
