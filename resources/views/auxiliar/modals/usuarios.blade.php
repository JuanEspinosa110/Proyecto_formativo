<!-- Modal USUARIOS (Listar y Crear) -->
<div class="modal fade" id="modalUsuarios" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center mb-0">
                    <span class="material-symbols-rounded me-2 fs-4 text-primary">group</span>
                    Gestión de Usuarios
                </h5>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            
            <div class="modal-body p-0">
                <!-- Tabs Nav -->
                <ul class="nav nav-tabs nav-justified border-bottom" id="usuariosTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold py-3 text-uppercase ls-1" id="lista-tab" data-bs-toggle="tab" data-bs-target="#tab-lista" type="button" role="tab">
                            <span class="material-symbols-rounded align-middle me-1">list</span> Listado
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-3 text-uppercase ls-1" id="crear-tab" data-bs-toggle="tab" data-bs-target="#tab-crear" type="button" role="tab">
                            <span class="material-symbols-rounded align-middle me-1">person_add</span> Registrar Nuevo
                        </button>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content" id="usuariosTabContent">
                    
                    <!-- TAB 1: LISTADO -->
                    <div class="tab-pane fade show active p-4" id="tab-lista" role="tabpanel">
                        <!-- Buscador rápido -->
                        <div class="d-flex gap-2 mb-3">
                            <div class="input-group input-group-sm w-50">
                                <span class="input-group-text bg-light border-0"><span class="material-symbols-rounded fs-6 text-muted">search</span></span>
                                <input type="text" id="searchUsuarios" class="form-control bg-light border-0" placeholder="Buscar por nombre o documento...">
                            </div>
                            <select id="filterRol" class="form-select form-select-sm bg-light border-0 w-25">
                                <option value="">Todos los roles</option>
                                <option value="conductor">Conductor</option>
                                <option value="propietario">Propietario</option>
                            </select>
                            <button class="btn btn-sm btn-dark px-3 fw-bold" onclick="cargarUsuarios()">Filtrar</button>
                        </div>

                        <!-- Contenedor de la Tabla -->
                        <div class="table-responsive" id="usuarios_table_container">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="text-muted mt-2 mb-0">Cargando listado...</p>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: CREAR USUARIO -->
                    <div class="tab-pane fade p-4" id="tab-crear" role="tabpanel">
                        <form method="POST" action="{{ route('auxiliar.usuarios.store') }}" id="formCrearUsuario" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="form_type" value="create">
                            
                            <div class="row g-3">
                                <div class="col-md-6 text-input-validate" data-type="number">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Documento <span class="text-danger">*</span></label>
                                    <input type="text" name="doc_usuario" class="form-control form-control-sm" required minlength="6" maxlength="10" pattern="[1-9][0-9]{5,9}" placeholder="Documento...">
                                    <small class="text-muted fs-xs">Mín. 6, máx. 10 dígitos (Sin 0 inicial).</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Rol Operativo <span class="text-danger">*</span></label>
                                    <select name="id_tipo_usuario" id="select_id_tipo_usuario" class="form-select form-select-sm" required>
                                        <option value="" selected disabled>Seleccione...</option>
                                        <!-- Se cargarán vía JS o se pasan desde controller -->
                                    </select>
                                </div>

                                <div class="col-md-6 text-input-validate" data-type="text">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Primer Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="primer_nombre" class="form-control form-control-sm" required minlength="2" maxlength="30" pattern="[a-zA-ZÁÉÍÓÚáéíóúÑñ]+(\s[a-zA-ZÁÉÍÓÚáéíóúÑñ]+)?">
                                </div>
                                
                                <div class="col-md-6 text-input-validate" data-type="text">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Segundo Nombre</label>
                                    <input type="text" name="segundo_nombre" class="form-control form-control-sm" minlength="2" maxlength="30" pattern="[a-zA-ZÁÉÍÓÚáéíóúÑñ]+(\s[a-zA-ZÁÉÍÓÚáéíóúÑñ]+)?">
                                </div>

                                <div class="col-md-6 text-input-validate" data-type="text">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Primer Apellido <span class="text-danger">*</span></label>
                                    <input type="text" name="primer_apellido" class="form-control form-control-sm" required minlength="2" maxlength="30" pattern="[a-zA-ZÁÉÍÓÚáéíóúÑñ]+(\s[a-zA-ZÁÉÍÓÚáéíóúÑñ]+)?">
                                </div>
                                <div class="col-md-6 text-input-validate" data-type="text">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Segundo Apellido <span class="text-danger">*</span></label>
                                    <input type="text" name="segundo_apellido" class="form-control form-control-sm" required minlength="2" maxlength="30" pattern="[a-zA-ZÁÉÍÓÚáéíóúÑñ]+(\s[a-zA-ZÁÉÍÓÚáéíóúÑñ]+)?">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Correo <span class="text-danger">*</span></label>
                                    <input type="email" name="correo" class="form-control form-control-sm" required placeholder="usuario@sigu.com">
                                </div>
                                <div class="col-md-6 text-input-validate" data-type="number">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Teléfono <span class="text-danger">*</span></label>
                                    <input type="text" name="telefono" class="form-control form-control-sm" required minlength="10" maxlength="10" pattern="[0-9]{10}">
                                </div>

                                <div class="col-md-12" id="wrapper_password_crear" style="display: none;">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Contraseña de Acceso <span class="text-info" id="msg_pass_obligatorio">(Opcional)</span></label>
                                    <div class="input-group input-group-sm">
                                        <input type="password" name="password" id="pass_crear" class="form-control" placeholder="Mínimo 8 caracteres">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('pass_crear')">
                                            <span class="material-symbols-rounded fs-6 align-middle">visibility</span>
                                        </button>
                                    </div>
                                    <small class="text-muted fs-xs">Si se deja vacío, se generará una contraseña aleatoria.</small>
                                </div>

                                <!-- SECCIÓN EXCLUSIVA PARA CONDUCTORES -->
                                <div class="col-12" id="wrapper_licencia_crear" style="display: none;">
                                    <hr class="my-3 border-light">
                                    <h6 class="text-primary small fw-bold mb-3 d-flex align-items-center">
                                        <span class="material-symbols-rounded fs-5 me-1">id_card</span>
                                        INFORMACIÓN DE LICENCIA (SOLO CONDUCTORES)
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Fecha de Nacimiento <span class="text-danger">*</span></label>
                                            <input type="date" name="fecha_nacimiento" id="fecha_nac_crear" class="form-control form-control-sm" max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Fecha de Expedición <span class="text-danger">*</span></label>
                                            <input type="date" name="fecha_expedicion" id="fecha_exp_crear" class="form-control form-control-sm" max="{{ date('Y-m-d') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Fecha de Vencimiento</label>
                                            <input type="date" name="fecha_vencimiento" id="fecha_venc_crear" class="form-control form-control-sm bg-light" readonly placeholder="Automático">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Archivo Licencia <span class="text-danger">*</span></label>
                                            <input type="file" name="archivo_licencia" id="archivo_lic_crear" class="form-control form-control-sm" accept=".pdf,.png,.jpg">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 text-end mt-4">
                                    <button type="button" class="btn btn-sm btn-light px-3 fw-bold" data-bs-dismiss="modal">CANCELAR</button>
                                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm">GUARDAR USUARIO</button>
                                </div>

                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Scripts específicos para este modal
document.addEventListener('DOMContentLoaded', function() {
    // Cargar Catálogos (Roles)
    const selectRol = document.getElementById('select_id_tipo_usuario');
    if (selectRol && selectRol.options.length <= 1) {
        // Cargar roles vía AJAX o pasar desde dashboard
        fetch('{{ route("auxiliar.usuarios.index") }}' + '?ajax_roles=1')
            .then(r => r.json())
            .then(data => {
                data.roles.forEach(r => {
                    let opt = document.createElement('option');
                    opt.value = r.id_tipo_usuario;
                    opt.textContent = r.nombre_tipo;
                    selectRol.appendChild(opt);
                });
            });
    }

    // Toggle Licencia
    if (selectRol) {
        selectRol.addEventListener('change', function() {
            const val = this.options[this.selectedIndex].text.toLowerCase();
            const wrapperLic = document.getElementById('wrapper_licencia_crear');
            const wrapperPass = document.getElementById('wrapper_password_crear');
            const msgPass = document.getElementById('msg_pass_obligatorio');

            wrapperPass.style.display = 'block';
            if (val.includes('propietario')) {
                msgPass.textContent = '(Recomendado)';
            } else {
                msgPass.textContent = '(Opcional)';
            }

            if (wrapperLic) {
                if (val.includes('conductor')) {
                    wrapperLic.style.display = 'block';
                    document.getElementById('fecha_nac_crear').required = true;
                    document.getElementById('fecha_exp_crear').required = true;
                    document.getElementById('archivo_lic_crear').required = true;
                } else {
                    wrapperLic.style.display = 'none';
                    document.getElementById('fecha_nac_crear').required = false;
                    document.getElementById('fecha_exp_crear').required = false;
                    document.getElementById('archivo_lic_crear').required = false;
                }
            }
        });
    }

    // Cálculo Vencimiento
    const fechaNac = document.getElementById('fecha_nac_crear');
    const fechaExp = document.getElementById('fecha_exp_crear');
    const fechaVenc = document.getElementById('fecha_venc_crear');

    const calcVenc = () => {
        if(fechaNac && fechaExp && fechaVenc && fechaNac.value && fechaExp.value) {
            const fn = new Date(fechaNac.value);
            const fe = new Date(fechaExp.value);
            let age = fe.getFullYear() - fn.getFullYear();
            if (fe.getMonth() < fn.getMonth() || (fe.getMonth() === fn.getMonth() && fe.getDate() < fn.getDate())) {
                age--;
            }
            let yrs = (age < 60) ? 3 : 1;
            let fv = new Date(fe);
            fv.setFullYear(fv.getFullYear() + yrs);
            fechaVenc.value = fv.toISOString().split('T')[0];
        }
    };

    if (fechaNac && fechaExp) {
        fechaNac.addEventListener('change', calcVenc);
        fechaExp.addEventListener('change', calcVenc);
    }
});

function cargarUsuarios() {
    const container = document.getElementById('usuarios_table_container');
    const search = document.getElementById('searchUsuarios').value;
    const rol = document.getElementById('filterRol').value;

    container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="text-muted mt-2">Cargando...</p></div>';

    fetch(`{{ route('auxiliar.usuarios.index') }}?ajax=1&search=${search}&role=${rol}`)
        .then(r => r.text())
        .then(html => {
            container.innerHTML = html;
        });
}

// Cargar al abrir el modal
document.getElementById('modalUsuarios').addEventListener('shown.bs.modal', cargarUsuarios);
</script>
