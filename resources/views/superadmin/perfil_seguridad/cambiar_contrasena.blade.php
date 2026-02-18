@extends('superadmin.layouts.admin')

@section('title', 'Cambiar Contraseña')

@section('content')
<div class="sa-perfil-container">
    <div class="sa-perfil-breadcrumb">
        <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
        <span>/</span>
        <a href="{{ route('superadmin.perfil.index') }}">Perfil</a>
        <span>/</span>
        <span>Cambiar Contraseña</span>
    </div>

    <div class="sa-perfil-header">
        <div>
            <h1 class="sa-perfil-title">Cambiar Contraseña</h1>
            <p class="sa-perfil-subtitle">Actualiza tu contraseña de acceso al sistema</p>
        </div>
        <a href="{{ route('superadmin.perfil.index') }}" class="sa-perfil-btn sa-perfil-btn-secondary">
            <span class="material-symbols-outlined"></span>
        Volver
        </a>
    </div>

    @if(session('error'))
    <div class="sa-perfil-alert sa-perfil-alert-error">
        <span class="material-symbols-outlined">error</span>
        <div>{{ session('error') }}</div>
    </div>
    @endif

    <form action="{{ route('superadmin.perfil.actualizar-contrasena') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="sa-perfil-card">
            <div class="sa-perfil-card-header">
                <h2 class="sa-perfil-card-title">
                    <span class="material-symbols-outlined"></span>
                    Nueva Contraseña
                </h2>
            </div>
            <div class="sa-perfil-card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="sa-perfil-form-group">
                            <label for="password_actual" class="sa-perfil-form-label required">Contraseña Actual</label>
                            <input type="password" 
                                   id="password_actual" 
                                   name="password_actual" 
                                   class="sa-perfil-form-control @error('password_actual') is-invalid @enderror"
                                   required>
                            @error('password_actual')
                            <div class="sa-perfil-invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="sa-perfil-form-group">
                            <label for="password" class="sa-perfil-form-label required">Nueva Contraseña</label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="sa-perfil-form-control @error('password') is-invalid @enderror"
                                   required>
                            @error('password')
                            <div class="sa-perfil-invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="sa-perfil-form-group">
                            <label for="password_confirmation" class="sa-perfil-form-label required">Confirmar Nueva Contraseña</label>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   class="sa-perfil-form-control"
                                   required>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="sa-perfil-password-requirements">
                            <strong style="display: block; margin-bottom: 0.5rem; color: #495057;">La contraseña debe cumplir:</strong>
                            <ul>
                                <li>Mínimo 8 caracteres</li>
                                <li>Al menos una letra mayúscula</li>
                                <li>Al menos una letra minúscula</li>
                                <li>Al menos un número</li>
                                <li>Al menos un símbolo especial (!@#$%^&*)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="sa-perfil-card">
            <div class="sa-perfil-card-body">
                <div class="sa-perfil-alert sa-perfil-alert-warning">
                    <span class="material-symbols-outlined"></span>
                    <div>
                        <strong>Importante:</strong> Al cambiar tu contraseña, deberás iniciar sesión nuevamente.
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('superadmin.perfil.index') }}" class="sa-perfil-btn sa-perfil-btn-secondary">
                        <span class="material-symbols-outlined"></span>
                        Cancelar
                    </a>
                    <button type="submit" class="sa-perfil-btn sa-perfil-btn-success">
                        <span class="material-symbols-outlined"></span>
                        Cambiar Contraseña
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
