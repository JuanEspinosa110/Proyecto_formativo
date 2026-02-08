@extends('superadmin.layouts.admin')

@section('title', 'Crear Tipo de Usuario')

@section('content')
<div class="sa-roles-container">
    <!-- Breadcrumb -->
    <div class="sa-roles-breadcrumb">
        <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
        <span>/</span>
        <a href="{{ route('superadmin.roles.index') }}">Tipos de Usuario</a>
        <span>/</span>
        <span>Crear</span>
    </div>

    <!-- Header -->
    <div class="sa-roles-header">
        <div>
            <h1 class="sa-roles-title">Crear Nuevo Tipo de Usuario</h1>
            <p class="sa-roles-subtitle">Define un nuevo tipo de usuario para el sistema</p>
        </div>
        <a href="{{ route('superadmin.roles.index') }}" class="sa-roles-btn sa-roles-btn-secondary">
            <span class="material-symbols-outlined">arrow_back</span>
            Volver
        </a>
    </div>

    <!-- Formulario -->
    <form action="{{ route('superadmin.roles.store') }}" method="POST">
        @csrf

        <div class="sa-roles-card">
            <div class="sa-roles-card-header">
                <h2 class="sa-roles-card-title">Información del Tipo de Usuario</h2>
            </div>
            <div class="sa-roles-card-body">
                <div class="row">
                    <!-- Nombre del tipo -->
                    <div class="col-md-6">
                        <div class="sa-roles-form-group">
                            <label for="nombre_tipo" class="sa-roles-form-label required">Nombre del Tipo</label>
                            <input type="text" 
                                   id="nombre_tipo" 
                                   name="nombre_tipo" 
                                   class="sa-roles-form-control @error('nombre_tipo') is-invalid @enderror" 
                                   value="{{ old('nombre_tipo') }}"
                                   placeholder="Ej: Supervisor, Conductor, Operador, etc."
                                   required>
                            @error('nombre_tipo')
                            <div class="sa-roles-invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="sa-roles-form-text">Nombre único para identificar el tipo de usuario</small>
                        </div>
                    </div>

                    <!-- Descripción (opcional - campo informativo) -->
                    <div class="col-12">
                        <div class="sa-roles-form-group">
                            <label for="descripcion" class="sa-roles-form-label">Descripción (Informativo)</label>
                            <textarea id="descripcion" 
                                      name="descripcion" 
                                      class="sa-roles-form-control @error('descripcion') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Describe las responsabilidades y alcance de este tipo de usuario">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                            <div class="sa-roles-invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="sa-roles-form-text">Nota: Este campo es solo informativo y no se guarda en la base de datos actual</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permisos (Vista de demostración) -->
        <div class="sa-roles-card">
            <div class="sa-roles-card-header">
                <h2 class="sa-roles-card-title">Permisos Disponibles (Vista Demo)</h2>
            </div>
            <div class="sa-roles-card-body">
                <div class="sa-roles-alert sa-roles-alert-info">
                    <span class="material-symbols-outlined">info</span>
                    <div>
                        <strong>Vista de Demostración:</strong> Los permisos que se muestran a continuación son solo visuales. 
                        Los permisos reales se controlan actualmente en el código de la aplicación según el tipo de usuario.
                        <br><br>
                        Para habilitar la gestión dinámica de permisos, será necesario implementar la tabla de permisos en el futuro.
                    </div>
                </div>

                <div class="sa-roles-permissions-section">
                    @foreach($permissions as $module => $modulePermissions)
                    <div class="sa-roles-permissions-module">
                        <h3 class="sa-roles-permissions-module-title">
                            <span class="material-symbols-outlined" style="vertical-align: middle; margin-right: 0.5rem;">folder</span>
                            {{ $module }}
                        </h3>
                        <div class="sa-roles-permissions-grid">
                            @foreach($modulePermissions as $permission)
                            <div class="sa-roles-permission-item" style="opacity: 0.6; cursor: not-allowed;">
                                <input type="checkbox" 
                                       id="permission_{{ $permission->id }}" 
                                       class="sa-roles-permission-checkbox"
                                       disabled>
                                <label for="permission_{{ $permission->id }}" class="sa-roles-permission-label">
                                    <div>{{ $permission->name }}</div>
                                    @if($permission->description)
                                    <div class="sa-roles-permission-description">{{ $permission->description }}</div>
                                    @endif
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="sa-roles-card">
            <div class="sa-roles-card-body">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('superadmin.roles.index') }}" class="sa-roles-btn sa-roles-btn-secondary">
                        <span class="material-symbols-outlined">close</span>
                        Cancelar
                    </a>
                    <button type="submit" class="sa-roles-btn sa-roles-btn-success">
                        <span class="material-symbols-outlined">save</span>
                        Guardar Tipo de Usuario
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
