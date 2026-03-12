@extends('admin.layouts.app')

@section('title', 'Buses — SIGU')

@section('content')
<div class="container-fluid pt-0 pb-4">
    <!-- Header de Página -->
    <div class="d-flex align-items-center justify-content-between mb-4 px-1">
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
                <div class="col-md-7">
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
                <div class="col-md-2 text-end">
                    <button type="submit" class="btn btn-dark w-100 fw-semibold">Consultar</button>
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
                            <div class="d-flex justify-content-end gap-3">
                                <a href="#" 
                                   class="text-info text-decoration-none d-flex align-items-center"
                                   title="Ver expediente"
                                   data-bs-toggle="modal"
                                   data-bs-target="#modalViewBus"
                                   data-json="{{ json_encode($bus) }}"
                                   data-estado="{{ optional($bus->estado)->nombre_estado }}">
                                    <span class="material-symbols-rounded fs-5">visibility</span>
                                </a>
                                <a href="#" 
                                   class="text-primary text-decoration-none d-flex align-items-center"
                                   title="Editar vehículo"
                                   data-bs-toggle="modal"
                                   data-bs-target="#modalEditBus"
                                   data-json="{{ json_encode($bus) }}">
                                    <span class="material-symbols-rounded fs-5">edit</span>
                                </a>
                            </div>
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

<!-- Modal CREAR -->
<div class="modal fade" id="modalCreateBus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small">
                    <span class="material-symbols-rounded text-primary me-2 fs-5">add_circle</span>
                    REGISTRAR NUEVO VEHÍCULO
                </h6>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formCreateBus" action="{{ route('admin.buses.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    {{-- Errores de validación --}}
                    @if($errors->any() && !old('_method'))
                        <div class="alert alert-danger shadow-sm py-2 small mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Placa <span class="text-danger">*</span></label>
                            <input type="text" name="placa" class="form-control form-control-sm fw-bold" placeholder="ABC123" required style="text-transform:uppercase" maxlength="6">
                            <div class="real-time-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Modelo / Ref. <span class="text-danger">*</span></label>
                            <input type="text" name="modelo" class="form-control form-control-sm" placeholder="Ej: Toyota 2019" required>
                            <div class="real-time-error"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Pasajeros <span class="text-danger">*</span></label>
                            <input type="text" name="capacidad_pasajeros" class="form-control form-control-sm" required placeholder="00">
                            <div class="real-time-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Kilometraje <span class="text-danger">*</span></label>
                            <input type="text" name="kilometraje" class="form-control form-control-sm" required placeholder="0">
                            <div class="real-time-error"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Licencia Tránsito <span class="text-danger">*</span></label>
                            <input type="text" name="linc_transito" class="form-control form-control-sm" required maxlength="12" placeholder="8 dígitos">
                            <div class="real-time-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Doc. Propietario <span class="text-danger">*</span></label>
                            <input type="text" name="doc_propietario" class="form-control form-control-sm" required maxlength="15" placeholder="Máx. 10 dígitos">
                            <div class="real-time-error"></div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Nombre Propietario <span class="text-danger">*</span></label>
                            <input type="text" name="nombre_propietario" class="form-control form-control-sm" placeholder="Nombre completo" required>
                            <div class="real-time-error"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Teléfono <span class="text-danger">*</span></label>
                            <input type="text" name="telefono" class="form-control form-control-sm" required maxlength="10" placeholder="312...">
                            <div class="real-time-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Correo <span class="text-danger">*</span></label>
                            <input type="email" name="correo" class="form-control form-control-sm" placeholder="ejemplo@correo.com" required>
                            <div class="real-time-error"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Serial Chasis <span class="text-danger">*</span></label>
                            <input type="text" name="numero_chasis" class="form-control form-control-sm" required maxlength="17" placeholder="17 dígitos">
                            <small class="text-muted fs-xs">Debe contener exactamente 17 dígitos numéricos.</small>
                            <div class="real-time-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Serial Motor <span class="text-danger">*</span></label>
                            <input type="text" name="numero_motor" class="form-control form-control-sm" required maxlength="17" placeholder="8-17 dígitos">
                            <small class="text-muted fs-xs">Debe contener entre 8 y 17 dígitos numéricos según el fabricante.</small>
                            <div class="real-time-error"></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Estado Operativo <span class="text-danger">*</span></label>
                            <select name="id_estado" class="form-select form-select-sm" required>
                                <option value="" selected disabled>Seleccionar...</option>
                                @foreach($estados as $est)
                                <option value="{{ $est->id_estado }}">{{ $est->nombre_estado }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-sm btn-light px-3 fw-bold" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm">GUARDAR BUS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EDITAR -->
<div class="modal fade" id="modalEditBus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small">
                    <span class="material-symbols-rounded text-warning me-2 fs-5">edit_square</span>
                    MODIFICAR VEHÍCULO
                </h6>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formEditBus" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    {{-- Errores de validación --}}
                    @if($errors->any() && old('_method') == 'PUT')
                        <div class="alert alert-danger shadow-sm py-2 small mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Placa</label>
                            <input type="text" id="edit_placa_display" class="form-control form-control-sm bg-light fw-bold" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Estado Operativo</label>
                            <select name="id_estado" id="edit_id_estado" class="form-select form-select-sm" required>
                                @foreach($estados as $est)
                                <option value="{{ $est->id_estado }}">{{ $est->nombre_estado }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Modelo / Referencia <span class="text-danger">*</span></label>
                            <input type="text" name="modelo" id="edit_modelo" class="form-control form-control-sm" required placeholder="Ej: Toyota 2019">
                            <div class="real-time-error"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Capacidad</label>
                            <input type="text" name="capacidad_pasajeros" id="edit_capacidad" class="form-control form-control-sm" required>
                            <div class="real-time-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Kilometraje</label>
                            <input type="text" name="kilometraje" id="edit_kilometraje" class="form-control form-control-sm" required>
                            <div class="real-time-error"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Licencia Tránsito</label>
                            <input type="text" name="linc_transito" id="edit_linc_transito" class="form-control form-control-sm" required maxlength="12" placeholder="8 dígitos">
                            <div class="real-time-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Doc. Propietario</label>
                            <input type="text" name="doc_propietario" id="edit_doc_propietario" class="form-control form-control-sm" required maxlength="15" placeholder="Máx. 10 dígitos">
                            <div class="real-time-error"></div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Nombre Propietario</label>
                            <input type="text" name="nombre_propietario" id="edit_nombre_propietario" class="form-control form-control-sm" required>
                            <div class="real-time-error"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Teléfono</label>
                            <input type="text" name="telefono" id="edit_telefono" class="form-control form-control-sm" required maxlength="10">
                            <div class="real-time-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Correo</label>
                            <input type="email" name="correo" id="edit_correo" class="form-control form-control-sm" required>
                            <div class="real-time-error"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Serial Chasis</label>
                            <input type="text" name="numero_chasis" id="edit_numero_chasis" class="form-control form-control-sm" required maxlength="17" placeholder="17 dígitos">
                            <small class="text-muted fs-xs">Debe contener exactamente 17 dígitos numéricos.</small>
                            <div class="real-time-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Serial Motor</label>
                            <input type="text" name="numero_motor" id="edit_numero_motor" class="form-control form-control-sm" required maxlength="17" placeholder="8-17 dígitos">
                            <small class="text-muted fs-xs">Debe contener entre 8 y 17 dígitos numéricos según el fabricante.</small>
                            <div class="real-time-error"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-sm btn-light px-3 fw-bold" data-bs-dismiss="modal">DESCARTAR</button>
                    <button type="submit" class="btn btn-sm btn-warning px-4 fw-bold shadow-sm">GUARDAR CAMBIOS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal VER BUS -->
<div class="modal fade" id="modalViewBus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small">
                    <span class="material-symbols-rounded text-info me-2 fs-5">directions_bus</span>
                    DETALLES DEL VEHÍCULO
                </h6>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            <h4 id="view_bus_placa" class="fw-bold mb-0 text-dark"></h4>
                            <p class="text-muted small mb-0" id="view_bus_modelo"></p>
                        </div>
                    </div>
                    <span id="view_bus_estado" class="badge rounded-pill"></span>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <div class="p-3 bg-light rounded-3 border border-light-subtle">
                            <div class="row g-3 small">
                                <div class="col-6">
                                    <label class="d-block text-muted fw-bold text-uppercase ls-1">Capacidad</label>
                                    <span class="text-dark fw-medium"><span id="view_bus_capacidad"></span> pasajeros</span>
                                </div>
                                <div class="col-6">
                                    <label class="d-block text-muted fw-bold text-uppercase ls-1">Kilometraje</label>
                                    <span id="view_bus_kilometraje" class="text-dark fw-medium"></span> <small class="text-muted">KM</small>
                                </div>
                                <div class="col-12 border-top pt-2">
                                    <label class="d-block text-muted fw-bold text-uppercase ls-1">Propietario</label>
                                    <span id="view_bus_nombre_prop" class="text-dark fw-medium d-block"></span>
                                    <small class="text-muted">Doc: <span id="view_bus_doc_prop"></span></small>
                                </div>
                                <div class="col-6 border-top pt-2">
                                    <label class="d-block text-muted fw-bold text-uppercase ls-1">Teléfono</label>
                                    <span id="view_bus_tel_prop" class="text-dark fw-medium"></span>
                                </div>
                                <div class="col-6 border-top pt-2">
                                    <label class="d-block text-muted fw-bold text-uppercase ls-1">Licencia</label>
                                    <span id="view_bus_licencia" class="text-dark fw-medium"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer border-0 p-3 bg-light">
                <button type="button" class="btn btn-sm btn-dark w-100 fw-bold" data-bs-dismiss="modal">CERRAR EXPEDIENTE</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Buses script initialized');

        // Delegación de eventos para sincronizar datos antes de mostrar modales
        document.addEventListener('click', function(e) {
            // Botón VER
            const btnVer = e.target.closest('[data-bs-target="#modalViewBus"]');
            if (btnVer) {
                e.preventDefault();
                handleVerBus(btnVer);
                return;
            }

            // Botón EDITAR
            const btnEdit = e.target.closest('[data-bs-target="#modalEditBus"]');
            if (btnEdit) {
                e.preventDefault();
                handleEditBus(btnEdit);
                return;
            }
        });

        function handleVerBus(btn) {
            try {
                const data = JSON.parse(btn.dataset.json);
                const estado = btn.dataset.estado;

                document.getElementById('view_bus_placa').textContent = data.placa;
                document.getElementById('view_bus_modelo').textContent = data.modelo || 'N/A';
                document.getElementById('view_bus_capacidad').textContent = data.capacidad_pasajeros || '0';
                document.getElementById('view_bus_kilometraje').textContent = Number(data.kilometraje || 0).toLocaleString();
                document.getElementById('view_bus_chasis').textContent = data.numero_chasis || 'No registrado';
                document.getElementById('view_bus_motor').textContent = data.numero_motor || 'No registrado';
                document.getElementById('view_bus_licencia').textContent = data.linc_transito || 'No asignada';
                document.getElementById('view_bus_nombre_prop').textContent = data.nombre_propietario || 'No asignado';
                document.getElementById('view_bus_doc_prop').textContent = data.doc_propietario || '—';
                document.getElementById('view_bus_tel_prop').textContent = data.telefono || '—';

                const viewEst = document.getElementById('view_bus_estado');
                viewEst.textContent = estado;
                viewEst.className = 'badge rounded-pill px-4 py-2 fs-6 fw-bold';
                
                const stateId = parseInt(data.id_estado);
                if (stateId === 1) {
                    viewEst.classList.add('bg-success-subtle', 'text-success', 'border', 'border-success-subtle');
                } else if (stateId === 2) {
                    viewEst.classList.add('bg-danger-subtle', 'text-danger', 'border', 'border-danger-subtle');
                } else if (stateId === 7) {
                    viewEst.classList.add('bg-info-subtle', 'text-info', 'border', 'border-info-subtle');
                } else {
                    viewEst.classList.add('bg-warning-subtle', 'text-warning', 'border', 'border-warning-subtle');
                }

                const modalEl = document.getElementById('modalViewBus');
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            } catch (err) { console.error('Ver Bus Error:', err); }
        }

        function handleEditBus(btn) {
            try {
                const data = JSON.parse(btn.dataset.json);
                const form = document.getElementById('formEditBus');
                if (!form) return;

                document.getElementById('edit_placa_display').value = data.placa;
                document.getElementById('edit_modelo').value = data.modelo || '';
                document.getElementById('edit_capacidad').value = data.capacidad_pasajeros || 0;
                document.getElementById('edit_kilometraje').value = data.kilometraje || 0;
                document.getElementById('edit_id_estado').value = data.id_estado;

                document.getElementById('edit_linc_transito').value = data.linc_transito || '';
                document.getElementById('edit_doc_propietario').value = data.doc_propietario || '';
                document.getElementById('edit_nombre_propietario').value = data.nombre_propietario || '';
                document.getElementById('edit_telefono').value = data.telefono || '';
                document.getElementById('edit_correo').value = data.correo || '';
                document.getElementById('edit_numero_chasis').value = data.numero_chasis || '';
                document.getElementById('edit_numero_motor').value = data.numero_motor || '';

                form.action = "{{ url('admin/buses') }}/" + data.placa;
                sessionStorage.setItem('last_edit_bus_placa', data.placa);

                const modalEl = document.getElementById('modalEditBus');
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            } catch (err) { console.error('Edit Bus Error:', err); }
        }

        // Manejo de errores y reapertura de modales
        @if($errors->any())
            @if(old('_method') == 'PUT')
                const lastPlaca = sessionStorage.getItem('last_edit_bus_placa');
                if (lastPlaca) {
                    const form = document.getElementById('formEditBus');
                    form.action = "{{ url('admin/buses') }}/" + lastPlaca;
                    new bootstrap.Modal(document.getElementById('modalEditBus')).show();
                }
            @else
                new bootstrap.Modal(document.getElementById('modalCreateBus')).show();
            @endif
        @endif

        // Función para mostrar/ocultar errores en tiempo real
        function toggleError(input, show, message = '') {
            const container = input.closest('.col-md-6, .col-md-12, .col-12');
            const errorDiv = container.querySelector('.real-time-error');
            
            if (show) {
                input.classList.add('is-invalid');
                if (errorDiv) {
                    errorDiv.textContent = message;
                    errorDiv.style.display = 'block';
                }
            } else {
                input.classList.remove('is-invalid');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }
            }
        }

        // Delegación de eventos para validar mientras se escribe
        document.addEventListener('input', function(e) {
            const input = e.target;
            const name = input.name;
            if (!name) return;

            let isValid = true;
            let message = '';

            // Limpiar solo números para campos específicos
            if (['linc_transito', 'telefono', 'doc_propietario', 'numero_chasis', 'numero_motor', 'capacidad_pasajeros', 'kilometraje'].includes(name)) {
                input.value = input.value.replace(/[^0-9]/g, '');
            }

            // Validaciones específicas
            switch(name) {
                case 'placa':
                    let val = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                    if (val.length <= 3) val = val.replace(/[^A-Z]/g, '');
                    else val = val.substring(0,3).replace(/[^A-Z]/g, '') + val.substring(3).replace(/[^0-9]/g, '');
                    input.value = val.substring(0,6);
                    
                    if (input.value.length > 0 && !/^[A-Z]{3}[0-9]{3}$/.test(input.value)) {
                        isValid = false;
                        message = 'Formato requerido: AAA000 (3 letras y 3 números).';
                    }
                    break;

                case 'linc_transito':
                    if (input.value.length > 0 && input.value.length !== 8) {
                        isValid = false;
                        message = 'La licencia debe tener exactamente 8 caracteres numéricos.';
                    }
                    break;

                case 'modelo':
                    // Marca (letras) + Espacio + Año (4 números)
                    if (input.value.length > 0) {
                        // Regex: Letras (incluyendo tildes) + un espacio + 4 números
                        const modelRegex = /^[\p{L}ÁÉÍÓÚáéíóúÑñ\s]+\s[0-9]{4}$/u;
                        if (!modelRegex.test(input.value)) {
                            isValid = false;
                            message = 'Formato: Marca Año (Ej: Toyota 2019).';
                        }
                    }
                    break;

                case 'doc_propietario':
                    if (input.value.length > 10) {
                        isValid = false;
                        message = 'El documento no debe superar los 10 dígitos.';
                    } else if (input.value.length > 0 && input.value.length < 6) {
                        isValid = false;
                        message = 'El documento debe tener al menos 6 dígitos.';
                    }
                    break;

                case 'nombre_propietario':
                    if (input.value.length > 0 && !/^[\p{L}ÁÉÍÓÚáéíóúÑñ\s]+$/u.test(input.value)) {
                        isValid = false;
                        message = 'Solo se permiten letras y espacios.';
                    }
                    break;

                case 'telefono':
                    if (input.value.length > 0 && input.value.length !== 10) {
                        isValid = false;
                        message = 'El teléfono debe tener exactamente 10 dígitos.';
                    }
                    break;

                case 'correo':
                    if (input.value.length > 0 && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value)) {
                        isValid = false;
                        message = 'Formato de correo inválido.';
                    }
                    break;

                case 'numero_chasis':
                    if (input.value.length > 0 && input.value.length !== 17) {
                        isValid = false;
                        message = 'El chasis debe tener exactamente 17 números.';
                    }
                    break;

                case 'numero_motor':
                    if (input.value.length > 0 && (input.value.length < 8 || input.value.length > 17)) {
                        isValid = false;
                        message = 'El motor debe tener entre 8 y 17 números.';
                    }
                    break;
            }

            toggleError(input, !isValid, message);
        });

        // Validar antes de enviar
        const forms = ['formCreateBus', 'formEditBus'];
        forms.forEach(id => {
            const f = document.getElementById(id);
            if (f) {
                f.addEventListener('submit', function(e) {
                    const invalidInputs = this.querySelectorAll('.is-invalid');
                    if (invalidInputs.length > 0) {
                        e.preventDefault();
                        invalidInputs[0].focus();
                        alert('Por favor corrija los errores en el formulario antes de continuar.');
                    }
                });
            }
        });
    });
</script>

<style>
    .fs-xs {
        font-size: 0.75rem;
    }

    .ls-1 {
        letter-spacing: 0.5px;
    }

    .fw-extrabold {
        font-weight: 800;
    }

    .rounded-4 {
        border-radius: 1rem !important;
    }

    .modal-content {
        border-radius: 1.5rem !important;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(94, 84, 142, 0.04) !important;
    }

    .tracking-tight {
        letter-spacing: -0.025em;
    }
    
    .tracking-tighter {
        letter-spacing: -0.05em;
    }

    .bg-info-subtle {
        background-color: #e1f5fe !important;
    }
    
    .text-info {
        color: #0288d1 !important;
    }

    .invalid-feedback {
        font-weight: 500;
        font-size: 0.8rem;
    }

    .real-time-error {
        color: #dc3545;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: none; /* Se muestra vía JS */
        font-weight: 500;
    }

    .form-control.is-invalid {
        border-color: #dc3545 !important;
        background-image: none !important;
    }
</style>
@endpush
@endsection