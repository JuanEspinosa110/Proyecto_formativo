@extends('superadmin.layouts.admin')

@section('title', 'Opciones de Seguridad')

@section('content')
<div class="sa-perfil-container">
    <div class="sa-perfil-breadcrumb">
        <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
        <span>/</span>
        <a href="{{ route('superadmin.perfil.index') }}">Perfil</a>
        <span>/</span>
        <span>Seguridad</span>
    </div>

    <div class="sa-perfil-header">
        <div>
            <h1 class="sa-perfil-title">Opciones de Seguridad</h1>
            <p class="sa-perfil-subtitle">Configuración de seguridad de tu cuenta</p>
        </div>
        <a href="{{ route('superadmin.perfil.index') }}" class="sa-perfil-btn sa-perfil-btn-secondary">
            <span class="material-symbols-outlined">arrow_back</span>
            Volver
        </a>
    </div>

    <!-- Estadísticas de Seguridad -->
    <div class="sa-perfil-stats">
        <div class="sa-perfil-stat-card">
            <div class="sa-perfil-stat-icon">
                <span class="material-symbols-outlined">today</span>
            </div>
            <div class="sa-perfil-stat-label">Accesos Hoy</div>
            <div class="sa-perfil-stat-value">{{ $estadisticas['accesos_hoy'] }}</div>
        </div>
        <div class="sa-perfil-stat-card">
            <div class="sa-perfil-stat-icon">
                <span class="material-symbols-outlined">date_range</span>
            </div>
            <div class="sa-perfil-stat-label">Esta Semana</div>
            <div class="sa-perfil-stat-value">{{ $estadisticas['actividades_semana'] }}</div>
        </div>
        <div class="sa-perfil-stat-card">
            <div class="sa-perfil-stat-icon">
                <span class="material-symbols-outlined">history</span>
            </div>
            <div class="sa-perfil-stat-label">Total Actividades</div>
            <div class="sa-perfil-stat-value">{{ $estadisticas['total_actividades'] }}</div>
        </div>
    </div>

    <!-- Información de Seguridad -->
    <div class="sa-perfil-card">
        <div class="sa-perfil-card-header">
            <h2 class="sa-perfil-card-title">
                <span class="material-symbols-outlined">info</span>
                Información de Acceso
            </h2>
        </div>
        <div class="sa-perfil-card-body">
            <div class="sa-perfil-info-grid">
                <div class="sa-perfil-info-item">
                    <div class="sa-perfil-info-label">Último Acceso</div>
                    <div class="sa-perfil-info-value">
                        @if($ultimoAcceso)
                            {{ \Carbon\Carbon::parse($ultimoAcceso->fecha_registro)->format('d/m/Y H:i:s') }}
                        @else
                            Primer acceso
                        @endif
                    </div>
                </div>
                <div class="sa-perfil-info-item">
                    <div class="sa-perfil-info-label">IP del Último Acceso</div>
                    <div class="sa-perfil-info-value" style="font-family: monospace;">
                        {{ $ultimoAcceso->ip_address ?? 'N/A' }}
                    </div>
                </div>
                <div class="sa-perfil-info-item">
                    <div class="sa-perfil-info-label">IP Actual</div>
                    <div class="sa-perfil-info-value" style="font-family: monospace;">
                        {{ request()->ip() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Opciones de Seguridad -->
    <div class="sa-perfil-card">
        <div class="sa-perfil-card-header">
            <h2 class="sa-perfil-card-title">
                <span class="material-symbols-outlined">security</span>
                Configuración de Seguridad
            </h2>
        </div>
        <div class="sa-perfil-card-body">
            <a href="{{ route('superadmin.perfil.cambiar-contrasena') }}" class="sa-perfil-seguridad-item" style="text-decoration: none;">
                <div class="sa-perfil-seguridad-info">
                    <h4 class="sa-perfil-seguridad-title">Cambiar Contraseña</h4>
                    <p class="sa-perfil-seguridad-desc">Actualiza tu contraseña regularmente para mayor seguridad</p>
                </div>
                <div>
                    <span class="sa-perfil-seguridad-status sa-perfil-seguridad-status-active">Activa</span>
                </div>
            </a>

            <div class="sa-perfil-seguridad-item">
                <div class="sa-perfil-seguridad-info">
                    <h4 class="sa-perfil-seguridad-title">Autenticación de Dos Factores</h4>
                    <p class="sa-perfil-seguridad-desc">Protección adicional para tu cuenta</p>
                </div>
                <div>
                    <span class="sa-perfil-seguridad-status sa-perfil-seguridad-status-inactive">Próximamente</span>
                </div>
            </div>

            <a href="{{ route('superadmin.perfil.actividad') }}" class="sa-perfil-seguridad-item" style="text-decoration: none;">
                <div class="sa-perfil-seguridad-info">
                    <h4 class="sa-perfil-seguridad-title">Registro de Actividad</h4>
                    <p class="sa-perfil-seguridad-desc">Revisa todas las acciones realizadas en tu cuenta</p>
                </div>
                <div>
                    <span class="material-symbols-outlined" style="color: #007bff; font-size: 1.5rem;">arrow_forward</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Recomendaciones de Seguridad -->
    <div class="sa-perfil-card">
        <div class="sa-perfil-card-header">
            <h2 class="sa-perfil-card-title">
                <span class="material-symbols-outlined">tips_and_updates</span>
                Recomendaciones de Seguridad
            </h2>
        </div>
        <div class="sa-perfil-card-body">
            <ul style="margin: 0; padding-left: 1.5rem; color: #495057;">
                <li style="margin-bottom: 0.75rem;">Cambia tu contraseña cada 3 meses</li>
                <li style="margin-bottom: 0.75rem;">No compartas tu contraseña con nadie</li>
                <li style="margin-bottom: 0.75rem;">Usa contraseñas únicas para cada servicio</li>
                <li style="margin-bottom: 0.75rem;">Cierra sesión cuando uses computadoras públicas</li>
                <li>Revisa regularmente tu actividad reciente</li>
            </ul>
        </div>
    </div>
</div>
@endsection
