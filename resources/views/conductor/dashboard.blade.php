@extends('conductor.layouts.app')

@section('title', 'Dashboard Conductor')

@push('styles')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<link rel="stylesheet" href="{{ asset('css/conductor-dashboard.css') }}">
@endpush

@section('content')

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
            $enCurso = $asignacionActiva->id_estado == 12; // En Servicio
            $vencido = $asignacionActiva->id_estado == 8; // Vencido/No ejecutado
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
                                <h5 class="fw-bold mb-3 text-primary d-inline-flex align-items-center gap-2 title-large">
                                    <span class="spinner-grow text-success spinner-small" role="status"></span>
                                    Recorrido en Progreso
                                </h5>
                                <div class="bg-light p-4 rounded-4 my-3 mx-auto box-route-dashed">
                                    <p class="mb-2 text-muted fw-bold text-uppercase small letter-spacing-1">Dirigiéndose en sentido</p>
                                    <h2 class="fw-black text-dark mb-0 d-flex align-items-center justify-content-center gap-2">
                                        <span class="material-symbols-rounded fs-2 text-primary">{{ $recorridoActivo->sentido == 'IDA' ? 'trending_flat' : 'sync_alt' }}</span>
                                        {{ $recorridoActivo->sentido }}
                                    </h2>
                                </div>
                                <p class="mb-3 d-flex justify-content-center align-items-center gap-2 text-muted fs-route-title">
                                    <span class="material-symbols-rounded">schedule</span>
                                    Hora de salida: <strong class="text-dark">{{ \Carbon\Carbon::parse($recorridoActivo->hora_salida)->format('h:i A') }}</strong>
                                </p>
                                
                                <div class="row g-2 justify-content-center max-width-stats mx-auto mb-4">
                                    <div class="col-6">
                                        <div class="bg-light p-2 rounded-3">
                                            <span class="small text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Pasajeros Hoy</span>
                                            <h4 class="fw-bold mb-0 text-primary">{{ $pasajerosTotalesHoy }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="bg-light p-2 rounded-3">
                                            <span class="small text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Ingresos Hoy</span>
                                            <h4 class="fw-bold mb-0 text-success">${{ number_format($ingresosTotalesHoy) }}</h4>
                                        </div>
                                    </div>
                                </div>
                                

                                <form action="{{ route('conductor.finalizarRecorrido', $recorridoActivo->id_recorrido) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-warning py-3 fw-bold text-dark rounded-pill shadow-sm d-inline-flex border-0 justify-content-center align-items-center gap-2 transition-all w-100 btn-route-action">
                                        <span class="material-symbols-rounded fs-3">sports_score</span> Llegada a Destino Final
                                    </button>
                                </form>
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
                    <div><span class="small text-muted d-block text-uppercase fw-bold">Pasajeros Totales</span> <h4 class="fw-bold mb-0 text-primary">{{ $pasajerosTotalesHoy }}</h4></div>
                    <div><span class="small text-muted d-block text-uppercase fw-bold">Ingresos Generados</span> <h4 class="fw-bold mb-0 text-success">${{ number_format($ingresosTotalesHoy) }}</h4></div>
                    <div><span class="small text-muted d-block text-uppercase fw-bold">Tiempo Trabajado</span> <h4 class="fw-bold mb-0 text-dark">{{ $tiempoTrabajadoFormato ?? '0h 0m' }}</h4></div>
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
                            <th class="border-0 text-center">PASAJEROS</th>
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
                                <td class="text-center fw-bold text-primary">{{ $rec->cantidad_pasajeros }}</td>
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
                                    @if($falla->id_estado == 19) <!-- PENDIENTE -->
                                        <span class="badge bg-danger rounded-pill">PENDIENTE</span>
                                    @elseif($falla->id_estado == 6 || $falla->id_estado == 7) <!-- EN_PROCESO o MANTENIMIENTO -->
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

@include('conductor.modals.falla_mecanica')
@include('conductor.modals.iniciar_ruta')

<!-- WIDGET FLOTANTE DE PASAJEROS -->
@if(isset($recorridoActivo) && $recorridoActivo)
<div class="position-fixed bottom-0 end-0 m-3 m-md-4 p-3 bg-white shadow-lg d-flex align-items-center gap-3 widget-passengers">
    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center widget-icon-box">
        <span class="material-symbols-rounded widget-icon-fs">group</span>
    </div>
    <div class="pe-2">
        <span class="d-block text-muted text-uppercase fw-bold pb-1 widget-text-small">Pasajeros</span>
        <span class="d-block text-dark fw-black widget-text-large" id="pasajeros-count">{{ $recorridoActivo->cantidad_pasajeros }}</span>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    color: '{{ in_array($asig->id_estado, [12, 13]) ? "#10b981" : "#94a3b8" }}',
                    extendedProps: {
                        ruta: '{{ $asig->ruta->nombre_ruta ?? "" }}',
                        estado: '{{ optional($asig->estado)->nombre_estado }}'
                    }
                },
                @endforeach
            ],
            eventClick: function(info) {
                alert('Detalles Turno:\n \n' + info.event.title + '\nRuta Asignada: ' + info.event.extendedProps.ruta + '\nEstado del Turno: ' + info.event.extendedProps.estado + '\nHora de Inicio: ' + info.event.start.toLocaleTimeString() + '\nHora Fin Estimada: ' + info.event.end.toLocaleTimeString());
            }
        });
        calendar.render();
    });
</script>
@endif
@endpush

