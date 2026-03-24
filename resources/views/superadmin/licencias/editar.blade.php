@extends('superadmin.layouts.admin')
@section('title', 'Editar Licencia')

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
            <p class="text-muted">Configure los parámetros, límites y módulos para <strong>{{ $licencia->nombre_empresa }}</strong>.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('superadmin.licencias.index') }}" class="btn btn-outline-secondary sa-licencia-btn-cancel">Cancelar</a>
            <button type="submit" form="edit-license-form" class="btn btn-primary sa-licencia-btn-save">
                <i class="fas fa-save me-2"></i> Guardar Cambios
            </button>
        </div>
    </div>

    <form id="edit-license-form" action="{{ route('superadmin.licencias.update', $licencia->id_licencia) }}" method="POST">
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
                                <input type="text" class="form-control sa-licencia-input bg-light" value="{{ $licencia->nombre_empresa }}" readonly>
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
                                <input id="fecha_inicio" type="date" name="fecha_inicio" class="form-control sa-licencia-input" value="{{ $licencia->fecha_inicio }}" min="{{ \Carbon\Carbon::today()->toDateString() }}">
                            </div>
                            <div class="col-md-6">
                                <label class="sa-licencia-label">Fecha de Expiración</label>
                                <input id="fecha_vencimiento" type="date" name="fecha_vencimiento" class="form-control sa-licencia-input" value="{{ $licencia->fecha_vencimiento }}" min="{{ \Carbon\Carbon::today()->toDateString() }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card sa-licencia-card">
                    <div class="card-body">
                        <h5 class="card-title mb-4"><i class="fas fa-layer-group text-primary me-2"></i> Plan de licencia</h5>
                        <label class="sa-licencia-label mb-3">Selección de Plan</label>
                        <div class="row g-3">
                            @foreach($planes as $plan)
                            <div class="col-md-4">
                                <label class="sa-licencia-plan-select @if($licencia->id_plan == $plan->id_plan) active @endif">
                                    @if($licencia->id_plan == $plan->id_plan)
                                        <span class="badge bg-primary sa-licencia-plan-tag">ACTUAL</span>
                                    @endif
                                    <input id="plan-{{ $plan->id_plan }}" type="radio" name="id_plan" value="{{ $plan->id_plan }}" class="d-none" @if($licencia->id_plan == $plan->id_plan) checked @endif>
                                    <div class="text-center py-2">
                                        <div class="fw-bold">{{ $plan->nombre_plan }}</div>
                                    </div>
                                </label>
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
                            <option value="6" @if($licencia->id_estado == 6) selected @endif>● VENCIDA</option>
                        </select>
                        <button type="button" class="btn btn-light w-100 btn-sm text-muted">
                            <i class="fas fa-history me-1"></i> Gestionar Estado
                        </button>
                    </div>
                </div>

                <div class="card sa-licencia-card">
                    <div class="card-body small">
                        @php
                        $diasRestantes = (int)\Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($licencia->fecha_vencimiento), false);
                        @endphp
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Última renovación:</span>
                            <span class="fw-bold">{{ \Carbon\Carbon::parse($licencia->fecha_inicio)->format('d M, Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Vencimiento en:</span>
                            <span class="@if($diasRestantes < 0) text-danger @elseif($diasRestantes <= 30) text-warning @else text-success @endif fw-bold">{{ abs($diasRestantes) }} días</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.sa-licencia-plan-select').forEach(function(el){
        el.addEventListener('click', function(e){
            e.preventDefault();
            document.querySelectorAll('.sa-licencia-plan-select').forEach(function(x){ x.classList.remove('active'); });
            el.classList.add('active');
            var input = el.querySelector('input[type="radio"]');
            if(input) input.checked = true;
        });
    });
    // Fecha: evitar seleccionar días anteriores a hoy
    var fechaInicio = document.getElementById('fecha_inicio');
    var fechaVenc = document.getElementById('fecha_vencimiento');
    var hoy = new Date().toISOString().split('T')[0];
    if(fechaInicio) fechaInicio.setAttribute('min', hoy);
    if(fechaVenc) fechaVenc.setAttribute('min', hoy);
    // Si el usuario cambia la fecha de inicio, actualizar el min de vencimiento
    if(fechaInicio && fechaVenc){
        fechaInicio.addEventListener('change', function(){
            var val = fechaInicio.value || hoy;
            fechaVenc.min = val;
            if(fechaVenc.value && fechaVenc.value < val) fechaVenc.value = val;
        });
    }
});
</script>
@endsection