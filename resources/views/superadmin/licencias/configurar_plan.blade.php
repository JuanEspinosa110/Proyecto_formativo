@extends('superadmin.layouts.admin')

@section('content')
<div class="container sa-licencia-container">
    <div class="mb-4">
        <h2 class="sa-licencia-title">Planes de Licencia Disponibles</h2>
        <p class="text-muted">Configure el nivel de servicio y la vigencia del contrato para la empresa seleccionada.</p>
    </div>

    <div class="alert alert-info sa-licencia-alert-info d-flex justify-content-between align-items-center mb-4">
        <div>
            <strong>Empresa solicitante:</strong> {{ $empresa_nombre }} (NIT: {{ $empresa_nit }})
        </div>
        <i class="fas fa-check-circle text-success fs-4"></i>
    </div>

    <div class="row g-4 mb-5">
        @foreach($planes as $plan)
        <div class="col-md-4">
            <div class="card sa-licencia-plan-card h-100 {{ $plan->nombre_plan == 'PREMIUM' ? 'sa-licencia-plan-recommended' : '' }}">
                <div class="card-body text-center p-4">
                    @if($plan->nombre_plan == 'PREMIUM')
                        <span class="badge bg-primary mb-3">RECOMENDADO</span>
                    @endif
                    <h4 class="fw-bold">{{ $plan->nombre_plan }}</h4>
                    <h2 class="sa-licencia-plan-price mb-4">${{ number_format($plan->precio, 0) }}</h2>
                    <ul class="list-unstyled sa-licencia-feature-list mb-5 text-start">
                        <li><i class="fas fa-check text-primary me-2"></i> {{ $plan->duracion_meses }} meses de servicio</li>
                        <li><i class="fas fa-check text-primary me-2"></i> Soporte prioritario</li>
                        <li><i class="fas fa-check text-primary me-2"></i> {{ $plan->descripcion }}</li>
                    </ul>
                    <button class="btn w-100 sa-licencia-btn-select">Seleccionar Plan</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card sa-licencia-card mb-4">
        <div class="card-body py-4">
            <h5 class="mb-4"><i class="fas fa-calendar-alt me-2 text-primary"></i> Vigencia de la Licencia</h5>
            <div class="row">
                <div class="col-md-6">
                    <label class="sa-licencia-label">Fecha de Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control sa-licencia-input">
                </div>
                <div class="col-md-6">
                    <label class="sa-licencia-label">Fecha de Vencimiento</label>
                    <input type="date" name="fecha_vencimiento" class="form-control sa-licencia-input">
                </div>
            </div>
            <div class="mt-3 small text-muted">
                <i class="fas fa-info-circle me-1"></i> La licencia se activará automáticamente en la fecha estipulada.
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('superadmin.licencias.index') }}" class="btn btn-link text-decoration-none text-muted">← Cancelar cambios</a>
        <button class="btn btn-primary sa-licencia-btn-save px-4">Guardar Plan de Licencia</button>
    </div>
</div>
@endsection