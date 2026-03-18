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
                                @if(str_contains(strtoupper($nomEstado), 'TALLER') || $bus->id_estado == 7)
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
                                <button type="button" class="btn btn-sm"
                                    style="background:var(--ct-accent-light); color:var(--ct-accent); border:1px solid var(--ct-accent-mid); border-radius:0.5rem; padding:0.2rem 0.7rem; font-size:0.78rem; font-weight:600;"
                                    title="Registrar novedad">
                                    <span class="material-symbols-rounded align-middle" style="font-size:1rem;">add_circle</span>
                                    Novedad
                                </button>
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
