<!-- Modal ASIGNACIONES (Listar y Crear) -->
<div class="modal fade" id="modalAsignaciones" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center mb-0">
                    <span class="material-symbols-rounded me-2 fs-4 text-primary">assignment</span>
                    Gestión de Asignaciones
                </h5>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            
            <div class="modal-body p-0">
                <!-- Tabs Nav -->
                <ul class="nav nav-tabs nav-justified border-bottom" id="asigTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold py-3 text-uppercase ls-1" id="lista-asig-tab" data-bs-toggle="tab" data-bs-target="#tab-lista-asig" type="button" role="tab">
                            <span class="material-symbols-rounded align-middle me-1">list</span> Listado
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-3 text-uppercase ls-1" id="crear-asig-tab" data-bs-toggle="tab" data-bs-target="#tab-crear-asig" type="button" role="tab">
                            <span class="material-symbols-rounded align-middle me-1">add_circle</span> CREAR ASIGNACIÓN
                        </button>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content" id="asigTabContent">
                    
                    <!-- TAB 1: LISTADO -->
                    <div class="tab-pane fade show active p-4" id="tab-lista-asig" role="tabpanel">
                        <div class="d-flex gap-2 mb-3">
                            <div class="input-group input-group-sm w-75">
                                <span class="input-group-text bg-light border-0"><span class="material-symbols-rounded fs-6 text-muted">search</span></span>
                                <input type="text" id="searchAsignaciones" class="form-control bg-light border-0" placeholder="Buscar por placa o conductor...">
                            </div>
                            <button class="btn btn-sm btn-dark px-3 fw-bold" onclick="cargarAsignaciones()">Filtrar</button>
                        </div>

                        <!-- Contenedor -->
                        <div class="table-responsive" id="asignaciones_table_container">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="text-muted mt-2 mb-0">Cargando...</p>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: CREAR ASIGNACIÓN -->
                    <div class="tab-pane fade p-4" id="tab-crear-asig" role="tabpanel">
                        <form method="POST" action="{{ route('auxiliar.asignaciones.store') }}" id="formCrearAsignacion">
                            @csrf
                            <div class="row g-3">
                                <!-- 1. Ruta -->
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Ruta <span class="text-danger">*</span></label>
                                    <select name="id_ruta" id="asig_ruta" class="form-select form-select-sm" required>
                                        <option value="" selected disabled>Seleccionar...</option>
                                    </select>
                                </div>

                                <!-- 2. Fecha -->
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Fecha <span class="text-danger">*</span></label>
                                    <input type="date" name="fecha" id="asig_fecha" class="form-control form-control-sm" required min="{{ date('Y-m-d') }}">
                                </div>

                                <!-- 3. Hora Salida -->
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Hora Salida <span class="text-danger">*</span></label>
                                    <input type="time" name="hora_salida" id="asig_hora_salida" class="form-control form-control-sm" required>
                                </div>

                                <!-- 4. Hora Llegada (Estimada) + Botón -->
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Hora Llegada (Est.)</label>
                                    <div class="d-flex gap-1">
                                        <input type="time" name="hora_llegada" id="asig_hora_llegada" class="form-control form-control-sm text-muted" readonly disabled>
                                        <button type="button" id="btn_asig_8h" class="btn btn-outline-primary btn-sm px-2 fw-bold" style="font-size: 0.65rem;">+8H</button>
                                    </div>
                                </div>

                                <!-- 5. Vehículo (Placa) -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Vehículo (Placa) <span class="text-danger">*</span></label>
                                    <select name="placa" id="asig_placa" class="form-select form-select-sm" required disabled title="Fije fecha primero">
                                        <option value="" selected disabled>Seleccionar...</option>
                                    </select>
                                </div>

                                <!-- 6. Conductor -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Conductor <span class="text-danger">*</span></label>
                                    <select name="doc_us" id="asig_conductor" class="form-select form-select-sm" required disabled title="Fije fecha primero">
                                        <option value="" selected disabled>Seleccionar...</option>
                                    </select>
                                </div>

                                <div class="col-12 text-end mt-4">
                                    <button type="button" class="btn btn-sm btn-light px-3 fw-bold" data-bs-dismiss="modal">CANCELAR</button>
                                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm">GUARDAR ASIGNACIÓN</button>
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
function cargarAsignaciones() {
    const container = document.getElementById('asignaciones_table_container');
    const search = document.getElementById('searchAsignaciones').value;

    container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="text-muted mt-2">Cargando...</p></div>';

    fetch(`{{ route('auxiliar.asignaciones.index') }}?ajax=1&search=${search}`)
        .then(r => r.text())
        .then(html => {
            container.innerHTML = html;
        });
}

function cargarOpcionesAsignacion() {
    const fecha = document.getElementById('asig_fecha').value;
    const hora = document.getElementById('asig_hora_salida').value;
    const horaLlegadaInput = document.getElementById('asig_hora_llegada');
    const selectPlaca = document.getElementById('asig_placa');
    const selectCond = document.getElementById('asig_conductor');
    const selectRuta = document.getElementById('asig_ruta');

    // Calcular Hora Llegada Estimada (+8h)
    if (hora) {
        const [h, m] = hora.split(':');
        let date = new Date();
        date.setHours(h, m);
        date.setHours(date.getHours() + 8);
        horaLlegadaInput.value = `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
    }

    // Control de habilitación
    if (fecha && hora) {
        selectPlaca.disabled = false;
        selectCond.disabled = false;
        selectPlaca.title = "";
        selectCond.title = "";
    } else {
        selectPlaca.disabled = true;
        selectCond.disabled = true;
        selectPlaca.title = "Fije fecha y hora primero";
        selectCond.title = "Fije fecha y hora primero";
        return;
    }

    // Mostrar estado de carga
    selectPlaca.innerHTML = '<option value="" disabled selected>Cargando...</option>';
    selectCond.innerHTML = '<option value="" disabled selected>Cargando...</option>';

    fetch(`{{ route('empresa.asignaciones.disponibilidad') }}?fecha=${fecha}&hora_salida=${hora}`)
        .then(r => r.json())
        .then(data => {
            selectPlaca.innerHTML = '<option value="" disabled selected>Seleccionar...</option>';
            if (data.buses.length === 0) {
                selectPlaca.innerHTML = '<option value="" disabled>Sin buses disponibles</option>';
            } else {
                data.buses.forEach(b => {
                    let opt = document.createElement('option');
                    opt.value = b.placa;
                    opt.textContent = b.label;
                    selectPlaca.appendChild(opt);
                });
            }

            selectCond.innerHTML = '<option value="" disabled selected>Seleccionar...</option>';
            if (data.conductores.length === 0) {
                selectCond.innerHTML = '<option value="" disabled>Sin conductores disponibles</option>';
            } else {
                data.conductores.forEach(c => {
                    let opt = document.createElement('option');
                    opt.value = c.doc_usuario;
                    opt.textContent = c.nombre_completo;
                    selectCond.appendChild(opt);
                });
            }
        });
}

// Cargar rutas iniciales
function cargarRutasIniciales() {
    const selectRuta = document.getElementById('asig_ruta');
    fetch(`{{ route('auxiliar.asignaciones.index') }}?ajax_options=1`)
        .then(r => r.json())
        .then(data => {
            selectRuta.innerHTML = '<option value="" disabled selected>Seleccionar...</option>';
            data.rutas.forEach(r => {
                let opt = document.createElement('option');
                opt.value = r.id_ruta;
                opt.textContent = r.nombre_ruta;
                selectRuta.appendChild(opt);
            });
        });
}

document.getElementById('asig_fecha').addEventListener('change', cargarOpcionesAsignacion);
document.getElementById('asig_hora_salida').addEventListener('change', cargarOpcionesAsignacion);

document.getElementById('btn_asig_8h').addEventListener('click', function() {
    const now = new Date();
    document.getElementById('asig_fecha').value = now.toISOString().split('T')[0];
    document.getElementById('asig_hora_salida').value = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;
    cargarOpcionesAsignacion();
});

document.getElementById('modalAsignaciones').addEventListener('shown.bs.modal', function() {
    cargarAsignaciones();
    cargarRutasIniciales();
    cargarOpcionesAsignacion();
});

// Submit Form AJAX
document.getElementById('formCrearAsignacion').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            alert(data.message);
            this.reset();
            // Switch to list tab
            const trigger = document.getElementById('lista-asig-tab');
            bootstrap.Tab.getInstance(trigger).show();
            cargarAsignaciones();
        } else {
            alert(data.message || 'Error al guardar.');
        }
    })
    .catch(err => {
        alert('Error en la petición.');
    });
});
</script>
