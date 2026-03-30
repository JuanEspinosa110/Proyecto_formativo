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

    {{-- ─── Filtros de búsqueda ───────────────────────────────────── --}}
    <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
        <form action="{{ route('controlador-tiempo.planillas.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Bus / Placa</label>
                <select name="placa" class="form-select border-0 bg-light shadow-none">
                    <option value="">Todos los buses</option>
                    @foreach($busesList as $b)
                        <option value="{{ $b->placa }}" {{ request('placa') == $b->placa ? 'selected' : '' }}>
                            {{ $b->placa }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Conductor</label>
                <select name="doc_usuario" class="form-select border-0 bg-light shadow-none">
                    <option value="">Todos los conductores</option>
                    @foreach($conductoresList as $c)
                        <option value="{{ $c->doc_usuario }}" {{ request('doc_usuario') == $c->doc_usuario ? 'selected' : '' }}>
                            {{ $c->primer_nombre }} {{ $c->primer_apellido }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Ruta</label>
                <select name="id_ruta" class="form-select border-0 bg-light shadow-none">
                    <option value="">Todas las rutas</option>
                    @foreach($rutasList as $r)
                        <option value="{{ $r->id_ruta }}" {{ request('id_ruta') == $r->id_ruta ? 'selected' : '' }}>
                            {{ $r->codigo_ruta ?? ('Ruta '.$r->id_ruta) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Día (Fecha)</label>
                <input type="date" name="fecha" value="{{ request('fecha') }}" class="form-control border-0 bg-light shadow-none">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100 fw-bold">Filtrar</button>
                <a href="{{ route('controlador-tiempo.planillas.index') }}" class="btn btn-light border bg-white px-3" title="Limpiar">
                    <span class="material-symbols-rounded align-middle">restart_alt</span>
                </a>
            </div>
        </form>
    </div>

    {{-- ─── Planilla del día ─────────────────────────────────────── --}}
    <div class="bg-white rounded-3 shadow-sm overflow-hidden">
        <div class="d-flex align-items-center justify-content-between p-4 pb-3">
            <div>
                <h6 class="fw-bold mb-0">Planilla de Despacho</h6>
                <span class="text-muted small">
                    @if(request('fecha'))
                        Filtrado por: {{ \Carbon\Carbon::parse(request('fecha'))->locale('es')->isoFormat('D [de] MMM, YYYY') }}
                    @else
                        {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                    @endif
                </span>
            </div>
            <div class="d-flex gap-2">
                <span class="badge" style="background:var(--ct-accent-light); color:var(--ct-accent); border:1px solid var(--ct-accent-mid); border-radius:999px; padding:0.3em 0.9em; font-size:0.8rem; font-weight:600;">
                    {{ $planilla->total() }} turnos encontrados
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
                        <th class="py-3">Fecha</th>
                        <th class="py-3 text-center">Novedad / Detalle</th>
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
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-primary small">Ruta #{{ $asig->ruta->codigo_ruta ?? $asig->ruta->id_ruta }}</span>
                                    <span class="text-muted" style="font-size: 0.75rem;">
                                        {{ $asig->ruta->barrioOrigen->nombre ?? '—' }}
                                        <span class="opacity-50">→</span>
                                        {{ $asig->ruta->barrioDestino->nombre ?? '—' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark">
                                    {{ \Carbon\Carbon::parse($asig->fecha_inicio)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $allNovedades = $asig->recorridos->flatMap->novedades;
                                    $checkpoints = $allNovedades->where('tipo', 'CHECKPOINT')->count();
                                    $incidencias = $allNovedades->where('tipo', 'INCIDENCIA')->count();
                                @endphp
                                <a href="{{ route('controlador-tiempo.planillas.show', $asig->id_asignacion) }}" class="text-decoration-none">
                                    <div class="d-flex justify-content-center gap-2">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" title="Checkpoints">
                                            <span class="material-symbols-rounded align-middle fs-6">beenhere</span> {{ $checkpoints }}
                                        </span>
                                        <span class="badge {{ $incidencias > 0 ? 'bg-danger text-white' : 'bg-light text-muted border' }}" title="Incidencias">
                                            <span class="material-symbols-rounded align-middle fs-6">warning</span> {{ $incidencias }}
                                        </span>
                                    </div>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <span class="material-symbols-rounded d-block mb-2" style="font-size:2.5rem;">assignment</span>
                                No se encontraron turnos con los filtros aplicados.
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
