@extends('conductor.layouts.app')

@section('title', 'Dashboard Conductor')

@push('styles')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<link rel="stylesheet" href="{{ asset('css/conductor-dashboard.css') }}">
@endpush

@section('content')

@if($errors->any())
<div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 mb-4 d-flex align-items-center gap-4">
    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
        <span class="material-symbols-rounded fs-1">error</span>
    </div>
    <div class="flex-grow-1">
        <h5 class="fw-bold mb-1">Error al procesar la solicitud</h5>
        <ul class="mb-0 text-danger" style="padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

@if($licenciaVencida || $licenciaProxima)
<div class="alert {{ $licenciaVencida ? 'alert-danger' : 'alert-warning' }} border-0 shadow-sm rounded-4 p-4 mb-4 d-flex align-items-center gap-4">
    <div class="bg-{{ $licenciaVencida ? 'danger' : 'warning' }} bg-opacity-10 text-{{ $licenciaVencida ? 'danger' : 'warning' }} p-3 rounded-circle">
        <span class="material-symbols-rounded fs-1">warning</span>
    </div>
    <div class="flex-grow-1">
        <h5 class="fw-bold mb-1">Alerta Documental Crítica</h5>
        <p class="mb-0">
            Su licencia o documentos del vehículo están @if($licenciaVencida) <strong class="text-danger">VENCIDOS</strong> @else <strong>próximos a vencer</strong> @endif. 
            Debe dirigirse a las oficinas para renovarlos de inmediato. 
            @if($licenciaVencida) <br><strong class="text-danger"><span class="material-symbols-rounded fs-6 align-middle">block</span> Bloqueo activo: No puede iniciar labores.</strong> @endif
        </p>
    </div>
</div>
@endif

@if($asignacionActiva && $fallasBus->count() > 0)
<div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 mb-4 d-flex align-items-center gap-4">
    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
        <span class="material-symbols-rounded fs-1">car_crash</span>
    </div>
    <div class="flex-grow-1">
        <h5 class="fw-bold mb-1">Vehículo con Fallas Críticas Registradas</h5>
        <p class="mb-0">
            El vehículo asignado (<strong>{{ $asignacionActiva->placa }}</strong>) reporta averías mecánicas pendientes. 
            Verifique la ficha técnica del autobús o contacte con Propietarios/Lideres.
        </p>
    </div>
</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <h4 class="fw-bold text-dark mb-0">Control de Jornada Laboral</h4>
    
    <ul class="nav nav-pills bg-white p-1 rounded-pill shadow-sm">
        <li class="nav-item">
            <a class="nav-link {{ request('view') != 'calendario' ? 'active' : '' }}" href="{{ route('conductor.dashboard') }}">
                <span class="material-symbols-rounded fs-6 align-middle">dashboard</span> Operación Activa
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('view') == 'calendario' ? 'active' : '' }}" href="{{ route('conductor.dashboard', ['view' => 'calendario']) }}">
                <span class="material-symbols-rounded fs-6 align-middle">calendar_month</span> Calendario
            </a>
        </li>
    </ul>
</div>

@if(request('view') != 'calendario')
<!-- ============================================== -->
<!-- VISTA OPERACIÓN (POR DEFECTO) -->
<!-- ============================================== -->
<div class="row g-4 mb-5">
    @if($asignacionActiva)
        @php
            $enCurso = $asignacionActiva->id_estado == 12; // EN CURSO
            $vencido = $asignacionActiva->id_estado == 6; // Vencido/No ejecutado
        @endphp
        <div class="col-md-8 col-xl-9">
            <div class="row g-3 h-100">
                <!-- Tarjeta Principal de Turno -->
                <div class="col-12">
                    <div class="card bg-white border-0 shadow-sm rounded-4 p-4 status-card {{ $enCurso ? 'card-working' : ($vencido ? 'border border-danger border-2' : 'card-active') }}">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-uppercase fw-bold text-{{ $enCurso ? 'primary' : ($vencido ? 'danger' : 'success') }} d-inline-block small label-turno">
                                <span class="material-symbols-rounded fs-6 align-middle">{{ $enCurso ? 'sensors' : ($vencido ? 'error' : 'event_available') }}</span> 
                                {{ $enCurso ? 'EN SERVICIO - JORNADA INICIADA' : ($vencido ? 'TURNO VENCIDO - NO EJECUTADO' : 'PROGRAMADO PARA HOY') }}
                            </span>
                            @if($enCurso)
                                <div class="spinner-grow text-primary spinner-grow-sm" role="status"><span class="visually-hidden">Loading...</span></div>
                            @endif
                        </div>
                        
                        <div class="row align-items-center">
                            <div class="col-sm-3 text-center text-sm-start mb-3 mb-sm-0 border-end">
                                <span class="text-muted d-block small fw-medium text-uppercase letter-spacing-1">Ruta</span>
                                <h3 class="fw-bold mb-0 text-dark">{{ $asignacionActiva->ruta->nombre_ruta ?? 'N/A' }}</h3>
                            </div>
                            <div class="col-sm-3 text-center text-sm-start mb-3 mb-sm-0 border-end">
                                <span class="text-muted d-block small fw-medium text-uppercase letter-spacing-1">Vehículo</span>
                                <h3 class="fw-bold mb-0 text-dark">{{ $asignacionActiva->placa }}</h3>
                            </div>
                            <div class="col-sm-3 text-center text-sm-start mb-3 mb-sm-0 border-end">
                                <span class="text-muted d-block small fw-medium text-uppercase letter-spacing-1">Horario</span>
                                <div class="d-flex align-items-center gap-1 justify-content-center justify-content-sm-start">
                                    <h5 class="fw-bold mb-0 text-dark">{{ \Carbon\Carbon::parse($asignacionActiva->fecha)->format('H:i') }} - {{ \Carbon\Carbon::parse($asignacionActiva->fecha)->addHours(8)->format('H:i') }}</h5>
                                </div>
                            </div>
                            <div class="col-sm-3 text-center text-sm-start">
                                <span class="text-muted d-block small fw-medium text-uppercase letter-spacing-1">Opciones</span>
                                
                                @if(!$enCurso && !$vencido)
                                    @php
                                        $horaProgramada = \Carbon\Carbon::parse($asignacionActiva->fecha);
                                        $ahora = now();
                                        $puedeIniciar = $ahora->between($horaProgramada->copy()->subMinutes(30), $horaProgramada->copy()->addHours(4));
                                    @endphp
                                    <form action="{{ route('conductor.iniciarTurno', $asignacionActiva->id_viaje) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success rounded-pill fw-bold w-100 shadow-sm mt-1" {{ $licenciaVencida || !$puedeIniciar ? 'disabled' : '' }}>
                                            <span class="material-symbols-rounded fs-6 align-middle">power_settings_new</span> Iniciar Turno
                                        </button>
                                        @if(!$puedeIniciar)
                                            <small class="text-danger d-block mt-1 text-center">Permitido: 30m antes o 4h después</small>
                                        @endif
                                    </form>
                                @elseif($vencido)
                                    <div class="mt-1">
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill w-100 d-flex align-items-center justify-content-center gap-1 fw-bold">
                                            <span class="material-symbols-rounded fs-6">lock_clock</span> EXPIRADO
                                        </span>
                                        <small class="text-muted d-block mt-1 text-center" style="font-size: 0.75rem;">No se inició en las 4h permitidas.</small>
                                    </div>
                                @else
                                    @php
                                        $horaInicio = \Carbon\Carbon::parse($asignacionActiva->fecha);
                                        $puedeFinalizar = now()->greaterThan($horaInicio->copy()->addMinutes(30));
                                    @endphp
                                    <form action="{{ route('conductor.finalizarTurno', $asignacionActiva->id_viaje) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger rounded-pill fw-bold w-100 shadow-sm mt-1" {{ $recorridoActivo || !$puedeFinalizar ? 'disabled' : '' }}>
                                            <span class="material-symbols-rounded fs-6 align-middle">stop_circle</span> Finalizar Turno
                                        </button>
                                        @if(!$puedeFinalizar)
                                            <small class="text-danger d-block mt-1 text-center">Disponible 30m después del inicio</small>
                                        @endif
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta Operación en Curso (Aparece al iniciar turno) -->
                @if($enCurso)
                <div class="col-12 mt-3">
                    <div class="card {{ $recorridoActivo ? 'bg-white border text-dark' : 'bg-dark text-white' }} border-0 shadow rounded-4 p-4 text-center">
                        <span class="{{ $recorridoActivo ? 'text-primary' : 'text-white-50' }} text-uppercase fw-bold small mb-2 d-block">Control y Validación en Pista</span>
                        
                        @if(!$recorridoActivo)
                            <h4 class="fw-bold mb-3 mt-2 text-dark">Ningún recorrido en progreso</h4>
                            <button class="btn btn-primary px-5 py-3 mt-2 fw-bold text-white rounded-pill btn-lg shadow-sm d-inline-flex justify-content-center align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalIniciarRuta">
                                <span class="material-symbols-rounded fs-3">play_arrow</span> <span class="fs-btn-route">Iniciar Nuevo Recorrido</span>
                            </button>
                            <p class="text-muted small mt-3 mb-0">Presione el botón justo al salir del punto de partida</p>
                        @else
                            <div class="text-center py-2">
                                <div class="d-flex justify-content-center align-items-center gap-3 mb-3">
                                    <h5 class="fw-bold mb-0 text-primary d-inline-flex align-items-center gap-2 title-large">
                                        <span class="spinner-grow text-success spinner-small" role="status"></span>
                                        Recorrido en Progreso
                                    </h5>
                                    <button class="btn btn-dark rounded-pill px-3 py-2 d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalQRViaje">
                                        <span class="material-symbols-rounded">qr_code_2</span> 
                                        <span class="small fw-bold">Ver QR de Viaje</span>
                                    </button>
                                </div>

                                <div class="bg-light p-4 rounded-4 my-3 mx-auto box-route-dashed" style="max-width: 450px;">
                                    <p class="mb-2 text-muted fw-bold text-uppercase small letter-spacing-1">Dirigiéndose en sentido</p>
                                    <h2 class="fw-black text-dark mb-0 d-flex align-items-center justify-content-center gap-2">
                                        <span class="material-symbols-rounded fs-2 text-primary">{{ $recorridoActivo->sentido == 'IDA' ? 'trending_flat' : 'sync_alt' }}</span>
                                        {{ $recorridoActivo->sentido }}
                                    </h2>
                                </div>
                                <p class="mb-4 d-flex justify-content-center align-items-center gap-2 text-muted fs-route-title">
                                    <span class="material-symbols-rounded">schedule</span>
                                    Hora de salida: <strong class="text-dark">{{ \Carbon\Carbon::parse($recorridoActivo->hora_salida)->format('h:i A') }}</strong>
                                </p>
                                
                                <button type="button" data-bs-toggle="modal" data-bs-target="#modalFinalizarRuta" class="btn btn-warning py-3 fw-bold text-dark rounded-pill shadow-sm d-inline-flex border-0 justify-content-center align-items-center gap-2 transition-all w-100" style="font-size: 1.25rem; max-width: 450px;">
                                    <span class="material-symbols-rounded fs-3">sports_score</span> Llegada a Destino Final
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <div class="col-12 mt-4 mt-lg-auto">
                    <!-- Botón Didáctico y grande para adultos mayores -->
                    <button class="btn btn-danger py-2 px-4 shadow rounded-4 d-flex align-items-center justify-content-center gap-2 border-0 bg-opacity-10 transition-transform max-width-falla mx-auto" data-bs-toggle="modal" data-bs-target="#fallaModal">
                        <span class="material-symbols-rounded bg-white text-danger rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm falla-btn-icon">build</span> 
                        <div class="text-start text-white">
                            <span class="d-block fw-bold falla-btn-title">Reportar Daño o Avería</span>
                            <span class="d-block small bg-black bg-opacity-25 px-2 py-1 rounded mt-1 d-inline-block fw-medium">Vehículo: {{ $asignacionActiva->placa }}</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    @elseif($turnoFinalizadoHoy)
        <div class="col-md-8 col-xl-9">
            <div class="card bg-success bg-opacity-10 border border-success border-opacity-25 shadow-sm rounded-4 h-100 p-5 d-flex flex-column justify-content-center text-center">
                <span class="material-symbols-rounded text-success mb-3 icon-large">task_alt</span>
                <h3 class="fw-black text-dark mb-1">Turno Finalizado</h3>
                <p class="text-success fw-bold flex-grow-1">Su jornada ha terminado por hoy. Excelente trabajo.</p>
                <div class="w-100 d-flex justify-content-center flex-wrap gap-4 mt-4 bg-white p-3 rounded-4 shadow-sm">
                    <div><span class="small text-muted d-block text-uppercase fw-bold">Recorridos</span> <h4 class="fw-bold mb-0 text-dark">{{ $recorridosHoy->count() }}</h4></div>
                    <div><span class="small text-muted d-block text-uppercase fw-bold">Tiempo Trabajado</span> <h4 class="fw-bold mb-0 text-success">{{ $tiempoTrabajadoFormato ?? '0h 0m' }}</h4></div>
                </div>
            </div>
        </div>
    @else
        <div class="col-md-8 col-xl-9">
            <div class="card bg-white border-0 shadow-sm rounded-4 h-100 p-4 status-card card-inactive d-flex flex-column justify-content-center text-center">
                <span class="material-symbols-rounded fs-1 text-muted mb-3 opacity-25 scale-large">block</span>
                <h4 class="fw-bold text-dark mb-1">Sin Jornada Activa</h4>
                <p class="text-muted mb-0">Actualmente no posee una ruta activa asignada para el día de hoy.</p>
                <button class="btn btn-danger py-2 px-4 mt-4 mx-auto fw-bold text-white shadow-sm rounded-pill d-flex align-items-center justify-content-center gap-2 border-0 max-width-falla" data-bs-toggle="modal" data-bs-target="#fallaModal">
                    <span class="material-symbols-rounded bg-white text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm falla-btn-small-icon">build</span> 
                    <span class="fs-btn-route">Reportar Daño del Bus</span>
                </button>
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
        <h5 class="fw-bold d-flex align-items-center gap-2 mb-4 text-dark">
            <span class="material-symbols-rounded text-primary">folder_open</span> 
            Vigencia de Documentos
        </h5>

        <div class="document-list">
            @forelse($documentos as $doc)
                @php
                    $estado = $doc->estado_expiracion;

                    if ($estado == 'VENCIDO') {
                        $color = 'danger';
                        $icon = 'cancel';
                    } elseif ($estado == 'PRÓXIMO A VENCER') {
                        $color = 'warning';
                        $icon = 'warning';
                    } else {
                        $color = 'success';
                        $icon = 'check_circle';
                    }

                    $fechaVence = \Carbon\Carbon::parse($doc->fecha_vencimiento)->startOfDay();
                    $hoy = now()->startOfDay();
                    $diasRestantes = $hoy->diffInDays($fechaVence, false);

                    $textoDias = '';
                    if ($diasRestantes > 0 && $diasRestantes <= 30) {
                        $textoDias = "Faltan {$diasRestantes} días";
                    } elseif ($diasRestantes == 0) {
                        $textoDias = "Vence hoy";
                    } elseif ($diasRestantes < 0) {
                        $textoDias = abs($diasRestantes) . " días vencido";
                    }
                @endphp

                <div class="doc-card doc-{{ $color }}">
                    <div class="doc-icon">
                        <span class="material-symbols-rounded">{{ $icon }}</span>
                    </div>

                    <div class="doc-content">
                        <h6>{{ $doc->tipoDocumento->nombre ?? 'Documento' }}</h6>

                        <small>
                            Vence: {{ $doc->fecha_vencimiento->format('d M Y') }}
                        </small>

                        @if($textoDias)
                            <span class="doc-time text-{{ $color }}">
                                {{ $textoDias }}
                            </span>
                        @endif
                    </div>

                    <div class="doc-status">
                        {{ $estado }}
                    </div>
                </div>

            @empty
                <div class="text-center py-5 text-muted">
                    <span class="material-symbols-rounded fs-1 opacity-25">inventory_2</span>
                    <p class="mt-2">No hay documentos registrados</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-6 mb-4 mb-lg-0">
        <div class="card p-4 rounded-4 shadow-sm border-0 bg-white h-100">
            <h5 class="fw-bold text-dark border-bottom pb-3 mb-3 d-flex align-items-center gap-2">
                <span class="material-symbols-rounded text-primary">analytics</span> Historial Recorridos (Trazabilidad)
                <a href="{{ route('conductor.recorridos') }}" class="btn btn-sm btn-link text-decoration-none ms-auto fw-bold">Ver Todo</a>
            </h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle border-top border-bottom">
                    <thead class="bg-light text-muted small">
                        <tr>
                            <th class="ps-3 border-0">FECHA / SENTIDO</th>
                            <th class="border-0">TIEMPOS</th>
                            <th class="border-0 text-center">EVIDENCIA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historialRecorridos as $rec)
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-light text-dark border px-2 py-1 mb-1">{{ \Carbon\Carbon::parse($rec->hora_salida)->format('d/m') }}</span>
                                    <div class="small fw-bold text-muted">{{ $rec->sentido ?? 'IDA' }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark small">{{ \Carbon\Carbon::parse($rec->hora_salida)->format('H:i') }} - @if($rec->hora_llegada) <span class="text-success">{{ \Carbon\Carbon::parse($rec->hora_llegada)->format('H:i') }}</span> @else <span class="badge bg-warning text-dark spinner-grow spinner-grow-sm" role="status"></span> @endif</div>
                                </td>
                                <td class="text-center">
                                    @if($rec->foto_torniquete)
                                        <a href="{{ asset('storage/' . $rec->foto_torniquete) }}" target="_blank" class="text-decoration-none">Ver Foto</a>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-4 text-muted small">No hay registros de recorridos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card p-4 rounded-4 shadow-sm border-0 bg-white h-100">
            <h5 class="fw-bold text-dark border-bottom pb-3 mb-3 d-flex align-items-center gap-2">
                <span class="material-symbols-rounded text-danger">car_repair</span> Fallas Reportadas
                <a href="{{ route('conductor.fallas') }}" class="btn btn-sm btn-link text-decoration-none ms-auto fw-bold">Ver Todo</a>
            </h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle border-top border-bottom">
                    <thead class="bg-light text-muted small">
                        <tr>
                            <th class="ps-3 border-0">FECHA</th>
                            <th class="border-0">FALLA</th>
                            <th class="border-0 text-end pe-3">ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historialFallas as $falla)
                            <tr>
                                <td class="ps-3 small text-muted">{{ \Carbon\Carbon::parse($falla->created_at)->format('d/m') }}</td>
                                <td>
                                    <div class="small fw-bold text-dark">{{ Str::limit($falla->descripcion, 40) }}</div>
                                    <span class="badge bg-light text-dark border mt-1">{{ $falla->nivel_urgencia }}</span>
                                </td>
                                <td class="text-end pe-3">
                                    @if($falla->id_estado == 5) <!-- PENDIENTE (Nuevo ID) -->
                                        <span class="badge bg-danger rounded-pill">PENDIENTE</span>
                                    @elseif($falla->id_estado == 4) <!-- EN_PROCESO o MANTENIMIENTO (Nuevo ID) -->
                                        <span class="badge bg-warning rounded-pill text-dark">EN PROCESO</span>
                                    @else
                                        <span class="badge bg-success rounded-pill">SOLUCIONADO</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-4 text-muted small">Sin fallas históricas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@else
<!-- ============================================== -->
<!-- VISTA CALENDARIO INDEPENDIENTE -->
<!-- ============================================== -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card p-4 rounded-4 shadow-sm border-0 bg-white">
            <div id='calendar' style="min-height: 650px;"></div>
        </div>
    </div>
</div>
@endif


<!-- END OF VIEWS -->
<!-- Modal Detalle del Turno (Calendario) -->
<div class="modal fade" id="modalDetalleTurno" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 bg-light py-3 px-4">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2 text-dark">
                    <span class="material-symbols-rounded text-primary">event_note</span>
                    DETALLES DEL TURNO
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4 bg-primary bg-opacity-10 p-3 rounded-4">
                    <div class="bg-primary text-white p-2 rounded-3 shadow-sm">
                        <span class="material-symbols-rounded fs-2">directions_bus</span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <h4 class="fw-black mb-0 text-dark" id="modal-placa">---</h4>
                            <span class="badge bg-primary text-white x-small fw-bold text-uppercase" id="modal-ruta" style="letter-spacing: 0.5px; padding: 0.5em 1em;">---</span>
                        </div>
                        <p class="mb-0 small text-dark fw-bold d-flex align-items-center gap-1 mt-1" id="modal-fecha-texto">
                            <span class="material-symbols-rounded fs-6">calendar_month</span>
                            <span>---</span>
                        </p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <div class="bg-light p-3 rounded-4 h-100">
                            <span class="text-muted d-block small fw-bold text-uppercase mb-1">Hora Inicio</span>
                            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                <span class="material-symbols-rounded text-success fs-5">schedule</span>
                                <span id="modal-inicio">--:--</span>
                            </h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-light p-3 rounded-4 h-100">
                            <span class="text-muted d-block small fw-bold text-uppercase mb-1">Hora Fin Est.</span>
                            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                <span class="material-symbols-rounded text-danger fs-5">update</span>
                                <span id="modal-fin">--:--</span>
                            </h5>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="bg-light p-3 rounded-4">
                            <span class="text-muted d-block small fw-bold text-uppercase mb-1">Estado actual</span>
                            <div id="modal-estado-badge">
                                <span class="badge bg-secondary rounded-pill px-3 py-2 fw-bold" id="modal-estado">---</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow-sm" data-bs-dismiss="modal">
                    Entendido, Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Validación de Final de Ruta -->
<div class="modal fade" id="modalFinalizarRuta" tabindex="-1" aria-labelledby="modalFinalizarRutaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="modalFinalizarRutaLabel">
                    <span class="material-symbols-rounded text-warning">photo_camera</span> Evidencia de Finalización
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center px-4 py-3">
                <p class="text-muted small mb-4">Por favor, toma o selecciona una foto clara del torniquete para confirmar la visual del recorrido.</p>
                
                <form action="{{ $recorridoActivo ? route('conductor.finalizarRecorrido', $recorridoActivo->id_recorrido) : '#' }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4 text-start">
                        <label for="foto_torniquete" class="form-label fw-bold text-dark small text-uppercase">Adjuntar Fotografía</label>
                        <input class="form-control bg-light rounded-3 py-2 px-3" type="file" id="foto_torniquete" name="foto_torniquete" accept="image/*" required>
                        <div class="form-text small mt-2">Asegúrate de que los números sean legibles.</div>
                    </div>
                    
                    <div class="modal-footer border-0 pb-4 px-0 pt-2 gap-2 w-100 d-flex justify-content-between">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" style="width: 48%" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark shadow-sm" style="width: 48%">
                            Enviar Evidencia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL REPORTE FALLA -->
<div class="modal fade" id="fallaModal" tabindex="-1" aria-labelledby="fallaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow rounded-4" action="{{ route('conductor.reportarFalla') }}" method="POST">
            @csrf
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="fallaModalLabel">
                    <span class="material-symbols-rounded text-warning">warning</span> Reportar Falla Mecánica
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <p class="text-muted small mb-4">Evidencie todo problema mecánico u operativo a los líderes. Esto permitirá asignar mantenimientos rápidamente.</p>
                
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small text-uppercase">Vehículo Implicado</label>
                    @if($asignacionActiva)
                        <input type="text" name="placa" class="form-control bg-light rounded-3 font-monospace fw-bold" value="{{ $asignacionActiva->placa }}" readonly required>
                    @else
                        <!-- Listamos todos los buses del sistema unicos de asignaciones para permitir el input -->
                        <select name="placa" class="form-select rounded-3" required>
                            <option value="" disabled selected>Seleccione placa del vehículo...</option>
                            @foreach($asignaciones->unique('placa') as $asig)
                                <option value="{{ $asig->placa }}">{{ $asig->placa }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small text-uppercase">Nivel de Urgencia</label>
                    <select name="nivel_urgencia" class="form-select rounded-3" required>
                        @foreach($nivelesUrgencia as $nivel)
                            <option value="{{ $nivel }}" {{ $nivel == 'Bajo' ? 'selected' : '' }}>{{ $nivel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small text-uppercase">Descripción Detallada y Contexto</label>
                    <textarea name="descripcion" class="form-control rounded-3 bg-light border-0 py-3 px-3" rows="4" placeholder="Explique la situación experimentada con detalle, qué sonido hace, la frecuencia, afectaciones al manejo, etc." required></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 pt-2">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Restablecer</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark shadow-sm">Registrar Envío</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL INICIAR RUTA (SENTIDO) -->
@if(isset($asignacionActiva) && !$recorridoActivo)
<div class="modal fade" id="modalIniciarRuta" tabindex="-1" aria-labelledby="modalIniciarRutaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow-lg rounded-4" action="{{ route('conductor.iniciarRecorrido', $asignacionActiva->id_viaje) }}" method="POST">
            @csrf
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-primary text-white rounded-top-4 pb-3">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded">route</span> Configurar Trayecto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-4">
                <div class="text-center mb-4">
                    <h5 class="fw-bold text-dark mb-1">Ruta: {{ $asignacionActiva->ruta->nombre_ruta ?? 'N/A' }}</h5>
                    <p class="text-muted small">Seleccione el sentido del recorrido a realizar. Cada sentido se guardará como un viaje/recorrido individual e independiente de la meta fiscal para su cierre total.</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="sentido" id="sentidoIda" value="IDA" required>
                        <label class="btn btn-outline-primary w-100 p-3 rounded-4 d-flex flex-column align-items-center" for="sentidoIda">
                            <span class="material-symbols-rounded fs-1 mb-2">trending_flat</span>
                            <span class="fw-bold">Viaje de IDA</span>
                            <small class="opacity-75 d-block mt-1">Origen &rarr; Destino</small>
                        </label>
                    </div>
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="sentido" id="sentidoVuelta" value="VUELTA" required>
                        <label class="btn btn-outline-primary w-100 p-3 rounded-4 d-flex flex-column align-items-center" for="sentidoVuelta">
                            <span class="material-symbols-rounded fs-1 mb-2">sync_alt</span>
                            <span class="fw-bold">Viaje VUELTA</span>
                            <small class="opacity-75 d-block mt-1">Destino &rarr; Origen</small>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 pt-1">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold fs-5 shadow-sm w-100 py-3">Comenzar y Marcar Salida</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- WIDGET FLOTANTE DE PASAJEROS REMOVIDO -->

<!-- MODAL QR DE VIAJE -->
@if(isset($recorridoActivo) && $recorridoActivo)
<div class="modal fade" id="modalQRViaje" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded text-primary">qr_code_scanner</span> <span class="text-white-50">Código de Validación</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-toggle="modal" data-bs-target="#modalQRViaje"></button>
            </div>
            <div class="modal-body p-5 text-center bg-white">
                <p class="text-secondary fw-bold text-uppercase small mb-4">Muestre este código al controlador de tiempo</p>
                
                <div class="d-inline-block p-3 bg-white border rounded-4 shadow-sm mb-4">
                    <div id="qrcode-container"></div>
                </div>

                <div class="mt-2">
                    <h4 class="fw-black text-dark mb-0">{{ $recorridoActivo->viaje->placa ?? '...' }}</h4>
                    <p class="text-primary small fw-bold text-uppercase">Ruta: {{ $recorridoActivo->viaje->ruta->nombre_ruta ?? '...' }}</p>
                </div>

                <div class="alert alert-dark bg-dark border-0 rounded-4 mt-4 text-start p-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="material-symbols-rounded fs-5 text-primary">info</span>
                        <span class="fw-bold text-white small">Información del Código</span>
                    </div>
                    <p class="small text-white opacity-75 mb-0">
                        Este código contiene su ID de recorrido actual. El controlador podrá verificar sus tiempos y pasajeros escaneándolo.
                    </p>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4">
                <button type="button" class="btn btn-dark w-100 py-3 rounded-pill fw-bold" data-bs-dismiss="modal">Listo, Entendido</button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- MODAL FINALIZAR RECORRIDO Y FOTO TORNIQUETE -->
@if(isset($recorridoActivo) && $recorridoActivo)
<div class="modal fade" id="modalFinalizarRecorrido" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow-lg rounded-4" action="{{ route('conductor.finalizarRecorrido', $recorridoActivo->id_recorrido) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-warning text-dark rounded-top-4 pb-3">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded">photo_camera</span> Fin de Recorrido: Cargar Evidencia
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-4 text-center">
                <p class="text-muted fw-bold mb-4">¿Estás seguro de que deseas enviar esta foto y finalizar el recorrido en sentido <strong>{{ $recorridoActivo->sentido }}</strong>?</p>
                <div class="mb-3 text-start">
                    <label class="form-label fw-bold small text-uppercase">Foto del Torniquete</label>
                    <input type="file" class="form-control form-control-lg bg-light" name="foto_torniquete" accept="image/*" capture="environment" required>
                    <small class="text-muted d-block mt-2">Tome o suba una foto clara del contador del torniquete al finalizar el viaje.</small>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 pt-1">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">Confirmar y Finalizar</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

@if(isset($recorridoActivo) && $recorridoActivo)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qrContainer = document.getElementById("qrcode-container");
        if (qrContainer) {
            // Generamos el QR con el ID del recorrido
            new QRCode(qrContainer, {
                text: "{{ $recorridoActivo->id_recorrido }}",
                width: 250,
                height: 250,
                colorDark: "#212529",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }
    });
</script>
@endif
@if(request('view') == 'calendario')
<!-- SOLO Renderizar calendario si estamos en la vista calendario para ahorrar memoria -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'title',
                center: '',
                right: 'prev,next today timeGridDay,timeGridWeek,dayGridMonth'
            },
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                day: 'Día'
            },
            slotMinTime: '04:00:00',
            slotMaxTime: '23:00:00',
            events: [
                @foreach($asignaciones as $asig)
                {
                    title: '{{ $asig->placa }} - {{ $asig->ruta->nombre_ruta ?? "Ruta" }}',
                    start: '{{ \Carbon\Carbon::parse($asig->fecha)->format("Y-m-d\TH:i:s") }}',
                    end: '{{ \Carbon\Carbon::parse($asig->fecha)->addHours(8)->format("Y-m-d\TH:i:s") }}',
                    color: '{{ in_array($asig->id_estado, [4, 7]) ? "#10b981" : "#94a3b8" }}',
                    extendedProps: {
                        ruta: '{{ $asig->ruta->nombre_ruta ?? "" }}',
                        estado: '{{ optional($asig->estado)->nombre_estado }}'
                    }
                },
                @endforeach
            ],
            eventClick: function(info) {
                // Rellenar datos en el modal
                document.getElementById('modal-placa').innerText = info.event.title.split(' - ')[0];
                document.getElementById('modal-ruta').innerText = info.event.extendedProps.ruta;
                document.getElementById('modal-inicio').innerText = info.event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                document.getElementById('modal-fin').innerText = info.event.end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                
                // Formatear Fecha
                const opcionesFecha = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                document.getElementById('modal-fecha-texto').querySelector('span:last-child').innerText = info.event.start.toLocaleDateString('es-ES', opcionesFecha);

                const estado = info.event.extendedProps.estado;
                const badge = document.getElementById('modal-estado');
                badge.innerText = estado;
                
                // Color dinámico según estado
                if (estado === 'ACTIVO' || estado === 'EN SERVICIO') {
                    badge.className = 'badge bg-success rounded-pill px-3 py-2 fw-bold';
                } else {
                    badge.className = 'badge bg-secondary rounded-pill px-3 py-2 fw-bold';
                }

                // Mostrar el modal
                var myModal = new bootstrap.Modal(document.getElementById('modalDetalleTurno'));
                myModal.show();
            }
        });
        calendar.render();
    });
</script>
@endif
@endpush

