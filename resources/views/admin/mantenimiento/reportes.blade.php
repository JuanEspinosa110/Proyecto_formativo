@extends('admin.layouts.app')

@section('title', 'Reportes de Fallas — SIGU')

@section('content')
<div class="sigu-fade">
    <div class="sigu-page-hd d-flex justify-content-between align-items-center">
        <div>
            <h1 class="sigu-page-title">Bandeja de Reportes de Fallas</h1>
            <p class="sigu-page-sub">Alertas enviadas por los conductores sobre novedades en los buses.</p>
        </div>
        <div>
            <a href="{{ route('admin.mantenimiento.index') }}" class="btn btn-outline-secondary" style="border-radius:0.5rem;">
                <span class="material-symbols-rounded" style="font-size:1rem;vertical-align:middle;">build</span>
                Ver Mantenimientos
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 mt-4 mb-4">
        <form action="{{ route('admin.mantenimiento.reportes') }}" method="GET">
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
                <a href="{{ route('admin.mantenimiento.reportes') }}" class="btn btn-sm btn-light border">Limpiar</a>
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
                        <th>Descripción</th>
                        <th>Urgencia</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportes as $reporte)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($reporte->created_at)->format('d/m/Y') }}</td>
                            <td>
                                <strong>{{ $reporte->placa }}</strong>
                                @if($reporte->bus)
                                    <br><small class="text-muted">{{ $reporte->bus->modelo }}</small>
                                @endif
                            </td>
                            <td>
                                @if($reporte->conductor)
                                    {{ $reporte->conductor->primer_nombre }} {{ $reporte->conductor->primer_apellido }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="max-width:250px;">{{ Str::limit($reporte->descripcion, 80) }}</td>
                            <td>
                                @php $nivel = $reporte->nivel_urgencia; @endphp
                                <span class="badge @if($nivel == 'Alto') bg-danger @elseif($nivel == 'Medio') bg-warning text-dark @else bg-success @endif">
                                    {{ $nivel ?: 'BAJA' }}
                                </span>
                            </td>
                            <td>
                                @if($reporte->id_estado == 5)
                                    <span class="text-success fw-bold small d-flex align-items-center gap-1">
                                        <span class="material-symbols-rounded fs-6">task_alt</span> FINALIZADO
                                    </span>
                                @elseif($reporte->id_estado == 4)
                                    <span class="text-info fw-bold small d-flex align-items-center gap-1">
                                        <span class="material-symbols-rounded fs-6">engineering</span> EN TALLER
                                    </span>
                                @else
                                    <span class="text-secondary fw-bold small d-flex align-items-center gap-1">
                                        <span class="material-symbols-rounded fs-6">pending</span> NO ATENDIDO
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No hay reportes de fallas registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $reportes->appends(request()->all())->links() }}</div>
    </div>
</div>
@endsection
