@extends('auxiliar.layouts.app')

@section('title', 'Asignaciones Operativas — Auxiliar')

@section('content')
<div class="container-fluid pt-0 pb-4">
    
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold">Asignaciones Operativas</h1>
            <p class="text-muted small mb-0">Gestión de rutas asignadas a buses y conductores.</p>
        </div>
        <a href="{{ route('auxiliar.asignaciones.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-3 fw-bold shadow-sm rounded-pill">
            <span class="material-symbols-rounded fs-5">add_circle</span> Nueva Asignación
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm py-2 mb-4">{{ session('success') }}</div>
    @endif

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body p-3">
            <form method="GET" action="" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">
                            <span class="material-symbols-rounded text-muted">search</span>
                        </span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Buscar por Placa o Conductor..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2 ms-auto">
                    <button class="btn btn-dark w-100 fw-semibold rounded-pill">Buscar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted text-uppercase small fw-bold">
                    <tr>
                        <th class="ps-4 py-3">VIAJE ID</th>
                        <th class="py-3">Bus (Placa)</th>
                        <th class="py-3">Ruta</th>
                        <th class="py-3">Conductor</th>
                        <th class="py-3">Fecha/Hora</th>
                        <th class="py-3 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asignaciones as $a)
                        <tr class="border-top">
                            <td class="ps-4 fw-bold text-secondary">#{{ $a->id_viaje }}</td>
                            <td>
                                <span class="badge bg-light text-dark border px-2">
                                    <span class="material-symbols-rounded fs-6 align-middle text-primary">directions_bus</span>
                                    {{ $a->placa }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ $a->ruta->nombre_ruta ?? 'N/A' }}</div>
                                <small class="text-muted">ID: {{ $a->id_ruta }}</small>
                            </td>
                            <td>
                                @if($a->conductor)
                                    <div class="fw-medium text-dark">{{ $a->conductor->primer_nombre }} {{ $a->conductor->primer_apellido }}</div>
                                    <small class="text-muted">Doc: {{ $a->doc_us }}</small>
                                @else
                                    <span class="text-danger small">No asignado</span>
                                @endif
                            </td>
                            <td>
                                <div class="small text-dark">{{ \Carbon\Carbon::parse($a->fecha)->format('d/m/Y') }}</div>
                                <div class="small text-muted">{{ \Carbon\Carbon::parse($a->fecha)->format('H:i A') }}</div>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('auxiliar.asignaciones.edit', $a->id_viaje) }}" class="btn btn-sm btn-light border text-primary" title="Editar">
                                        <span class="material-symbols-rounded fs-5">edit</span>
                                    </a>
                                    <form action="{{ route('auxiliar.asignaciones.destroy', $a->id_viaje) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta asignación?')">
                                            <span class="material-symbols-rounded fs-5">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <span class="material-symbols-rounded fs-1 opacity-25">assignment</span>
                                <p class="mt-2 mb-0">No hay asignaciones registradas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $asignaciones->links('pagination::bootstrap-5') }}</div>
</div>
@endsection
