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

    @include('admin.buses.partials.table')
</div>

@include('admin.buses.partials.create_modal')


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
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 p-4 bg-light">
                <h5 class="modal-title fw-black text-dark d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                        <span class="material-symbols-rounded text-primary">analytics</span>
                    </div>
                    Expediente del Vehículo: <span id="view_bus_placa" class="text-primary">---</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fw-bold ms-auto" id="view_bus_modelo">---</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4 bg-light">
                <!-- 1. Información General y Conductor -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-muted text-uppercase mb-4 d-flex align-items-center gap-2">
                                    <span class="material-symbols-rounded fs-5 text-primary">info</span>
                                    Información Técnica
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-3 col-6">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <label class="d-block text-muted fw-bold text-uppercase x-small mb-1">Capacidad</label>
                                            <span class="text-dark fw-bold"><span id="view_bus_capacidad"></span> pasj.</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <label class="d-block text-muted fw-bold text-uppercase x-small mb-1">Kilometraje</label>
                                            <span class="text-dark fw-bold"><span id="view_bus_kilometraje"></span> km</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <label class="d-block text-muted fw-bold text-uppercase x-small mb-1">Licencia</label>
                                            <span id="view_bus_licencia" class="text-dark fw-bold small"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <label class="d-block text-muted fw-bold text-uppercase x-small mb-1">Estado</label>
                                            <span id="view_bus_estado" class="badge rounded-pill x-small px-3"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 border-top pt-3">
                                        <label class="d-block text-muted fw-bold text-uppercase x-small ls-1">Propietario</label>
                                        <span id="view_bus_nombre_prop" class="text-dark fw-bold d-block"></span>
                                        <div class="d-flex gap-3 x-small text-muted mt-1">
                                            <span>NIT/CC: <span id="view_bus_doc_prop" class="fw-medium"></span></span>
                                            <span>TEL: <span id="view_bus_tel_prop" class="fw-medium"></span></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 border-top pt-3">
                                        <div class="row">
                                            <div class="col-6">
                                                <label class="d-block text-muted fw-bold text-uppercase x-small ls-1">Chasis</label>
                                                <span id="view_bus_chasis" class="text-dark family-monospace small fw-bold"></span>
                                            </div>
                                            <div class="col-6">
                                                <label class="d-block text-muted fw-bold text-uppercase x-small ls-1">Motor</label>
                                                <span id="view_bus_motor" class="text-dark family-monospace small fw-bold"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-muted text-uppercase mb-4 d-flex align-items-center gap-2">
                                    <span class="material-symbols-rounded fs-5 text-primary">person</span>
                                    Servicio Actual
                                </h6>
                                <div id="view_bus_no_asignacion" class="alert alert-light text-center small py-4 mb-0">
                                    <span class="material-symbols-rounded fs-1 opacity-25 d-block mb-2">person_off</span>
                                    Sin servicio asignado.
                                </div>
                                <div id="view_bus_con_asignacion" class="d-none">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                                            <span class="material-symbols-rounded fs-2">person</span>
                                        </div>
                                        <div>
                                            <h5 class="fw-black mb-0" id="view_cond_nombre">---</h5>
                                            <span class="badge bg-primary-subtle text-primary x-small" id="view_ruta_nombre">---</span>
                                        </div>
                                    </div>
                                    <div class="p-3 bg-light rounded-4 border small d-flex flex-column gap-2">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Documento:</span>
                                            <span id="view_cond_doc" class="fw-bold">---</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Licencia:</span>
                                            <span id="view_cond_lic" class="fw-bold">---</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Documentos y Gastos -->
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold text-dark text-uppercase mb-0 d-flex align-items-center gap-2">
                                    <span class="material-symbols-rounded text-primary">folder_shared</span>
                                    Documentación Legal
                                </h6>
                                <button type="button" class="btn btn-sm btn-outline-dark d-flex align-items-center gap-1 fw-bold rounded-pill px-3" id="btn_abrir_boveda_admin" data-placa="">
                                    <span class="material-symbols-rounded fs-6">history_edu</span>
                                    Bóveda Histórica
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 border-0 small fw-bold text-muted">DOCUMENTO</th>
                                            <th class="border-0 small fw-bold text-muted">VENCIMIENTO</th>
                                            <th class="border-0 small fw-bold text-muted text-center">ESTADO</th>
                                            <th class="border-0 small fw-bold text-muted text-center pe-4">VER</th>
                                        </tr>
                                    </thead>
                                    <tbody id="view_bus_docs_body">
                                        <!-- Documentos via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer border-0 p-4 bg-light">
                <button type="button" class="btn btn-dark w-100 fw-bold px-5 rounded-pill" data-bs-dismiss="modal">CERRAR EXPEDIENTE</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bóveda Histórica Admin -->
<div class="modal fade" id="modalBovedaHistorialAdmin" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom p-4" style="background: #f8fafc;">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-3 text-dark">
                    <span class="material-symbols-rounded text-primary">history_edu</span>
                    Expediente Histórico del Vehículo: <span id="boveda_admin_placa" class="text-primary">---</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div id="boveda_admin_content" class="d-flex flex-column gap-4">
                    <!-- Contenido dinámico -->
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-light">
                <button type="button" class="btn btn-dark fw-bold px-5 rounded-pill" data-bs-dismiss="modal">Cerrar Bóveda</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Visor de Documentos -->
<div class="modal fade" id="modalVisorDocumento" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pt-4 px-4 bg-dark text-white">
                <h5 class="modal-title fw-bold" id="visor_titulo">Visualización de Documento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-secondary bg-opacity-10" style="height: 70vh;">
                <iframe id="visor_iframe" class="w-100 h-100 d-none border-0" src=""></iframe>
                <div id="visor_image_container" class="w-100 h-100 d-none d-flex align-items-center justify-content-center p-3">
                    <img id="visor_img" src="" class="img-fluid rounded-3 shadow-sm" style="max-height: 100%;">
                </div>
                <div id="visor_error" class="w-100 h-100 d-none d-flex flex-column align-items-center justify-content-center text-muted">
                    <span class="material-symbols-rounded display-1 mb-3">error</span>
                    <p class="fw-bold">No se puede previsualizar este archivo.</p>
                    <a id="visor_download" href="#" class="btn btn-primary rounded-pill px-4" download>Descargar Archivo</a>
                </div>
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

        async function handleVerBus(btn) {
            try {
                const placa = JSON.parse(btn.dataset.json).placa;
                
                // Cargar datos completos vía AJAX (incluye asignación)
                const prefix = '{{ Auth::user()->id_tipo_usuario == 1 ? "admin" : "empresa" }}';
                const respBus = await fetch(`/${prefix}/buses/${placa}`);
                const fullData = await respBus.json();
                
                const data = fullData.bus;
                const asignacion = fullData.asignacion;
                const estado = data.estado ? data.estado.nombre_estado : 'N/D';

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

                // Asignación
                const noAsign = document.getElementById('view_bus_no_asignacion');
                const conAsign = document.getElementById('view_bus_con_asignacion');
                
                if (asignacion) {
                    noAsign.classList.add('d-none');
                    conAsign.classList.remove('d-none');
                    document.getElementById('view_cond_nombre').textContent = asignacion.conductor;
                    document.getElementById('view_cond_doc').textContent = asignacion.doc_conductor;
                    document.getElementById('view_cond_lic').textContent = asignacion.licencia;
                    document.getElementById('view_ruta_nombre').textContent = asignacion.ruta;
                } else {
                    noAsign.classList.remove('d-none');
                    conAsign.classList.add('d-none');
                }

                document.getElementById('btn_abrir_boveda_admin').setAttribute('data-placa', data.placa);

                // Documentos
                const dBody = document.getElementById('view_bus_docs_body');
                dBody.innerHTML = '';
                
                if (fullData.documentos && fullData.documentos.length > 0) {
                    fullData.documentos.forEach(doc => {
                        const tr = document.createElement('tr');
                        const fechaVenc = new Date(doc.fecha_vencimiento).toLocaleDateString();
                        
                        tr.innerHTML = `
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="material-symbols-rounded text-${doc.status_color} small">description</span>
                                    <span class="small fw-bold text-dark">${doc.tipo_documento.nombre}</span>
                                </div>
                            </td>
                            <td class="small text-muted">${fechaVenc}</td>
                            <td class="text-center">
                                <span class="badge bg-${doc.status_color}-subtle text-${doc.status_color} border border-${doc.status_color} x-small rounded-pill px-2">
                                    ${doc.status_vigencia}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm btn-light border p-1 rounded-circle btn-visor-admin" data-url="${doc.url_archivo}" data-nombre="${doc.tipo_documento.nombre}">
                                        <span class="material-symbols-rounded fs-6">visibility</span>
                                    </button>
                                    <a href="${doc.url_archivo}" class="btn btn-sm btn-light border p-1 rounded-circle text-primary" download>
                                        <span class="material-symbols-rounded fs-6">download</span>
                                    </a>
                                </div>
                            </td>
                        `;
                        dBody.appendChild(tr);
                    });

                    // Eventos Visor para Admin
                    document.querySelectorAll('.btn-visor-admin').forEach(bv => {
                        bv.addEventListener('click', function() {
                           const url = this.getAttribute('data-url');
                           const nombre = this.getAttribute('data-nombre');
                           if (typeof mostrarVisor === 'function') {
                               mostrarVisor(url, nombre);
                           } else {
                               window.open(url, '_blank');
                           }
                        });
                    });

                } else {
                    dBody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted small">Sin documentos registrados.</td></tr>';
                }

                const viewEst = document.getElementById('view_bus_estado');
                viewEst.textContent = estado;
                viewEst.className = 'badge rounded-pill px-3 py-1 x-small fw-bold';
                
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



            } catch (err) { console.error('Ver Bus Error:', err); }
        }

        // Bóveda Histórica Admin
        const btnBovedaAdmin = document.getElementById('btn_abrir_boveda_admin');
        const modalBovedaAdmin = new bootstrap.Modal(document.getElementById('modalBovedaHistorialAdmin'));
        
        btnBovedaAdmin.addEventListener('click', async function() {
            const placa = this.getAttribute('data-placa');
            if(!placa) return;
            
            try {
                const prefix = '{{ Auth::user()->id_tipo_usuario == 1 ? "admin" : "empresa" }}';
                const response = await fetch(`/${prefix}/buses/${placa}/historial-documental`);
                if(!response.ok) throw new Error('Error de red');
                const data = await response.json();
                
                document.getElementById('boveda_admin_placa').innerText = data.placa;
                const content = document.getElementById('boveda_admin_content');
                content.innerHTML = '';

                const isObjectEmpty = (obj) => {
                    return Object.keys(obj).length === 0;
                };

                if (isObjectEmpty(data.grupos)) {
                    content.innerHTML = '<div class="alert alert-light text-center border-0 shadow-sm rounded-4 p-5"><span class="material-symbols-rounded display-1 opacity-25 d-block mb-3">folder_off</span><h5 class="fw-bold">Sin Historial</h5><p class="mb-0 text-muted">No hay documentos registrados para este vehículo.</p></div>';
                } else {
                    for (const [tipo, docs] of Object.entries(data.grupos)) {
                        let rows = '';
                        docs.forEach(doc => {
                            const isArchivado = doc.es_archivado;
                            const trClass = isArchivado ? 'opacity-75 bg-light' : '';
                            rows += `
                            <tr class="${trClass}">
                                <td class="ps-4">
                                    <div class="fw-bold text-dark small text-truncate" style="max-width: 250px;" title="${doc.nombre}">${doc.nombre}</div>
                                    ${isArchivado ? '<span class="badge bg-secondary-subtle text-secondary x-small border border-secondary mt-1">Archivado</span>' : '<span class="badge bg-success-subtle text-success x-small border border-success mt-1">Documento Activo</span>'}
                                </td>
                                <td class="text-muted small">${doc.fecha_carga}</td>
                                <td class="fw-bold text-dark small">${doc.fecha_vencimiento}</td>
                                <td><span class="badge bg-${doc.status_color}-subtle text-${doc.status_color} px-3 py-1 x-small border border-${doc.status_color} rounded-pill fw-bold">${doc.status_vigencia}</span></td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        ${doc.url_archivo ? `
                                        <button class="btn btn-sm btn-light border p-2 rounded-circle text-dark" onclick="mostrarVisor('${doc.url_archivo}', '${tipo}')" title="Vista Previa"><span class="material-symbols-rounded fs-6">visibility</span></button>
                                        <a href="${doc.url_archivo}" download class="btn btn-sm btn-light border text-primary p-2 rounded-circle" title="Descargar PDF"><span class="material-symbols-rounded fs-6">download</span></a>
                                        ` : '<span class="text-muted small">N/A</span>'}
                                    </div>
                                </td>
                            </tr>`;
                        });

                        content.innerHTML += `
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                            <div class="card-header bg-white border-0 p-4">
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2 text-uppercase letter-spacing-1 small">
                                    <span class="material-symbols-rounded text-primary fs-5">folder_open</span>
                                    ${tipo}
                                </h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 py-3 border-0 small text-muted fw-bold">NOMBRES Y ESTADO</th>
                                            <th class="py-3 border-0 small text-muted fw-bold">CARGA</th>
                                            <th class="py-3 border-0 small text-muted fw-bold">VENCIMIENTO</th>
                                            <th class="py-3 border-0 small text-muted fw-bold">VIGENCIA</th>
                                            <th class="py-3 border-0 text-end pe-4 small text-muted fw-bold">ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>${rows}</tbody>
                                </table>
                            </div>
                        </div>`;
                    }
                }
                modalBovedaAdmin.show();
            } catch (e) {
                console.error(e);
                alert("No se pudo cargar la información de la bóveda histórica.");
            }
        });

        // Visor de Documentos Global
        window.mostrarVisor = function(url, nombre) {
            const modalViewer = new bootstrap.Modal(document.getElementById('modalVisorDocumento'));
            const iframe = document.getElementById('visor_iframe');
            const imgContainer = document.getElementById('visor_image_container');
            const img = document.getElementById('visor_img');
            const error = document.getElementById('visor_error');
            const download = document.getElementById('visor_download');
            
            document.getElementById('visor_titulo').innerText = 'Documento: ' + nombre;
            
            // Reset
            iframe.classList.add('d-none');
            imgContainer.classList.add('d-none');
            error.classList.add('d-none');
            iframe.src = '';
            img.src = '';
            download.href = url;

            if (!url) {
                error.classList.remove('d-none');
            } else {
                const ext = url.split('.').pop().toLowerCase();
                if (ext === 'pdf') {
                    iframe.src = url;
                    iframe.classList.remove('d-none');
                } else if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                    img.src = url;
                    imgContainer.classList.remove('d-none');
                } else {
                    error.classList.remove('d-none');
                }
            }
            modalViewer.show();
        };

        function handleEditBus(btn) {
            try {
                const data = JSON.parse(btn.dataset.json);
                console.log('Editing bus:', data);
                const form = document.getElementById('formEditBus');
                if (!form) return;

                document.getElementById('edit_placa_display').value = data.placa || '';
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
                
                // Lógica de restricción para Admin (Rol 1)
                const isUserAdmin = {{ Auth::user()->id_tipo_usuario == 1 ? 'true' : 'false' }};
                const maintenanceOption = document.querySelector('#edit_id_estado option[value="4"]');
                if (isUserAdmin && maintenanceOption) {
                    if (data.id_estado != 4) {
                        maintenanceOption.style.display = 'none'; // Ocultar si no está ya en ese estado
                    } else {
                        maintenanceOption.style.display = 'block'; // Mostrar si ya está en ese estado (para mantenerlo o cambiarlo a Activo)
                    }
                }

                const prefix = '{{ Auth::user()->id_tipo_usuario == 1 ? "admin" : "empresa" }}';
                form.action = '/' + prefix + '/buses/' + data.placa;
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
                    const prefix = '{{ Auth::user()->id_tipo_usuario == 1 ? "admin" : "empresa" }}';
                    form.action = '/' + prefix + '/buses/' + lastPlaca;
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

        // Autocompletado de Propietario por Doc/NIT
        const docInput = document.querySelector('#formCreateBus input[name="doc_propietario"]');
        if (docInput) {
            docInput.addEventListener('blur', async function() {
                const doc = this.value.trim();
                const container = this.closest('.col-md-6');
                let feedback = container.querySelector('.autocomplete-feedback');
                
                if (doc.length >= 6) {
                    if (!feedback) {
                        feedback = document.createElement('div');
                        feedback.className = 'autocomplete-feedback small fw-bold mt-1';
                        container.appendChild(feedback);
                    }
                    
                    feedback.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> <span class="ms-1">Consultando...</span>';
                    feedback.className = 'autocomplete-feedback text-primary small fw-bold mt-1';
                    feedback.style.display = 'block';

                    try {
                        const prefix = '{{ Auth::user()->id_tipo_usuario == 1 ? "admin" : "empresa" }}';
                        const response = await fetch(`/${prefix}/buses/propietario/${doc}`);
                        if (response.ok) {
                            const data = await response.json();
                            if (data) {
                                // Rellenar campos
                                const fields = {
                                    'nombre_propietario': data.nombre_propietario,
                                    'telefono': data.telefono,
                                    'correo': data.correo
                                };

                                Object.entries(fields).forEach(([name, value]) => {
                                    const input = document.querySelector(`#formCreateBus input[name="${name}"]`);
                                    if (input && value) {
                                        input.value = value;
                                        // Disparar evento input para limpiar errores previos si los hubiera
                                        input.dispatchEvent(new Event('input', { bubbles: true }));
                                    }
                                });

                                feedback.innerHTML = '<span class="material-symbols-rounded fs-xs align-middle">verified</span> Datos encontrados y cargados';
                                feedback.className = 'autocomplete-feedback text-success small fw-bold mt-1';
                                setTimeout(() => feedback.style.display = 'none', 5000);
                            } else {
                                feedback.style.display = 'none';
                            }
                        } else {
                            feedback.style.display = 'none';
                        }
                    } catch (error) {
                        console.error('Error en autocompletado:', error);
                        feedback.style.display = 'none';
                    }
                } else {
                    if (feedback) feedback.style.display = 'none';
                }
            });
        }

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