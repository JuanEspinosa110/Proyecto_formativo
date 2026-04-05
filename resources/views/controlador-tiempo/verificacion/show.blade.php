@extends('controlador-tiempo.layouts.app')

@section('title', 'Ficha de Verificación — ' . ($recorrido->viaje->placa ?? 'Recorrido'))

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
                    <h2 class="fw-black mb-0" style="letter-spacing: 2px;">{{ $recorrido->viaje->placa }}</h2>
                    <span class="badge bg-primary rounded-pill mt-2">EN RUTA</span>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="bg-light rounded-circle p-2">
                            <span class="material-symbols-rounded text-primary">person</span>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Conductor</span>
                            <span class="fw-bold">{{ $recorrido->viaje->conductor->primer_nombre ?? 'N/A' }} {{ $recorrido->viaje->conductor->primer_apellido ?? '' }}</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <span class="text-muted small d-block mb-1 fw-bold text-uppercase">Ruta Actual</span>
                        <div class="p-3 bg-light rounded-3">
                            <div class="fw-bold text-dark">{{ $recorrido->viaje->ruta->nombre_ruta }}</div>
                            <div class="small text-muted">{{ $recorrido->viaje->ruta->barrioOrigen->nombre }} → {{ $recorrido->viaje->ruta->barrioDestino->nombre }}</div>
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
                                <span class="d-block text-muted small">Evidencia</span>
                                @if($recorrido->foto_torniquete)
                                    <a href="{{ asset('storage/' . $recorrido->foto_torniquete) }}" target="_blank" class="fw-black text-primary text-decoration-none">Ver Foto</a>
                                @else
                                    <span class="fw-black text-muted">N/A</span>
                                @endif
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
                        <div class="p-4 bg-light rounded-4 border-start border-primary border-4 h-100">
                            <h2 class="fw-black mb-0 text-dark">
                                {{ is_numeric($intervaloAnterior) ? round($intervaloAnterior) : '—' }}
                            </h2>
                            <span class="text-muted fw-bold small text-uppercase">Intervalo (Minutos)</span>
                            <div class="mt-2 small text-muted">Respecto al bus anterior</div>
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
                        
                        <button type="button" class="btn btn-outline-warning px-4 py-2 rounded-pill fw-bold d-flex align-items-center gap-2" 
                                data-bs-toggle="modal" data-bs-target="#modalFallaMecanica">
                            <span class="material-symbols-rounded">build</span> Reportar Falla Mecánica
                        </button>

                        <button type="button" class="btn btn-outline-danger px-4 py-2 rounded-pill fw-bold d-flex align-items-center gap-2" 
                                data-bs-toggle="modal" data-bs-target="#modalNovedad">
                            <span class="material-symbols-rounded">warning</span> Novedad en Pista
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

<!-- Modal Reportar Novedad -->
<div class="modal fade" id="modalNovedad" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded">report</span> Reportar Novedad en Pista
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('controlador-tiempo.verificacion.incidencia', $recorrido->id_recorrido) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-danger bg-danger bg-opacity-10 border-0 rounded-3 small mb-4">
                        <span class="fw-bold">Atención:</span> Use este formulario para reportar retrasos significativos, fallas mecánicas menores o incumplimiento de normas del conductor.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">Descripción de la Novedad</label>
                        <textarea name="descripcion" class="form-control border-0 bg-light rounded-3" rows="4" 
                                  placeholder="Describa brevemente lo ocurrido..." required minlength="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Enviar Reporte</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reportar Falla Mecánica -->
<div class="modal fade" id="modalFallaMecanica" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-warning text-dark border-0 py-3">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded">build</span> Reportar Falla Mecánica
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('controlador-tiempo.verificacion.falla', $recorrido->id_recorrido) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Use este formulario si el vehículo presenta averías que comprometen la operación.</p>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">Nivel de Urgencia</label>
                        <select name="nivel_urgencia" class="form-select border-0 bg-light rounded-3" required>
                            <option value="Bajo">Bajo (Solo registro)</option>
                            <option value="Medio">Medio (Solo registro)</option>
                            <option value="Alto">Alto (Finaliza viaje y bloquea bus)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">Descripción de la Falla</label>
                        <textarea name="descripcion" class="form-control border-0 bg-light rounded-3" rows="4" 
                                  placeholder="Detalle el problema mecánico..." required minlength="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark shadow-sm">Reportar Falla</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
