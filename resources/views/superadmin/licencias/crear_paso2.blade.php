@extends('superadmin.layouts.admin')

@section('title', 'Crear Licencia - Paso 2')

@section('content')
<div class="container sa-licencia-container">
    <!-- Header con progreso -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="sa-licencia-title">Seleccionar Plan de Licencia</h2>
                <p class="text-muted">Configure el plan y vigencia para {{ $datos['nombre_empresa'] }}</p>
            </div>
            <div>
                <span class="badge bg-success" style="font-size: 1rem; padding: 0.5rem 1rem;">
                    Paso 2 de 2
                </span>
            </div>
        </div>
        
        <!-- Barra de progreso -->
        <div class="progress" style="height: 4px;">
            <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Información de la empresa -->
    <div class="alert alert-info d-flex justify-content-between align-items-center mb-4">
        <div>
            <strong><i class="fas fa-building me-2"></i>Empresa:</strong> {{ $datos['nombre_empresa'] }}
        </div>
        <div>
            <strong><i class="fas fa-id-card me-2"></i>NIT:</strong> {{ number_format($datos['NIT'], 0, ',', '.') }}
        </div>
    </div>

    <form action="{{ route('superadmin.licencias.store') }}" method="POST">
        @csrf

        <!-- Selección de Planes -->
        <div class="card sa-licencia-card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-layer-group me-2 text-primary"></i> 
                    Planes Disponibles
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @foreach($planes as $plan)
                    <div class="col-md-3">
                        <label class="w-100 h-100" style="cursor: pointer;">
                            <input type="radio" 
                                   name="id_plan" 
                                   value="{{ $plan->id_plan }}" 
                                   class="d-none plan-radio" 
                                   {{ old('id_plan') == $plan->id_plan ? 'checked' : '' }}
                                   required>
                            <div class="card h-100 sa-licencia-plan-card {{ old('id_plan') == $plan->id_plan ? 'sa-licencia-plan-recommended' : '' }}">
                                <div class="card-body text-center p-4">
                                    @if($plan->nombre_plan == 'PREMIUM')
                                        <span class="badge bg-primary mb-3">RECOMENDADO</span>
                                    @endif
                                    
                                    <h4 class="fw-bold mb-3">{{ $plan->nombre_plan }}</h4>
                                    
                                    <div class="sa-licencia-plan-price mb-4">
                                        ${{ number_format($plan->precio, 0, ',', '.') }}
                                    </div>
                                    
                                    <ul class="list-unstyled sa-licencia-feature-list text-start mb-4">
                                        <li><i class="fas fa-check text-primary me-2"></i> {{ $plan->duracion_meses }} meses de servicio</li>
                                        <li><i class="fas fa-check text-primary me-2"></i> Soporte técnico</li>
                                        <li><i class="fas fa-check text-primary me-2"></i> {{ $plan->descripcion }}</li>
                                    </ul>
                                    
                                    <button type="button" class="btn w-100 sa-licencia-btn-select">
                                        Seleccionar Plan
                                    </button>
                                </div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
                @error('id_plan')<div class="text-danger mt-3">{{ $message }}</div>@enderror
            </div>
        </div>

        <!-- Vigencia de la Licencia -->
        <div class="card sa-licencia-card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt me-2 text-primary"></i> 
                    Vigencia de la Licencia
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="sa-licencia-label">Fecha de Inicio *</label>
                        <input type="date" 
                               name="fecha_inicio" 
                               class="form-control sa-licencia-input @error('fecha_inicio') is-invalid @enderror" 
                               value="{{ old('fecha_inicio', date('Y-m-d')) }}" 
                               required>
                        @error('fecha_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="sa-licencia-label">Fecha de Vencimiento *</label>
                        <input type="date" 
                               name="fecha_vencimiento" 
                               class="form-control sa-licencia-input @error('fecha_vencimiento') is-invalid @enderror" 
                               value="{{ old('fecha_vencimiento') }}" 
                               required>
                        @error('fecha_vencimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mt-3 small text-muted">
                    <i class="fas fa-info-circle me-1"></i> 
                    La licencia se activará automáticamente en la fecha de inicio.
                </div>
            </div>
        </div>

        <!-- Botones de navegación -->
        <div class="d-flex justify-content-between">
            <a href="{{ route('superadmin.licencias.create') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver al Paso 1
            </a>
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-check me-2"></i>Crear Licencia
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activar selección de plan visualmente
    const planRadios = document.querySelectorAll('.plan-radio');
    
    planRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Remover clase activa de todos
            document.querySelectorAll('.sa-licencia-plan-card').forEach(card => {
                card.classList.remove('sa-licencia-plan-recommended');
            });
            
            // Agregar clase activa al seleccionado
            const card = this.closest('.sa-licencia-plan-card');
            card.classList.add('sa-licencia-plan-recommended');
        });
    });
    
    // Auto-calcular fecha de vencimiento según plan seleccionado
    const fechaInicio = document.querySelector('input[name="fecha_inicio"]');
    const fechaVencimiento = document.querySelector('input[name="fecha_vencimiento"]');
    
    planRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (fechaInicio.value) {
                calcularFechaVencimiento();
            }
        });
    });
    
    fechaInicio.addEventListener('change', calcularFechaVencimiento);
    
    function calcularFechaVencimiento() {
        const planSeleccionado = document.querySelector('.plan-radio:checked');
        if (!planSeleccionado || !fechaInicio.value) return;
        
        const mesesPlan = {
            '1': 12,  // Básico
            '2': 12,  // Profesional
            '3': 12,  // Premium
            '4': 24   // Enterprise
        };
        
        const meses = mesesPlan[planSeleccionado.value] || 12;
        const fecha = new Date(fechaInicio.value);
        fecha.setMonth(fecha.getMonth() + meses);
        
        const year = fecha.getFullYear();
        const month = String(fecha.getMonth() + 1).padStart(2, '0');
        const day = String(fecha.getDate()).padStart(2, '0');
        
        fechaVencimiento.value = `${year}-${month}-${day}`;
    }
});
</script>
@endsection
