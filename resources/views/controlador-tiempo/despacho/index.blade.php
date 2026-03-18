@extends('controlador-tiempo.layouts.app')

@section('title', 'Despacho — Controlador de Tiempo')

@section('content')
<div class="sigu-fade">

    <div class="sigu-page-hd">
        <h1 class="sigu-page-title">Módulo de Despacho</h1>
        <p class="sigu-page-sub">Gestión de turnos, intervalos y coordinación de conductores.</p>
    </div>

    {{-- ─── Resumen rápido ───────────────────────────────────────── --}}
    <div class="row g-3 mt-2 mb-3">
        <div class="col-6 col-md-4">
            <div class="bg-white rounded-3 shadow-sm p-3 ct-kpi d-flex align-items-center gap-3">
                <span class="material-symbols-rounded fs-2 ct-kpi-icon">directions_bus</span>
                <div>
                    <div class="fs-3 fw-bold lh-1">{{ $busesDisponibles->count() }}</div>
                    <div class="text-muted small">Buses disponibles</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="bg-white rounded-3 shadow-sm p-3 ct-kpi d-flex align-items-center gap-3">
                <span class="material-symbols-rounded fs-2 ct-kpi-icon">alt_route</span>
                <div>
                    <div class="fs-3 fw-bold lh-1">{{ $rutas->count() }}</div>
                    <div class="text-muted small">Rutas activas</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="bg-white rounded-3 shadow-sm p-3 ct-kpi d-flex align-items-center gap-3">
                <span class="material-symbols-rounded fs-2 ct-kpi-icon">assignment_ind</span>
                <div>
                    <div class="fs-3 fw-bold lh-1">{{ $asignaciones->total() }}</div>
                    <div class="text-muted small">Asignaciones totales</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Tabla de turnos ──────────────────────────────────────── --}}
    <div class="bg-white rounded-3 shadow-sm overflow-hidden">
        <div class="d-flex align-items-center justify-content-between p-4 pb-3">
            <h6 class="fw-bold mb-0">Registro de Asignaciones (Turnos)</h6>
            <span class="badge" style="background:var(--ct-accent-light); color:var(--ct-accent); font-size:0.8rem; border-radius:999px; padding:0.3em 0.9em;">
                {{ $asignaciones->total() }} registros
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted text-uppercase small fw-bold">
                    <tr>
                        <th class="ps-4 py-3">Bus / Placa</th>
                        <th class="py-3">Conductor</th>
                        <th class="py-3">Ruta</th>
                        <th class="py-3">Tipo Asignación</th>
                        <th class="py-3">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asignaciones as $asig)
                        <tr class="border-top">
                            <td class="ps-4">
                                <span class="fw-bold text-dark">{{ $asig->placa ?? $asig->bus->placa ?? '—' }}</span>
                                <div class="text-muted small">{{ $asig->bus->modelo ?? '' }}</div>
                            </td>
                            <td>
                                @if($asig->usuario)
                                    <span class="fw-semibold">{{ $asig->usuario->primer_nombre }} {{ $asig->usuario->primer_apellido }}</span>
                                    <div class="text-muted small">Doc: {{ $asig->usuario->doc_usuario }}</div>
                                @else
                                    <span class="text-muted">Sin conductor</span>
                                @endif
                            </td>
                            <td>
                                @if($asig->ruta)
                                    <span class="small fw-medium">
                                        {{ $asig->ruta->barrioOrigen->nombre ?? '?' }}
                                        <span class="text-muted">→</span>
                                        {{ $asig->ruta->barrioDestino->nombre ?? '?' }}
                                    </span>
                                @else
                                    <span class="text-muted small">Sin ruta</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 fw-semibold">
                                    {{ $asig->tipoAsignacion->nombre_tipo ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge" style="background:var(--ct-accent-light); color:var(--ct-accent); border:1px solid var(--ct-accent-mid); border-radius:999px; padding:0.25em 0.8em; font-size:0.78rem; font-weight:600;">
                                    Activa
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <span class="material-symbols-rounded d-block mb-2" style="font-size:2.5rem;">search_off</span>
                                No hay asignaciones registradas para esta empresa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3">{{ $asignaciones->links() }}</div>
    </div>

</div>
@endsection
