@extends('jefemantenimiento.layouts.app')

@section('title', 'Historial de Mantenimientos — SIGU')

@section('content')
<div class="sigu-fade">
    <div class="sigu-page-hd d-flex justify-content-between align-items-center">
        <div>
            <h1 class="sigu-page-title">Historial de Mantenimientos</h1>
            <p class="sigu-page-sub">Registro histórico de reparaciones y preventivos de la flota.</p>
        </div>
        <div>
            <a href="{{ route('jefemantenimiento.create') }}" class="btn" style="background:var(--p); color:white; border-radius:0.5rem; padding: 0.5rem 1rem; text-decoration:none; display:inline-flex; align-items:center; gap:0.5rem;">
                <span class="material-symbols-rounded">add</span> Nuevo Mantenimiento
            </a>
        </div>
    </div>

        @if(session('success'))
            <div class="alert alert-success mt-4 mb-4" style="background:#e6fffa; color:#234e52; padding:1rem; border-radius:0.5rem;">
                {{ session('success') }}
            </div>
        @endif

    <div class="bg-white rounded-lg shadow-sm p-4 mt-4 mb-4">
        <form action="{{ route('jefemantenimiento.index') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Fecha Desde</label>
                    <input type="date" name="fecha_desde" class="form-control form-control-sm" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control form-control-sm" value="{{ request('fecha_hasta') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Placa</label>
                    <input type="text" name="placa" class="form-control form-control-sm" placeholder="Ej: ABC-123" value="{{ request('placa') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Costo Mín</label>
                    <input type="number" name="costo_min" class="form-control form-control-sm" placeholder="0" min="0" value="{{ request('costo_min') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Costo Máx</label>
                    <input type="number" name="costo_max" class="form-control form-control-sm" placeholder="9999999" min="0" value="{{ request('costo_max') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Estado</label>
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="4" {{ request('estado') == 4 ? 'selected' : '' }}>En Taller</option>
                        <option value="5" {{ request('estado') == 5 ? 'selected' : '' }}>Finalizado</option>
                    </select>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2 justify-content-end">
                <a href="{{ route('jefemantenimiento.index') }}" class="btn btn-sm btn-light border">Limpiar</a>
                <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
                <button type="submit" name="export" value="1" class="btn btn-sm btn-success d-flex align-items-center gap-1">
                    <span class="material-symbols-rounded" style="font-size: 1rem;">description</span> Exportar Excel
                </button>
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
                        <th>Kilometraje</th>
                        <th>Costo Total</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mantenimientos as $mant)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($mant->fecha_mantenimiento)->format('d/m/Y') }}</td>
                            <td>
                                <strong>{{ $mant->placa }}</strong>
                            </td>
                            <td>{{ number_format($mant->kilometraje) }} KM</td>
                            <td>${{ number_format($mant->costo_total, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge @if($mant->id_estado == 5) bg-success @elseif($mant->id_estado == 4) bg-warning text-dark @else bg-secondary @endif">
                                    {{ $mant->estado ? $mant->estado->nombre_estado : 'Desconocido' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('jefemantenimiento.show', $mant->id_mantenimiento) }}" class="btn btn-sm" style="border:1px solid var(--p); color:var(--p); border-radius:0.5rem; padding: 0.25rem 0.5rem; text-decoration:none;">
                                    Detalles
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No se han registrado mantenimientos aún.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $mantenimientos->appends(request()->all())->links() }}
        </div>
    </div>
</div>
@endsection
