@extends('controlador-tiempo.layouts.app')

@section('title', 'Detalle de Planilla — ' . ($asig->placa ?? 'Recorrido'))

@section('content')
<div class="sigu-fade">

    <div class="sigu-page-hd d-flex align-items-center justify-content-between">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('controlador-tiempo.planillas.index') }}" class="text-decoration-none">Planillas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detalle de Turno</li>
                </ol>
            </nav>
            <h1 class="sigu-page-title">Detalle de Operación</h1>
            <p class="sigu-page-sub">Información técnica y novedades registradas para este turno.</p>
        </div>
        <a href="{{ route('controlador-tiempo.planillas.index') }}" class="btn btn-outline-dark rounded-pill fw-bold px-4">
            <span class="material-symbols-rounded align-middle me-1">arrow_back</span> Volver
        </a>
    </div>

    <div class="row g-4 mt-2">
        {{-- ─── Panel Izquierdo: Información General ───────────────────── --}}
        <div class="col-lg-4">
            <div class="bg-white rounded-4 shadow-sm p-4 h-100">
                <div class="text-center mb-4">
                    <div class="bg-light rounded-circle d-inline-flex p-3 mb-3">
                        <span class="material-symbols-rounded fs-1 text-primary">directions_bus</span>
                    </div>
                    <h2 class="fw-black mb-0" style="letter-spacing: 2px;">{{ $asig->placa ?? $asig->bus->placa }}</h2>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3">
                        Ruta #{{ $asig->ruta->codigo_ruta ?? $asig->ruta->id_ruta }}
                    </span>
                </div>

                <hr class="opacity-25">

                <div class="mb-4">
                    <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Conductor</label>
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-light rounded-3 p-2">
                            <span class="material-symbols-rounded text-muted">person</span>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">{{ $asig->usuario->primer_nombre }} {{ $asig->usuario->primer_apellido }}</div>
                            <div class="small text-muted">{{ $asig->usuario->doc_usuario }}</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Trayecto Asignado</label>
                    <div class="p-3 bg-light rounded-3 border border-light-subtle">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="material-symbols-rounded text-success fs-5">location_on</span>
                            <span class="fw-semibold small text-dark">{{ $asig->ruta->barrioOrigen->nombre ?? 'N/A' }}</span>
                        </div>
                        <div class="ms-1 border-start border-2 border-primary border-opacity-25 ps-3 my-1 py-1">
                            <span class="small text-muted">Vía principal</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="material-symbols-rounded text-danger fs-5">flag</span>
                            <span class="fw-semibold small text-dark">{{ $asig->ruta->barrioDestino->nombre ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Estadísticas del Turno</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-3 border rounded-3 text-center bg-light bg-opacity-50">
                                <div class="fs-4 fw-bold text-dark">{{ $asig->recorridos->count() }}</div>
                                <div class="small text-muted">Viajes</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded-3 text-center bg-light bg-opacity-50">
                                <div class="fs-4 fw-bold text-dark">{{ $asig->recorridos->flatMap->novedades->where('tipo', 'CHECKPOINT')->count() }}</div>
                                <div class="small text-muted">Controles</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── Panel Derecho: Timeline de Novedades ────────────────────── --}}
        <div class="col-lg-8">
            <div class="bg-white rounded-4 shadow-sm p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3">
                            <span class="material-symbols-rounded text-primary">history</span>
                        </div>
                        <h5 class="fw-bold mb-0 text-dark">Línea de Tiempo Operativa</h5>
                    </div>

                    {{-- Filtro de fecha interno --}}
                    <form action="{{ route('controlador-tiempo.planillas.show', $asig->id_viaje) }}" method="GET" class="d-flex gap-2">
                        <input type="date" name="fecha" value="{{ request('fecha') }}" class="form-control form-control-sm border-0 bg-light shadow-none rounded-pill px-3" style="width: 150px;">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">Filtrar</button>
                        @if(request('fecha'))
                            <a href="{{ route('controlador-tiempo.planillas.show', $asig->id_viaje) }}" class="btn btn-light btn-sm rounded-pill border shadow-none" title="Limpiar">
                                <span class="material-symbols-rounded align-middle fs-6">restart_alt</span>
                            </a>
                        @endif
                    </form>
                </div>

                <div class="ct-timeline">
                    @forelse($recorridos as $rec)
                        @php
                            $eventos = collect();
                            if ($rec->hora_salida) {
                                $eventos->push([
                                    'hora' => $rec->hora_salida,
                                    'tipo' => 'INICIO',
                                    'titulo' => 'Inicio de Recorrido',
                                    'desc' => 'Vehículo salió de terminal.'
                                ]);
                            }
                            foreach($rec->novedades as $nov) {
                                $eventos->push([
                                    'hora' => $nov->created_at,
                                    'tipo' => $nov->tipo,
                                    'titulo' => $nov->tipo == 'CHECKPOINT' ? 'Control Validado' : 'Incidencia Reportada',
                                    'desc' => $nov->descripcion
                                ]);
                            }
                            if ($rec->hora_llegada) {
                                $eventos->push([
                                    'hora' => $rec->hora_llegada,
                                    'tipo' => 'FIN',
                                    'titulo' => 'Llegada a Destino',
                                    'desc' => 'Recorrido finalizado exitosamente.'
                                ]);
                            }
                            $eventos = $eventos->sortBy('hora');
                        @endphp

                        <div class="card border-0 bg-light bg-opacity-50 rounded-4 mb-4">
                            <div class="card-header bg-transparent border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                                <h6 class="fw-black text-uppercase tracking-wider text-muted mb-0" style="font-size: 0.75rem;">
                                    <span class="badge bg-dark rounded-pill me-2 px-3">Recorrido #{{ $rec->id_recorrido }}</span>
                                    Día: {{ \Carbon\Carbon::parse($rec->hora_salida)->format('d/m/Y') }}
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-4 d-flex gap-4 flex-wrap">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="material-symbols-rounded text-muted" style="font-size: 1.1rem;">schedule</span>
                                        <span class="small fw-bold">Salida: {{ \Carbon\Carbon::parse($rec->hora_salida)->format('h:i A') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="material-symbols-rounded text-muted" style="font-size: 1.1rem;">flag</span>
                                        <span class="small fw-bold">
                                            Llegada: {{ $rec->hora_llegada ? \Carbon\Carbon::parse($rec->hora_llegada)->format('h:i A') : 'En curso...' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="timeline ps-2">
                                    @foreach($eventos as $evento)
                                        <div class="d-flex gap-4 mb-4 position-relative">
                                            <div class="text-muted small fw-bold pt-1" style="min-width: 70px;">
                                                {{ \Carbon\Carbon::parse($evento['hora'])->format('h:i A') }}
                                            </div>
                                            <div class="border-start border-2 border-primary border-opacity-10 ps-4 pb-1">
                                                @php
                                                    $icon = match($evento['tipo']) {
                                                        'INICIO' => 'play_circle',
                                                        'CHECKPOINT' => 'check_circle',
                                                        'INCIDENCIA' => 'report',
                                                        'FIN' => 'stop_circle',
                                                        default => 'info'
                                                    };
                                                    $color = match($evento['tipo']) {
                                                        'INICIO' => 'primary',
                                                        'CHECKPOINT' => 'success',
                                                        'INCIDENCIA' => 'danger',
                                                        'FIN' => 'dark',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="material-symbols-rounded fs-4 text-{{ $color }} position-absolute bg-white" 
                                                      style="left:88px; top:0; z-index: 1;">{{ $icon }}</span>
                                                <div class="fw-bold text-{{ $color }} small text-uppercase mb-1" style="letter-spacing: 0.5px;">{{ $evento['titulo'] }}</div>
                                                <div class="text-dark small opacity-75">{{ $evento['desc'] }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <span class="material-symbols-rounded fs-1 text-muted mb-3 d-block">history_toggle_off</span>
                            <p class="text-muted">No se registraron recorridos para esta combinación en la fecha seleccionada.</p>
                        </div>
                    @endforelse

                    <div class="mt-4 pt-2">
                        {{ $recorridos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
