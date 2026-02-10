@extends('superadmin.layouts.admin')

@section('content')
<div class="container sa-licencia-container">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.licencias.index') }}">Licencias</a></li>
                    <li class="breadcrumb-item active">Editar Licencia</li>
                </ol>
            </nav>
            <h2 class="sa-licencia-title">Editar Licencia: {{ $licencia->id_licencia }}</h2>
            <p class="text-muted">Configure los parámetros, límites y módulos para <strong>{{ $licencia->empresa->nombre_empresa }}</strong>.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('superadmin.licencias.index') }}" class="btn btn-outline-secondary sa-licencia-btn-cancel">Cancelar</a>
            <button type="submit" form="edit-license-form" class="btn btn-primary sa-licencia-btn-save">
                <i class="fas fa-save me-2"></i> Guardar Cambios
            </button>
        </div>
    </div>

    <form id="edit-license-form" action="#" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-8">
                <div class="card sa-licencia-card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4"><i class="fas fa-info-circle text-primary me-2"></i> Información General</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="sa-licencia-label">Cliente Responsable</label>
                                <input type="text" class="form-control sa-licencia-input bg-light" value="{{ $licencia->empresa->nombre_empresa }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="sa-licencia-label">Clave de Licencia</label>
                                <div class="input-group">
                                    <input type="text" class="form-control sa-licencia-input bg-light" value="{{ $licencia->id_licencia }}" readonly>
                                    <button class="btn btn-outline-secondary" type="button"><i class="far fa-copy"></i></button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="sa-licencia-label">Fecha de Inicio</label>
                                <input type="date" name="fecha_inicio" class="form-control sa-licencia-input" value="{{ $licencia->fecha_inicio }}">
                            </div>
                            <div class="col-md-6">
                                <label class="sa-licencia-label">Fecha de Expiración</label>
                                <input type="date" name="fecha_vencimiento" class="form-control sa-licencia-input" value="{{ $licencia->fecha_vencimiento }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card sa-licencia-card">
                    <div class="card-body">
                        <h5 class="card-title mb-4"><i class="fas fa-layer-group text-primary me-2"></i> Plan y Límites de Uso</h5>
                        <label class="sa-licencia-label mb-3">Selección de Plan</label>
                        <div class="row g-3">
                            @foreach($planes as $plan)
                            <div class="col-md-4">
                                <div class="sa-licencia-plan-select @if($licencia->id_plan == $plan->id_plan) active @endif">
                                    @if($licencia->id_plan == $plan->id_plan)
                                        <span class="badge bg-primary sa-licencia-plan-tag">ACTUAL</span>
                                    @endif
                                    <input type="radio" name="id_plan" value="{{ $plan->id_plan }}" class="d-none" @if($licencia->id_plan == $plan->id_plan) checked @endif>
                                    <div class="text-center py-2">
                                        <div class="fw-bold">{{ $plan->nombre_plan }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card sa-licencia-card mb-4">
                    <div class="card-body text-center">
                        <label class="sa-licencia-label d-block text-start mb-2">ESTADO DE LICENCIA</label>
                        <select name="id_estado" class="form-select sa-licencia-status-select mb-3">
                            <option value="1" @if($licencia->id_estado == 1) selected @endif>● ACTIVA</option>
                            <option value="3" @if($licencia->id_estado == 3) selected @endif>● SUSPENDIDA</option>
                            <option value="21" @if($licencia->id_estado == 21) selected @endif>● VENCIDA</option>
                        </select>
                        <button type="button" class="btn btn-light w-100 btn-sm text-muted">
                            <i class="fas fa-history me-1"></i> Gestionar Estado
                        </button>
                    </div>
                </div>

                <div class="card sa-licencia-card">
                    <div class="card-body small">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Última renovación:</span>
                            <span class="fw-bold">12 Oct, 2025</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Vencimiento en:</span>
                            <span class="text-danger fw-bold">92 días</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection