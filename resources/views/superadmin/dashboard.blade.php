@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')

<!-- Quick Actions -->
<div class="sa-actions">
  <button class="sa-btn-primary">Crear Usuario</button>
  <button class="sa-btn-secondary">Nueva Empresa</button>
  <button class="sa-btn-secondary">Gestionar Afiliaciones</button>
  <button class="sa-btn-secondary sa-ml-auto">Exportar Reporte</button>
</div>

<!-- KPIs -->
<section class="sa-kpi-grid">
  <div class="sa-kpi-card">
    <p>Usuarios</p>
    <h3>12,450</h3>
  </div>
  <div class="sa-kpi-card">
    <p>Empresas</p>
    <h3>85</h3>
  </div>
  <div class="sa-kpi-card">
    <p>Buses</p>
    <h3>1,200</h3>
  </div>
  <div class="sa-kpi-card">
    <p>Viajes Diarios</p>
    <h3>45,600</h3>
  </div>
  <div class="sa-kpi-card">
    <p>Mantenimientos</p>
    <h3>24</h3>
  </div>
  <div class="sa-kpi-card sa-kpi-alert">
    <p>Alertas</p>
    <h3>3</h3>
  </div>
</section>

@endsection
