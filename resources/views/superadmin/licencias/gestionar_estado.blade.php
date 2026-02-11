@extends('superadmin.layouts.admin')
@section('title', 'Gestionar Estado de Licencia')
@section('content')
<div class="container sa-licencia-container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
            <li class="breadcrumb-item"><a href="#">Licencias</a></li>
            <li class="breadcrumb-item active">Suspender o Cancelar</li>
        </ol>
    </nav>
    
    <div class="mb-4">
        <h2 class="fw-bold h3">Gestionar Estado: {{ $licencia->nombre_empresa }}</h2>
        <p class="text-muted">
            ID: {{ $licencia->id_licencia }} • 
            Cliente desde: {{ \Carbon\Carbon::parse($licencia->fecha_creacion)->format('M Y') }}
        </p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card sa-status-summary-card">
                <div class="card-body">
                    <label class="text-muted small text-uppercase fw-bold">Estado Actual</label>
                    <div class="d-flex align-items-center mt-1">
                        <span class="sa-status-dot bg-success me-2"></span>
                        <span class="fw-bold">{{ $licencia->nombre_estado }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card sa-status-summary-card">
                <div class="card-body">
                    <label class="text-muted small text-uppercase fw-bold">Plan Contratado</label>
                    <div class="fw-bold mt-1 text-truncate">{{ $licencia->nombre_plan }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card sa-status-summary-card">
                <div class="card-body">
                    <label class="text-muted small text-uppercase fw-bold">Usuarios</label>
                    <div class="fw-bold mt-1">1,240 / 1,500</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card sa-status-summary-card">
                <div class="card-body">
                    <label class="text-muted small text-uppercase fw-bold">Vencimiento</label>
                    <div class="fw-bold mt-1 text-danger">{{ \Carbon\Carbon::parse($licencia->fecha_vencimiento)->format('d M Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('superadmin.licencias.actualizar-estado', $licencia->id_licencia) }}" method="POST">
        @csrf
        @method('PATCH')

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="sa-status-option-label w-100">
                    <input type="radio" name="id_estado" value="1" class="d-none" @if(old('id_estado', $licencia->id_estado) == 1) checked @endif>
                    <div class="card sa-status-option-card h-100 border-success shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle text-success fs-3 mb-2"></i>
                            <h6 class="fw-bold">Activa</h6>
                            <p class="small text-muted mb-0">Operación normal. Todos los servicios habilitados.</p>
                        </div>
                    </div>
                </label>
            </div>

            <div class="col-md-3">
                <label class="sa-status-option-label w-100">
                    <input type="radio" name="id_estado" value="3" class="d-none" @if(old('id_estado', $licencia->id_estado) == 3) checked @endif>
                    <div class="card sa-status-option-card h-100 border-warning">
                        <div class="card-body text-center">
                            <i class="fas fa-pause-circle text-warning fs-3 mb-2"></i>
                            <h6 class="fw-bold">Suspendida</h6>
                            <p class="small text-muted mb-0">Acceso restringido temporalmente. Datos preservados.</p>
                        </div>
                    </div>
                </label>
            </div>

            <div class="col-md-3">
                <label class="sa-status-option-label w-100">
                    <input type="radio" name="id_estado" value="21" class="d-none" @if(old('id_estado', $licencia->id_estado) == 21) checked @endif>
                    <div class="card sa-status-option-card h-100 border-info">
                        <div class="card-body text-center">
                            <i class="fas fa-hourglass-half text-info fs-3 mb-2"></i>
                            <h6 class="fw-bold">Vencida</h6>
                            <p class="small text-muted mb-0">Licencia vencida con acceso limitado para regularización.</p>
                        </div>
                    </div>
                </label>
            </div>

            <div class="col-md-3">
                <label class="sa-status-option-label w-100">
                    <input type="radio" name="id_estado" value="14" class="d-none" @if(old('id_estado', $licencia->id_estado) == 14) checked @endif>
                    <div class="card sa-status-option-card h-100 border-danger">
                        <div class="card-body text-center">
                            <i class="fas fa-times-circle text-danger fs-3 mb-2"></i>
                            <h6 class="fw-bold">Cancelada</h6>
                            <p class="small text-muted mb-0">Terminación total del contrato. Eliminación programada.</p>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <div class="card sa-licencia-card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <label class="fw-bold">Motivo de la acción <span class="badge bg-danger-soft text-danger ms-2">OBLIGATORIO</span></label>
                </div>
                <textarea name="motivo" class="form-control" rows="4" placeholder="Proporcione una explicación detallada de por qué se está realizando este cambio de estado (mín. 20 caracteres)...">{{ old('motivo') }}</textarea>
                <div class="text-end small text-muted mt-1">{{ strlen(old('motivo', '')) }} / 500 caracteres</div>
            </div>
        </div>

        <div class="alert alert-warning border-warning d-flex align-items-center mb-4 p-3 rounded-3">
            <i class="fas fa-exclamation-triangle fs-4 me-3"></i>
            <div>
                <strong class="d-block mb-1">Aviso de Riesgo Administrativo</strong>
                <p class="small mb-0 text-muted">Está a punto de modificar el acceso de {{ $licencia->nombre_empresa }}. Esta acción enviará una notificación automática al contacto administrativo del cliente.</p>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3">
            <a href="{{ route('superadmin.licencias.index') }}" class="btn btn-light px-4">Cancelar y Volver</a>
            <button type="submit" class="btn btn-primary px-5 fw-bold sa-btn-confirm-status">
                Confirmar Cambio
            </button>
        </div>
    </form>
</div>
@endsection