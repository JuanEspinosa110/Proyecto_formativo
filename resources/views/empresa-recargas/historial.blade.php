@extends('empresa-recargas.layouts.app')

@section('title', 'Historial de Recargas')

@section('content')
<div class="admin-dashboard sigu-fade">
    <div class="sigu-page-hd d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="sigu-page-title">Historial de Recargas</h1>
            <p class="sigu-page-sub">Registro de todas las recargas realizadas por la empresa</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
        <form method="GET" action="{{ route('gestor-recargas.historial') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-medium text-muted small">ID/Código de Tarjeta</label>
                <input type="text" name="id_tarjeta" class="form-control" value="{{ request('id_tarjeta') }}" placeholder="Buscar por ID...">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium text-muted small">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium text-muted small">Fecha Fin</label>
                <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-medium">
                    <span class="material-symbols-rounded align-middle fs-5 me-1">search</span> Filtrar
                </button>
                <a href="{{ route('gestor-recargas.historial') }}" class="btn btn-light w-100 fw-medium">Limpiar</a>
            </div>
        </form>
        
        <div class="mt-3 pt-3 border-top d-flex justify-content-end">
            <a href="{{ route('gestor-recargas.historial', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success text-white fw-medium d-flex align-items-center gap-2">
                <span class="material-symbols-rounded fs-5">download</span> Descargar a Excel
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">ID Recarga</th>
                            <th>Tarjeta</th>
                            <th>Monto</th>
                            <th>Fecha y Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recargas as $recarga)
                        <tr>
                            <td class="ps-4 fw-medium">#{{ $recarga->id_recarga }}</td>
                            <td>{{ $recarga->id_tarjeta }}</td>
                            <td class="text-success fw-bold">${{ number_format($recarga->monto, 0, ',', '.') }}</td>
                            <td class="text-muted">{{ $recarga->created_at->format('d/m/Y h:i A') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                No hay recargas registradas aún.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end">
        {{ $recargas->links() }}
    </div>
</div>
@endsection
