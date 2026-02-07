@extends('superadmin.layouts.admin')

@section('title', 'Dashboard Super Admin')

@section('content')

<header class="sa-dash-header">
    <h2>Dashboard Administrativo</h2>

    <input
        type="text"
        class="sa-dash-search"
        placeholder="Buscar empresas, usuarios o documentos..."
    >
</header>

<section class="sa-dash-kpis">
    <div class="sa-kpi">
        <span class="material-symbols-outlined">business</span>
        <p>Empresas</p>
        <h3>124</h3>
    </div>

    <div class="sa-dash-kpi">
        <span class="material-symbols-outlined">person</span>
        <p>Usuarios</p>
        <h3>12,850</h3>
    </div>

    <div class="sa-dash-kpi alert">
        <span class="material-symbols-outlined">report_problem</span>
        <p>Alertas</p>
        <h3>5</h3>
    </div>
</section>

<section class="sa-dash-actions">
    <button class="btn btn-primary">Crear Usuario</button>
    <button class="btn btn-outline-secondary">Registrar Empresa</button>
    <button class="btn btn-outline-secondary">Cargar Documento</button>
</section>

@endsection
