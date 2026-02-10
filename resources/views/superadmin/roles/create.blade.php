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
