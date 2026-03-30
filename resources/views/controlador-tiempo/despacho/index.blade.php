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
                    <div class="fs-3 fw-bold lh-1">{{ $busesIniciados }}</div>
                    <div class="text-muted small">Buses en recorrido</div>
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

    {{-- ─── Tabla de turnos con Filtros ────────────────────────── --}}
    <div class="bg-white rounded-4 shadow-sm overflow-hidden border-0 mb-4">
        <div class="p-4 border-bottom bg-light bg-opacity-50">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0">Gestión de Turnos y Asignaciones</h6>
                <span class="badge" style="background:var(--ct-accent-light); color:var(--ct-accent); font-size:0.8rem; border-radius:999px; padding:0.3em 0.9em;">
                    {{ $asignaciones->total() }} registros encontrados
                </span>
            </div>
            
            <form action="{{ route('controlador-tiempo.despacho.index') }}" method="GET" class="row g-2">
                <div class="col-md-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text border-0 bg-white shadow-sm"><span class="material-symbols-rounded fs-6">search</span></span>
                        <input type="text" name="placa" value="{{ request('placa') }}" class="form-control border-0 shadow-sm" placeholder="Bus / Placa...">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text border-0 bg-white shadow-sm"><span class="material-symbols-rounded fs-6">alt_route</span></span>
                        <input type="number" name="ruta_id" value="{{ request('ruta_id') }}" class="form-control border-0 shadow-sm" placeholder="ID Ruta...">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text border-0 bg-white shadow-sm"><span class="material-symbols-rounded fs-6">person</span></span>
                        <input type="number" name="doc_usuario" value="{{ request('doc_usuario') }}" class="form-control border-0 shadow-sm" placeholder="Doc. Conductor...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="estado" class="form-select form-select-sm border-0 shadow-sm">
                        <option value="">Todos los estados</option>
                        <option value="iniciado" {{ request('estado') == 'iniciado' ? 'selected' : '' }}>En Recorrido</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-ct btn-sm w-100 shadow-sm">Filtro</button>
                    @if(request()->anyFilled(['placa', 'ruta_id', 'doc_usuario', 'estado']))
                        <a href="{{ route('controlador-tiempo.despacho.index') }}" class="btn btn-light btn-sm shadow-sm">
                            <span class="material-symbols-rounded fs-6 align-middle">close</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted text-uppercase small fw-bold">
                    <tr>
                        <th class="ps-4 py-3">Bus / Placa</th>
                        <th class="py-3">Conductor</th>
                        <th class="py-3 text-center">Ruta ID</th>
                        <th class="py-3 text-center">Estado</th>
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
                            <td class="text-center">
                                <div class="badge bg-light text-primary border px-3 py-2 fw-black fs-6 mb-1" style="border-radius: 0.5rem;">
                                    {{ $asig->id_ruta }}
                                </div>
                                <div class="small text-muted fw-medium" style="font-size: 0.7rem; letter-spacing: 0.3px;">
                                    {{ $asig->ruta->barrioOrigen->nombre ?? '?' }}
                                    <span class="opacity-50 mx-1">→</span>
                                    {{ $asig->ruta->barrioDestino->nombre ?? '?' }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge" style="background:var(--ct-accent-light); color:var(--ct-accent); border:1px solid var(--ct-accent-mid); border-radius:999px; padding:0.25em 0.8em; font-size:0.78rem; font-weight:600;">
                                    {{ $asig->viajes->isNotEmpty() ? 'EN CURSO' : 'PENDIENTE' }}
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
