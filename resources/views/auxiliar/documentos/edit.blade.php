@extends('auxiliar.layouts.app')

@section('title', 'Editar Documento — Auxiliar')

@section('content')
<div class="container-fluid pt-0 pb-4">
    
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold">Editar Documento</h1>
            <p class="text-muted small mb-0">Modifique los datos del documento: {{ $documento->nombre }}.</p>
        </div>
        <a href="{{ route('auxiliar.documentos.index') }}" class="btn btn-light btn-sm d-flex align-items-center gap-2 px-3 rounded-pill border shadow-sm">
            <span class="material-symbols-rounded fs-5">arrow_back</span> Volver
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger shadow-sm py-2 mb-4">{{ session('error') }}</div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <form method="POST" action="{{ route('auxiliar.documentos.update', $documento->id_documento) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <!-- Nombre -->
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nombre del Documento <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" required value="{{ old('nombre') ?? $documento->nombre }}">
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Tipo (Inmutable en edición para mantener integridad) -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Tipo de Documento</label>
                            <input type="text" class="form-control bg-light" value="{{ $documento->tipoDocumento->nombre ?? 'N/A' }}" readonly disabled>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Estado de Vigencia <span class="text-danger">*</span></label>
                            <select name="id_estado" class="form-select @error('id_estado') is-invalid @enderror" required>
                                @foreach($estados as $e)
                                    <option value="{{ $e->id_estado }}" {{ (old('id_estado') ?? $documento->id_estado) == $e->id_estado ? 'selected' : '' }}>
                                        {{ $e->nombre_estado }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Fechas -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Fecha Expedición <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_expedicion" class="form-control @error('fecha_expedicion') is-invalid @enderror" required value="{{ old('fecha_expedicion') ?? \Carbon\Carbon::parse($documento->fecha_expedicion)->format('Y-m-d') }}">
                            @error('fecha_expedicion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Fecha Vencimiento <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror" required value="{{ old('fecha_vencimiento') ?? \Carbon\Carbon::parse($documento->fecha_vencimiento)->format('Y-m-d') }}">
                            @error('fecha_vencimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Archivos -->
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Reemplazar Archivo (Opcional)</label>
                            <input type="file" name="archivo" class="form-control @error('archivo') is-invalid @enderror" accept=".pdf,.png,.jpg,.jpeg">
                            @error('archivo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted fs-xs">Deje vacío para mantener el archivo actual.</small>
                        </div>

                        <div class="col-12 mt-4 d-grid">
                            <button type="submit" class="btn btn-warning fw-bold rounded-pill shadow-sm py-2">
                                <span class="material-symbols-rounded fs-5 align-middle me-1">save</span> Guardar Cambios & Auditar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
