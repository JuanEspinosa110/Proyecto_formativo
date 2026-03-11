@extends('admin.layouts.app')

@section('title', 'Asignaciones — SIGU')

@section('content')
<div class="container-fluid pt-0 pb-4">
    <!-- Header de Página -->
    <div class="d-flex align-items-center justify-content-between mb-4 px-1">
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
                            <div class="d-flex justify-content-end gap-3">
                                <a href="#" 
                                   class="text-info text-decoration-none d-flex align-items-center view-asignacion"
                                   title="Ver detalles"
                                   data-json="{{ json_encode($asig) }}"
                                   data-conductor="{{ optional($asig->conductor)->primer_nombre }} {{ optional($asig->conductor)->primer_apellido }}"
                                   data-ruta="{{ $asig->ruta->nombre_ruta ?? 'Ruta #'.$asig->id_ruta }}"
                                   data-estado="{{ optional($asig->estado)->nombre_estado }}">
                                    <span class="material-symbols-rounded fs-5">visibility</span>
                                </a>
                                <a href="#" 
                                   class="text-primary text-decoration-none d-flex align-items-center edit-asignacion"
                                   title="Editar asignación"
                                   data-bs-toggle="modal"
                                   data-bs-target="#modalEditAsignacion"
                                   data-json="{{ json_encode($asig) }}">
                                    <span class="material-symbols-rounded fs-5">edit</span>
                                </a>
                                <form action="{{ route('admin.asignaciones.destroy', $asig->id_viaje) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta asignación?')">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="form_type" value="delete">
                                    <button type="submit" class="p-0 border-0 bg-transparent text-danger d-flex align-items-center" title="Eliminar">
                                        <span class="material-symbols-rounded fs-5">delete</span>
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
<div class="modal fade @if($errors->any() && old('form_type') == 'create') show @endif" id="modalCreateAsignacion" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small">
                    <span class="material-symbols-rounded text-primary me-2 fs-5">add_circle</span>
                    NUEVA ASIGNACIÓN
                </h6>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formCreateAsignacion" action="{{ route('admin.asignaciones.store') }}" method="POST">
                @csrf
                <input type="hidden" name="form_type" value="create">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Vehículo (Placa) <span class="text-danger">*</span></label>
                            <select name="placa" class="form-select form-select-sm @error('placa') is-invalid @enderror" required>
                                <option value="" selected disabled>Seleccionar...</option>
                                @foreach($buses as $bus)
                                <option value="{{ $bus->placa }}" @if(old('placa') == $bus->placa) selected @endif>{{ $bus->placa }} - {{ $bus->modelo }}</option>
                                @endforeach
                            </select>
                            @error('placa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Ruta <span class="text-danger">*</span></label>
                            <select name="id_ruta" class="form-select form-select-sm @error('id_ruta') is-invalid @enderror" required>
                                <option value="" selected disabled>Seleccionar...</option>
                                @foreach($rutas as $ruta)
                                <option value="{{ $ruta->id_ruta }}" @if(old('id_ruta') == $ruta->id_ruta) selected @endif>{{ $ruta->nombre_ruta ?? 'Ruta #'.$ruta->id_ruta }}</option>
                                @endforeach
                            </select>
                            @error('id_ruta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Conductor <span class="text-danger">*</span></label>
                            <select name="doc_us" class="form-select form-select-sm @error('doc_us') is-invalid @enderror" required>
                                <option value="" selected disabled>Seleccionar...</option>
                                @foreach($conductores as $con)
                                <option value="{{ $con->doc_usuario }}" @if(old('doc_us') == $con->doc_usuario) selected @endif>{{ $con->primer_nombre }} {{ $con->primer_apellido }}</option>
                                @endforeach
                            </select>
                            @error('doc_us') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Fecha y Hora <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="fecha" class="form-control form-control-sm @error('fecha') is-invalid @enderror" value="{{ old('fecha') }}" required>
                            @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Estado <span class="text-danger">*</span></label>
                            <select name="id_estado" class="form-select form-select-sm @error('id_estado') is-invalid @enderror" required>
                                @foreach($estados as $est)
                                <option value="{{ $est->id_estado }}" @if(old('id_estado', 1) == $est->id_estado) selected @endif>{{ $est->nombre_estado }}</option>
                                @endforeach
                            </select>
                            @error('id_estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-sm btn-light px-3 fw-bold" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm">GUARDAR ASIGNACIÓN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EDITAR -->
<div class="modal fade @if($errors->any() && old('form_type') == 'edit') show @endif" id="modalEditAsignacion" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small">
                    <span class="material-symbols-rounded text-warning me-2 fs-5">edit_square</span>
                    MODIFICAR ASIGNACIÓN
                </h6>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formEditAsignacion" action="{{ old('edit_action') }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="form_type" value="edit">
                <input type="hidden" name="edit_action" id="edit_action_hidden" value="{{ old('edit_action') }}">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Vehículo (Placa) <span class="text-danger">*</span></label>
                            <select name="placa" id="edit_placa" class="form-select form-select-sm @error('placa') is-invalid @enderror" required>
                                @foreach($buses as $bus)
                                <option value="{{ $bus->placa }}" @if(old('placa') == $bus->placa) selected @endif>{{ $bus->placa }} - {{ $bus->modelo }}</option>
                                @endforeach
                            </select>
                            @error('placa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Ruta <span class="text-danger">*</span></label>
                            <select name="id_ruta" id="edit_id_ruta" class="form-select form-select-sm @error('id_ruta') is-invalid @enderror" required>
                                @foreach($rutas as $ruta)
                                <option value="{{ $ruta->id_ruta }}" @if(old('id_ruta') == $ruta->id_ruta) selected @endif>{{ $ruta->nombre_ruta ?? 'Ruta #'.$ruta->id_ruta }}</option>
                                @endforeach
                            </select>
                            @error('id_ruta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Conductor <span class="text-danger">*</span></label>
                            <select name="doc_us" id="edit_doc_us" class="form-select form-select-sm @error('doc_us') is-invalid @enderror" required>
                                @foreach($conductores as $con)
                                <option value="{{ $con->doc_usuario }}" @if(old('doc_us') == $con->doc_usuario) selected @endif>{{ $con->primer_nombre }} {{ $con->primer_apellido }}</option>
                                @endforeach
                            </select>
                            @error('doc_us') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Fecha y Hora <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="fecha" id="edit_fecha" class="form-control form-control-sm @error('fecha') is-invalid @enderror" value="{{ old('fecha') }}" required>
                            @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Estado <span class="text-danger">*</span></label>
                            <select name="id_estado" id="edit_id_estado" class="form-select form-select-sm @error('id_estado') is-invalid @enderror" required>
                                @foreach($estados as $est)
                                <option value="{{ $est->id_estado }}" @if(old('id_estado') == $est->id_estado) selected @endif>{{ $est->nombre_estado }}</option>
                                @endforeach
                            </select>
                            @error('id_estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-sm btn-light px-3 fw-bold" data-bs-dismiss="modal">DESCARTAR</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm">GUARDAR CAMBIOS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal VER ASIGNACIÓN -->
<div class="modal fade" id="modalViewAsignacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small">
                    <span class="material-symbols-rounded text-info me-2 fs-5">assignment_ind</span>
                    DETALLES DE ASIGNACIÓN
                </h6>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            <h4 id="view_placa" class="fw-bold mb-0 text-dark"></h4>
                            <p class="text-muted small mb-0">Vehículo Registrado</p>
                        </div>
                    </div>
                    <span id="view_estado_asig" class="badge rounded-pill"></span>
                </div>

                <div class="p-3 bg-light rounded-3 border border-light-subtle small">
                    <div class="mb-3">
                        <label class="d-block text-muted fw-bold text-uppercase ls-1">Ruta Programada</label>
                        <span id="view_ruta" class="text-dark fw-medium d-block"></span>
                        <small id="view_id_ruta" class="text-muted"></small>
                    </div>
                    <div class="mb-3 border-top pt-2">
                        <label class="d-block text-muted fw-bold text-uppercase ls-1">Conductor Responsable</label>
                        <span id="view_conductor" class="text-dark fw-medium d-block"></span>
                        <small id="view_doc_us" class="text-muted"></small>
                    </div>
                    <div class="row g-2 border-top pt-2">
                        <div class="col-6">
                            <label class="d-block text-muted fw-bold text-uppercase ls-1">Fecha</label>
                            <span id="view_fecha" class="text-dark fw-medium"></span>
                        </div>
                        <div class="col-6">
                            <label class="d-block text-muted fw-bold text-uppercase ls-1">Hora Salida</label>
                            <span id="view_hora" class="text-dark fw-medium"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-light">
                <button type="button" class="btn btn-sm btn-dark w-100 fw-bold" data-bs-dismiss="modal">CERRAR DETALLES</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Delegación para botones VER y EDITAR
    document.addEventListener('click', function(e) {
        // Botón VER
        const btnVer = e.target.closest('.view-asignacion');
        if (btnVer) {
            e.preventDefault();
            try {
                const data = JSON.parse(btnVer.dataset.json);
                const conductor = btnVer.dataset.conductor;
                const ruta = btnVer.dataset.ruta;
                const estado = btnVer.dataset.estado;

                document.getElementById('view_placa').textContent = data.placa;
                document.getElementById('view_ruta').textContent = ruta;
                document.getElementById('view_id_ruta').textContent = `Código Ruta: ${data.id_ruta}`;
                document.getElementById('view_conductor').textContent = conductor;
                document.getElementById('view_doc_us').textContent = `Documento: ${data.doc_us}`;
                
                if (data.fecha) {
                    const date = new Date(data.fecha);
                    document.getElementById('view_fecha').textContent = date.toLocaleDateString();
                    document.getElementById('view_hora').textContent = date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                }

                const viewEst = document.getElementById('view_estado_asig');
                viewEst.textContent = estado;
                viewEst.className = 'badge rounded-pill px-3 py-2 fw-bold';
                if (data.id_estado == 1) {
                    viewEst.classList.add('bg-success-subtle', 'text-success', 'border', 'border-success-subtle');
                } else if (data.id_estado == 2) {
                    viewEst.classList.add('bg-danger-subtle', 'text-danger', 'border', 'border-danger-subtle');
                } else {
                    viewEst.classList.add('bg-warning-subtle', 'text-warning', 'border', 'border-warning-subtle');
                }

                const modalEl = document.getElementById('modalViewAsignacion');
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            } catch (err) { console.error('Ver Asignacion Error:', err); }
        }

        // Botón EDITAR
        const btnEdit = e.target.closest('.edit-asignacion');
        if (btnEdit) {
            e.preventDefault();
            try {
                const data = JSON.parse(btnEdit.dataset.json);
                document.getElementById('edit_placa').value = data.placa;
                document.getElementById('edit_id_ruta').value = data.id_ruta;
                document.getElementById('edit_doc_us').value = data.doc_us;
                document.getElementById('edit_id_estado').value = data.id_estado;
                
                if (data.fecha) {
                    const date = new Date(data.fecha);
                    const offset = date.getTimezoneOffset() * 60000;
                    const localISOTime = (new Date(date - offset)).toISOString().slice(0, 16);
                    document.getElementById('edit_fecha').value = localISOTime;
                }

                const action = `asignaciones/${data.id_viaje}`; // RELATIVA
                document.getElementById('formEditAsignacion').action = action;
                document.getElementById('edit_action_hidden').value = action;
                
                const modalEl = document.getElementById('modalEditAsignacion');
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            } catch (err) { console.error('Edit Asignacion Error:', err); }
        }
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
    .ls-1 {
        letter-spacing: 0.5px;
    }
    .modal-content {
        border-radius: 1rem !important;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(94, 84, 142, 0.03) !important;
        cursor: default;
    }
</style>
@endpush
@endsection
