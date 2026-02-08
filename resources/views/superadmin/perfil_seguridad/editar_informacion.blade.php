@extends('superadmin.layouts.admin')

@section('title', 'Editar Información Personal')

@section('content')
<div class="sa-perfil-container">
    <div class="sa-perfil-breadcrumb">
        <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
        <span>/</span>
        <a href="{{ route('superadmin.perfil.index') }}">Perfil</a>
        <span>/</span>
        <span>Editar Información</span>
    </div>

    <div class="sa-perfil-header">
        <div>
            <h1 class="sa-perfil-title">Editar Información Personal</h1>
            <p class="sa-perfil-subtitle">Actualiza tus datos personales</p>
        </div>
        <a href="{{ route('superadmin.perfil.index') }}" class="sa-perfil-btn sa-perfil-btn-secondary">
            <span class="material-symbols-outlined">arrow_back</span>
            Volver
        </a>
    </div>

    @if(session('error'))
    <div class="sa-perfil-alert sa-perfil-alert-error">
        <span class="material-symbols-outlined">error</span>
        <div>{{ session('error') }}</div>
    </div>
    @endif

    <form action="{{ route('superadmin.perfil.actualizar-informacion') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="sa-perfil-card">
            <div class="sa-perfil-card-header">
                <h2 class="sa-perfil-card-title">
                    <span class="material-symbols-outlined">edit</span>
                    Información Personal
                </h2>
            </div>
            <div class="sa-perfil-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="sa-perfil-form-group">
                            <label for="nombre" class="sa-perfil-form-label required">Nombre Completo</label>
                            <input type="text" 
                                   id="nombre" 
                                   name="nombre" 
                                   class="sa-perfil-form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre', $superAdmin->nombre) }}"
                                   required>
                            @error('nombre')
                            <div class="sa-perfil-invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="sa-perfil-form-group">
                            <label for="correo" class="sa-perfil-form-label required">Correo Electrónico</label>
                            <input type="email" 
                                   id="correo" 
                                   name="correo" 
                                   class="sa-perfil-form-control @error('correo') is-invalid @enderror"
                                   value="{{ old('correo', $superAdmin->correo) }}"
                                   required>
                            @error('correo')
                            <div class="sa-perfil-invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="sa-perfil-form-group">
                            <label for="telefono" class="sa-perfil-form-label">Teléfono</label>
                            <input type="text" 
                                   id="telefono" 
                                   name="telefono" 
                                   class="sa-perfil-form-control @error('telefono') is-invalid @enderror"
                                   value="{{ old('telefono', $superAdmin->telefono) }}">
                            @error('telefono')
                            <div class="sa-perfil-invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="sa-perfil-form-text">Opcional</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="sa-perfil-form-group">
                            <label class="sa-perfil-form-label">Documento</label>
                            <input type="text" 
                                   class="sa-perfil-form-control"
                                   value="{{ number_format($superAdmin->doc_super_admin, 0, ',', '.') }}"
                                   readonly
                                   style="background-color: #e9ecef;">
                            <small class="sa-perfil-form-text">No se puede modificar</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="sa-perfil-card">
            <div class="sa-perfil-card-body">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('superadmin.perfil.index') }}" class="sa-perfil-btn sa-perfil-btn-secondary">
                        <span class="material-symbols-outlined">close</span>
                        Cancelar
                    </a>
                    <button type="submit" class="sa-perfil-btn sa-perfil-btn-success">
                        <span class="material-symbols-outlined">save</span>
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
