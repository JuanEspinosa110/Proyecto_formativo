@extends('jefemantenimiento.layouts.app')

@section('title', 'Bandeja de Reportes de Fallas — SIGU')

@push('css')
<style>
    .report-card { border-left: 4px solid var(--p); }
    .report-card.urgencia-Alto { border-left-color: #e53e3e; } /* Red */
    .report-card.urgencia-Medio { border-left-color: #d69e2e; } /* Yellow */
    .report-card.urgencia-Bajo { border-left-color: #38a169; } /* Green */
</style>
@endpush

@section('content')
<div class="sigu-fade">
    <div class="sigu-page-hd">
        <div>
            <h1 class="sigu-page-title">Reportes de Fallas</h1>
            <p class="sigu-page-sub">Bandeja de entrada de fallas reportadas por los conductores.</p>
        </div>
    </div>

        @if(session('success'))
            <div class="alert alert-success mt-4 mb-4" style="background:#e6fffa; color:#234e52; padding:1rem; border-radius:0.5rem;">
                {{ session('success') }}
            </div>
        @endif

    <div class="bg-white rounded-lg shadow-sm p-4 mt-4 mb-4">
        <form action="{{ route('jefemantenimiento.reportes') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Fecha Desde</label>
                    <input type="date" name="fecha_desde" class="form-control form-control-sm" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control form-control-sm" value="{{ request('fecha_hasta') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Placa del Bus</label>
                    <input type="text" name="placa" class="form-control form-control-sm" placeholder="Ej: ABC-123" value="{{ request('placa') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Estado</label>
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="1" {{ request('estado') == 1 ? 'selected' : '' }}>No Atendido</option>
                        <option value="4" {{ request('estado') == 4 ? 'selected' : '' }}>En Taller</option>
                        <option value="5" {{ request('estado') == 5 ? 'selected' : '' }}>Finalizado</option>
                    </select>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2 justify-content-end">
                <a href="{{ route('jefemantenimiento.reportes') }}" class="btn btn-sm btn-light border">Limpiar</a>
                <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4">

        <div class="table-responsive">
            <table class="table sigu-table w-100 table-hover">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Bus (Placa)</th>
                        <th>Conductor</th>
                        <th>Falla Reportada</th>
                        <th>Urgencia</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportes as $reporte)
                        <tr class="report-card urgencia-{{ $reporte->nivel_urgencia }}">
                            <td>{{ $reporte->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <strong>{{ $reporte->placa }}</strong>
                                @if($reporte->bus)
                                <br><small class="text-muted">Mod: {{ $reporte->bus->modelo }}</small>
                                @endif
                            </td>
                            <td>
                                @if($reporte->conductor)
                                    {{ $reporte->conductor->primer_nombre }} {{ $reporte->conductor->primer_apellido }}
                                @else
                                    <span class="text-muted">Desconocido</span>
                                @endif
                            </td>
                            <td>
                                {{ Str::limit($reporte->descripcion, 50) }}
                                <br>
                                <small class="text-muted" style="cursor:help;" title="{{ $reporte->descripcion }}">Ver detale completo...</small>
                            </td>
                            <td>
                                <span class="badge @if($reporte->nivel_urgencia == 'Alto') bg-danger @elseif($reporte->nivel_urgencia == 'Medio') bg-warning text-dark @else bg-success @endif">
                                    {{ $reporte->nivel_urgencia }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('jefemantenimiento.reportes.attend', $reporte->id_reporte) }}" class="btn btn-sm" style="background:var(--p); color:white; border-radius:0.5rem; padding: 0.25rem 0.5rem; text-decoration:none;">
                                    Atender
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No hay reportes de fallas pendientes en este momento.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $reportes->appends(request()->all())->links() }}
        </div>
    </div>
</div>
@endsection
