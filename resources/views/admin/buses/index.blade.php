@extends('admin.layouts.app')

@section('title', 'Buses — SIGU')

@section('content')
<div class="container-fluid py-4">
    <!-- Header de Página -->
    <div class="d-flex align-items-center justify-content-between mb-4 mt-2 px-1">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold">Gestión de Flota</h1>
            <p class="text-muted small mb-0">Administra los vehículos y sus estados operativos en tiempo real.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.buses.export', request()->all()) }}" class="btn btn-outline-success d-flex align-items-center gap-2 px-3 fw-semibold">
                <span class="material-symbols-rounded" style="font-size: 1.2rem;">file_download</span>
                Excel
            </a>
            <button class="btn btn-primary d-flex align-items-center gap-2 px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateBus">
                <span class="material-symbols-rounded">add</span>
                Nuevo Bus
            </button>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="card border-0 shadow-sm mb-4 rounded-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.buses.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <span class="material-symbols-rounded text-muted">search</span>
                        </span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Buscar por placa o modelo..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="id_estado" class="form-select bg-light">
                        <option value="">Todos los estados</option>
                        @foreach($estados as $est)
                        <option value="{{ $est->id_estado }}" {{ request('id_estado') == $est->id_estado ? 'selected' : '' }}>
                            {{ $est->nombre_estado }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 fw-semibold">Filtrar Resultados</button>
                </div>
                @if(request()->hasAny(['search', 'id_estado']))
                <div class="col-md-1">
                    <a href="{{ route('admin.buses.index') }}" class="btn btn-light w-100 text-muted" title="Limpiar filtros">
                        <span class="material-symbols-rounded" style="font-size: 1.2rem;">filter_alt_off</span>
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Alerta de Éxito -->
    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <span class="material-symbols-rounded">check_circle</span>
            <span class="fw-medium">{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Tabla de Datos Reales -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted border-0">Vehículo / Propietario</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Datos Técnicos</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Capacidad</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Kilometraje</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Estado</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($buses as $bus)
                    <tr class="border-top">
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-2 text-primary">
                                    <span class="material-symbols-rounded">directions_bus</span>
                                </div>
                                <div>
                                    <span class="d-block fw-bold text-dark fs-6">{{ $bus->placa }}</span>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                                        <span class="material-symbols-rounded fs-xs align-middle">person</span>
                                        {{ $bus->doc_propietario ?? 'Sin propietario' }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="lh-sm">
                                <span class="fw-medium d-block text-dark">{{ $bus->modelo ?? 'N/D' }}</span>
                                <small class="text-muted" style="font-size: 0.7rem;">
                                    <strong>Chasis:</strong> {{ $bus->numero_chasis ?? '—' }} |
                                    <strong>Motor:</strong> {{ $bus->numero_motor ?? '—' }}
                                </small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 fw-medium">
                                <span class="material-symbols-rounded fs-6 align-middle me-1">group</span>
                                {{ $bus->capacidad_pasajeros }} pasj.
                            </span>
                        </td>
                        <td class="text-muted small">
                            <div class="d-flex align-items-center gap-1">
                                <span class="material-symbols-rounded fs-6 opacity-50">speed</span>
                                {{ number_format($bus->kilometraje) }} km
                            </div>
                            @if($bus->linc_transito)
                            <small class="text-primary d-block mt-1" style="font-size: 0.65rem;">
                                <strong>LIC:</strong> {{ $bus->linc_transito }}
                            </small>
                            @endif
                        </td>
                        <td>
                            @php
                            $c = match((int)$bus->id_estado) {
                            1 => 'success', // Activo
                            2 => 'danger', // Inactivo
                            7 => 'info', // En mantenimiento
                            default => 'warning'
                            };
                            @endphp
                            <span class="badge bg-{{ $c }}-subtle text-{{ $c }} border border-{{ $c }} rounded-pill px-3">
                                {{ optional($bus->estado)->nombre_estado ?? 'Desconocido' }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-outline-primary btn-sm rounded-3 edit-bus"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditBus"
                                data-json="{{ json_encode($bus) }}">
                                <span class="material-symbols-rounded" style="font-size: 1.1rem; vertical-align: middle;">edit</span>
                                Editar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <span class="material-symbols-rounded display-4 opacity-25">directions_bus</span>
                            <p class="mt-2 fw-medium">No se encontraron buses que coincidan con los criterios.</p>
                            <small>Verifica los filtros activos o agrega un nuevo registro.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            {{ $buses->links() }}
        </div>
    </div>
</div>

<!-- Modal CREAR (Standard Bootstrap Modal) -->
<div class="modal fade" id="modalCreateBus" tabindex="-1" aria-labelledby="modalCreateLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalCreateLabel">
                    <span class="material-symbols-rounded align-middle me-2 text-primary">add_circle</span>
                    Registrar Nuevo Vehículo
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCreateBus" action="{{ route('admin.buses.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4 pb-4">
                    <div id="create-errors-alert" class="alert alert-danger d-none shadow-sm py-2 small mb-4"></div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Placa <span class="text-danger">*</span></label>
                            <input type="text" name="placa" class="form-control bg-light border-0 py-2" placeholder="ABC-123" required style="text-transform:uppercase">
                            <div class="invalid-feedback feedback-placa"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Modelo / Ref. <span class="text-danger">*</span></label>
                            <input type="text" name="modelo" class="form-control bg-light border-0 py-2" placeholder="Toyota 2024" required>
                            <div class="invalid-feedback feedback-modelo"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Pasajeros <span class="text-danger">*</span></label>
                            <input type="number" name="capacidad_pasajeros" class="form-control bg-light border-0 py-2" required min="10">
                            <div class="invalid-feedback feedback-capacidad_pasajeros"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Kilometraje <span class="text-danger">*</span></label>
                            <input type="number" name="kilometraje" class="form-control bg-light border-0 py-2" required min="0">
                            <div class="invalid-feedback feedback-kilometraje"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Lic. Tránsito</label>
                            <input type="number" name="linc_transito" class="form-control bg-light border-0 py-2" placeholder="Número de licencia">
                            <div class="invalid-feedback feedback-linc_transito"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Doc. Propietario</label>
                            <input type="number" name="doc_propietario" class="form-control bg-light border-0 py-2" placeholder="Cédula o NIT">
                            <div class="invalid-feedback feedback-doc_propietario"></div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nombre del Propietario <span class="text-danger">*</span></label>
                            <input type="text" name="nombre_propietario" class="form-control bg-light border-0 py-2" placeholder="Nombre completo" required>
                            <div class="invalid-feedback feedback-nombre_propietario"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Teléfono</label>
                            <input type="text" name="telefono" class="form-control bg-light border-0 py-2" placeholder="Ej: 3001234567">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Correo Electrónico</label>
                            <input type="email" name="correo" class="form-control bg-light border-0 py-2" placeholder="correo@ejemplo.com">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Núm. Chasis</label>
                            <input type="text" name="numero_chasis" class="form-control bg-light border-0 py-2" maxlength="17">
                            <div class="invalid-feedback feedback-numero_chasis"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Núm. Motor</label>
                            <input type="text" name="numero_motor" class="form-control bg-light border-0 py-2" maxlength="14">
                            <div class="invalid-feedback feedback-numero_motor"></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Estado Operativo <span class="text-danger">*</span></label>
                            <select name="id_estado" class="form-select bg-light border-0 py-2" required>
                                <option value="" selected disabled>Seleccionar estado...</option>
                                @foreach($estados as $est)
                                <option value="{{ $est->id_estado }}">{{ $est->nombre_estado }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback feedback-id_estado"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 bg-white d-flex gap-2">
                    <button type="button" class="btn btn-light px-4 fw-semibold border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm flex-fill">Guardar Registro</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EDITAR (Standard Bootstrap Modal) -->
<div class="modal fade" id="modalEditBus" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalEditLabel">
                    <span class="material-symbols-rounded align-middle me-2 text-primary">edit_square</span>
                    Editar Información del Vehículo
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditBus" method="POST">
                @csrf @method('PUT')
                <div class="modal-body px-4 pb-4">
                    <div id="edit-errors-alert" class="alert alert-danger d-none shadow-sm py-2 small mb-4"></div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Placa del Vehículo</label>
                            <input type="text" id="edit_placa_display" class="form-control bg-light border-0 fw-bold text-primary" disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Modelo / Ref. <span class="text-danger">*</span></label>
                            <input type="text" name="modelo" id="edit_modelo" class="form-control bg-light border-0 py-2" required>
                            <div class="invalid-feedback feedback-modelo"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Estado <span class="text-danger">*</span></label>
                            <select name="id_estado" id="edit_id_estado" class="form-select bg-light border-0 py-2" required>
                                @foreach($estados as $est)
                                <option value="{{ $est->id_estado }}">{{ $est->nombre_estado }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Capacidad <span class="text-danger">*</span></label>
                            <input type="number" name="capacidad_pasajeros" id="edit_capacidad" class="form-control bg-light border-0 py-2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Kilometraje <span class="text-danger">*</span></label>
                            <input type="number" name="kilometraje" id="edit_kilometraje" class="form-control bg-light border-0 py-2" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Lic. Tránsito</label>
                            <input type="number" name="linc_transito" id="edit_linc_transito" class="form-control bg-light border-0 py-2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Doc. Propietario</label>
                            <input type="number" name="doc_propietario" id="edit_doc_propietario" class="form-control bg-light border-0 py-2">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nombre del Propietario <span class="text-danger">*</span></label>
                            <input type="text" name="nombre_propietario" id="edit_nombre_propietario" class="form-control bg-light border-0 py-2" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Teléfono</label>
                            <input type="text" name="telefono" id="edit_telefono" class="form-control bg-light border-0 py-2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Correo Electrónico</label>
                            <input type="email" name="correo" id="edit_correo" class="form-control bg-light border-0 py-2">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Núm. Chasis</label>
                            <input type="text" name="numero_chasis" id="edit_numero_chasis" class="form-control bg-light border-0 py-2" maxlength="17">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Núm. Motor</label>
                            <input type="text" name="numero_motor" id="edit_numero_motor" class="form-control bg-light border-0 py-2" maxlength="14">
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
    document.querySelectorAll('.edit-bus').forEach(btn => {
        btn.addEventListener('click', function() {
            const data = JSON.parse(this.dataset.json);

            // Campos existentes
            document.getElementById('edit_placa_display').value = data.placa;
            document.getElementById('edit_modelo').value = data.modelo || '';
            document.getElementById('edit_capacidad').value = data.capacidad_pasajeros || 0;
            document.getElementById('edit_kilometraje').value = data.kilometraje || 0;
            document.getElementById('edit_id_estado').value = data.id_estado;

            // NUEVOS CAMPOS (Asegúrate de que los nombres coincidan con el JSON del objeto $bus)
            document.getElementById('edit_linc_transito').value = data.linc_transito || '';
            document.getElementById('edit_doc_propietario').value = data.doc_propietario || '';
            document.getElementById('edit_nombre_propietario').value = data.nombre_propietario || '';
            document.getElementById('edit_telefono').value = data.telefono || '';
            document.getElementById('edit_correo').value = data.correo || '';
            document.getElementById('edit_numero_chasis').value = data.numero_chasis || '';
            document.getElementById('edit_numero_motor').value = data.numero_motor || '';

            document.getElementById('formEditBus').action = `/admin/buses/${data.placa}`;
            resetValidation('formEditBus', 'edit-errors-alert');
        });
    });

    // Gestión AJAX Estándar para Modales Flotantes
    function manageAjax(formId, alertId) {
        const form = document.getElementById(formId);
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = e.submitter;
            const originalBtnHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Procesando...';

            resetValidation(formId, alertId);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    location.reload();
                } else if (response.status === 422) {
                    const data = await response.json();
                    applyErrors(formId, alertId, data.errors);
                } else {
                    const alertEl = document.getElementById(alertId);
                    alertEl.textContent = "Error inesperado en el servidor.";
                    alertEl.classList.remove('d-none');
                }
            } catch (err) {
                console.error(err);
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalBtnHtml;
            }
        });
    }

    function resetValidation(formId, alertId) {
        const f = document.getElementById(formId);
        f.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        f.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');
        if (alertId) document.getElementById(alertId).classList.add('d-none');
    }

    function applyErrors(formId, alertId, errors) {
        const f = document.getElementById(formId);
        const alertEl = document.getElementById(alertId);
        let errorCount = Object.keys(errors).length;

        Object.keys(errors).forEach(key => {
            const input = f.querySelector(`[name="${key}"]`) || f.querySelector(`#edit_${key}`);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = f.querySelector(`.feedback-${key}`);
                if (feedback) {
                    feedback.innerText = errors[key][0];
                }
            }
        });

        const firstError = Object.values(errors)[0][0];
        alertEl.innerHTML = `<div class="d-flex align-items-center gap-2">
            <span class="material-symbols-rounded" style="font-size:1.2rem">error</span>
            <span><strong>Error:</strong> ${firstError}</span>
        </div>`;
        alertEl.classList.remove('d-none');
    }

    manageAjax('formCreateBus', 'create-errors-alert');
    manageAjax('formEditBus', 'edit-errors-alert');
</script>

<style>
    .fs-xs {
        font-size: 0.75rem;
    }

    .modal-content {
        border-radius: 1rem !important;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(94, 84, 142, 0.03) !important;
    }
</style>
@endpush
@endsection