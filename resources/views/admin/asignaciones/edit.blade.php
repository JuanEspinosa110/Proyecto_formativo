@extends('admin.layouts.app')

@section('title', 'Editar Asignación — SIGU')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex align-items-center gap-3 mb-4 mt-2">
                <a href="{{ route('admin.asignaciones.index') }}" class="btn btn-light rounded-circle p-2 shadow-sm d-flex border">
                    <span class="material-symbols-rounded align-middle">arrow_back</span>
                </a>
                <div>
                    <h1 class="h3 mb-0 text-dark fw-bold">Actualizar Asignación #{{ $asignacion->id_viaje }}</h1>
                    <p class="text-muted small mb-0">Modifica los detalles de la vinculación actual.</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                <div class="card-body p-4 p-md-5">
                    
                    @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4" role="alert">
                        <div class="d-flex align-items-start gap-2">
                            <span class="material-symbols-rounded mt-1">error</span>
                            <div>
                                <strong class="d-block mb-1">Se encontraron errores de validación:</strong>
                                <ul class="mb-0 small ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif

                    <form action="{{ route('admin.asignaciones.update', $asignacion->id_viaje) }}" method="POST">
                        @csrf 
                        @method('PUT')
                        
                        <div class="row g-4">
                            <!-- Conductor -->
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Conductor Responsable <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><span class="material-symbols-rounded text-muted">person</span></span>
                                    <select name="doc_us" class="form-select bg-light border-0 py-2 @error('doc_us') is-invalid @enderror" required>
                                        @foreach($conductores as $c)
                                            <option value="{{ $c->doc_usuario }}" {{ (old('doc_us', $asignacion->doc_us) == $c->doc_usuario) ? 'selected' : '' }}>
                                                {{ $c->primer_nombre }} {{ $c->primer_apellido }} ({{ $c->doc_usuario }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Bus -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Vehículo asignado (Placa) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><span class="material-symbols-rounded text-muted">directions_bus</span></span>
                                    <select name="placa" class="form-select bg-light border-0 py-2 @error('placa') is-invalid @enderror" required>
                                        @foreach($buses as $b)
                                            <option value="{{ $b->placa }}" {{ (old('placa', $asignacion->placa) == $b->placa) ? 'selected' : '' }}>
                                                {{ $b->placa }} - {{ $b->modelo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Ruta -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Ruta de operación <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><span class="material-symbols-rounded text-muted">route</span></span>
                                    <select name="id_ruta" class="form-select bg-light border-0 py-2 @error('id_ruta') is-invalid @enderror" required>
                                        @foreach($rutas as $r)
                                            <option value="{{ $r->id_ruta }}" {{ (old('id_ruta', $asignacion->id_ruta) == $r->id_ruta) ? 'selected' : '' }}>
                                                ID: {{ $r->id_ruta }} - {{ $r->nombre_ruta ?? 'Ruta Actual' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Fecha y Hora -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Fecha y Hora de Inicio <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><span class="material-symbols-rounded text-muted">calendar_today</span></span>
                                    <input type="datetime-local" name="fecha" class="form-control bg-light border-0 py-2 @error('fecha') is-invalid @enderror" value="{{ old('fecha', \Carbon\Carbon::parse($asignacion->fecha)->format('Y-m-d\TH:i')) }}" required>
                                </div>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Estado de la Asignación <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><span class="material-symbols-rounded text-muted">info</span></span>
                                    <select name="id_estado" class="form-select bg-light border-0 py-2 @error('id_estado') is-invalid @enderror" required>
                                        @foreach($estados as $e)
                                            <option value="{{ $e->id_estado }}" {{ (old('id_estado', $asignacion->id_estado) == $e->id_estado) ? 'selected' : '' }}>
                                                {{ $e->nombre_estado }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="mt-5 d-flex gap-3">
                            <a href="{{ route('admin.asignaciones.index') }}" class="btn btn-light px-4 fw-bold border flex-fill py-2">
                                <span class="material-symbols-rounded align-middle me-1">arrow_back</span> Volver
                            </a>
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm flex-fill py-2">
                                <span class="material-symbols-rounded align-middle me-1">save</span> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .input-group-text { border-radius: 0.5rem 0 0 0.5rem !important; }
    .form-select, .form-control { border-radius: 0 0.5rem 0.5rem 0 !important; }
    .card { border-radius: 1.5rem !important; }
</style>
@endsection
