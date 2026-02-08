@extends('superadmin.layouts.admin')

@section('title', 'Permisos del Tipo de Usuario')

@section('content')
<div class="sa-roles-container">
    <!-- Breadcrumb -->
    <div class="sa-roles-breadcrumb">
        <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
        <span>/</span>
        <a href="{{ route('superadmin.roles.index') }}">Tipos de Usuario</a>
        <span>/</span>
        <span>Permisos</span>
    </div>

    <!-- Header -->
    <div class="sa-roles-header">
        <div>
            <h1 class="sa-roles-title">Permisos: {{ $tipoUsuario->nombre_tipo }}</h1>
            <p class="sa-roles-subtitle">Vista de permisos asociados al tipo de usuario</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('superadmin.roles.edit', $tipoUsuario->id_tipo_usuario) }}" class="sa-roles-btn sa-roles-btn-warning">
                <span class="material-symbols-outlined">edit</span>
                Editar Tipo
            </a>
            <a href="{{ route('superadmin.roles.index') }}" class="sa-roles-btn sa-roles-btn-secondary">
                <span class="material-symbols-outlined">arrow_back</span>
                Volver
            </a>
        </div>
    </div>

    <!-- Alerta informativa -->
    <div class="sa-roles-alert sa-roles-alert-info">
        <span class="material-symbols-outlined">info</span>
        <div>
            <strong>Vista de Demostración:</strong> Esta página muestra una representación visual de los permisos. 
            Los permisos reales se controlan actualmente en el código de la aplicación según el tipo de usuario.
            <br><br>
            <strong>Próximos pasos:</strong> Para habilitar la gestión dinámica de permisos desde esta interfaz, 
            será necesario implementar la tabla de permisos y las relaciones correspondientes en la base de datos.
        </div>
    </div>

    <!-- Información del tipo de usuario -->
    <div class="sa-roles-card">
        <div class="sa-roles-card-header">
            <h2 class="sa-roles-card-title">Información del Tipo de Usuario</h2>
        </div>
        <div class="sa-roles-card-body">
            <div class="row">
                <div class="col-md-4">
                    <div style="margin-bottom: 1rem;">
                        <small style="color: #6c757d; display: block; margin-bottom: 0.25rem;">Nombre</small>
                        <strong style="font-size: 1.1rem;">{{ $tipoUsuario->nombre_tipo }}</strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div style="margin-bottom: 1rem;">
                        <small style="color: #6c757d; display: block; margin-bottom: 0.25rem;">ID del Tipo</small>
                        <strong style="font-size: 1.1rem;">#{{ $tipoUsuario->id_tipo_usuario }}</strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div style="margin-bottom: 1rem;">
                        <small style="color: #6c757d; display: block; margin-bottom: 0.25rem;">Permisos Asignados</small>
                        <strong style="font-size: 1.1rem;">{{ $permissionsByModule->flatten()->count() }} permisos</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permisos por módulo -->
    <div class="sa-roles-card">
        <div class="sa-roles-card-header">
            <h2 class="sa-roles-card-title">Permisos por Módulo (Demo)</h2>
        </div>
        <div class="sa-roles-card-body">
            @if($permissionsByModule->count() > 0)
            <div class="sa-roles-permissions-section">
                @foreach($permissionsByModule as $module => $permissions)
                <div class="sa-roles-permissions-module">
                    <h3 class="sa-roles-permissions-module-title">
                        <span class="material-symbols-outlined" style="vertical-align: middle; margin-right: 0.5rem;">folder_open</span>
                        {{ $module }}
                        <span class="sa-roles-badge sa-roles-badge-primary" style="margin-left: 1rem;">
                            {{ count($permissions) }} {{ count($permissions) == 1 ? 'permiso' : 'permisos' }}
                        </span>
                    </h3>
                    <div class="sa-roles-permissions-grid">
                        @foreach($permissions as $permission)
                        <div class="sa-roles-permission-item" style="border-color: #28a745;">
                            <span class="material-symbols-outlined" style="color: #28a745; font-size: 1.25rem;">
                                check_circle
                            </span>
                            <div class="sa-roles-permission-label">
                                <div><strong>{{ $permission->name }}</strong></div>
                                @if($permission->description)
                                <div class="sa-roles-permission-description">{{ $permission->description }}</div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="sa-roles-empty">
                <div class="sa-roles-empty-icon">
                    <span class="material-symbols-outlined" style="font-size: 5rem;">security</span>
                </div>
                <h3 class="sa-roles-empty-title">Sin Permisos Asignados</h3>
                <p class="sa-roles-empty-text">Este tipo de usuario no tiene permisos configurados en la vista demo</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Usuarios con este tipo -->
    <div class="sa-roles-card">
        <div class="sa-roles-card-header">
            <h2 class="sa-roles-card-title">Usuarios con este Tipo</h2>
        </div>
        <div class="sa-roles-card-body">
            @php
                $usuariosCount = DB::table('usuario')->where('id_tipo_usuario', $tipoUsuario->id_tipo_usuario)->count();
            @endphp
            
            @if($usuariosCount > 0)
            <div class="sa-roles-alert sa-roles-alert-success">
                <span class="material-symbols-outlined">group</span>
                Este tipo de usuario está asignado a <strong>{{ $usuariosCount }}</strong> {{ $usuariosCount == 1 ? 'usuario' : 'usuarios' }}.
            </div>
            @else
            <div class="sa-roles-alert sa-roles-alert-warning">
                <span class="material-symbols-outlined">info</span>
                Este tipo de usuario no está asignado a ningún usuario.
            </div>
            @endif
        </div>
    </div>

    <!-- Información adicional para desarrolladores -->
    <div class="sa-roles-card">
        <div class="sa-roles-card-header">
            <h2 class="sa-roles-card-title">
                <span class="material-symbols-outlined" style="vertical-align: middle; margin-right: 0.5rem;">code</span>
                Para Desarrolladores
            </h2>
        </div>
        <div class="sa-roles-card-body">
            <div style="background: #f8f9fa; padding: 1rem; border-radius: 6px; font-family: monospace; font-size: 0.875rem;">
                <p style="margin: 0 0 0.5rem 0;"><strong>Control de permisos en código:</strong></p>
                <p style="margin: 0 0 0.5rem 0; color: #6c757d;">// Verificar tipo de usuario en controladores:</p>
                <p style="margin: 0; color: #0066cc;">if (auth()->user()->id_tipo_usuario == {{ $tipoUsuario->id_tipo_usuario }}) { ... }</p>
                <br>
                <p style="margin: 0 0 0.5rem 0; color: #6c757d;">// En vistas Blade:</p>
                <p style="margin: 0; color: #0066cc;">@if(auth()->user()->id_tipo_usuario == $tipoUsuario->id_tipo_usuario) ... @endif</p>
            </div>
            
            <div style="margin-top: 1rem;">
                <p style="color: #6c757d; margin: 0;">
                    <strong>Nota:</strong> Para implementar un sistema de permisos dinámico completo, 
                    consulta la documentación sobre la creación de la tabla de permisos y sus relaciones.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
