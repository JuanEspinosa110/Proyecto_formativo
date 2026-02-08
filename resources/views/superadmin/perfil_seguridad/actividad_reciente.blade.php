@extends('superadmin.layouts.admin')

@section('title', 'Actividad Reciente')

@section('content')
<div class="sa-perfil-container">
    <div class="sa-perfil-breadcrumb">
        <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
        <span>/</span>
        <a href="{{ route('superadmin.perfil.index') }}">Perfil</a>
        <span>/</span>
        <span>Actividad Reciente</span>
    </div>

    <div class="sa-perfil-header">
        <div>
            <h1 class="sa-perfil-title">Actividad Reciente</h1>
            <p class="sa-perfil-subtitle">Historial de acciones realizadas en el sistema</p>
        </div>
        <a href="{{ route('superadmin.perfil.index') }}" class="sa-perfil-btn sa-perfil-btn-secondary">
            <span class="material-symbols-outlined">arrow_back</span>
            Volver
        </a>
    </div>

    <div class="sa-perfil-card">
        <div class="sa-perfil-card-header">
            <h2 class="sa-perfil-card-title">
                <span class="material-symbols-outlined">history</span>
                Registro de Actividades
            </h2>
        </div>
        <div class="sa-perfil-card-body">
            @if($actividades->count() > 0)
            <ul class="sa-perfil-actividad-lista">
                @foreach($actividades as $actividad)
                <li class="sa-perfil-actividad-item">
                    <div class="sa-perfil-actividad-icon">
                        <span class="material-symbols-outlined">
                            @if(str_contains($actividad->accion, 'Inicio de sesión'))
                                login
                            @elseif(str_contains($actividad->accion, 'Actualización'))
                                edit
                            @elseif(str_contains($actividad->accion, 'Cambio'))
                                swap_horiz
                            @elseif(str_contains($actividad->accion, 'Eliminación'))
                                delete
                            @else
                                history
                            @endif
                        </span>
                    </div>
                    <div class="sa-perfil-actividad-content">
                        <p class="sa-perfil-actividad-accion">{{ $actividad->accion }}</p>
                        <div>
                            <span class="sa-perfil-actividad-modulo">{{ $actividad->modulo }}</span>
                            <span class="sa-perfil-actividad-fecha">
                                {{ \Carbon\Carbon::parse($actividad->fecha_registro)->format('d/m/Y H:i:s') }}
                            </span>
                            @if($actividad->ip_address)
                            <span class="sa-perfil-actividad-ip">IP: {{ $actividad->ip_address }}</span>
                            @endif
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
            @else
            <div class="sa-perfil-empty">
                <div class="sa-perfil-empty-icon">
                    <span class="material-symbols-outlined" style="font-size: 5rem;">history</span>
                </div>
                <h3 class="sa-perfil-empty-title">No hay actividad registrada</h3>
                <p class="sa-perfil-empty-text">Aún no se ha registrado ninguna actividad en el sistema</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
