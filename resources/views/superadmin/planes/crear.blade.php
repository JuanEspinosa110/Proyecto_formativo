@extends('superadmin.layouts.admin')

@section('title', 'Crear Plan de Licencia')

@section('content')
<div class="container sa-licencia-container">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.planes.index') }}">Planes</a></li>
                <li class="breadcrumb-item active">Crear Plan</li>
            </ol>
        </nav>
        <h2 class="sa-licencia-title">Crear Nuevo Plan de Licencia</h2>
        <p class="text-muted">Complete la información del plan que desea ofrecer</p>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('superadmin.planes.store') }}" method="POST">
        @csrf
        
        <div class="card sa-licencia-card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-layer-group me-2 text-primary"></i>
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
                               value="{{ old('nombre_plan') }}" 
                               placeholder="Ej: PREMIUM, ENTERPRISE" 
                               required>
                        @error('nombre_plan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Nombre descriptivo del plan (único)</small>
                    </div>

                    <div class="col-md-3">
                        <label class="sa-licencia-label">Duración (Meses) *</label>
                        <input type="number" 
                               name="duracion_meses" 
                               class="form-control sa-licencia-input @error('duracion_meses') is-invalid @enderror" 
                               value="{{ old('duracion_meses', 12) }}" 
                               min="1" 
                               max="120" 
                               required>
                        @error('duracion_meses')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Entre 1 y 120 meses</small>
                    </div>

                    <div class="col-md-3">
                        <label class="sa-licencia-label">Precio (COP) *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" 
                                   name="precio" 
                                   class="form-control sa-licencia-input @error('precio') is-invalid @enderror" 
                                   value="{{ old('precio') }}" 
                                   min="0" 
                                   step="0.01" 
                                   required>
                        </div>
                        @error('precio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Precio en pesos colombianos</small>
                    </div>

                    <div class="col-12">
                        <label class="sa-licencia-label">Descripción *</label>
                        <textarea name="descripcion" 
                                  rows="4" 
                                  class="form-control sa-licencia-input @error('descripcion') is-invalid @enderror" 
                                  placeholder="Describa las características y beneficios del plan..."
                                  required>{{ old('descripcion') }}</textarea>
                        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Máximo 500 caracteres</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview -->
        <div class="card sa-licencia-card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-eye me-2 text-primary"></i>
                    Vista Previa
                </h5>
            </div>
            <div class="card-body">
                <div class="card sa-licencia-plan-card" style="max-width: 350px; margin: 0 auto;">
                    <div class="card-body text-center p-4">
                        <h4 class="fw-bold mb-3" id="preview-nombre">NOMBRE DEL PLAN</h4>
                        <div class="sa-licencia-plan-price mb-4" id="preview-precio">$0</div>
                        <small class="text-muted d-block mb-3" id="preview-duracion">12 meses de servicio</small>
                        <p class="text-muted small" id="preview-descripcion">La descripción aparecerá aquí...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('superadmin.planes.index') }}" class="btn btn-light sa-licencia-btn-cancel">
                <i class="fas fa-times me-2"></i>Cancelar
            </a>
            <button type="submit" class="btn btn-primary sa-licencia-btn-next">
                <i class="fas fa-save me-2"></i>Crear Plan
            </button>
        </div>
    </form>
</div>

<script>
// Preview en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const nombreInput = document.querySelector('input[name="nombre_plan"]');
    const precioInput = document.querySelector('input[name="precio"]');
    const duracionInput = document.querySelector('input[name="duracion_meses"]');
    const descripcionInput = document.querySelector('textarea[name="descripcion"]');

    nombreInput.addEventListener('input', () => {
        document.getElementById('preview-nombre').textContent = nombreInput.value || 'NOMBRE DEL PLAN';
    });

    precioInput.addEventListener('input', () => {
        const precio = parseFloat(precioInput.value) || 0;
        document.getElementById('preview-precio').textContent = '$' + precio.toLocaleString('es-CO');
    });

    duracionInput.addEventListener('input', () => {
        document.getElementById('preview-duracion').textContent = (duracionInput.value || 12) + ' meses de servicio';
    });

    descripcionInput.addEventListener('input', () => {
        document.getElementById('preview-descripcion').textContent = descripcionInput.value || 'La descripción aparecerá aquí...';
    });
});
</script>
@endsection
