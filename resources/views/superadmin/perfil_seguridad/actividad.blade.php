@extends('superadmin.layouts.admin')

@section('title', 'Actividad Reciente')

@section('content')
<div class="sa-perfil-container">
    <!-- Breadcrumb -->
    <div class="sa-perfil-breadcrumb">
        <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
        <span>/</span>
        <a href="{{ route('superadmin.perfil.index') }}">Mi Perfil</a>
        <span>/</span>
        <span>Actividad</span>
    </div>

    <!-- Header -->
    <div class="sa-perfil-header">
        <div>
            <h1 class="sa-perfil-title">Actividad Reciente</h1>
            <p class="sa-perfil-subtitle">Historial completo de acciones realizadas en el sistema</p>
        </div>
        <a href="{{ route('superadmin.perfil.index') }}" class="sa-perfil-btn sa-perfil-btn-secondary">
            <span class="material-symbols-outlined">arrow_back</span>
            Volver al Perfil
        </a>
    </div>

    <!-- Filtros -->
    <div class="sa-perfil-card">
        <div class="sa-perfil-card-header">
            <h2 class="sa-perfil-card-title">
                <span class="material-symbols-outlined">filter_list</span>
                Filtros
            </h2>
        </div>
        <div class="sa-perfil-card-body">
            <form action="{{ route('superadmin.perfil.actividad') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <div class="sa-perfil-form-group">
                            <label for="modulo" class="sa-perfil-form-label">Módulo</label>
                            <select id="modulo" name="modulo" class="sa-perfil-form-control">
                                <option value="">Todos los módulos</option>
                                @foreach($modulos as $modulo)
                                <option value="{{ $modulo }}" {{ request('modulo') == $modulo ? 'selected' : '' }}>
                                    {{ $modulo }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="sa-perfil-form-group">
                            <label for="fecha_inicio" class="sa-perfil-form-label">Fecha Inicio</label>
                            <input type="date" 
                                   id="fecha_inicio" 
                                   name="fecha_inicio" 
                                   class="sa-perfil-form-control"
                                   value="{{ request('fecha_inicio') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="sa-perfil-form-group">
                            <label for="fecha_fin" class="sa-perfil-form-label">Fecha Fin</label>
                            <input type="date" 
                                   id="fecha_fin" 
                                   name="fecha_fin" 
                                   class="sa-perfil-form-control"
                                   value="{{ request('fecha_fin') }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="sa-perfil-form-group">
                            <label class="sa-perfil-form-label" style="opacity: 0;">Acciones</label>
                            <button type="submit" class="sa-perfil-btn sa-perfil-btn-primary" style="width: 100%;">
                                <span class="material-symbols-outlined">search</span>
                                Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de actividades -->
    <div class="sa-perfil-card">
        <div class="sa-perfil-card-header">
            <h2 class="sa-perfil-card-title">
                <span class="material-symbols-outlined">list</span>
                Historial de Actividades ({{ $actividades->total() }})
            </h2>
        </div>
        <div class="sa-perfil-card-body">
            @if($actividades->count() > 0)
            <div class="sa-perfil-timeline">
                @foreach($actividades as $actividad)
                <div class="sa-perfil-timeline-item">
                    <div class="sa-perfil-timeline-marker {{ strtolower($actividad->color) }}"></div>
                    <div class="sa-perfil-timeline-content">
                        <div class="sa-perfil-timeline-header">
                            <div class="sa-perfil-timeline-icon {{ strtolower($actividad->color) }}">
                                <span class="material-symbols-outlined">{{ $actividad->icono }}</span>
                            </div>
                            <div style="flex: 1;">
                                <p class="sa-perfil-timeline-title">{{ $actividad->accion }}</p>
                                <div class="sa-perfil-timeline-meta">
                                    <span class="sa-perfil-timeline-badge {{ strtolower($actividad->color) }}">
                                        <span class="material-symbols-outlined" style="font-size: 0.875rem;">{{ $actividad->icono }}</span>
                                        {{ $actividad->modulo }}
                                    </span>
                                    <span>
                                        <span class="material-symbols-outlined" style="font-size: 0.875rem; vertical-align: middle;">event</span>
                                        {{ $actividad->fecha_registro->format('d/m/Y H:i:s') }}
                                    </span>
                                    @if($actividad->ip_address)
                                    <span>
                                        <span class="material-symbols-outlined" style="font-size: 0.875rem; vertical-align: middle;">router</span>
                                        {{ $actividad->ip_address }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div style="margin-top: 2rem;">
                {{ $actividades->links() }}
            </div>
            @else
            <div class="sa-perfil-empty">
                <div class="sa-perfil-empty-icon">
                    <span class="material-symbols-outlined" style="font-size: 5rem;">history</span>
                </div>
                <h3 class="sa-perfil-empty-title">No se encontraron actividades</h3>
                <p class="sa-perfil-empty-text">Intenta ajustar los filtros de búsqueda</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
