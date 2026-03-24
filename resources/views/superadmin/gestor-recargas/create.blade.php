@extends('superadmin.layouts.admin')

@section('title', 'Nuevo Gestor de Recargas')

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('superadmin.gestores-recargas.index') }}">Gestores Recargas</a>
            </li>
            <li class="breadcrumb-item active">Nuevo gestor</li>
        </ol>
    </nav>

    <div class="gs-form-wrap">
        <div class="gs-form-card">
            <div class="gs-form-header">
                <div class="icon-wrap">
                    <span class="material-symbols-rounded">person_add</span>
                </div>
                <div>
                    <h2>Nuevo Gestor de Recargas</h2>
                    <p>Complete los datos para registrar al nuevo usuario administrador de recargas.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('superadmin.gestores-recargas.store') }}" novalidate>
                @csrf
                <div class="gs-form-body">

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <ul class="mb-0 small">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- ── Empresa y Documento ──────────────────── --}}
                    <p class="gs-section-title">
                        <span class="material-symbols-rounded">business</span>
                        Asignación y Validación
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Documento <span class="req">*</span></label>
                            <input type="number" name="doc_usuario" value="{{ old('doc_usuario') }}"
                                   class="form-control @error('doc_usuario') is-invalid @enderror"
                                   placeholder="Ej: 1073456789" required>
                            @error('doc_usuario') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Empresa de Recargas <span class="req">*</span></label>
                            <select name="NIT" class="form-select @error('NIT') is-invalid @enderror" required>
                                <option value="" selected disabled>— Seleccione empresa —</option>
                                @foreach($empresasRecarga as $emp)
                                    <option value="{{ $emp->NIT }}" {{ old('NIT') == $emp->NIT ? 'selected' : '' }}>
                                        {{ $emp->nombre_empresa }}
                                    </option>
                                @endforeach
                            </select>
                            @error('NIT') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- ── Información personal ─────────── --}}
                    <p class="gs-section-title">
                        <span class="material-symbols-rounded">person</span>
                        Información Personal
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Primer Nombre <span class="req">*</span></label>
                            <input type="text" name="primer_nombre" value="{{ old('primer_nombre') }}"
                                   class="form-control @error('primer_nombre') is-invalid @enderror" placeholder="Ej: Carlos" required>
                            @error('primer_nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Segundo Nombre</label>
                            <input type="text" name="segundo_nombre" value="{{ old('segundo_nombre') }}"
                                   class="form-control @error('segundo_nombre') is-invalid @enderror" placeholder="Opcional">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Primer Apellido <span class="req">*</span></label>
                            <input type="text" name="primer_apellido" value="{{ old('primer_apellido') }}"
                                   class="form-control @error('primer_apellido') is-invalid @enderror" placeholder="Ej: López" required>
                            @error('primer_apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Segundo Apellido</label>
                            <input type="text" name="segundo_apellido" value="{{ old('segundo_apellido') }}"
                                   class="form-control @error('segundo_apellido') is-invalid @enderror" placeholder="Opcional">
                        </div>
                    </div>

                    {{-- ── Contacto y Ubicación ────────────── --}}
                    <p class="gs-section-title">
                        <span class="material-symbols-rounded">contact_phone</span>
                        Contacto y Ubicación
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Correo <span class="req">*</span></label>
                            <input type="email" name="correo" value="{{ old('correo') }}"
                                   class="form-control @error('correo') is-invalid @enderror" placeholder="gestor@empresa.com" required>
                            @error('correo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" value="{{ old('telefono') }}"
                                   class="form-control @error('telefono') is-invalid @enderror" placeholder="Ej: 3001234567">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ciudad <span class="req">*</span></label>
                            <select name="id_ciudad" class="form-select @error('id_ciudad') is-invalid @enderror" required>
                                <option value="" selected disabled>— Seleccione ciudad —</option>
                                @foreach($ciudades as $ciudad)
                                    <option value="{{ $ciudad->id_ciudad }}" {{ old('id_ciudad') == $ciudad->id_ciudad ? 'selected' : '' }}>
                                        {{ $ciudad->nombre_city }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_ciudad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- ── Credenciales ────────────────── --}}
                    <p class="gs-section-title">
                        <span class="material-symbols-rounded">lock</span>
                        Seguridad
                    </p>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Contraseña <span class="req">*</span></label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror" placeholder="Mínimo 8 caracteres" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmar Contraseña <span class="req">*</span></label>
                            <input type="password" name="password_confirmation"
                                   class="form-control" placeholder="Repita la contraseña" required>
                        </div>
                    </div>

                </div>{{-- /gs-form-body --}}

                <div class="gs-form-footer">
                    <a href="{{ route('superadmin.gestores-recargas.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <span class="material-symbols-rounded">save</span>
                        Crear Gestor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

