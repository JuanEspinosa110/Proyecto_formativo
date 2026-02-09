@extends('superadmin.layouts.admin')

@section('content')
<div class="container sa-licencia-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#">Gestión de licencias</a></li>
            <li class="breadcrumb-item active">Renovar Licencia</li>
        </ol>
    </nav>
    <h2 class="sa-licencia-title">Renovar Licencia</h2>
    <p class="text-muted mb-4">Configure la extensión de la licencia para la cuenta empresarial seleccionada.</p>

    <div class="card sa-licencia-card mb-4 border-0 shadow-sm">
        <div class="card-body d-flex align-items-center">
            <div class="sa-licencia-icon-blue me-4">
                <i class="fas fa-shield-alt text-white fs-2"></i>
            </div>
            <div>
                <span class="badge sa-licencia-badge-warning mb-1">EXPIRA PRONTO</span>
                <h4 class="fw-bold mb-1">{{ $licencia->empresa->nombre_empresa }} - {{ $licencia->plan->nombre_plan }}</h4>
                <div class="text-muted small">
                    <i class="far fa-calendar-alt me-1"></i> Expira en: <strong>{{ \Carbon\Carbon::parse($licencia->fecha_vencimiento)->format('M d, Y') }}</strong>
                </div>
                <div class="text-muted small">
                    <i class="fas fa-users me-1"></i> Usuarios activos: <strong>1,240 / 1,500</strong>
                </div>
            </div>
        </div>
    </div>

    <form action="#" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card sa-licencia-card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <h5 class="mb-4"><i class="fas fa-calendar-check text-primary me-2"></i> Detalles de Renovación</h5>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="sa-licencia-label">Nueva Fecha de Vencimiento <span class="text-danger">***</span></label>
                        <input type="date" name="nueva_fecha" class="form-control sa-licencia-input" value="{{ \Carbon\Carbon::parse($licencia->fecha_vencimiento)->addYear()->format('Y-m-d') }}">
                        <small class="text-primary mt-1 d-block">Extensión Recomendada: +1 año ({{ \Carbon\Carbon::parse($licencia->fecha_vencimiento)->addYear()->format('M d, Y') }})</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <label class="sa-licencia-label">Notas de Renovación</label>
                        <textarea name="notas" class="form-control sa-licencia-input" rows="4" placeholder="Agregue detalles sobre el acuerdo de renovación, precios especiales o términos..."></textarea>
                        <div class="text-end small text-muted">0 / 500 caracteres</div>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <a href="{{ route('superadmin.licencias.index') }}" class="btn btn-light px-4">Cancelar</a>
                    <button type="submit" class="btn btn-primary sa-licencia-btn-renew px-4">
                        <i class="fas fa-check me-2"></i> Renovar Licencia
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div class="row g-4">
        <div class="col-md-5">
            <div class="sa-licencia-alert-info p-3 rounded-3 d-flex align-items-start">
                <i class="fas fa-info-circle text-primary mt-1 me-3"></i>
                <div>
                    <h6 class="fw-bold mb-1">Impacto de la renovación</h6>
                    <p class="small text-muted mb-0">Al renovar esta licencia se notificará automáticamente al contacto de facturación de la empresa y se actualizará el acceso a las funciones de su panel de control de inmediato.</p>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="sa-licencia-history-box p-3 rounded-3 d-flex align-items-start h-100 border">
                <i class="fas fa-history text-muted mt-1 me-3"></i>
                <div>
                    <h6 class="fw-bold mb-1 text-muted">Renovaciones anteriores</h6>
                    <p class="small text-muted mb-0">Última renovación: <strong>2 de enero de 2026</strong> por SuperAdmin (ID: #8821). Ver historial completo en informes.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection