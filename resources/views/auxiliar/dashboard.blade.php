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

    <!-- Sección Principal: Módulos y Alertas -->
    <div class="row g-4">
        
        <!-- COLUMNA IZQUIERDA: MÓDULOS DE GESTIÓN -->
        <div class="col-lg-9">
            <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                <span class="material-symbols-rounded text-primary">apps</span>
                Módulos de Gestión
            </h5>
            
            <div class="row g-3">
                <!-- Usuarios -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white hover-up transition" data-bs-toggle="modal" data-bs-target="#modalUsuarios" style="cursor: pointer;">
                        <div class="card-body d-flex flex-column text-center">
                            <div class="bg-primary-subtle rounded-4 p-3 mb-3 mx-auto" style="width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                                <span class="material-symbols-rounded fs-2 text-primary">group</span>
                            </div>
                            <h6 class="fw-bold mb-1">Usuarios</h6>
                            <p class="small text-muted mb-0">Conductores y Propietarios.</p>
                        </div>
                    </div>
                </div>

                <!-- Vehículos -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white hover-up transition" data-bs-toggle="modal" data-bs-target="#modalVehiculos" style="cursor: pointer;">
                        <div class="card-body d-flex flex-column text-center">
                            <div class="bg-success-subtle rounded-4 p-3 mb-3 mx-auto" style="width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                                <span class="material-symbols-rounded fs-2 text-success">directions_bus</span>
                            </div>
                            <h6 class="fw-bold mb-1">Vehículos</h6>
                            <p class="small text-muted mb-0">Ver flota y estado operativo.</p>
                        </div>
                    </div>
                </div>

                <!-- Documentos -->
                <div class="col-md-4">
                    <a href="{{ route('empresa.documentos.solicitudes') }}" class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white hover-up transition text-decoration-none">
                        <div class="card-body d-flex flex-column text-center position-relative">
                            @if(isset($documentosPendientes) && $documentosPendientes > 0)
                                <span class="position-absolute badge rounded-pill bg-danger shadow-sm" style="top: -5px; right: -5px; font-size: 0.75rem; padding: 0.4em 0.6em;">
                                    {{ $documentosPendientes }}
                                </span>
                            @endif
                            <div class="bg-info-subtle rounded-4 p-3 mb-3 mx-auto" style="width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                                <span class="material-symbols-rounded fs-2 text-info">folder_shared</span>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark">Solicitudes Docs.</h6>
                            <p class="small text-muted mb-0">Revisión y aprobación.</p>
                        </div>
                    </a>
                </div>

                <!-- Asignaciones -->
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white hover-up transition" data-bs-toggle="modal" data-bs-target="#modalAsignaciones" style="cursor: pointer;">
                        <div class="card-body d-flex flex-column text-center">
                            <div class="bg-warning-subtle rounded-4 p-3 mb-3 mx-auto" style="width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                                <span class="material-symbols-rounded fs-2 text-warning">assignment</span>
                            </div>
                            <h6 class="fw-bold mb-1">Asignaciones</h6>
                            <p class="small text-muted mb-0">Rutas, conductores y horarios.</p>
                        </div>
                    </div>
                </div>

                <!-- Reportes -->
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white hover-up transition" data-bs-toggle="modal" data-bs-target="#modalReportes" style="cursor: pointer;">
                        <div class="card-body d-flex flex-column text-center">
                            <div class="bg-purple-subtle rounded-4 p-3 mb-3 mx-auto" style="width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                                <span class="material-symbols-rounded fs-2 text-purple">analytics</span>
                            </div>
                            <h6 class="fw-bold mb-1">Reportes</h6>
                            <p class="small text-muted mb-0">Exportar datos a Excel/PDF.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: SISTEMA DE ALERTAS -->
        <div class="col-lg-3">
            <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                <span class="material-symbols-rounded text-danger">notifications_active</span>
                Alertas
            </h5>

            <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                <div class="d-flex flex-column gap-2" style="max-height: 400px; overflow-y: auto;">
                    
                    <!-- Alert Documentos -->
                    @if(count($alertasDocumentos) > 0)
                        <div class="alert alert-danger border-0 shadow-none mb-2 py-2 px-3 rounded-3 d-flex align-items-<ctrl94> gap-2">
                            <span class="material-symbols-rounded fs-5 text-danger mt-1">warning</span>
                            <div>
                                <strong class="d-block small text-danger">Documentos Críticos</strong>
                                <span class="fs-xs text-secondary">{{ count($alertasDocumentos) }} documentos requieren revisión inmediata (Vencidos o Rechazados).</span>
                            </div>
                        </div>
                    @endif

                    <!-- Alert Buses Inactivos -->
                    @if(count($busesInactivos) > 0)
                        <div class="alert alert-warning border-0 shadow-none mb-2 py-2 px-3 rounded-3 d-flex align-items-start gap-2">
                            <span class="material-symbols-rounded fs-5 text-warning mt-1">directions_bus</span>
                            <div>
                                <strong class="d-block small text-warning">Buses Inactivos</strong>
                                <span class="fs-xs text-secondary">{{ count($busesInactivos) }} vehículos fuera de servicio actualmente.</span>
                            </div>
                        </div>
                    @endif

                    @if(count($alertasDocumentos) == 0 && count($busesInactivos) == 0)
                        <div class="text-center py-4 text-muted">
                            <span class="material-symbols-rounded fs-2 d-block mb-1 text-muted opacity-50">check_circle</span>
                            <p class="small mb-0">Todo en orden por hoy.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>

    <!-- INCLUDES DE MODALES -->
    @include('auxiliar.modals.usuarios')
    @include('auxiliar.modals.vehiculos')
    @include('auxiliar.modals.documentos')
    @include('auxiliar.modals.asignaciones')
    @include('auxiliar.modals.reportes')


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
