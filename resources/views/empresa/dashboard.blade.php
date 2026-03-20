@extends('empresa.layouts.app')

@section('title', 'Dashboard Empresa — SIGU')

@section('content')
<div class="container-fluid pb-5">
    
    @if ($errors->any())
    <div class="row mb-4 mt-2">
        <div class="col-12">
            <div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 mb-0 d-flex align-items-center gap-4">
                <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
                    <span class="material-symbols-rounded fs-1">error</span>
                </div>
                <div class="flex-grow-1">
                    <h5 class="fw-bold mb-1">Error al procesar la solicitud</h5>
                    <ul class="mb-0 text-dark opacity-75 ps-3 py-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row mb-5">
        <div class="col-12">
            <span class="text-primary fw-bold text-uppercase small letter-spacing-1 mb-2 d-block">Panel de Empresa</span>
            <h1 class="display-5 fw-bold text-dark mb-2">Bienvenido, {{ auth()->user()->primer_nombre }}</h1>
            <p class="text-muted fs-5">Gestión y monitoreo de la operación de transporte.</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- Tarjeta de Bienvenida o Info -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-dark text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;">
                <div class="card-body p-0 d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="fw-bold mb-2">Módulo de Empresa</h3>
                        <p class="text-white-50 mb-0">Aquí podrás gestionar la flota, conductores y rutas asociadas a tu empresa.</p>
                    </div>
                    <span class="material-symbols-rounded" style="font-size: 5rem; opacity: 0.1;">corporate_fare</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de accesos rápidos o resumen (Placeholder) -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-inline-flex mx-auto mb-3" style="width: 60px; height: 60px; align-items: center; justify-content: center;">
                    <span class="material-symbols-rounded fs-2">assessment</span>
                </div>
                <h5 class="fw-bold mb-1">Operación</h5>
                <p class="text-muted small mb-0">Monitoreo en tiempo real.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle d-inline-flex mx-auto mb-3" style="width: 60px; height: 60px; align-items: center; justify-content: center;">
                    <span class="material-symbols-rounded fs-2">group</span>
                </div>
                <h5 class="fw-bold mb-1">Personal</h5>
                <p class="text-muted small mb-0">Gestión de conductores.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle d-inline-flex mx-auto mb-3" style="width: 60px; height: 60px; align-items: center; justify-content: center;">
                    <span class="material-symbols-rounded fs-2">description</span>
                </div>
                <h5 class="fw-bold mb-1">Documentos</h5>
                <p class="text-muted small mb-0">Control de vigencias.</p>
            </div>
        </div>
    </div>

</div>
@endsection
