@extends('superadmin.layouts.admin')

@section('title', 'Tipos de Usuario')

@section('content')
<div class="sa-roles-container">
    <!-- Header -->
    <div class="sa-roles-header">
        <div>
            <h1 class="sa-roles-title">Tipos de Usuario</h1>
            <p class="sa-roles-subtitle">Administración de tipos de usuario del sistema</p>
        </div>
        <a href="{{ route('superadmin.roles.create') }}" class="sa-roles-btn sa-roles-btn-primary">
            <span class="material-symbols-outlined">add</span>
            Crear Tipo de Usuario
        </a>
    </div>

    <!-- Alertas -->
    @if(session('success'))
    <div class="sa-roles-alert sa-roles-alert-success">
        <span class="material-symbols-outlined">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="sa-roles-alert sa-roles-alert-error">
        <span class="material-symbols-outlined">error</span>
        {{ session('error') }}
    </div>
    @endif

    @if(session('warning'))
    <div class="sa-roles-alert sa-roles-alert-warning">
        <span class="material-symbols-outlined">warning</span>
        {{ session('warning') }}
    </div>
    @endif

    <!-- Estadísticas -->
    <div class="sa-roles-stats">
        <div class="sa-roles-stat-card">
            <div class="sa-roles-stat-label">Total de Tipos</div>
            <div class="sa-roles-stat-value">{{ $tiposUsuario->count() }}</div>
        </div>
        <div class="sa-roles-stat-card">
            <div class="sa-roles-stat-label">Total Usuarios</div>
            <div class="sa-roles-stat-value">{{ $tiposUsuario->sum('usuarios_count') }}</div>
        </div>
        <div class="sa-roles-stat-card">
            <div class="sa-roles-stat-label">Permisos Disponibles</div>
            <div class="sa-roles-stat-value">15</div>
        </div>
    </div>

    <!-- Tabla de tipos de usuario -->
    <div class="sa-roles-card">
        <div class="sa-roles-card-header">
            <h2 class="sa-roles-card-title">Lista de Tipos de Usuario</h2>
        </div>
        <div class="sa-roles-card-body">
            @if($tiposUsuario->count() > 0)
            <div class="table-responsive">
                <table class="sa-roles-table table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Usuarios Asignados</th>
                            <th>Permisos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tiposUsuario as $tipo)
                        <tr>
                            <td><strong>#{{ $tipo->id_tipo_usuario }}</strong></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span class="material-symbols-outlined" style="color: #007bff;">shield_person</span>
                                    <strong>{{ $tipo->nombre_tipo }}</strong>
                                </div>
                            </td>
                            <td>
                                @if($tipo->usuarios_count > 0)
                                <span class="sa-roles-badge sa-roles-badge-primary">
                                    {{ $tipo->usuarios_count }} {{ $tipo->usuarios_count == 1 ? 'usuario' : 'usuarios' }}
                                </span>
                                @else
                                <span class="sa-roles-badge sa-roles-badge-secondary">
                                    Sin usuarios
                                </span>
                                @endif
                            </td>
                            <td>
                                <span class="sa-roles-badge sa-roles-badge-success">
                                    @if($tipo->id_tipo_usuario == 1)
                                        Acceso completo
                                    @else
                                        Acceso limitado
                                    @endif
                                </span>
                            </td>
                            <td>
                                <div class="sa-roles-actions">
                                    <a href="{{ route('superadmin.roles.permissions.show', $tipo->id_tipo_usuario) }}" 
                                       class="sa-roles-btn sa-roles-btn-sm sa-roles-btn-secondary"
                                       title="Ver permisos">
                                        <span class="material-symbols-outlined" style="font-size: 1rem;">visibility</span>
                                    </a>
                                    <a href="{{ route('superadmin.roles.edit', $tipo->id_tipo_usuario) }}" 
                                       class="sa-roles-btn sa-roles-btn-sm sa-roles-btn-warning"
                                       title="Editar">
                                        <span class="material-symbols-outlined" style="font-size: 1rem;">edit</span>
                                    </a>
                                    @if($tipo->usuarios_count == 0)
                                    <form action="{{ route('superadmin.roles.destroy', $tipo->id_tipo_usuario) }}" 
                                          method="POST" 
                                          style="display: inline;"
                                          onsubmit="return confirm('¿Está seguro de eliminar este tipo de usuario?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="sa-roles-btn sa-roles-btn-sm sa-roles-btn-danger"
                                                title="Eliminar">
                                            <span class="material-symbols-outlined" style="font-size: 1rem;">delete</span>
                                        </button>
                                    </form>
                                    @else
                                    <button type="button" 
                                            class="sa-roles-btn sa-roles-btn-sm sa-roles-btn-danger"
                                            style="opacity: 0.5; cursor: not-allowed;"
                                            title="No se puede eliminar: tiene usuarios asignados"
                                            disabled>
                                        <span class="material-symbols-outlined" style="font-size: 1rem;">delete</span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <!-- Estado vacío -->
            <div class="sa-roles-empty">
                <div class="sa-roles-empty-icon">
                    <span class="material-symbols-outlined" style="font-size: 5rem;">shield_person</span>
                </div>
                <h3 class="sa-roles-empty-title">No hay tipos de usuario</h3>
                <p class="sa-roles-empty-text">Comienza creando el primer tipo de usuario del sistema</p>
                <a href="{{ route('superadmin.roles.create') }}" class="sa-roles-btn sa-roles-btn-primary">
                    <span class="material-symbols-outlined">add</span>
                    Crear Primer Tipo
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Información adicional -->
    <div class="sa-roles-card">
        <div class="sa-roles-card-header">
            <h2 class="sa-roles-card-title">
                <span class="material-symbols-outlined" style="vertical-align: middle; margin-right: 0.5rem;">info</span>
                Acerca de los Permisos
            </h2>
        </div>
        <div class="sa-roles-card-body">
            <div class="sa-roles-alert sa-roles-alert-info">
                <span class="material-symbols-outlined">lightbulb</span>
                <div>
                    <strong>Nota:</strong> La gestión granular de permisos está disponible como vista de demostración. 
                    Los permisos actualmente se controlan a nivel de tipo de usuario en el código de la aplicación.
                    <br><br>
                    Para implementar un sistema completo de permisos dinámicos, considera agregar la tabla de permisos en el futuro.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
