@extends('admin.layouts.app')

@section('title', 'Asignaciones — SIGU')

@section('content')
<div class="container-fluid py-4">
    <!-- Header de Página -->
    <div class="d-flex align-items-center justify-content-between mb-4 mt-2 px-1">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold">Gestión de Asignaciones</h1>
            <p class="text-muted small mb-0">Controla la vinculación de conductores, buses y rutas en tiempo real.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="#" class="btn btn-outline-success d-flex align-items-center gap-2 px-3 fw-semibold">
                <span class="material-symbols-rounded" style="font-size: 1.2rem;">file_download</span>
                Excel
            </a>
            <button class="btn btn-primary d-flex align-items-center gap-2 px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateAsignacion">
                <span class="material-symbols-rounded">add</span>
                Nueva Asignación
            </button>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="card border-0 shadow-sm mb-4 rounded-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.asignaciones.index') }}" class="row g-2 align-items-center">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <span class="material-symbols-rounded text-muted">search</span>
                        </span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Buscar por placa o conductor..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-dark w-100 fw-semibold">Filtrar Asignaciones</button>
                </div>
                @if(request()->has('search') && request('search') != '')
                <div class="col-md-2">
                    <a href="{{ route('admin.asignaciones.index') }}" class="btn btn-light w-100 text-muted" title="Limpiar filtros">
                        <span class="material-symbols-rounded" style="font-size: 1.2rem;">filter_alt_off</span>
                    </a>
                </div>
                @endif
            </form>
        </div>
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

    @if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <span class="material-symbols-rounded">error</span>
            <span class="fw-medium">Por favor, corrija los errores en el formulario.</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Tabla de Asignaciones -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted border-0">ID</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Vehículo</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Ruta Asignada</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Conductor</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Fecha / Hora</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Estado</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asignaciones as $asig)
                    <tr class="border-top">
                        <td class="ps-4 fw-bold text-muted">#{{ $asig->id_viaje }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-2 text-primary d-flex">
                                    <span class="material-symbols-rounded fs-5">directions_bus</span>
                                </div>
                                <span class="fw-bold text-dark">{{ $asig->placa }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="lh-1">
                                <span class="d-block fw-medium text-dark">{{ $asig->ruta->nombre_ruta ?? 'Ruta #'.$asig->id_ruta }}</span>
                                <small class="text-muted" style="font-size: 0.7rem;">ID Sistema: {{ $asig->id_ruta }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="material-symbols-rounded text-muted fs-5">person</span>
                                <span class="text-dark">{{ optional($asig->conductor)->primer_nombre }} {{ optional($asig->conductor)->primer_apellido }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1 text-muted small">
                                <span class="material-symbols-rounded fs-6 opacity-50">calendar_today</span>
                                {{ \Carbon\Carbon::parse($asig->fecha)->format('d/m/Y H:i') }}
                            </div>
                        </td>
                        <td>
                            @php
                            $c = match((int)$asig->id_estado) {
                                1 => 'success',
                                2 => 'danger',
                                default => 'warning'
                            };
                            @endphp
                            <span class="badge bg-{{ $c }}-subtle text-{{ $c }} border border-{{ $c }} rounded-pill px-3">
                                {{ optional($asig->estado)->nombre_estado ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-outline-primary btn-sm rounded-3 px-3 edit-asignacion"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditAsignacion"
                                    data-json="{{ json_encode($asig) }}">
                                    <span class="material-symbols-rounded fs-6 align-middle me-1">edit</span>
                                    Editar
                                </button>
                                <form action="{{ route('admin.asignaciones.destroy', $asig->id_viaje) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta asignación?')">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="form_type" value="delete">
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-3">
                                        <span class="material-symbols-rounded fs-6 align-middle">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <span class="material-symbols-rounded display-4 opacity-25">assignment_late</span>
                            <p class="mt-2 fw-medium">No se encontraron asignaciones activas.</p>
                            <small>Comience vinculando un bus y un conductor a una ruta.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($asignaciones->hasPages())
        <div class="p-4 border-top">
            {{ $asignaciones->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal CREAR -->
<div class="modal fade @if($errors->any() && old('form_type') == 'create') show @endif" id="modalCreateAsignacion" tabindex="-1" aria-labelledby="modalCreateLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalCreateLabel">
                    <span class="material-symbols-rounded align-middle me-2 text-primary">add_circle</span>
                    Registrar Nueva Asignación
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCreateAsignacion" action="{{ route('admin.asignaciones.store') }}" method="POST">
                @csrf
                <input type="hidden" name="form_type" value="create">
                <div class="modal-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Vehículo (Placa) <span class="text-danger">*</span></label>
                            <select name="placa" class="form-select bg-light border-0 py-2 @error('placa') is-invalid @enderror" required>
                                <option value="" selected disabled>Seleccionar vehículo...</option>
                                @foreach($buses as $bus)
                                <option value="{{ $bus->placa }}" @if(old('placa') == $bus->placa) selected @endif>{{ $bus->placa }} - {{ $bus->modelo }}</option>
                                @endforeach
                            </select>
                            @error('placa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Ruta <span class="text-danger">*</span></label>
                            <select name="id_ruta" class="form-select bg-light border-0 py-2 @error('id_ruta') is-invalid @enderror" required>
                                <option value="" selected disabled>Seleccionar ruta...</option>
                                @foreach($rutas as $ruta)
                                <option value="{{ $ruta->id_ruta }}" @if(old('id_ruta') == $ruta->id_ruta) selected @endif>{{ $ruta->nombre_ruta ?? 'Ruta #'.$ruta->id_ruta }}</option>
                                @endforeach
                            </select>
                            @error('id_ruta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Conductor <span class="text-danger">*</span></label>
                            <select name="doc_us" class="form-select bg-light border-0 py-2 @error('doc_us') is-invalid @enderror" required>
                                <option value="" selected disabled>Seleccionar conductor...</option>
                                @foreach($conductores as $con)
                                <option value="{{ $con->doc_usuario }}" @if(old('doc_us') == $con->doc_usuario) selected @endif>{{ $con->primer_nombre }} {{ $con->primer_apellido }}</option>
                                @endforeach
                            </select>
                            @error('doc_us') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Fecha y Hora <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="fecha" class="form-control bg-light border-0 py-2 @error('fecha') is-invalid @enderror" value="{{ old('fecha') }}" required>
                            @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Estado <span class="text-danger">*</span></label>
                            <select name="id_estado" class="form-select bg-light border-0 py-2 @error('id_estado') is-invalid @enderror" required>
                                <option value="" selected disabled>Seleccionar estado...</option>
                                @foreach($estados as $est)
                                <option value="{{ $est->id_estado }}" @if(old('id_estado') == $est->id_estado) selected @endif>{{ $est->nombre_estado }}</option>
                                @endforeach
                            </select>
                            @error('id_estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 bg-white d-flex gap-2">
                    <button type="button" class="btn btn-light px-4 fw-semibold border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm flex-fill">Guardar Asignación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EDITAR -->
<div class="modal fade @if($errors->any() && old('form_type') == 'edit') show @endif" id="modalEditAsignacion" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalEditLabel">
                    <span class="material-symbols-rounded align-middle me-2 text-primary">edit_square</span>
                    Editar Asignación
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditAsignacion" action="{{ old('edit_action') }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="form_type" value="edit">
                <input type="hidden" name="edit_action" id="edit_action_hidden" value="{{ old('edit_action') }}">
                <div class="modal-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Vehículo (Placa) <span class="text-danger">*</span></label>
                            <select name="placa" id="edit_placa" class="form-select bg-light border-0 py-2 @error('placa') is-invalid @enderror" required>
                                @foreach($buses as $bus)
                                <option value="{{ $bus->placa }}" @if(old('placa') == $bus->placa) selected @endif>{{ $bus->placa }} - {{ $bus->modelo }}</option>
                                @endforeach
                            </select>
                            @error('placa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Ruta <span class="text-danger">*</span></label>
                            <select name="id_ruta" id="edit_id_ruta" class="form-select bg-light border-0 py-2 @error('id_ruta') is-invalid @enderror" required>
                                @foreach($rutas as $ruta)
                                <option value="{{ $ruta->id_ruta }}" @if(old('id_ruta') == $ruta->id_ruta) selected @endif>{{ $ruta->nombre_ruta ?? 'Ruta #'.$ruta->id_ruta }}</option>
                                @endforeach
                            </select>
                            @error('id_ruta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Conductor <span class="text-danger">*</span></label>
                            <select name="doc_us" id="edit_doc_us" class="form-select bg-light border-0 py-2 @error('doc_us') is-invalid @enderror" required>
                                @foreach($conductores as $con)
                                <option value="{{ $con->doc_usuario }}" @if(old('doc_us') == $con->doc_usuario) selected @endif>{{ $con->primer_nombre }} {{ $con->primer_apellido }}</option>
                                @endforeach
                            </select>
                            @error('doc_us') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Fecha y Hora <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="fecha" id="edit_fecha" class="form-control bg-light border-0 py-2 @error('fecha') is-invalid @enderror" value="{{ old('fecha') }}" required>
                            @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Estado <span class="text-danger">*</span></label>
                            <select name="id_estado" id="edit_id_estado" class="form-select bg-light border-0 py-2 @error('id_estado') is-invalid @enderror" required>
                                @foreach($estados as $est)
                                <option value="{{ $est->id_estado }}" @if(old('id_estado') == $est->id_estado) selected @endif>{{ $est->nombre_estado }}</option>
                                @endforeach
                            </select>
                            @error('id_estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 bg-white d-flex gap-2">
                    <button type="button" class="btn btn-light px-4 fw-semibold border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm flex-fill">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Inicializar Datos de Edición
    document.querySelectorAll('.edit-asignacion').forEach(btn => {
        btn.addEventListener('click', function() {
            const data = JSON.parse(this.dataset.json);

            document.getElementById('edit_placa').value = data.placa;
            document.getElementById('edit_id_ruta').value = data.id_ruta;
            document.getElementById('edit_doc_us').value = data.doc_us;
            document.getElementById('edit_id_estado').value = data.id_estado;
            
            // Formatear fecha para input datetime-local
            if (data.fecha) {
                const date = new Date(data.fecha);
                const offset = date.getTimezoneOffset() * 60000;
                const localISOTime = (new Date(date - offset)).toISOString().slice(0, 16);
                document.getElementById('edit_fecha').value = localISOTime;
            }

            const action = `/admin/asignaciones/${data.id_viaje}`;
            document.getElementById('formEditAsignacion').action = action;
            document.getElementById('edit_action_hidden').value = action;
        });
    });

    @if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        @if(old('form_type') == 'edit')
            var modal = new bootstrap.Modal(document.getElementById('modalEditAsignacion'));
            modal.show();
        @else
            var modal = new bootstrap.Modal(document.getElementById('modalCreateAsignacion'));
            modal.show();
        @endif
    });
    @endif
</script>

<style>
    .modal-content {
        border-radius: 1rem !important;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(94, 84, 142, 0.03) !important;
    }
    .badge {
        font-weight: 600;
        letter-spacing: 0.3px;
    }
</style>
@endpush
@endsection
