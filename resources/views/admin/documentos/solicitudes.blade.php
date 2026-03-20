@extends('admin.layouts.app')

@section('title', 'Solicitudes de Documentos — SIGU')

@section('content')
<div class="container-fluid pt-0 pb-4">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 px-1">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold">Solicitudes de Documentos</h1>
            <p class="text-muted small mb-0">Revisa y gestiona los documentos enviados por los propietarios.</p>
        </div>
        <div>
            <a href="{{ route('empresa.documentos.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 px-3 fw-semibold">
                <span class="material-symbols-rounded">folder</span>
                Inventario General
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4 rounded-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('empresa.documentos.solicitudes') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="placa" class="form-control bg-light" placeholder="Filtrar por placa..." value="{{ request('placa') }}">
                </div>
                <div class="col-md-5">
                    <input type="text" name="propietario" class="form-control bg-light" placeholder="Filtrar por propietario..." value="{{ request('propietario') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 fw-semibold">Buscar</button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2 mb-4 alert-dismissible fade show" role="alert">
        <span class="material-symbols-rounded">check_circle</span>
        <span class="fw-medium">{{ session('success') }}</span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Tabla -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted border-0">Vehículo / Propietario</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Documento</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Vencimiento</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Archivo</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documentos as $doc)
                    <tr class="border-top">
                        <td class="ps-4">
                            <span class="d-block fw-bold text-dark">{{ $doc->placa }}</span>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">
                                {{ $doc->bus->doc_propietario ?? 'Placa asociada' }}
                            </small>
                        </td>
                        <td>
                            <span class="fw-medium d-block text-dark">{{ $doc->tipoDocumento->nombre ?? 'N/D' }}</span>
                            <small class="text-muted" style="font-size: 0.7rem;">{{ $doc->nombre }}</small>
                        </td>
                        <td class="small">{{ $doc->fecha_vencimiento ? $doc->fecha_vencimiento->format('d/m/Y') : '—' }}</td>
                        <td>
                            <a href="{{ route('empresa.documentos.download', $doc->id_documento) }}" class="btn btn-sm btn-light border d-inline-flex align-items-center gap-1 text-secondary" title="Descargar / Ver">
                                <span class="material-symbols-rounded fs-5">visibility</span> Ver
                            </a>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <form action="{{ route('empresa.documentos.aprobar', $doc->id_documento) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success d-flex align-items-center gap-1 fw-semibold py-1 px-2 shadow-sm" onclick="return confirm('¿Aprobar este documento?')">
                                        <span class="material-symbols-rounded fs-6">check</span> Aprobar
                                    </button>
                                </form>
                                <form action="{{ route('empresa.documentos.rechazar', $doc->id_documento) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger d-flex align-items-center gap-1 fw-semibold py-1 px-2 shadow-sm" onclick="return confirm('¿Rechazar este documento?')">
                                        <span class="material-symbols-rounded fs-6">close</span> Rechazar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <span class="material-symbols-rounded display-4 opacity-25">description</span>
                            <p class="mt-2 fw-medium">No se encontraron solicitudes pendientes.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($documentos->count() > 0 && $documentos->hasPages())
        <div class="p-3 border-top">
            {{ $documentos->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
