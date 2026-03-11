@extends('admin.layouts.app')

@section('title', 'Propietarios — SIGU')

@section('content')
<div class="container-fluid pt-0 pb-4">
    <!-- Header de Página -->
    <div class="d-flex align-items-center justify-content-between mb-4 px-1">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold">Panel de Propietario</h1>
            <p class="text-muted small mb-0">Gestiona la información y documentación de tu vehículo.</p>
        </div>
        @if($bus)
        <button class="btn btn-primary d-flex align-items-center gap-2 px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSubirDocumento">
            <span class="material-symbols-rounded">upload_file</span>
            Subir Documento
        </button>
        @endif
    </div>

    <!-- Alertas -->
    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <span class="material-symbols-rounded">check_circle</span>
            <span class="fw-medium">{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <span class="material-symbols-rounded">error</span>
            <span class="fw-medium">{{ session('error') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- TARJETAS SUPERIORES -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                            <span class="material-symbols-rounded">directions_bus</span>
                        </div>
                        <span class="text-muted small fw-bold text-uppercase ls-1">Mi Bus</span>
                    </div>
                    <h3 class="fw-black mb-0 text-dark">{{ $bus->placa ?? 'N/A' }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-success bg-opacity-10 text-success p-2 rounded-3">
                            <span class="material-symbols-rounded">check_circle</span>
                        </div>
                        <span class="text-muted small fw-bold text-uppercase ls-1">Estado</span>
                    </div>
                    <h3 class="fw-black mb-0 text-dark">{{ optional($bus?->estado)->nombre_estado ?? '—' }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-info bg-opacity-10 text-info p-2 rounded-3">
                            <span class="material-symbols-rounded">route</span>
                        </div>
                        <span class="text-muted small fw-bold text-uppercase ls-1">Asignaciones</span>
                    </div>
                    <h3 class="fw-black mb-0 text-dark">{{ $conteoAsignaciones }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3">
                            <span class="material-symbols-rounded">description</span>
                        </div>
                        <span class="text-muted small fw-bold text-uppercase ls-1">Documentos</span>
                    </div>
                    <h3 class="fw-black mb-0 text-dark">{{ $conteoDocumentos }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- SECCIÓN 1: INFORMACIÓN DEL BUS -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark d-flex align-items-center gap-2">
                        <span class="material-symbols-rounded text-primary">directions_bus</span>
                        Información del Bus
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($bus)
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="text-muted small fw-bold text-uppercase ls-1 d-block">Placa</label>
                            <span class="fs-5 fw-extrabold text-primary">{{ $bus->placa }}</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small fw-bold text-uppercase ls-1 d-block">Estado</label>
                            @php
                            $c = match((int)($bus->id_estado ?? 0)) {
                                1 => 'success',
                                2 => 'danger',
                                7 => 'info',
                                default => 'warning'
                            };
                            @endphp
                            <span class="badge bg-{{ $c }}-subtle text-{{ $c }} border border-{{ $c }} rounded-pill px-3">
                                {{ optional($bus->estado)->nombre_estado ?? 'Desconocido' }}
                            </span>
                        </div>
                        <div class="col-12">
                            <hr class="my-2 opacity-10">
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase ls-1 d-block">Modelo / Referencia</label>
                            <span class="fw-medium text-dark">{{ $bus->modelo }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase ls-1 d-block">Capacidad</label>
                            <span class="fw-medium text-dark">{{ $bus->capacidad_pasajeros }} pasajeros</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase ls-1 d-block">Kilometraje</label>
                            <span class="fw-medium text-dark">{{ number_format($bus->kilometraje) }} km</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase ls-1 d-block">Licencia Tránsito</label>
                            <span class="fw-medium text-dark">{{ $bus->linc_transito }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase ls-1 d-block">Núm. Chasis</label>
                            <span class="fw-medium text-dark">{{ $bus->numero_chasis ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase ls-1 d-block">Núm. Motor</label>
                            <span class="fw-medium text-dark">{{ $bus->numero_motor ?? 'N/A' }}</span>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <span class="material-symbols-rounded display-4 opacity-25">contact_support</span>
                        <p class="mt-2 text-muted fw-medium">No se encontró un vehículo asociado a tu documento.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- SECCIÓN 2: DOCUMENTACIÓN -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark d-flex align-items-center gap-2">
                        <span class="material-symbols-rounded text-primary">description</span>
                        Documentación del Bus
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3 py-3 text-uppercase small fw-bold text-muted border-0">Documento</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted border-0 text-center">Archivo</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted border-0 text-center">Vencimiento</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted border-0 text-end pe-3">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documentos as $doc)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="material-symbols-rounded text-muted fs-5">article</span>
                                            <div>
                                                <span class="d-block fw-bold text-dark">{{ $doc->tipoDocumento->nombre ?? $doc->nombre }}</span>
                                                <small class="text-muted">{{ $doc->nombre }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($doc->archivo)
                                        <a href="{{ Storage::url($doc->archivo) }}" target="_blank" class="btn btn-sm btn-light border rounded-pill px-3 fw-medium text-primary d-inline-flex align-items-center gap-1">
                                            <span class="material-symbols-rounded fs-6">visibility</span>
                                            Ver
                                        </a>
                                        @else
                                        <span class="text-muted small italic">Sin archivo</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                        $vencido = $doc->fecha_vencimiento < now();
                                        @endphp
                                        <span class="badge bg-{{ $vencido ? 'danger' : 'success' }}-subtle text-{{ $vencido ? 'danger' : 'success' }} border border-{{ $vencido ? 'danger' : 'success' }} rounded-pill px-2">
                                            {{ $doc->fecha_vencimiento ? $doc->fecha_vencimiento->format('d/m/Y') : '—' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <button class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalActualizarDocumento"
                                                data-id="{{ $doc->id_documento }}"
                                                data-nombre="{{ $doc->nombre }}"
                                                data-expedicion="{{ $doc->fecha_expedicion ? $doc->fecha_expedicion->format('Y-m-d') : '' }}"
                                                data-vencimiento="{{ $doc->fecha_vencimiento ? $doc->fecha_vencimiento->format('Y-m-d') : '' }}">
                                            Actualizar
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <p class="mb-0 small">No hay documentos registrados para este vehículo.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 3: ASIGNACIONES -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold text-dark d-flex align-items-center gap-2">
                        <span class="material-symbols-rounded text-primary">route</span>
                        Asignaciones del Bus
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Filtros Asignaciones -->
                    <form method="GET" action="{{ route('propietario.dashboard') }}" class="row g-2 mb-4 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">FECHA</label>
                            <input type="date" name="fecha" class="form-control form-control-sm" value="{{ request('fecha') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">CONDUCTOR</label>
                            <input type="text" name="conductor" class="form-control form-control-sm" placeholder="Nombre..." value="{{ request('conductor') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">ESTADO</label>
                            <select name="estado" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                @foreach($estados as $est)
                                <option value="{{ $est->id_estado }}" {{ request('estado') == $est->id_estado ? 'selected' : '' }}>
                                    {{ $est->nombre_estado }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-dark btn-sm w-100 fw-bold">Filtrar</button>
                                <a href="{{ route('propietario.dashboard') }}" class="btn btn-light btn-sm w-50 fw-bold border">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3 py-3 text-uppercase small fw-bold text-muted border-0">ID</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Ruta</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Conductor</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted border-0 text-center">Fecha</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted border-0 text-center">Hora</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted border-0 text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($asignaciones as $asig)
                                <tr>
                                    <td class="ps-3 fw-bold text-muted">#{{ $asig->id_viaje }}</td>
                                    <td><span class="badge bg-dark px-2">{{ $asig->placa }}</span></td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $asig->ruta->nombre_ruta ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $asig->ruta->codigo_ruta ?? '' }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                <span class="material-symbols-rounded fs-6 text-muted">person</span>
                                            </div>
                                            <div>
                                                <span class="d-block fw-medium text-dark">{{ $asig->conductor->primer_nombre ?? 'N/A' }} {{ $asig->conductor->primer_apellido ?? '' }}</span>
                                                <small class="text-muted">{{ $asig->conductor->doc_usuario ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-bold text-dark">{{ $asig->fecha ? date('d/m/Y', strtotime($asig->fecha)) : '—' }}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-bold text-dark">{{ $asig->fecha ? date('H:i', strtotime($asig->fecha)) : '—' }}</div>
                                    </td>
                                    <td class="text-center">
                                        @php
                                        $ec = match((int)($asig->id_estado ?? 0)) {
                                            1 => 'success',
                                            2 => 'danger',
                                            11 => 'primary',
                                            default => 'secondary'
                                        };
                                        @endphp
                                        <span class="badge bg-{{ $ec }}-subtle text-{{ $ec }} border border-{{ $ec }} rounded-pill px-3">
                                            {{ optional($asig->estado)->nombre_estado ?? 'Desconocido' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <span class="material-symbols-rounded display-4 opacity-25">assignment_late</span>
                                        <p class="mt-2 fw-medium">No se encontraron asignaciones.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $asignaciones->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal SUBIR DOCUMENTO -->
@if($bus)
<div class="modal fade" id="modalSubirDocumento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded text-primary">add_circle</span>
                    Subir Nuevo Documento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('propietario.subirDocumento') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Nombre Descriptivo</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: SOAT 2024" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Tipo de Documento</label>
                            <select name="id_tipo_documento" class="form-select" required>
                                <option value="" disabled selected>Seleccionar tipo...</option>
                                @foreach($tiposDocumento as $tipo)
                                <option value="{{ $tipo->id_tipo_documento }}">{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Fecha Expedición</label>
                            <input type="date" name="fecha_expedicion" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Fecha Vencimiento</label>
                            <input type="date" name="fecha_vencimiento" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Archivo (PDF, Imagen)</label>
                            <input type="file" name="archivo" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                            <small class="text-muted mt-1 d-block">Máximo 5MB</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">Guardar Documento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal ACTUALIZAR DOCUMENTO -->
<div class="modal fade" id="modalActualizarDocumento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded text-warning">edit_square</span>
                    Actualizar Documento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formActualizarDoc" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Nombre Descriptivo</label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Fecha Expedición</label>
                            <input type="date" name="fecha_expedicion" id="edit_expedicion" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Fecha Vencimiento</label>
                            <input type="date" name="fecha_vencimiento" id="edit_vencimiento" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Reemplazar Archivo (Opcional)</label>
                            <input type="file" name="archivo" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <small class="text-muted mt-1 d-block">Deja en blanco para conservar el archivo actual.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4 shadow-sm text-dark">Actualizar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalActualizar = document.getElementById('modalActualizarDocumento');
        if (modalActualizar) {
            modalActualizar.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nombre = button.getAttribute('data-nombre');
                const expedicion = button.getAttribute('data-expedicion');
                const vencimiento = button.getAttribute('data-vencimiento');

                const form = document.getElementById('formActualizarDoc');
                form.action = `/propietario/documento/${id}`;

                document.getElementById('edit_nombre').value = nombre;
                document.getElementById('edit_expedicion').value = expedicion;
                document.getElementById('edit_vencimiento').value = vencimiento;
            });
        }
    });
</script>

<style>
    .fw-extrabold { font-weight: 800; }
    .ls-1 { letter-spacing: 0.5px; }
    .card { border-radius: 1.25rem !important; }
    .table-hover tbody tr:hover { background-color: rgba(0,0,0,0.02) !important; }
    .badge { font-weight: 600; font-size: 0.75rem; }
    .form-control:focus, .form-select:focus {
        border-color: #5d548e;
        box-shadow: 0 0 0 0.25rem rgba(93, 84, 142, 0.1);
    }
    .material-symbols-rounded { font-variant-ligatures: common-ligatures; }
    hr { border-top: 1px solid #000; }
</style>
@endpush
@endsection
