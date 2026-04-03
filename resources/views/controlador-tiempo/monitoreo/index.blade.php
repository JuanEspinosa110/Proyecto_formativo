@extends('controlador-tiempo.layouts.app')

@section('title', 'Monitoreo en Vivo — Controlador de Tiempo')

@section('content')
    <div class="sigu-fade">

        <div class="sigu-page-hd">
            <h1 class="sigu-page-title">Monitoreo en Tiempo Real</h1>
            <p class="sigu-page-sub">Seguimiento del estado y la operación de cada bus en la flota.</p>
        </div>

        {{-- ─── Estadísticas ─────────────────────────────────────────── --}}
        <div class="row g-3 mt-2 mb-3">
            <div class="col-6 col-md-3">
                <div class="bg-white rounded-3 shadow-sm p-3 ct-kpi d-flex align-items-center gap-3">
                    <span class="material-symbols-rounded fs-2" style="color:#38a169;">check_circle</span>
                    <div>
                        <div class="fs-3 fw-bold lh-1">{{ $estadisticas['en_ruta'] }}</div>
                        <div class="text-muted small">En ruta</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="bg-white rounded-3 shadow-sm p-3 ct-kpi d-flex align-items-center gap-3">
                    <span class="material-symbols-rounded fs-2" style="color:#e53e3e;">cancel</span>
                    <div>
                        <div class="fs-3 fw-bold lh-1">{{ $estadisticas['inactivos'] }}</div>
                        <div class="text-muted small">Inactivos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="bg-white rounded-3 shadow-sm p-3 ct-kpi d-flex align-items-center gap-3">
                    <span class="material-symbols-rounded fs-2" style="color:#d69e2e;">engineering</span>
                    <div>
                        <div class="fs-3 fw-bold lh-1">{{ $estadisticas['en_taller'] }}</div>
                        <div class="text-muted small">En taller</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="bg-white rounded-3 shadow-sm p-3 ct-kpi d-flex align-items-center gap-3">
                    <span class="material-symbols-rounded fs-2 ct-kpi-icon">garage</span>
                    <div>
                        <div class="fs-3 fw-bold lh-1">{{ $estadisticas['total'] }}</div>
                        <div class="text-muted small">Flota total de buses</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── Tarjetas de buses ────────────────────────────────────── --}}
        <div class="row g-3">
            @forelse($buses as $bus)
                @php
                    $estadoBus = $bus->estado->nombre_estado ?? 'Desconocido';
                    $esActivo = $bus->id_estado == 1;
                    $esTaller = $bus->id_estado == 4;
                    $badgeClass = $esActivo ? 'bg-success-subtle text-success border-success-subtle'
                        : ($esTaller ? 'bg-warning-subtle text-warning border-warning-subtle'
                            : 'bg-danger-subtle text-danger border-danger-subtle');
                    $iconoBus = $esActivo ? 'directions_bus'
                        : ($esTaller ? 'engineering' : 'bus_alert');
                @endphp
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="bg-white rounded-3 shadow-sm p-4 h-100" style="border-top: 3px solid var(--ct-accent);">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="material-symbols-rounded ct-kpi-icon">{{ $iconoBus }}</span>
                                <span class="fw-bold fs-6 text-dark">{{ $bus->placa }}</span>
                            </div>
                            <span class="badge {{ $badgeClass }} border rounded-pill px-3"
                                style="font-size:0.75rem; font-weight:600;">
                                {{ $estadoBus }}
                            </span>
                        </div>

                        <div class="text-muted small mb-2">
                            <span class="fw-semibold text-dark">Modelo:</span> {{ $bus->modelo ?? 'N/A' }}
                        </div>
                        <div class="text-muted small mb-2">
                            <span class="fw-semibold text-dark">Km actual:</span>
                            {{ number_format($bus->kilometraje ?? 0, 0, ',', '.') }} km
                        </div>

                        {{-- Recorrido (Viaje en curso) --}}
                        @if($bus->recorridos->isNotEmpty())
                            @php $rec = $bus->recorridos->first(); @endphp
                            <div class="mt-2 pt-2 border-top border-primary border-opacity-10">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-primary bg-opacity-10 text-primary border-0 small">RECORRIDO ACTUAL</span>
                                    <span class="small fw-bold {{ $rec->hora_llegada ? 'text-success' : 'text-primary' }}">
                                        {{ $rec->hora_llegada ? 'Completado' : 'En Tránsito' }}
                                    </span>
                                </div>
                                <div class="text-muted small d-flex justify-content-between align-items-center">
                                    <span><span class="fw-semibold text-dark">Propio:</span>
                                        {{ \Carbon\Carbon::parse($rec->hora_salida)->format('h:i A') }}</span>
                                    @if($bus->intervalo_anterior)
                                        <span class="badge bg-light text-dark border small fw-bold">
                                            +{{ $bus->intervalo_anterior }} min gap
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Asignación activa --}}
                        @if($bus->asignaciones->isNotEmpty())
                            @php $asig = $bus->asignaciones->first(); @endphp
                            <div class="mt-2 pt-2 border-top">
                                <div class="text-muted small">
                                    <span class="fw-semibold text-dark">Conductor:</span>
                                    {{ $asig->usuario->primer_nombre ?? '—' }} {{ $asig->usuario->primer_apellido ?? '' }}
                                </div>
                                <div class="text-muted small">
                                    <span class="fw-semibold text-dark">Ruta:</span>
                                    {{ $asig->ruta->barrioOrigen->nombre ?? '?' }} →
                                    {{ $asig->ruta->barrioDestino->nombre ?? '?' }}
                                </div>
                            </div>
                        @else
                            <div class="mt-2 pt-2 border-top">
                                <span class="text-muted small">Sin asignación activa</span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="bg-white rounded-3 shadow-sm p-5 text-center">
                        <span class="material-symbols-rounded d-block mb-2"
                            style="font-size:3rem; color:var(--ct-accent);">directions_bus</span>
                        <p class="text-muted mb-0">No hay buses que hayan comenzado su recorrido.</p>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
@endsection