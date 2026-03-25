@extends('superadmin.layouts.admin')

@section('title', 'Editar Gestor de Recargas')

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('superadmin.gestores-recargas.index') }}">Gestores Recargas</a>
            </li>
            <li class="breadcrumb-item active">Editar gestor</li>
        </ol>
    </nav>

    <div class="gs-form-wrap">
        <div class="gs-form-card">
            <div class="gs-form-header">
                <div class="icon-wrap">
                    <span class="material-symbols-rounded">edit</span>
                </div>
                <div>
                    <h2>Editar Gestor de Recargas</h2>
                    <p>
                        Documento: 
                        <span class="gs-doc-badge ms-1">
                            <span class="material-symbols-rounded">badge</span>
                            {{ $gestor->doc_usuario }}
                        </span>
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('superadmin.gestores-recargas.update', $gestor->doc_usuario) }}" novalidate>
                @csrf @method('PUT')
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

                    {{-- ── Empresa ─────────────────────────── --}}
                    <p class="gs-section-title">
                        <span class="material-symbols-rounded">business</span>
                        Asignación Laboral
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label">Empresa de Recargas <span class="req">*</span></label>
                            <select name="NIT" class="form-select @error('NIT') is-invalid @enderror" required>
                                <option value="" disabled>— Seleccione empresa —</option>
                                @foreach($empresasRecarga as $emp)
                                    <option value="{{ $emp->NIT }}"
                                        {{ old('NIT', $gestor->NIT) == $emp->NIT ? 'selected' : '' }}>
                                        {{ $emp->nombre_empresa }}
                                    </option>
                                @endforeach
                            </select>
                            @error('NIT') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- ── Información personal ─────────────────── --}}
                    <p class="gs-section-title">
                        <span class="material-symbols-rounded">person</span>
                        Información Personal
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Primer Nombre <span class="req">*</span></label>
                            <input type="text" name="primer_nombre" value="{{ old('primer_nombre', $gestor->primer_nombre) }}"
                                   class="form-control @error('primer_nombre') is-invalid @enderror" required>
                            @error('primer_nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Segundo Nombre</label>
                            <input type="text" name="segundo_nombre" value="{{ old('segundo_nombre', $gestor->segundo_nombre) }}"
                                   class="form-control @error('segundo_nombre') is-invalid @enderror">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Primer Apellido <span class="req">*</span></label>
                            <input type="text" name="primer_apellido" value="{{ old('primer_apellido', $gestor->primer_apellido) }}"
                                   class="form-control @error('primer_apellido') is-invalid @enderror" required>
                            @error('primer_apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Segundo Apellido</label>
                            <input type="text" name="segundo_apellido" value="{{ old('segundo_apellido', $gestor->segundo_apellido) }}"
                                   class="form-control @error('segundo_apellido') is-invalid @enderror">
                        </div>
                    </div>

                    {{-- ── Contacto y ciudad ────────────────────── --}}
                    <p class="gs-section-title">
                        <span class="material-symbols-rounded">contact_phone</span>
                        Contacto y Ubicación
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Correo <span class="req">*</span></label>
                            <input type="email" name="correo" value="{{ old('correo', $gestor->correo) }}"
                                   class="form-control @error('correo') is-invalid @enderror" required>
                            @error('correo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" value="{{ old('telefono', $gestor->telefono) }}"
                                   class="form-control @error('telefono') is-invalid @enderror">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ciudad <span class="req">*</span></label>
                            <select name="id_ciudad" class="form-select @error('id_ciudad') is-invalid @enderror" required>
                                <option value="" disabled>— Seleccione ciudad —</option>
                                @foreach($ciudades as $ciudad)
                                    <option value="{{ $ciudad->id_ciudad }}"
                                        {{ old('id_ciudad', $gestor->id_ciudad) == $ciudad->id_ciudad ? 'selected' : '' }}>
                                        {{ $ciudad->nombre_city }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_ciudad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado <span class="req">*</span></label>
                            <select name="id_estado" class="form-select @error('id_estado') is-invalid @enderror" required>
                                <option value="1" {{ old('id_estado', $gestor->id_estado) == 1 ? 'selected' : '' }}>Activo</option>
                                <option value="2" {{ old('id_estado', $gestor->id_estado) == 2 ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('id_estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- ── Seguridad ─────────── --}}
                    <p class="gs-section-title">
                        <span class="material-symbols-rounded">lock_reset</span>
                        Cambiar Contraseña <small class="text-muted fw-normal">(opcional)</small>
                    </p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Dejar vacío para no cambiar">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation"
                                   class="form-control" placeholder="Repita la nueva contraseña">
                        </div>
                    </div>

                </div>{{-- /gs-form-body --}}

                <div class="gs-form-footer">
                    <a href="{{ route('superadmin.gestores-recargas.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <span class="material-symbols-rounded">save</span>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

