@extends('controlador-tiempo.layouts.app')

@section('title', 'Ficha de Verificación — ' . ($recorrido->bus->placa ?? 'Recorrido'))

@section('content')
<div class="sigu-fade">
    <div class="sigu-page-hd d-flex justify-content-between align-items-center">
        <div>
            <h1 class="sigu-page-title">Ficha Técnica de Ruta</h1>
            <p class="sigu-page-sub">Información validada mediante código QR</p>
        </div>
        <a href="{{ route('controlador-tiempo.verificacion.scanner') }}" class="btn btn-outline-secondary rounded-pill fw-bold">
            <span class="material-symbols-rounded align-middle fs-5">arrow_back</span> Volver a Escanear
        </a>
    </div>

    <div class="row g-4 mt-2">
        <!-- Tarjeta Principal del Vehículo -->
        <div class="col-md-5 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="bg-dark p-4 text-center text-white">
                    <span class="material-symbols-rounded fs-1 mb-2">directions_bus</span>
                    <h2 class="fw-black mb-0" style="letter-spacing: 2px;">{{ $recorrido->bus->placa }}</h2>
                    <span class="badge bg-primary rounded-pill mt-2">EN RUTA</span>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="bg-light rounded-circle p-2">
                            <span class="material-symbols-rounded text-primary">person</span>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Conductor</span>
                            <span class="fw-bold">{{ $recorrido->conductor->primer_nombre ?? 'N/A' }} {{ $recorrido->conductor->primer_apellido ?? '' }}</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <span class="text-muted small d-block mb-1 fw-bold text-uppercase">Ruta Actual</span>
                        <div class="p-3 bg-light rounded-3">
                            <div class="fw-bold text-dark">{{ $recorrido->ruta->nombre_ruta }}</div>
                            <div class="small text-muted">{{ $recorrido->ruta->barrioOrigen->nombre }} → {{ $recorrido->ruta->barrioDestino->nombre }}</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 border rounded-3 text-center">
                                <span class="d-block text-muted small">Sentido</span>
                                <span class="fw-black text-primary">{{ $recorrido->sentido }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded-3 text-center">
                                <span class="d-block text-muted small">Pasajeros</span>
                                <span class="fw-black text-primary">{{ $recorrido->cantidad_pasajeros }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicadores de Tiempo y Acciones -->
        <div class="col-md-7 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded text-primary">history</span> Cronometría de Operación
                </h5>

                <div class="row g-4">
                    <div class="col-sm-6">
                        <div class="p-4 bg-light rounded-4 border-start border-primary border-4 h-100">
                            <h2 class="fw-black mb-0 text-dark">{{ is_numeric($minutosEnRuta) ? round($minutosEnRuta) : 0 }}</h2>
                            <span class="text-muted fw-bold small text-uppercase">Minutos Transcurridos</span>
                            <div class="mt-2 small text-muted">Desde: {{ \Carbon\Carbon::parse($recorrido->hora_salida)->format('H:i') }}</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        @php
                            $retraso = $minutosEnRuta - 35; // Supongamos 35 min promedio
                        @endphp
                        <div class="p-4 bg-light rounded-4 border-start border-{{ $retraso > 0 ? 'danger' : 'success' }} border-4 h-100">
                            <h2 class="fw-black mb-0 text-{{ $retraso > 0 ? 'danger' : 'success' }}">
                                {{ $retraso > 0 ? '+'.round($retraso) : round($retraso) }}
                            </h2>
                            <span class="text-muted fw-bold small text-uppercase">Diferencia Estimada (En minutos)</span>
                            <div class="mt-2 small text-muted">Respecto al promedio</div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-3">
                    <h5 class="fw-bold mb-3">Acciones de Calidad y Registro</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <form action="{{ route('controlador-tiempo.verificacion.checkpoint', $recorrido->id_recorrido) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold d-flex align-items-center gap-2">
                                <span class="material-symbols-rounded">beenhere</span> Registrar Paso (Checkpoint)
                            </button>
                        </form>
                        
                        <button class="btn btn-outline-danger px-4 py-2 rounded-pill fw-bold d-flex align-items-center gap-2">
                            <span class="material-symbols-rounded">warning</span> Reportar Novedad en Pista
                        </button>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3">
                    <p class="mb-0 small text-dark">
                        <strong>Nota:</strong> Al registrar un checkpoint, se confirma que el bus se encuentra en un punto intermedio de su recorrido bajo las normas de la empresa.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
