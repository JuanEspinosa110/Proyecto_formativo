@extends('empresa.layouts.app')

@section('title', 'Portal de Gestión — SIGU')

@section('content')
<div class="container-fluid pt-0 pb-4">
    <!-- Header de Página Premium -->
    <div class="d-flex align-items-center justify-content-between mb-4 px-1">
        <div>
            <h1 class="h3 mb-1 text-dark fw-black text-uppercase ls-1">Portal de Empresa</h1>
            <p class="text-muted small mb-0">Gestión integral de flota, personal operativo y documentación legal.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary d-flex align-items-center gap-2 px-3 fw-bold shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalCrearBus">
                <span class="material-symbols-rounded">add_circle</span>
                Registrar Vehículo
            </button>
            <button class="btn btn-primary d-flex align-items-center gap-2 px-3 fw-bold shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalAsignarConductor">
                <span class="material-symbols-rounded">event_available</span>
                Nueva Asignación
            </button>
        </div>
    </div>

    <!-- Navegación por Pestañas (Premium Tabs) -->
    <ul class="nav nav-pills nav-fill bg-white p-2 rounded-pill shadow-sm mb-4" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill py-2 {{ $tab == 'personal' ? 'active' : '' }}" id="pills-personal-tab" data-bs-toggle="pill" data-bs-target="#tab-personal" type="button" role="tab">
                <span class="material-symbols-rounded align-middle me-2">group</span> Personal
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill py-2 {{ $tab == 'flota' ? 'active' : '' }}" id="pills-flota-tab" data-bs-toggle="pill" data-bs-target="#tab-flota" type="button" role="tab">
                <span class="material-symbols-rounded align-middle me-2">directions_bus</span> Flota
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill py-2 {{ $tab == 'asignaciones' ? 'active' : '' }}" id="pills-asignaciones-tab" data-bs-toggle="pill" data-bs-target="#tab-asignaciones" type="button" role="tab">
                <span class="material-symbols-rounded align-middle me-2">route</span> Asignaciones
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill py-2 {{ $tab == 'documentacion' ? 'active' : '' }}" id="pills-documentacion-tab" data-bs-toggle="pill" data-bs-target="#tab-documentacion" type="button" role="tab">
                <span class="material-symbols-rounded align-middle me-2">folder_shared</span> Documental
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill py-2 {{ $tab == 'reportes' ? 'active' : '' }}" id="pills-reportes-tab" data-bs-toggle="pill" data-bs-target="#tab-reportes" type="button" role="tab">
                <span class="material-symbols-rounded align-middle me-2">bar_chart</span> Reportes
            </button>
        </li>
    </ul>

    <!-- Alertas de Licencias -->
    @if(isset($licenciasAlerta) && $licenciasAlerta->count() > 0)
        <div class="mb-4">
            @foreach($licenciasAlerta as $licencia)
                <div class="alert alert-{{ $licencia->status_color }} alert-dismissible fade show d-flex align-items-center shadow-sm py-2 rounded-4 border-0 mb-2" role="alert">
                    <span class="material-symbols-rounded flex-shrink-0 me-2">{{ $licencia->estado_expiracion == 'VENCIDO' ? 'error' : 'warning' }}</span>
                    <div class="small">
                        <strong>Licencia {{ $licencia->estado_expiracion }}:</strong> {{ $licencia->usuario->primer_nombre }} {{ $licencia->usuario->primer_apellido }} ({{ $licencia->doc_usuario }}).
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Contenido de Pestañas -->
    <div class="tab-content" id="pills-tabContent">
        @include('empresa.dashboard.tabs.personal')
        @include('empresa.dashboard.tabs.flota')
        @include('empresa.dashboard.tabs.asignaciones')
        @include('empresa.dashboard.tabs.documentacion')
        @include('empresa.dashboard.tabs.reportes')
    </div>
</div>

{{-- MODALES DE CONTROL (Modulo Auxiliar) --}}
@include('admin.usuarios.partials.create_modal', [
    'formAction' => route('empresa.usuarios.store'),
    'restrictedRoles' => [3, 4, 5]
])
@include('empresa.auxiliar.modals.crear_bus', ['propietarios' => $usuarios->where('id_tipo_usuario', 5)])
@include('empresa.auxiliar.modals.asignar_conductor', ['busesDisponibles' => $buses, 'rutas' => $rutas, 'conductores' => $usuarios->where('id_tipo_usuario', 3)])
@include('empresa.auxiliar.modals.expediente_bus')
@include('empresa.auxiliar.modals.detalle_asignacion')
@include('empresa.auxiliar.modals.detalle_documento')

{{-- Modales Adicionales Compartidos (Sincronizados con Admin) --}}
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light py-3 border-0">
                <h6 class="modal-title fw-bold d-flex align-items-center small text-uppercase"><span class="material-symbols-rounded text-warning me-2 fs-5">edit</span> Modificar Perfil de Usuario</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="formEditarUsuario" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label x-small fw-bold text-muted text-uppercase">Documento</label>
                            <input type="text" name="doc_usuario" id="editDoc" class="form-control form-control-sm bg-light fw-bold border-0" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label x-small fw-bold text-muted text-uppercase">Rol Operativo</label>
                            <select name="id_tipo_usuario" id="editRol" class="form-select form-select-sm border-0 bg-light">
                                @foreach($roles as $rol) <option value="{{ $rol->id_tipo_usuario }}">{{ $rol->nombre_tipo }}</option> @endforeach
                            </select>
                        </div>
                        
                        <!-- Campos de Nombre (Habilitados según requerimiento de mejora) -->
                        <div class="col-md-6">
                            <label class="form-label x-small fw-bold text-muted text-uppercase">Primer Nombre</label>
                            <input type="text" name="primer_nombre" id="editPrimerNombre" class="form-control form-control-sm border-0 bg-light" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label x-small fw-bold text-muted text-uppercase">Segundo Nombre</label>
                            <input type="text" name="segundo_nombre" id="editSegundoNombre" class="form-control form-control-sm border-0 bg-light">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label x-small fw-bold text-muted text-uppercase">Primer Apellido</label>
                            <input type="text" name="primer_apellido" id="editPrimerApellido" class="form-control form-control-sm border-0 bg-light" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label x-small fw-bold text-muted text-uppercase">Segundo Apellido</label>
                            <input type="text" name="segundo_apellido" id="editSegundoApellido" class="form-control form-control-sm border-0 bg-light" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label x-small fw-bold text-muted text-uppercase">Correo</label>
                            <input type="email" name="correo" id="editCorreo" class="form-control form-control-sm border-0 bg-light" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label x-small fw-bold text-muted text-uppercase">Teléfono</label>
                            <input type="text" name="telefono" id="editTelefono" class="form-control form-control-sm border-0 bg-light" required maxlength="10">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label x-small fw-bold text-muted text-uppercase">Estado de Cuenta</label>
                            <select name="id_estado" id="editEstado" class="form-select form-select-sm border-0 bg-light">
                                <option value="1">ACTIVO</option>
                                <option value="2">INACTIVO</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm rounded-pill w-100 py-2">ACTUALIZAR DATOS AHORA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal VER USUARIO (Mismo estilo que Admin) -->
<div class="modal fade" id="modalVerUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light py-3 border-0">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small text-uppercase">
                    <span class="material-symbols-rounded text-info me-2 fs-5">person_search</span> Detalles de Usuario
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div id="verFotoContainer"></div>
                    <div>
                        <h5 id="verNombreCompleto" class="fw-black text-dark mb-0"></h5>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="text-muted small">Doc: <span id="verDoc" class="fw-bold"></span></span>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-bold small" id="verRol"></span>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-12 p-3 bg-light rounded-4">
                        <div class="row g-3 small">
                            <div class="col-6">
                                <label class="text-muted fw-bold text-uppercase d-block mb-1">Contacto</label>
                                <span id="verTelefono" class="text-dark fw-bold"></span>
                            </div>
                            <div class="col-6 text-end">
                                <label class="text-muted fw-bold text-uppercase d-block mb-1">Estado</label>
                                <span id="verEstado" class="badge rounded-pill"></span>
                            </div>
                            <div class="col-12 border-top pt-2">
                                <label class="text-muted fw-bold text-uppercase d-block mb-1">Correo Electrónico</label>
                                <span id="verCorreo" class="text-dark fw-bold"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3">
                <button type="button" class="btn btn-outline-dark btn-sm rounded-pill w-100 fw-bold" data-bs-dismiss="modal">CERRAR EXPEDIENTE</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bóveda Histórica Auxiliar -->
<div class="modal fade" id="modalBovedaHistorialAux" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom p-4" style="background: #f8fafc;">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-3 text-dark">
                    <span class="material-symbols-rounded text-primary">history_edu</span>
                    Expediente Histórico del Vehículo: <span id="boveda_aux_placa" class="text-primary">---</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div id="boveda_aux_content" class="d-flex flex-column gap-4">
                    <!-- Contenido dinámico -->
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-light">
                <button type="button" class="btn btn-dark fw-bold px-5 rounded-pill" data-bs-dismiss="modal">Cerrar Bóveda</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Visor de Documentos (Global) -->
<div class="modal fade" id="modalVisorDocumento" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
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
document.addEventListener('DOMContentLoaded', function () {
    // --- LÓGICA DE EXPEDIENTE DE BUS (AJAX PREMIUM) ---
    const modalExpBus = new bootstrap.Modal(document.getElementById('modalViewBus'));

    async function handleVerBus(placa) {
        try {
            console.log('Cargando expediente para:', placa);
            const resp = await fetch(`/empresa/buses/${placa}`);
            const fullData = await resp.json();
            
            const data = fullData.bus;
            const asignacion = fullData.asignacion;
            const estado = data.estado ? data.estado.nombre_estado : 'N/D';

            // Datos Técnicos
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

            // Asignación Actual
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

            document.getElementById('btn_abrir_boveda_auxiliar').setAttribute('data-placa', data.placa);

            // Documentación Legal
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
                        <td class="small text-muted text-center">${fechaVenc}</td>
                        <td class="text-center">
                            <span class="badge bg-${doc.status_color}-subtle text-${doc.status_color} border border-${doc.status_color} x-small rounded-pill px-2 fw-bold">
                                ${doc.status_vigencia}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-sm btn-light border p-1 rounded-circle btn-visor-aux" data-url="${doc.url_archivo}" data-nombre="${doc.tipo_documento.nombre}" title="Ver Documento">
                                    <span class="material-symbols-rounded fs-6">visibility</span>
                                </button>
                                ${doc.status_vigencia !== 'VIGENTE' ? `
                                    <a href="/empresa/documentos/${doc.id_documento}/edit" class="btn btn-sm btn-light border p-1 rounded-circle text-warning" title="Actualizar / Renovar">
                                        <span class="material-symbols-rounded fs-6">edit_note</span>
                                    </a>
                                ` : ''}
                                <a href="${doc.url_archivo}" class="btn btn-sm btn-light border p-1 rounded-circle text-primary" download title="Descargar">
                                    <span class="material-symbols-rounded fs-6">download</span>
                                </a>
                            </div>
                        </td>
                    `;
                    dBody.appendChild(tr);
                });

                // Eventos del Visor
                document.querySelectorAll('.btn-visor-aux').forEach(bv => {
                    bv.addEventListener('click', function() {
                       mostrarVisor(this.dataset.url, this.dataset.nombre);
                    });
                });
            } else {
                dBody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted small">Sin documentos registrados.</td></tr>';
            }

            // Estado Operativo (Badge)
            const viewEst = document.getElementById('view_bus_estado');
            viewEst.textContent = estado;
            viewEst.className = 'badge rounded-pill px-3 py-1 x-small fw-bold';
            const sId = parseInt(data.id_estado);
            if (sId === 1) viewEst.classList.add('bg-success-subtle', 'text-success', 'border', 'border-success-subtle');
            else if (sId === 2) viewEst.classList.add('bg-danger-subtle', 'text-danger', 'border', 'border-danger-subtle');
            else viewEst.classList.add('bg-warning-subtle', 'text-warning', 'border', 'border-warning-subtle');

            modalExpBus.show();
        } catch (err) { console.error('Error AJAX Ficha Bus:', err); }
    }

    // Delegación de eventos para botones de apertura
    document.addEventListener('click', function(e) {
        const btnVer = e.target.closest('.btn-ver-expediente-aux');
        if (btnVer) {
            handleVerBus(btnVer.dataset.placa);
            return;
        }

        const btnBoveda = e.target.closest('#btn_abrir_boveda_auxiliar');
        if (btnBoveda) {
            abrirBovedaAuxiliar(btnBoveda.dataset.placa);
            return;
        }
    });

    // --- BÓVEDA HISTÓRICA (AJAX) ---
    const modalBovedaAux = new bootstrap.Modal(document.getElementById('modalBovedaHistorialAux'));
    async function abrirBovedaAuxiliar(placa) {
        try {
            const resp = await fetch(`/empresa/buses/${placa}/historial-documental`);
            const data = await resp.json();
            
            document.getElementById('boveda_aux_placa').innerText = data.placa;
            const content = document.getElementById('boveda_aux_content');
            content.innerHTML = '';

            if (!data.grupos || Object.keys(data.grupos).length === 0) {
                content.innerHTML = '<div class="alert alert-light text-center border-0 shadow-sm rounded-4 p-5"><span class="material-symbols-rounded display-1 opacity-25 d-block mb-3">folder_off</span><h5 class="fw-bold">Sin Historial</h5><p class="mb-0 text-muted">No hay documentos registrados para este vehículo.</p></div>';
            } else {
                for (const [tipo, docs] of Object.entries(data.grupos)) {
                    let rows = '';
                    docs.forEach(doc => {
                        rows += `
                        <tr class="${doc.es_archivado ? 'opacity-75 bg-light' : ''}">
                            <td class="ps-4">
                                <div class="fw-bold text-dark small text-truncate" style="max-width: 250px;">${doc.nombre}</div>
                                ${doc.es_archivado ? '<span class="badge bg-secondary-subtle text-secondary x-small border border-secondary mt-1">Archivado</span>' : '<span class="badge bg-success-subtle text-success x-small border border-success mt-1">Activo</span>'}
                            </td>
                            <td class="text-muted small text-center">${doc.fecha_carga}</td>
                            <td class="fw-bold text-dark small text-center">${doc.fecha_vencimiento}</td>
                            <td class="text-center"><span class="badge bg-${doc.status_color}-subtle text-${doc.status_color} px-3 py-1 x-small border border-${doc.status_color} rounded-pill fw-bold">${doc.status_vigencia}</span></td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-light border p-2 rounded-circle text-dark" onclick="mostrarVisor('${doc.url_archivo}', '${tipo}')"><span class="material-symbols-rounded fs-6">visibility</span></button>
                                    <a href="${doc.url_archivo}" download class="btn btn-sm btn-light border text-primary p-2 rounded-circle"><span class="material-symbols-rounded fs-6">download</span></a>
                                </div>
                            </td>
                        </tr>`;
                    });

                    content.innerHTML += `
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                        <div class="card-header bg-white border-0 p-4">
                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2 text-uppercase letter-spacing-1 small">
                                <span class="material-symbols-rounded text-primary fs-5">folder_open</span> ${tipo}
                            </h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr class="small fw-bold text-muted">
                                        <th class="ps-4 py-3 border-0">NOMBRES Y ESTADO</th>
                                        <th class="py-3 border-0 text-center">CARGA</th>
                                        <th class="py-3 border-0 text-center">VENCIMIENTO</th>
                                        <th class="py-3 border-0 text-center">VIGENCIA</th>
                                        <th class="py-3 border-0 text-end pe-4">ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>${rows}</tbody>
                            </table>
                        </div>
                    </div>`;
                }
            }
            modalBovedaAux.show();
        } catch (e) { console.error(e); }
    }

    // --- VISOR GLOBAL ---
    window.mostrarVisor = function(url, nombre) {
        const modalViewer = new bootstrap.Modal(document.getElementById('modalVisorDocumento'));
        const iframe = document.getElementById('visor_iframe');
        const imgContainer = document.getElementById('visor_image_container');
        const img = document.getElementById('visor_img');
        const error = document.getElementById('visor_error');
        const download = document.getElementById('visor_download');
        
        document.getElementById('visor_titulo').innerText = 'Documento: ' + nombre;
        iframe.classList.add('d-none'); imgContainer.classList.add('d-none'); error.classList.add('d-none');
        iframe.src = ''; img.src = ''; download.href = url;

        if (!url) { error.classList.remove('d-none'); } 
        else {
            const ext = url.split('.').pop().toLowerCase();
            if (ext === 'pdf') { iframe.src = url; iframe.classList.remove('d-none'); } 
            else if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) { img.src = url; imgContainer.classList.remove('d-none'); } 
            else { error.classList.remove('d-none'); }
        }
        modalViewer.show();
    };

    // Lógica de edición y visualización de Usuario
    document.addEventListener('click', function(e) {
        // Editar Usuario
        const btnEdit = e.target.closest('[data-bs-target="#modalEditarUsuario"]');
        if (btnEdit) {
            const d = btnEdit.dataset;
            document.getElementById('editDoc').value = d.doc;
            document.getElementById('editRol').value = d.rol;
            document.getElementById('editPrimerNombre').value = d.primerNombre || '';
            document.getElementById('editSegundoNombre').value = d.segundoNombre || '';
            document.getElementById('editPrimerApellido').value = d.primerApellido || '';
            document.getElementById('editSegundoApellido').value = d.segundoApellido || '';
            document.getElementById('editCorreo').value = d.correo;
            document.getElementById('editTelefono').value = d.telefono;
            document.getElementById('editEstado').value = d.estado_id || '1';
            document.getElementById('formEditarUsuario').action = '/empresa/usuarios/' + d.doc;
        }

        // Inactivar Viaje (Confirmación)
        const btnInactivar = e.target.closest('.form-inactivar-viaje button');
        if (btnInactivar) {
            e.preventDefault();
            const form = btnInactivar.closest('form');
            if (confirm('¿Estás seguro de inactivar esta asignación? Esta acción no se puede deshacer y el viaje quedará cancelado en el historial.')) {
                form.submit();
            }
        }

        // Ver Usuario
        const btnVer = e.target.closest('[data-bs-target="#modalVerUsuario"]');
        if (btnVer) {
            const d = btnVer.dataset;
            document.getElementById('verDoc').textContent = d.doc;
            document.getElementById('verNombreCompleto').textContent = `${d.primerNombre} ${d.segundoNombre || ''} ${d.primerApellido} ${d.segundoApellido || ''}`.trim();
            document.getElementById('verCorreo').textContent = d.correo;
            document.getElementById('verTelefono').textContent = d.telefono;
            document.getElementById('verRol').textContent = d.rol;
            
            const est = document.getElementById('verEstado');
            est.textContent = d.estado || 'ACTIVO';
            est.className = `badge rounded-pill px-3 py-1 ${d.estado === 'INACTIVO' ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success'}`;

            const fotoCont = document.getElementById('verFotoContainer');
            if (d.foto && d.foto !== 'null' && d.foto !== '') {
                fotoCont.innerHTML = `<img src="/storage/${d.foto}" class="rounded-circle shadow-sm" style="width: 60px; height: 60px; object-fit: cover;" alt="Foto">`;
            } else {
                fotoCont.innerHTML = `<div class="bg-primary bg-opacity-10 p-2 rounded-circle text-primary d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;"><span class="material-symbols-rounded fs-2">person</span></div>`;
            }
        }
    });

    // Lógica para Crear Usuario: Mostrar/Ocultar campos de licencia según Rol
    const selectRol = document.getElementById('select_id_tipo_usuario');
    const wrapperLicencia = document.getElementById('wrapper_licencia_crear');
    const fechaNac = document.getElementById('fecha_nac_crear');
    const fechaExp = document.getElementById('fecha_exp_crear');
    const fechaVenc = document.getElementById('fecha_venc_crear');
    const archivoLic = document.getElementById('archivo_lic_crear');

    const calcVencimiento = () => {
        if(fechaNac && fechaExp && fechaVenc && fechaNac.value && fechaExp.value) {
            const fn = new Date(fechaNac.value);
            const fe = new Date(fechaExp.value);
            let ageAtExp = fe.getFullYear() - fn.getFullYear();
            if (fe.getMonth() < fn.getMonth() || (fe.getMonth() === fn.getMonth() && fe.getDate() < fn.getDate())) {
                ageAtExp--;
            }
            let diffYears = (ageAtExp < 60) ? 3 : 1;
            let fv = new Date(fe);
            fv.setFullYear(fv.getFullYear() + diffYears);
            fechaVenc.value = fv.toISOString().split('T')[0];
        }
    };

    if (fechaNac && fechaExp) {
        fechaNac.addEventListener('change', calcVencimiento);
        fechaExp.addEventListener('change', calcVencimiento);
    }

    if (selectRol) {
        const checkRol = () => {
            const selectValue = selectRol.value;
            const text = selectRol.options[selectRol.selectedIndex].text.toLowerCase();
            const wrapperPass = document.getElementById('msg_pass_obligatorio');

            if (wrapperPass) {
                if (text.includes('propietario')) {
                    wrapperPass.textContent = '(Recomendado)';
                    wrapperPass.className = 'text-warning small fw-bold';
                } else {
                    wrapperPass.textContent = '(Opcional)';
                    wrapperPass.className = 'text-info small fw-bold';
                }
            }

            if (wrapperLicencia) {
                if (text.includes('conductor')) {
                    wrapperLicencia.style.display = 'block';
                    if(fechaNac) fechaNac.required = true;
                    if(fechaExp) fechaExp.required = true;
                    if(fechaVenc) fechaVenc.required = true;
                    if(archivoLic) archivoLic.required = true;
                } else {
                    wrapperLicencia.style.display = 'none';
                    if(fechaNac) fechaNac.required = false;
                    if(fechaExp) fechaExp.required = false;
                    if(fechaVenc) fechaVenc.required = false;
                    if(archivoLic) archivoLic.required = false;
                }
            }
        };
        selectRol.addEventListener('change', checkRol);
        checkRol();
    }

    // Lógica para Detalle de Asignación
    document.addEventListener('click', function(e) {
        const btnAsig = e.target.closest('.btn-ver-detalle-asignacion');
        if (btnAsig) {
            const d = btnAsig.dataset;
            document.getElementById('display-asig-placa').textContent = d.placa;
            document.getElementById('display-asig-modelo').textContent = d.modelo || '---';
            document.getElementById('display-asig-ruta').textContent = d.ruta;
            document.getElementById('display-asig-conductor').textContent = d.conductor;
            document.getElementById('display-asig-doc-cond').textContent = `Documento: ${d.docCond}`;
            document.getElementById('display-asig-propietario').textContent = d.propietario;
            document.getElementById('display-asig-tel-prop').textContent = `Tel: ${d.telProp}`;
            document.getElementById('display-asig-fecha').textContent = d.fecha;
            document.getElementById('display-asig-estado').textContent = d.estado;
            
            const banner = document.getElementById('asignacion-status-banner');
            banner.className = `py-2 px-4 text-center fw-bold small text-uppercase ls-1 bg-${d.estadoColor} ${d.estadoColor === 'warning' ? 'text-dark' : 'text-white'}`;
            
            new bootstrap.Modal(document.getElementById('modalDetalleAsignacion')).show();
        }
    });

    // Lógica para Detalle de Documento
    document.addEventListener('click', function(e) {
        const btnDoc = e.target.closest('.btn-ver-detalle-documento');
        if (btnDoc) {
            const d = btnDoc.dataset;
            document.getElementById('display-doc-nombre').textContent = d.nombre;
            document.getElementById('display-doc-placa').textContent = `Placa: ${d.placa}`;
            document.getElementById('display-doc-venc').textContent = d.venc;
            
            const est = document.getElementById('display-doc-estado');
            est.textContent = d.estado;
            est.className = `badge rounded-pill px-3 py-1 bg-${d.color} ${d.color === 'warning' ? 'text-dark' : ''}`;
            
            document.getElementById('btn-descargar-doc').href = d.archivo;
            document.getElementById('btn-previsualizar-doc').href = d.archivo;
            
            new bootstrap.Modal(document.getElementById('modalDetalleDocumento')).show();
        }
    });

    // Mantener la pestaña activa después de recargar (Priorizar URL sobre localStorage)
    const urlParams = new URLSearchParams(window.location.search);
    const urlTab = urlParams.get('tab');
    let lastTab = urlTab || localStorage.getItem('sigu_aux_tab');
    
    if (lastTab) {
        // Asegurar que el ID sea correcto (tab-personal vs personal)
        const targetId = lastTab.startsWith('tab-') ? lastTab : `tab-${lastTab}`;
        const tabEl = document.querySelector(`[data-bs-target="#${targetId}"]`);
        if (tabEl) {
            bootstrap.Tab.getOrCreateInstance(tabEl).show();
        }
    }

    document.querySelectorAll('button[data-bs-toggle="pill"]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', e => {
            const targetId = e.target.getAttribute('data-bs-target').replace('#', '');
            localStorage.setItem('sigu_aux_tab', targetId);
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('tab', targetId.replace('tab-', ''));
            window.history.pushState({}, '', newUrl);
        });
    });

    // --- NUEVA LÓGICA DE ASIGNACIONES (SISTEMA ROBUSTO) ---
    const createFechaIn = document.getElementById('create_fecha');
    const createHoraFin = document.getElementById('create_hora_fin');
    
    if (createFechaIn && createHoraFin) {
        console.log("Sistema de Asignaciones inicializado correctamente.");

        function updateHoraFin() {
            const val = createFechaIn.value;
            console.log("Trigger updateHoraFin:", val);
            if (!val) {
                createHoraFin.value = '';
                toggleModalSelects(false);
                return;
            }
            const start = new Date(val);
            if (isNaN(start.getTime())) {
                console.warn("Fecha inválida:", val);
                return;
            }

            const end = new Date(start.getTime() + (8 * 60 * 60 * 1000));
            const options = { hour: '2-digit', minute: '2-digit', hour12: true };
            createHoraFin.value = end.toLocaleTimeString([], options).toUpperCase();

            toggleModalSelects(true);
            fetchDisponibilidad(val);
        }

        function toggleModalSelects(enabled) {
            const placa = document.getElementById('create_placa');
            const cond = document.getElementById('create_doc_us');
            if (placa) {
                placa.disabled = !enabled;
                if(!enabled) placa.innerHTML = '<option value="" selected disabled>Seleccionar primero la fecha...</option>';
            }
            if (cond) {
                cond.disabled = !enabled;
                if(!enabled) cond.innerHTML = '<option value="" selected disabled>Seleccionar primero la fecha...</option>';
            }
        }

        async function fetchDisponibilidad(dateTime) {
            const placaSelect = document.getElementById('create_placa');
            const condSelect = document.getElementById('create_doc_us');
            if (!placaSelect || !condSelect) return;

            placaSelect.innerHTML = '<option value="" disabled selected>⏳ Buscando buses...</option>';
            condSelect.innerHTML = '<option value="" disabled selected>⏳ Buscando conductores...</option>';

            try {
                const url = `{{ route('empresa.asignaciones.disponibilidad') }}?fecha=${encodeURIComponent(dateTime)}`;
                console.log("Fetching disponibilidad:", url);
                
                const resp = await fetch(url);
                const data = await resp.json();

                placaSelect.innerHTML = '<option value="" disabled selected>Seleccionar vehículo...</option>';
                if (data.buses && data.buses.length > 0) {
                    data.buses.forEach(b => {
                        const opt = document.createElement('option');
                        opt.value = b.placa;
                        opt.textContent = b.label;
                        if (b.disabled) opt.disabled = true;
                        placaSelect.appendChild(opt);
                    });
                } else {
                    placaSelect.innerHTML = '<option value="" disabled>🚫 Sin buses disponibles</option>';
                }

                condSelect.innerHTML = '<option value="" disabled selected>Seleccionar conductor...</option>';
                if (data.conductores && data.conductores.length > 0) {
                    data.conductores.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.doc_usuario;
                        opt.textContent = c.nombre_completo;
                        if (c.disabled) opt.disabled = true;
                        condSelect.appendChild(opt);
                    });
                } else {
                    condSelect.innerHTML = '<option value="" disabled>🚫 Sin conductores disponibles</option>';
                }
            } catch (err) {
                console.error('Error AJAX Disponibilidad:', err);
                placaSelect.innerHTML = '<option value="" disabled>⚠️ Error de conexión</option>';
                condSelect.innerHTML = '<option value="" disabled>⚠️ Error de conexión</option>';
            }
        }

        createFechaIn.addEventListener('input', updateHoraFin);

        document.addEventListener('click', function(e) {
            const btnQuick = e.target.closest('.quick-time');
            if (btnQuick) {
                console.log("Quick Time click detectado:", btnQuick.dataset.time);
                const time = btnQuick.dataset.time;
                
                // Preservar la fecha ya ingresada o usar hoy si está vacío
                let datePart = createFechaIn.value ? createFechaIn.value.split('T')[0] : new Date().toISOString().split('T')[0];
                
                createFechaIn.value = `${datePart}T${time}`;
                updateHoraFin();
            }
        });

        @if($errors->any())
            @if(old('form_type') === 'create')
                const modalUsr = new bootstrap.Modal(document.getElementById('modalCrearUsuario'));
                modalUsr.show();
            @else
                const modalAsig = new bootstrap.Modal(document.getElementById('modalAsignarConductor'));
                if (modalAsig) {
                    modalAsig.show();
                    if(typeof updateHoraFin === 'function' && createFechaIn && createFechaIn.value) updateHoraFin();
                }
            @endif
        @endif
    } else {
        console.error("No se encontraron los elementos create_fecha o create_hora_fin");
    }
});
</script>

<style>
    .fw-black { font-weight: 900; }
    .ls-1 { letter-spacing: 0.5px; }
    .x-small { font-size: 0.72rem; }
    .nav-pills .nav-link { color: #6c757d; font-weight: 600; font-size: 0.9rem; transition: all 0.3s; }
    .nav-pills .nav-link.active { background-color: var(--bs-primary); color: white; box-shadow: 0 4px 10px rgba(var(--bs-primary-rgb), 0.3); }
    .rounded-4 { border-radius: 1rem !important; }
    .min-w-max { min-width: max-content; }
</style>
@endpush
@endsection
