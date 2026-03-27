@extends('controlador-tiempo.layouts.app')

@section('title', 'Planillas de Despacho — Controlador de Tiempo')

@section('content')
<div class="sigu-fade">

    <div class="sigu-page-hd">
        <h1 class="sigu-page-title">Planillas de Despacho</h1>
        <p class="sigu-page-sub">Documentación legal de operación y registro de novedades del día.</p>
    </div>

    {{-- ─── Novedades operativas ─────────────────────────────────── --}}
    @if($novedades->isNotEmpty())
    <div class="bg-white rounded-3 shadow-sm p-4 mb-3 border-start border-4 border-warning">
        <div class="d-flex align-items-center mb-3">
            <span class="material-symbols-rounded text-warning me-2" style="font-size:1.4rem;">warning</span>
            <h6 class="fw-bold mb-0 text-dark">Novedades Activas — Buses Fuera de Servicio</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th>Bus (Placa)</th>
                        <th>Modelo</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($novedades as $bus)
                        <tr>
                            <td class="fw-bold">{{ $bus->placa }}</td>
                            <td>{{ $bus->modelo ?? 'N/A' }}</td>
                            <td>
                                @php $nomEstado = $bus->estado->nombre_estado ?? 'Desconocido'; @endphp
                                @if(str_contains(strtoupper($nomEstado), 'TALLER') || $bus->id_estado == 4)
                                    <span class="badge bg-warning text-dark rounded-pill">En Taller</span>
                                @else
                                    <span class="badge bg-danger rounded-pill">{{ $nomEstado }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="small text-muted">Registrar en planilla →</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ─── Planilla del día ─────────────────────────────────────── --}}
    <div class="bg-white rounded-3 shadow-sm overflow-hidden">
        <div class="d-flex align-items-center justify-content-between p-4 pb-3">
            <div>
                <h6 class="fw-bold mb-0">Planilla de Despacho del Día</h6>
                <span class="text-muted small">{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</span>
            </div>
            <div class="d-flex gap-2">
                <span class="badge" style="background:var(--ct-accent-light); color:var(--ct-accent); border:1px solid var(--ct-accent-mid); border-radius:999px; padding:0.3em 0.9em; font-size:0.8rem; font-weight:600;">
                    {{ $planilla->total() }} turnos
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted text-uppercase small fw-bold">
                    <tr>
                        <th class="ps-4 py-3">#</th>
                        <th class="py-3">Bus / Placa</th>
                        <th class="py-3">Conductor</th>
                        <th class="py-3">Ruta</th>
                        <th class="py-3">Tipo</th>
                        <th class="py-3 text-center">Novedad</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($planilla as $i => $asig)
                        <tr class="border-top">
                            <td class="ps-4 text-muted small">{{ $planilla->firstItem() + $i }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ $asig->placa ?? $asig->bus->placa ?? '—' }}</span>
                                <div class="text-muted small">{{ $asig->bus->modelo ?? '' }}</div>
                            </td>
                            <td>
                                @if($asig->usuario)
                                    <span class="fw-semibold">{{ $asig->usuario->primer_nombre }} {{ $asig->usuario->primer_apellido }}</span>
                                    <div class="text-muted small">{{ $asig->usuario->doc_usuario }}</div>
                                @else
                                    <span class="text-muted small">Sin conductor</span>
                                @endif
                            </td>
                            <td>
                                <span class="small">
                                    {{ $asig->ruta->barrioOrigen->nombre ?? '—' }}
                                    <span class="text-muted">→</span>
                                    {{ $asig->ruta->barrioDestino->nombre ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 small" style="font-weight:600;">
                                    {{ $asig->tipoAsignacion->nombre_tipo ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    $allNovedades = $asig->recorridos->flatMap->novedades;
                                    $checkpoints = $allNovedades->where('tipo', 'CHECKPOINT')->count();
                                    $incidencias = $allNovedades->where('tipo', 'INCIDENCIA')->count();
                                @endphp
                                <button type="button" class="btn btn-link p-0 text-decoration-none" 
                                        data-bs-toggle="modal" data-bs-target="#modalHistorial{{ $asig->id_viaje }}">
                                    <div class="d-flex justify-content-center gap-2">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" title="Checkpoints">
                                            <span class="material-symbols-rounded align-middle fs-6">beenhere</span> {{ $checkpoints }}
                                        </span>
                                        <span class="badge {{ $incidencias > 0 ? 'bg-danger text-white' : 'bg-light text-muted border' }}" title="Incidencias">
                                            <span class="material-symbols-rounded align-middle fs-6">warning</span> {{ $incidencias }}
                                        </span>
                                    </div>
                                </button>

                                <!-- Modal de Historial (Timeline) -->
                                <div class="modal fade" id="modalHistorial{{ $asig->id_viaje }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden pt-2">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold text-dark px-3 mt-2">Actividad de Operación</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4 pt-2 text-start">
                                                <div class="mb-4 px-2">
                                                    <span class="text-muted small fw-bold text-uppercase">Bus: {{ $asig->placa }} • Conductor: {{ $asig->usuario->primer_nombre ?? 'N/A' }}</span>
                                                </div>

                                                <div class="ct-timeline">
                                                    @forelse($asig->recorridos->sortByDesc('hora_salida') as $rec)
                                                        @php
                                                            // Unificar todos los eventos de este recorrido para ordenarlos por fecha
                                                            $eventos = collect();
                                                            
                                                            // Inicio
                                                            if ($rec->hora_salida) {
                                                                $eventos->push([
                                                                    'hora' => $rec->hora_salida,
                                                                    'tipo' => 'INICIO',
                                                                    'titulo' => 'Inicio de Recorrido',
                                                                    'desc' => 'Vehículo salió de terminal.'
                                                                ]);
                                                            }
                                                            
                                                            // Novedades (Checkpoints e Incidencias)
                                                            foreach($rec->novedades as $nov) {
                                                                $eventos->push([
                                                                    'hora' => $nov->created_at,
                                                                    'tipo' => $nov->tipo,
                                                                    'titulo' => $nov->tipo == 'CHECKPOINT' ? 'Control Validado' : 'Incidencia Reportada',
                                                                    'desc' => $nov->descripcion
                                                                ]);
                                                            }
                                                            
                                                            // Fin
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

                                                        <div class="mb-4">
                                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                                <span class="badge bg-light text-dark border-start border-3 border-primary rounded-0 small fw-bold">
                                                                    RECORRIDO #{{ $rec->id_recorrido }}
                                                                </span>
                                                                <span class="text-muted" style="font-size: 0.75rem; font-weight: 600;">
                                                                    {{ \Carbon\Carbon::parse($rec->hora_salida)->format('d/m/Y') }}
                                                                </span>
                                                            </div>
                                                            <div class="small text-muted mb-3 ps-1" style="font-size: 0.8rem;">
                                                                <span class="material-symbols-rounded align-middle fs-6">schedule</span>
                                                                Salida: {{ \Carbon\Carbon::parse($rec->hora_salida)->format('h:i A') }} 
                                                                • 
                                                                Llegada: {{ $rec->hora_llegada ? \Carbon\Carbon::parse($rec->hora_llegada)->format('h:i A') : 'En curso' }}
                                                            </div>
                                                            @foreach($eventos as $evento)
                                                                <div class="d-flex gap-3 mb-3">
                                                                    <div class="text-muted small pt-1 fw-bold" style="min-width: 65px;">
                                                                        {{ \Carbon\Carbon::parse($evento['hora'])->format('h:i A') }}
                                                                    </div>
                                                                    <div class="border-start border-2 border-light ps-3 position-relative">
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
                                                                        <span class="material-symbols-rounded fs-5 text-{{ $color }} position-absolute" 
                                                                              style="left:-14px; top:0; background:white;">{{ $icon }}</span>
                                                                        <div class="fw-bold text-{{ $color }} small text-uppercase">{{ $evento['titulo'] }}</div>
                                                                        <div class="small text-muted">{{ $evento['desc'] }}</div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <hr class="my-3 opacity-25">
                                                    @empty
                                                        <div class="text-center py-4 text-muted small">
                                                            No hay actividad de recorridos registrada aún.
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pb-4">
                                                <button type="button" class="btn btn-dark w-100 rounded-pill fw-bold" data-bs-dismiss="modal">Cerrar Historial</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <span class="material-symbols-rounded d-block mb-2" style="font-size:2.5rem;">assignment</span>
                                No hay turnos asignados para generar la planilla.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3">{{ $planilla->links() }}</div>
    </div>

</div>
@endsection
