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
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Vehículo (Placa) <span class="text-danger">*</span></label>
                                    <select name="placa" id="asig_placa" class="form-select form-select-sm" required>
                                        <option value="" selected disabled>Seleccionar...</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Ruta <span class="text-danger">*</span></label>
                                    <select name="id_ruta" id="asig_ruta" class="form-select form-select-sm" required>
                                        <option value="" selected disabled>Seleccionar...</option>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Conductor <span class="text-danger">*</span></label>
                                    <select name="doc_us" id="asig_conductor" class="form-select form-select-sm" required>
                                        <option value="" selected disabled>Seleccionar...</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Fecha <span class="text-danger">*</span></label>
                                    <input type="date" name="fecha" class="form-control form-control-sm" required min="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Hora Salida <span class="text-danger">*</span></label>
                                    <input type="time" name="hora_salida" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Hora Llegada <span class="text-danger">*</span></label>
                                    <input type="time" name="hora_llegada" class="form-control form-control-sm" required>
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
    fetch(`{{ route('auxiliar.asignaciones.index') }}?ajax_options=1`)
        .then(r => r.json())
        .then(data => {
            const selectPlaca = document.getElementById('asig_placa');
            const selectRuta = document.getElementById('asig_ruta');
            const selectCond = document.getElementById('asig_conductor');

            // Limpiar
            selectPlaca.innerHTML = '<option value="" disabled selected>Seleccionar...</option>';
            selectRuta.innerHTML = '<option value="" disabled selected>Seleccionar...</option>';
            selectCond.innerHTML = '<option value="" disabled selected>Seleccionar...</option>';

            // Llenar Placas
            data.buses.forEach(b => {
                let opt = document.createElement('option');
                opt.value = b.placa;
                opt.textContent = b.placa;
                selectPlaca.appendChild(opt);
            });

            // Llenar Rutas
            data.rutas.forEach(r => {
                let opt = document.createElement('option');
                opt.value = r.id_ruta;
                opt.textContent = r.nombre_ruta;
                selectRuta.appendChild(opt);
            });

            // Llenar Conductores
            data.conductores.forEach(c => {
                let opt = document.createElement('option');
                opt.value = c.doc_usuario;
                opt.textContent = `${c.primer_nombre} ${c.primer_apellido} (${c.doc_usuario})`;
                selectCond.appendChild(opt);
            });
        });
}

document.getElementById('modalAsignaciones').addEventListener('shown.bs.modal', function() {
    cargarAsignaciones();
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
