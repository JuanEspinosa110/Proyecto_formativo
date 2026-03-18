@extends('auxiliar.layouts.app')

@section('title', 'Dashboard — Auxiliar SIGU')

@section('content')
<div class="admin-dashboard sigu-fade">

    <!-- Header -->
    <div class="sigu-page-hd d-flex justify-content-between align-items-center">
        <div>
            <h1 class="sigu-page-title">Dashboard</h1>
            <p class="sigu-page-sub">Panel de Control para el Auxiliar Administrativo</p>
        </div>
        <div class="text-secondary small">
            <span class="material-symbols-rounded align-middle fs-6">calendar_month</span> {{ now()->format('d M, Y') }}
        </div>
    </div>

    <!-- KPIs Resumen -->
    <section class="sa-kpi-section mb-5">
        <div class="sa-kpi-card shadow-sm border-0 rounded-4 p-4 bg-white d-flex align-items-center gap-3">
            <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                <span class="material-symbols-rounded fs-2">group</span>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-bold d-block">Conductores</span>
                <strong class="fs-3 text-dark">{{ $totalConductores }}</strong>
            </div>
        </div>

        <div class="sa-kpi-card shadow-sm border-0 rounded-4 p-4 bg-white d-flex align-items-center gap-3">
            <div class="bg-success-subtle p-3 rounded-circle text-success">
                <span class="material-symbols-rounded fs-2">person_pin</span>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-bold d-block">Propietarios</span>
                <strong class="fs-3 text-dark">{{ $totalPropietarios }}</strong>
            </div>
        </div>

        <div class="sa-kpi-card shadow-sm border-0 rounded-4 p-4 bg-white d-flex align-items-center gap-3">
            <div class="bg-info-subtle p-3 rounded-circle text-info">
                <span class="material-symbols-rounded fs-2">assignment</span>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-bold d-block">Asignaciones</span>
                <strong class="fs-3 text-dark">{{ $totalAsignaciones }}</strong>
            </div>
        </div>

        <div class="sa-kpi-card shadow-sm border-0 rounded-4 p-4 bg-white d-flex align-items-center gap-3">
            <div class="bg-danger-subtle p-3 rounded-circle text-danger">
                <span class="material-symbols-rounded fs-2">error</span>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-bold d-block">Docs. Vencidos</span>
                <strong class="fs-3 text-danger">{{ $docsVencidos }}</strong>
                @if($docsProximos > 0)
                    <span class="small text-warning d-block" style="font-size: 0.75rem;">+{{ $docsProximos }} Próximos</span>
                @endif
            </div>
        </div>
    </section>

    <!-- Accesos Rápidos (Módulos) -->
    <h5 class="fw-bold text-dark mb-4">Módulos de Gestión</h5>
    
    <div class="row g-4">
        <!-- Usuarios -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white hover-up transition">
                <div class="card-body d-flex flex-column text-center">
                    <div class="bg-light rounded-4 p-3 mb-3 mx-auto" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <span class="material-symbols-rounded fs-2 text-primary">group_add</span>
                    </div>
                    <h6 class="fw-bold mb-2">Gestión Usuarios</h6>
                    <p class="small text-muted mb-4">Creación de conductores (con licencias) y propietarios.</p>
                    <div class="mt-auto d-grid gap-2">
                        <a href="{{ route('auxiliar.usuarios.createConductor') }}" class="btn btn-outline-primary btn-sm rounded-pill">Crear Conductor</a>
                        <a href="{{ route('auxiliar.usuarios.createPropietario') }}" class="btn btn-outline-secondary btn-sm rounded-pill">Crear Propietario</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Asignaciones -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white hover-up transition">
                <div class="card-body d-flex flex-column text-center">
                    <div class="bg-light rounded-4 p-3 mb-3 mx-auto" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <span class="material-symbols-rounded fs-2 text-success">assignment_turned_in</span>
                    </div>
                    <h6 class="fw-bold mb-2">Asignaciones</h6>
                    <p class="small text-muted mb-4">Asignar rutas a buses y buses a conductores operativos.</p>
                    <div class="mt-auto d-grid">
                        <a href="{{ route('auxiliar.asignaciones.index') }}" class="btn btn-light btn-sm rounded-pill fw-bold border">Ir a Asignaciones</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documentos -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white hover-up transition">
                <div class="card-body d-flex flex-column text-center">
                    <div class="bg-light rounded-4 p-3 mb-3 mx-auto" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <span class="material-symbols-rounded fs-2 text-info">folder_open</span>
                    </div>
                    <h6 class="fw-bold mb-2">Documentos</h6>
                    <p class="small text-muted mb-4">Subir y editar documentos. Auditoría de cambios completa.</p>
                    <div class="mt-auto d-grid">
                        <a href="{{ route('auxiliar.documentos.index') }}" class="btn btn-light btn-sm rounded-pill fw-bold border">Ver Documentación</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reportes -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white hover-up transition">
                <div class="card-body d-flex flex-column text-center">
                    <div class="bg-light rounded-4 p-3 mb-3 mx-auto" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <span class="material-symbols-rounded fs-2 text-purple">analytics</span>
                    </div>
                    <h6 class="fw-bold mb-2">Reportes</h6>
                    <p class="small text-muted mb-4">Exportar listados a Excel con filtros avanzados de auditoría.</p>
                    <div class="mt-auto d-grid">
                        <a href="{{ route('auxiliar.reportes.index') }}" class="btn btn-light btn-sm rounded-pill fw-bold border">Ir a Reportes</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .sa-kpi-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
    }
    .hover-up:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important;
    }
    .transition {
        transition: all 0.2s ease-in-out;
    }
</style>
@endsection
