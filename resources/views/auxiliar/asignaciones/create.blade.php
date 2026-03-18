@extends('auxiliar.layouts.app')

@section('title', 'Nueva Asignación — Auxiliar')

@section('content')
<div class="container-fluid pt-0 pb-4">
    
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold">Nueva Asignación</h1>
            <p class="text-muted small mb-0">Asigne un vehículo a una ruta y un conductor para un nuevo viaje.</p>
        </div>
        <a href="{{ route('auxiliar.asignaciones.index') }}" class="btn btn-light btn-sm d-flex align-items-center gap-2 px-3 rounded-pill border shadow-sm">
            <span class="material-symbols-rounded fs-5">arrow_back</span> Volver
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger shadow-sm py-2 mb-4">{{ session('error') }}</div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <form method="POST" action="{{ route('auxiliar.asignaciones.store') }}">
                    @csrf
                    
                    <div class="row g-3">
                        <!-- Vehículo -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Vehículo (Placa) <span class="text-danger">*</span></label>
                            <select name="placa" class="form-select @error('placa') is-invalid @enderror" required>
                                <option value="" selected disabled>Seleccione un bus...</option>
                                @foreach($buses as $b)
                                    <option value="{{ $b->placa }}" {{ old('placa') == $b->placa ? 'selected' : '' }}>
                                        {{ $b->placa }} — Mod: {{ $b->modelo ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('placa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Ruta -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Ruta Operativa <span class="text-danger">*</span></label>
                            <select name="id_ruta" class="form-select @error('id_ruta') is-invalid @enderror" required>
                                <option value="" selected disabled>Seleccione una ruta...</option>
                                @foreach($rutas as $r)
                                    <option value="{{ $r->id_ruta }}" {{ old('id_ruta') == $r->id_ruta ? 'selected' : '' }}>
                                        {{ $r->nombre_ruta }} ({{ $r->origen }} ➔ {{ $r->destino }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_ruta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Conductor -->
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Conductor Asignado <span class="text-danger">*</span></label>
                            <select name="doc_us" class="form-select @error('doc_us') is-invalid @enderror" required>
                                <option value="" selected disabled>Seleccione un conductor...</option>
                                @foreach($conductores as $c)
                                    <option value="{{ $c->doc_usuario }}" {{ old('doc_us') == $c->doc_usuario ? 'selected' : '' }}>
                                        {{ $c->primer_nombre }} {{ $c->primer_apellido }} (Doc: {{ $c->doc_usuario }})
                                    </option>
                                @endforeach
                            </select>
                            @error('doc_us') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Fecha y Hora -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Fecha y Hora de Inicio <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="fecha" class="form-control @error('fecha') is-invalid @enderror" required value="{{ old('fecha') }}">
                            @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted fs-xs">El turno dura 8 horas. No se permiten solapamientos.</small>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Estado de Asignación <span class="text-danger">*</span></label>
                            <select name="id_estado" class="form-select @error('id_estado') is-invalid @enderror" required>
                                @foreach($estados as $e)
                                    <option value="{{ $e->id_estado }}" {{ (old('id_estado') == $e->id_estado || $e->nombre_estado == 'ACTIVO') ? 'selected' : '' }}>
                                        {{ $e->nombre_estado }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 mt-4 d-grid">
                            <button type="submit" class="btn btn-primary fw-bold rounded-pill shadow-sm py-2">
                                <span class="material-symbols-rounded fs-5 align-middle me-1">save</span> Crear Asignación & Iniciar Turno
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
