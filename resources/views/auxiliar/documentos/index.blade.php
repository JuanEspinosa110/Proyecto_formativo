@extends('auxiliar.layouts.app')

@section('title', 'Gestión de Documentos — Auxiliar')

@section('content')
<div class="container-fluid pt-0 pb-4">
    
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold">Gestión de Documentos</h1>
            <p class="text-muted small mb-0">Suba y gestione documentos de vehículos y conductores.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('auxiliar.documentos.auditoria') }}" class="btn btn-light btn-sm d-flex align-items-center gap-2 px-3 fw-semibold shadow-sm rounded-pill border">
                <span class="material-symbols-rounded fs-5 text-secondary">history</span> Auditoría
            </a>
            <a href="{{ route('auxiliar.documentos.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-3 fw-bold shadow-sm rounded-pill">
                <span class="material-symbols-rounded fs-5">upload_file</span> Subir Documento
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm py-2 mb-4">{{ session('success') }}</div>
    @endif

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body p-3">
            <form method="GET" action="" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">
                            <span class="material-symbols-rounded text-muted">search</span>
                        </span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Buscar por Nombre o Placa..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="tipo" class="form-select bg-light border-0">
                        <option value="">Todos los tipos</option>
                        @foreach($tiposDocumento as $t)
                            <option value="{{ $t->id_tipo_documento }}" {{ request('tipo') == $t->id_tipo_documento ? 'selected' : '' }}>
                                {{ $t->nombre }}
                            </option>
                        @endforeach
                    </select>
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
                        <th class="ps-4 py-3">DOCUMENTO</th>
                        <th class="py-3">Asociado A</th>
                        <th class="py-3">Vencimiento</th>
                        <th class="py-3">Estado</th>
                        <th class="py-3 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documentos as $d)
                        <tr class="border-top">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="material-symbols-rounded text-secondary fs-3">description</span>
                                    <div>
                                        <span class="fw-bold d-block text-dark">{{ $d->nombre }}</span>
                                        <small class="text-muted">{{ $d->tipoDocumento->nombre ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($d->placa)
                                    <span class="badge bg-light text-dark border">
                                        <span class="material-symbols-rounded fs-6 align-middle text-primary">directions_bus</span> {{ $d->placa }}
                                    </span>
                                @elseif($d->doc_usuario)
                                    <span class="badge bg-light text-dark border">
                                        <span class="material-symbols-rounded fs-6 align-middle text-success">person</span> {{ $d->doc_usuario }}
                                    </span>
                                @else
                                    <span class="text-muted small">Global</span>
                                @endif
                            </td>
                            <td>
                                <div class="small fw-medium {{ $d->fecha_vencimiento && \Carbon\Carbon::parse($d->fecha_vencimiento)->isPast() ? 'text-danger' : 'text-dark' }}">
                                    {{ $d->fecha_vencimiento ? \Carbon\Carbon::parse($d->fecha_vencimiento)->format('d/m/Y') : 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $d->estado && $d->estado->nombre_estado == 'VIGENTE' ? 'success' : 'warning' }}-subtle text-{{ $d->estado && $d->estado->nombre_estado == 'VIGENTE' ? 'success' : 'warning' }} border rounded-pill px-3">
                                    {{ $d->estado->nombre_estado ?? 'Desconocido' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ asset('storage/' . $d->archivo) }}" target="_blank" class="btn btn-sm btn-light border text-info" title="Ver Archivo">
                                        <span class="material-symbols-rounded fs-5">visibility</span>
                                    </a>
                                    <a href="{{ route('auxiliar.documentos.edit', $d->id_documento) }}" class="btn btn-sm btn-light border text-primary" title="Editar">
                                        <span class="material-symbols-rounded fs-5">edit</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <span class="material-symbols-rounded fs-1 opacity-25">folder_open</span>
                                <p class="mt-2 mb-0">No se encontraron documentos.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $documentos->links('pagination::bootstrap-5') }}</div>
</div>
@endsection
