@extends('superadmin.layouts.admin')

@section('title', 'Editar Plan')

@section('content')
<div class="container sa-licencia-container">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.planes.index') }}">Planes</a></li>
                <li class="breadcrumb-item active">Editar Plan</li>
            </ol>
        </nav>
        <h2 class="sa-licencia-title">Editar Plan: {{ $plan->nombre_plan }}</h2>
        <p class="text-muted">Modifique la información del plan de licencia</p>
    </div>

    @if($plan->total_licencias > 0)
    <div class="alert alert-danger">
        <i class="fas fa-lock me-2"></i>
        <strong>No se puede editar:</strong> Este plan tiene {{ $plan->total_licencias }} licencia(s) asociada(s). 
        Los planes con licencias activas no pueden ser modificados.
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('superadmin.planes.update', $plan->id_plan) }}" method="POST" {{ $plan->total_licencias > 0 ? 'disabled' : '' }}>
        @csrf
        @method('PUT')
        
        <div class="card sa-licencia-card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2 text-primary"></i>
                    Información del Plan
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="sa-licencia-label">Nombre del Plan *</label>
                        <input type="text" 
                               name="nombre_plan" 
                               class="form-control sa-licencia-input @error('nombre_plan') is-invalid @enderror" 
                               value="{{ old('nombre_plan', $plan->nombre_plan) }}" 
                               {{ $plan->total_licencias > 0 ? 'readonly' : '' }}
                               required>
                        @error('nombre_plan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="sa-licencia-label">Duración (Meses) *</label>
                        <input type="number" 
                               name="duracion_meses" 
                               class="form-control sa-licencia-input @error('duracion_meses') is-invalid @enderror" 
                               value="{{ old('duracion_meses', $plan->duracion_meses) }}" 
                               min="1" 
                               max="120" 
                               {{ $plan->total_licencias > 0 ? 'readonly' : '' }}
                               required>
                        @error('duracion_meses')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="sa-licencia-label">Precio (COP) *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" 
                                   name="precio" 
                                   class="form-control sa-licencia-input @error('precio') is-invalid @enderror" 
                                   value="{{ old('precio', $plan->precio) }}" 
                                   min="0" 
                                   step="0.01" 
                                   {{ $plan->total_licencias > 0 ? 'readonly' : '' }}
                                   required>
                        </div>
                        @error('precio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="sa-licencia-label">Descripción *</label>
                        <textarea name="descripcion" 
                                  rows="4" 
                                  class="form-control sa-licencia-input @error('descripcion') is-invalid @enderror" 
                                  {{ $plan->total_licencias > 0 ? 'readonly' : '' }}
                                  required>{{ old('descripcion', $plan->descripcion) }}</textarea>
                        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="sa-licencia-label">Estado *</label>
                        <select name="id_estado" class="form-select sa-licencia-input" {{ $plan->total_licencias > 0 ? 'disabled' : '' }} required>
                            <option value="1" {{ $plan->id_estado == 1 ? 'selected' : '' }}>Activo</option>
                            <option value="2" {{ $plan->id_estado == 2 ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        <small class="text-muted">Los planes inactivos no aparecerán para nuevas licencias</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="card sa-licencia-card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2 text-primary"></i>
                    Estadísticas del Plan
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <div class="text-muted small mb-1">Total Licencias</div>
                            <div class="h3 fw-bold mb-0">{{ $plan->total_licencias }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <div class="text-muted small mb-1">ID del Plan</div>
                            <div class="h3 fw-bold mb-0">{{ $plan->id_plan }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <div class="text-muted small mb-1">Estado Actual</div>
                            <div class="h5 mb-0">
                                @if($plan->id_estado == 1)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('superadmin.planes.index') }}" class="btn btn-light sa-licencia-btn-cancel">
                <i class="fas fa-times me-2"></i>Cancelar
            </a>
            <button type="submit" class="btn btn-success" {{ $plan->total_licencias > 0 ? 'disabled' : '' }}>
                <i class="fas fa-save me-2"></i>Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection
