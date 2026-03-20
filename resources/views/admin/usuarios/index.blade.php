@extends('admin.layouts.app')

@section('title', 'Usuarios — SIGU')

@section('content')
<div class="container-fluid pt-0 pb-4">
    <!-- Header de Página -->
    <div class="d-flex align-items-center justify-content-between mb-4 px-1">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold">Gestión de Usuarios</h1>
            <p class="text-muted small mb-0">Administra los accesos y perfiles del personal de la empresa.</p>
        </div>
        <button class="btn btn-primary d-flex align-items-center gap-2 px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
            <span class="material-symbols-rounded">person_add</span>
            Nuevo Usuario
        </button>
    </div>

    @if(isset($licenciasAlerta) && $licenciasAlerta->count() > 0)
        <div class="mb-4">
            @foreach($licenciasAlerta as $licencia)
                <div class="alert alert-{{ $licencia->status_color }} alert-dismissible fade show d-flex align-items-center shadow-sm py-2" role="alert">
                    <span class="material-symbols-rounded flex-shrink-0 me-2">
                        {{ $licencia->estado_expiracion == 'VENCIDO' ? 'error' : 'warning' }}
                    </span>
                    <div>
                        <strong>Licencia {{ ucfirst(strtolower($licencia->estado_expiracion)) }}:</strong> 
                        El conductor <strong>{{ $licencia->usuario->primer_nombre ?? 'N/A' }} {{ $licencia->usuario->primer_apellido ?? '' }}</strong> (Doc: {{ $licencia->doc_usuario }}) 
                        tiene su licencia de conducción 
                        @if($licencia->estado_expiracion == 'VENCIDO')
                            vencida desde el {{ $licencia->fecha_vencimiento->format('d/m/Y') }}.
                        @else
                            que vence el {{ $licencia->fecha_vencimiento->format('d/m/Y') }}.
                        @endif
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endforeach
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4 rounded-3">
        <div class="card-body p-3">
            <form method="GET" action="" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">
                            <span class="material-symbols-rounded text-muted">search</span>
                        </span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Buscar por nombre o documento..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-muted small fw-bold text-uppercase mb-0">Rol:</label>
                        <select name="role" class="form-select bg-light border-0">
                            <option value="">Todos los roles</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id_tipo_usuario }}" {{ (string)($selectedRole ?? '') === (string)$r->id_tipo_usuario ? 'selected' : '' }}>
                                    {{ $r->nombre_tipo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2 ms-auto">
                    <button class="btn btn-dark w-100 fw-semibold">Consultar</button>
                </div>
            </form>
        </div>
    </div>

        @include('admin.usuarios.partials.table')

</div>


@include('admin.usuarios.partials.create_modal')



<!-- Modal EDITAR USUARIO -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold d-flex align-items-center small">
                    <span class="material-symbols-rounded text-warning me-2 fs-5">edit</span>
                    MODIFICAR PERFIL
                </h6>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form method="POST" id="formEditarUsuario" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    {{-- Errores de validación --}}
                    @if($errors->any() && old('form_type') == 'edit')
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
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Documento</label>
                            <input type="text" name="doc_usuario" id="editDoc" class="form-control form-control-sm bg-light fw-bold" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Rol Operativo <span class="text-danger">*</span></label>
                            <select name="id_tipo_usuario" id="editRol" class="form-select form-select-sm" required
                                oninvalid="this.setCustomValidity('Este campo es obligatorio')"
                                oninput="this.setCustomValidity('')">
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->id_tipo_usuario }}">{{ $rol->nombre_tipo }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-12">
                            <div class="alert alert-info py-2 small mb-0 border-0 shadow-none bg-info bg-opacity-10 text-info">
                                <span class="material-symbols-rounded fs-6 align-middle">info</span>
                                Los nombres y apellidos no pueden ser modificados.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Primer Nombre</label>
                            <input type="text" id="editPrimerNombre" class="form-control form-control-sm bg-light" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Segundo Nombre</label>
                            <input type="text" id="editSegundoNombre" class="form-control form-control-sm bg-light" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Primer Apellido</label>
                            <input type="text" id="editPrimerApellido" class="form-control form-control-sm bg-light" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Segundo Apellido</label>
                            <input type="text" id="editSegundoApellido" class="form-control form-control-sm bg-light" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Correo <span class="text-danger">*</span></label>
                            <input type="email" name="correo" id="editCorreo" class="form-control form-control-sm" required
                                oninvalid="if(this.validity.valueMissing){this.setCustomValidity('Este campo es obligatorio')}else{this.setCustomValidity('Ingrese un correo válido')}"
                                oninput="this.setCustomValidity('')">
                        </div>
                        <div class="col-md-6 text-input-validate" data-type="number">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Teléfono <span class="text-danger">*</span></label>
                            <input type="text" name="telefono" id="editTelefono" class="form-control form-control-sm" required minlength="10" maxlength="10" pattern="[0-9]{10}"
                                oninvalid="if(this.validity.valueMissing){this.setCustomValidity('Este campo es obligatorio')}else{this.setCustomValidity('El teléfono debe tener 10 dígitos')}"
                                oninput="this.setCustomValidity('')">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Foto de Perfil (Opcional)</label>
                            <input type="file" name="foto_usuario" class="form-control form-control-sm" accept="image/*">
                            <small class="text-muted fs-xs">Deje en blanco para mantener la actual.</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Estatus del Usuario</label>
                            <select name="id_estado" id="editEstado" class="form-select form-select-sm" required
                                oninvalid="this.setCustomValidity('Este campo es obligatorio')"
                                oninput="this.setCustomValidity('')">
                                @foreach($estados as $estado)
                                    <option value="{{ $estado->id_estado }}">{{ $estado->nombre_estado }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- SECCIÓN EXCLUSIVA PARA EDITAR LICENCIA GESTIÓN DE CONDUCTORES -->
                        <div class="col-12" id="wrapper_licencia_editar" style="display: none;">
                            <hr class="my-3 border-light">
                            <h6 class="text-primary small fw-bold mb-3 d-flex align-items-center">
                                <span class="material-symbols-rounded fs-5 me-1">id_card</span>
                                ACTUALIZAR LICENCIA DE CONDUCCIÓN
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Fecha de Expedición</label>
                                    <input type="date" name="fecha_expedicion" id="editLicExp" class="form-control form-control-sm" max="{{ date('Y-m-d') }}">
                                    <small class="text-muted fs-xs">Al cambiarla, se recalculará el vencimiento.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Nueva Fecha Vencimiento</label>
                                    <input type="date" name="fecha_vencimiento" id="editLicVenc" class="form-control form-control-sm bg-light" readonly>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Renovar Archivo Licencia (PDF/PNG)</label>
                                    <input type="file" name="archivo_licencia" class="form-control form-control-sm" accept=".pdf,.png">
                                    <small class="text-muted fs-xs">Deje en blanco para mantener el documento actual.</small>
                                </div>
                            </div>
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

<!-- Modal VER USUARIO -->
<div class="modal fade" id="modalVerUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small">
                    <span class="material-symbols-rounded text-info me-2 fs-5">person_search</span>
                    DETALLES DE USUARIO
                </h6>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="mb-4 d-flex align-items-center gap-3">
                    <div id="verFotoContainer">
                        <!-- Se llena vía JS -->
                    </div>
                    <div>
                        <h5 id="verNombreCompleto" class="fw-bold mb-0 text-dark"></h5>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="text-muted small">ID: <span id="verDoc" class="fw-bold"></span></span>
                            <span class="text-muted small">·</span>
                            <span id="verRol" class="badge bg-dark text-white px-3 py-2 border-0 fw-bold" style="font-size: 0.9rem; letter-spacing: 0.5px;"></span>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-12">
                        <div class="p-3 bg-light rounded-3 border border-light-subtle">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="d-block text-muted small fw-bold text-uppercase ls-1">Sede / Ciudad</label>
                                    <span id="verCiudad" class="fw-medium text-dark"></span>
                                </div>
                                <div class="col-6 text-end">
                                    <label class="d-block text-muted small fw-bold text-uppercase ls-1">Estado</label>
                                    <span id="verEstado" class="badge rounded-pill"></span>
                                </div>
                                <div class="col-12 border-top pt-2">
                                    <label class="d-block text-muted small fw-bold text-uppercase ls-1">Correo Electrónico</label>
                                    <span id="verCorreo" class="fw-medium text-dark"></span>
                                </div>
                                <div class="col-12 border-top pt-2">
                                    <label class="d-block text-muted small fw-bold text-uppercase ls-1">Teléfono de Contacto</label>
                                    <span id="verTelefono" class="fw-medium text-dark"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- SECCIÓN DE LICENCIA (SE MUESTRA SOLO A CONDUCTORES) -->
                    <div class="col-12" id="verSeccionLicencia" style="display: none;">
                        <h6 class="text-primary small fw-bold mt-2 mb-2 d-flex align-items-center text-uppercase ls-1 border-bottom pb-2">
                            <span class="material-symbols-rounded fs-5 me-1">id_card</span>
                            Información de Licencia
                        </h6>
                        <div class="p-3 bg-primary bg-opacity-10 rounded-3 border border-primary border-opacity-25">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="d-block text-muted small fw-bold text-uppercase ls-1">Fecha Expedición</label>
                                    <span id="verLicExp" class="fw-medium text-dark"></span>
                                </div>
                                <div class="col-6 text-end">
                                    <label class="d-block text-muted small fw-bold text-uppercase ls-1">Estado Licencia</label>
                                    <span id="verLicEstado" class="badge rounded-pill"></span>
                                </div>
                                <div class="col-6 border-top pt-2">
                                    <label class="d-block text-muted small fw-bold text-uppercase ls-1">Fecha Vencimiento</label>
                                    <span id="verLicVenc" class="fw-medium text-dark"></span>
                                </div>
                                <div class="col-6 border-top pt-2 text-end">
                                    <label class="d-block text-muted small fw-bold text-uppercase ls-1">Documento Licencia</label>
                                    <a href="#" id="verLicArchivoBtn" target="_blank" class="btn btn-sm btn-primary py-1 px-3 d-inline-flex align-items-center fw-bold shadow-sm" style="display: none !important;">
                                        <span class="material-symbols-rounded fs-6 me-1">download</span> Doc
                                    </a>
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
document.addEventListener('DOMContentLoaded', function () {
    // Sincronización para creación de usuario
    const formCrear = document.querySelector('#modalCrearUsuario form');
    if (formCrear) {
        let h = document.createElement('input');
        h.type = 'hidden';
        h.name = 'form_type';
        h.value = 'create';
        formCrear.appendChild(h);
    }
    const formEdit = document.querySelector('#modalEditarUsuario form');
    if (formEdit) {
        let h = document.createElement('input');
        h.type = 'hidden';
        h.name = 'form_type';
        h.value = 'edit';
        formEdit.appendChild(h);
    }

    // Persistencia de modales en error
    @if($errors->any())
        const formType = "{{ old('form_type') }}";
        if (formType === 'edit') {
            const modalEl = document.getElementById('modalEditarUsuario');
            if (modalEl) bootstrap.Modal.getOrCreateInstance(modalEl).show();
        } else {
            const modalEl = document.getElementById('modalCrearUsuario');
            if (modalEl) bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    @endif

    // Delegación para botones VER y EDITAR
    document.addEventListener('click', function(e) {
        // Botón VER
        const btnVer = e.target.closest('[data-bs-target="#modalVerUsuario"]');
        if (btnVer) {
            e.preventDefault();
            try {
                const primerNombre = btnVer.getAttribute('data-primer-nombre') || '';
                const segundoNombre = btnVer.getAttribute('data-segundo-nombre') || '';
                const primerApellido = btnVer.getAttribute('data-primer-apellido') || '';
                const segundoApellido = btnVer.getAttribute('data-segundo-apellido') || '';
                const estadoNombre = btnVer.getAttribute('data-estado') || '';

                const foto = btnVer.getAttribute('data-foto');
                const fotoCont = document.getElementById('verFotoContainer');
                if (foto && foto !== 'null' && foto !== '') {
                    fotoCont.innerHTML = `<img src="/storage/${foto}" class="rounded-circle shadow-sm" style="width: 80px; height: 80px; object-fit: cover;" alt="Foto">`;
                } else {
                    fotoCont.innerHTML = `<div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                            <span class="material-symbols-rounded fs-1">person</span>
                                          </div>`;
                }

                document.getElementById('verDoc').textContent = btnVer.getAttribute('data-doc');
                document.getElementById('verNombreCompleto').textContent = `${primerNombre} ${segundoNombre} ${primerApellido} ${segundoApellido}`.replace(/\s+/g, ' ').trim();
                document.getElementById('verCorreo').textContent = btnVer.getAttribute('data-correo');
                document.getElementById('verTelefono').textContent = btnVer.getAttribute('data-telefono');
                document.getElementById('verRol').textContent = btnVer.getAttribute('data-rol');
                document.getElementById('verCiudad').textContent = btnVer.getAttribute('data-ciudad') || 'No asignada';
                
                const verEstado = document.getElementById('verEstado');
                if (verEstado) {
                    verEstado.textContent = estadoNombre;
                    verEstado.className = 'badge rounded-pill px-3 py-2';
                    if (estadoNombre.toLowerCase().includes('activ')) {
                        verEstado.classList.add('bg-success-subtle', 'text-success', 'border', 'border-success-subtle');
                    } else {
                        verEstado.classList.add('bg-danger-subtle', 'text-danger', 'border', 'border-danger-subtle');
                    }
                }

                // Lógica para mostrar la sección de la licencia si es un conductor
                const seccionLicencia = document.getElementById('verSeccionLicencia');
                if (seccionLicencia) {
                    if (btnVer.getAttribute('data-rol') && btnVer.getAttribute('data-rol').toLowerCase().includes('conductor')) {
                        seccionLicencia.style.display = 'block';
                        document.getElementById('verLicExp').textContent = btnVer.getAttribute('data-licencia-exp') || 'No registrada';
                        document.getElementById('verLicVenc').textContent = btnVer.getAttribute('data-licencia-venc') || 'No registrada';
                        
                        const estadoLic = btnVer.getAttribute('data-licencia-estado');
                        const verLicEstado = document.getElementById('verLicEstado');
                        if(estadoLic) {
                            verLicEstado.textContent = estadoLic;
                            verLicEstado.className = 'badge rounded-pill px-3 py-1';
                            if(estadoLic === 'VIGENTE') verLicEstado.classList.add('bg-success-subtle', 'text-success');
                            else if(estadoLic === 'VENCIDO') verLicEstado.classList.add('bg-danger-subtle', 'text-danger');
                            else verLicEstado.classList.add('bg-warning-subtle', 'text-warning');
                        } else {
                            verLicEstado.textContent = 'Sin estado';
                            verLicEstado.className = 'badge rounded-pill bg-secondary px-3 py-1';
                        }
                        
                        const docArchivo = btnVer.getAttribute('data-licencia-archivo');
                        const btnArchivo = document.getElementById('verLicArchivoBtn');
                        if (docArchivo) {
                            btnArchivo.href = '/storage/' + docArchivo;
                            btnArchivo.style.setProperty('display', 'inline-flex', 'important');
                        } else {
                            btnArchivo.href = '#';
                            btnArchivo.style.setProperty('display', 'none', 'important');
                        }
                    } else {
                        seccionLicencia.style.display = 'none';
                    }
                }

                const modalEl = document.getElementById('modalVerUsuario');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }
            } catch (err) { console.error('Error in Ver Usuario:', err); }
        }

        // Botón EDITAR
        const btnEdit = e.target.closest('[data-bs-target="#modalEditarUsuario"]');
        if (btnEdit) {
            e.preventDefault();
            try {
                document.getElementById('editDoc').value = btnEdit.getAttribute('data-doc');
                document.getElementById('editPrimerNombre').value = btnEdit.getAttribute('data-primer-nombre');
                document.getElementById('editSegundoNombre').value = btnEdit.getAttribute('data-segundo-nombre');
                document.getElementById('editPrimerApellido').value = btnEdit.getAttribute('data-primer-apellido');
                document.getElementById('editSegundoApellido').value = btnEdit.getAttribute('data-segundo-apellido');
                document.getElementById('editCorreo').value = btnEdit.getAttribute('data-correo');
                document.getElementById('editTelefono').value = btnEdit.getAttribute('data-telefono');
                document.getElementById('editRol').value = btnEdit.getAttribute('data-rol');
                document.getElementById('editEstado').value = btnEdit.getAttribute('data-estado_id');
                
                // Lógica Licencia
                const editLicExp = document.getElementById('editLicExp');
                const editLicVenc = document.getElementById('editLicVenc');
                const wrapperLicEditar = document.getElementById('wrapper_licencia_editar');
                
                const fechaNacEdit = btnEdit.getAttribute('data-fecha-nacimiento');
                
                let selectedRolText = '';
                const editRolOptions = document.getElementById('editRol').options;
                for(let i=0; i<editRolOptions.length; i++) {
                    if(editRolOptions[i].value == btnEdit.getAttribute('data-rol')) {
                        selectedRolText = editRolOptions[i].text.toLowerCase();
                        break;
                    }
                }
                
                if (selectedRolText.includes('conductor')) {
                    wrapperLicEditar.style.display = 'block';
                    editLicExp.value = btnEdit.getAttribute('data-licencia-exp');
                    editLicVenc.value = btnEdit.getAttribute('data-licencia-venc');
                    
                    const calcVencEdit = () => {
                        if(fechaNacEdit && editLicExp.value) {
                            const fn = new Date(fechaNacEdit);
                            const fe = new Date(editLicExp.value);
                            let ageAtExp = fe.getFullYear() - fn.getFullYear();
                            if (fe.getMonth() < fn.getMonth() || (fe.getMonth() === fn.getMonth() && fe.getDate() < fn.getDate())) {
                                ageAtExp--;
                            }
                            let diffYears = (ageAtExp < 60) ? 3 : 1;
                            let fv = new Date(fe);
                            fv.setFullYear(fv.getFullYear() + diffYears);
                            editLicVenc.value = fv.toISOString().split('T')[0];
                        }
                    };
                    editLicExp.removeEventListener('change', window.calcVencEditFn); 
                    window.calcVencEditFn = calcVencEdit;
                    editLicExp.addEventListener('change', window.calcVencEditFn);
                } else {
                    wrapperLicEditar.style.display = 'none';
                }

                const form = document.getElementById('formEditarUsuario');
                if (form) {
                    const prefix = '{{ Auth::user()->id_tipo_usuario == 1 ? "admin" : "empresa" }}';
                    form.action = '/' + prefix + '/usuarios/' + btnEdit.getAttribute('data-doc');
                }

                const modalEl = document.getElementById('modalEditarUsuario');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }
            } catch (err) { console.error('Error in Edit Usuario:', err); }
        }
    });

    // Validaciones de Entrada en Tiempo Real
    document.querySelectorAll('.text-input-validate').forEach(container => {
        const input = container.querySelector('input');
        const type = container.getAttribute('data-type');
        const errorDiv = container.querySelector('.real-time-error');
        
        if (input) {
            input.addEventListener('input', function(e) {
                let value = this.value;
                let isValid = true;
                let errorMessage = '';

                if (type === 'number') {
                    this.value = value.replace(/[^0-9]/g, '');
                } else if (type === 'text') {
                    // Solo permitir letras y espacios (Regex para validación en vivo)
                    const nameRegex = /^[A-Za-zÁÉÍÓÚáéíóúÑñ]+(\s[A-Za-zÁÉÍÓÚáéíóúÑñ]+)?$/;
                    
                    if (value.length > 0) {
                        if (/[0-9]/.test(value)) {
                            errorMessage = 'No se permiten números.';
                            isValid = false;
                        } else if (/[^a-zA-ZÁÉÍÓÚáéíóúÑñ\s]/.test(value)) {
                            errorMessage = 'No se permiten caracteres especiales.';
                            isValid = false;
                        } else if (value.startsWith(' ')) {
                            errorMessage = 'No debe iniciar con espacios.';
                            isValid = false;
                        } else if (value.length < 2) {
                            errorMessage = 'Mínimo 2 caracteres.';
                            isValid = false;
                        } else if (value.length > 30) {
                            errorMessage = 'Máximo 30 caracteres.';
                            isValid = false;
                        } else if (!nameRegex.test(value)) {
                            errorMessage = 'Máximo dos palabras y sin espacios al final.';
                            isValid = false;
                        }
                    } else if (this.hasAttribute('required')) {
                        errorMessage = 'Este campo es obligatorio.';
                        isValid = false;
                    }

                    // UI Feedback
                    if (!isValid && value.length > 0) {
                        this.classList.add('is-invalid');
                        if (errorDiv) {
                            errorDiv.textContent = errorMessage;
                            errorDiv.style.display = 'block';
                        }
                    } else {
                        this.classList.remove('is-invalid');
                        if (errorDiv) {
                            errorDiv.style.display = 'none';
                        }
                    }
                }
            });
        }
    });

    // Validar antes de enviar el formulario
    const formCrearUser = document.getElementById('formCrearUsuario');
    if (formCrearUser) {
        formCrearUser.addEventListener('submit', function(e) {
            const invalidInputs = this.querySelectorAll('.is-invalid');
            if (invalidInputs.length > 0) {
                e.preventDefault();
                invalidInputs[0].focus();
            }
        });
    }

    const selectRol = document.getElementById('select_id_tipo_usuario');
    const wrapperPass = document.getElementById('wrapper_password_crear');
    const msgPass = document.getElementById('msg_pass_obligatorio');
    const wrapperLicencia = document.getElementById('wrapper_licencia_crear');
    
    // Inputs de licencia
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

    if (selectRol && wrapperPass) {
        const checkRol = () => {
            const val = selectRol.options[selectRol.selectedIndex].text.toLowerCase();
            
            if (val.includes('propietario')) {
                wrapperPass.style.display = 'block';
                msgPass.textContent = '(Recomendado para propietarios)';
                msgPass.classList.remove('text-info');
                msgPass.classList.add('text-warning');
            } else {
                wrapperPass.style.display = 'block'; // Siempre visible pero opcional
                msgPass.textContent = '(Opcional)';
                msgPass.classList.remove('text-warning');
                msgPass.classList.add('text-info');
            }
            
            if (wrapperLicencia) {
                if (val.includes('conductor')) {
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
});

function togglePasswordVisibility(id) {
    const input = document.getElementById(id);
    if (input) {
        input.type = input.type === 'password' ? 'text' : 'password';
    }
}
</script>
<style>
    .ls-1 {
        letter-spacing: 0.5px;
    }

    .fw-extrabold {
        font-weight: 800;
    }

    .tracking-tighter {
        letter-spacing: -0.05em;
    }

    .rounded-4 {
        border-radius: 1rem !important;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(94, 84, 142, 0.04) !important;
    }

    .bg-primary-subtle {
        background-color: rgba(94, 84, 142, 0.1) !important;
    }

    .bg-success-subtle {
        background-color: #e8f5e9 !important;
    }

    .bg-danger-subtle {
        background-color: #fbe9e7 !important;
    }

    .bg-info-subtle {
        background-color: #e3f2fd !important;
    }

    .text-info {
        color: #0288d1 !important;
    }
</style>
@endpush
@endsection
