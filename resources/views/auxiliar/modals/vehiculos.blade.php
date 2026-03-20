<!-- Modal VEHÍCULOS (Listar y Crear) -->
<div class="modal fade" id="modalVehiculos" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center mb-0">
                    <span class="material-symbols-rounded me-2 fs-4 text-primary">directions_bus</span>
                    Gestión de Vehículos
                </h5>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            
            <div class="modal-body p-0">
                <!-- Tabs Nav -->
                <ul class="nav nav-tabs nav-justified border-bottom" id="busesTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold py-3 text-uppercase ls-1" id="lista-buses-tab" data-bs-toggle="tab" data-bs-target="#tab-lista-buses" type="button" role="tab">
                            <span class="material-symbols-rounded align-middle me-1">list</span> Listado
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-3 text-uppercase ls-1" id="crear-bus-tab" data-bs-toggle="tab" data-bs-target="#tab-crear-bus" type="button" role="tab">
                            <span class="material-symbols-rounded align-middle me-1">add_circle</span> Registrar Nuevo
                        </button>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content" id="busesTabContent">
                    
                    <!-- TAB 1: LISTADO -->
                    <div class="tab-pane fade show active p-4" id="tab-lista-buses" role="tabpanel">
                        <!-- Buscador -->
                        <div class="d-flex gap-2 mb-3">
                            <div class="input-group input-group-sm w-75">
                                <span class="input-group-text bg-light border-0"><span class="material-symbols-rounded fs-6 text-muted">search</span></span>
                                <input type="text" id="searchBuses" class="form-control bg-light border-0" placeholder="Buscar por placa o modelo...">
                            </div>
                            <button class="btn btn-sm btn-dark px-3 fw-bold" onclick="cargarBuses()">Filtrar</button>
                        </div>

                        <!-- Contenedor -->
                        <div class="table-responsive" id="buses_table_container">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="text-muted mt-2 mb-0">Cargando...</p>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: CREAR BUS -->
                    <div class="tab-pane fade p-4" id="tab-crear-bus" role="tabpanel">
                        <form method="POST" action="{{ route('auxiliar.buses.store') }}" id="formCrearBus">
                            @csrf
                            <input type="hidden" name="form_type" value="create">

                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Placa <span class="text-danger">*</span></label>
                                    <input type="text" name="placa" class="form-control form-control-sm fw-bold" placeholder="ABC123" required style="text-transform:uppercase" maxlength="6">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Modelo / Ref. <span class="text-danger">*</span></label>
                                    <input type="text" name="modelo" class="form-control form-control-sm" placeholder="Ej: Toyota 2019" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Capacidad <span class="text-danger">*</span></label>
                                    <input type="number" name="capacidad_pasajeros" class="form-control form-control-sm" required placeholder="00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Kilometraje <span class="text-danger">*</span></label>
                                    <input type="number" name="kilometraje" class="form-control form-control-sm" required placeholder="0">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Licencia Tránsito <span class="text-danger">*</span></label>
                                    <input type="text" name="linc_transito" class="form-control form-control-sm" required maxlength="12" placeholder="8 dígitos">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Doc. Propietario <span class="text-danger">*</span></label>
                                    <input type="text" name="doc_propietario" class="form-control form-control-sm" required maxlength="15" placeholder="Documento">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Nombre Propietario <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre_propietario" class="form-control form-control-sm" placeholder="Nombre completo" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Teléfono <span class="text-danger">*</span></label>
                                    <input type="text" name="telefono" class="form-control form-control-sm" required maxlength="10" placeholder="312...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Correo <span class="text-danger">*</span></label>
                                    <input type="email" name="correo" class="form-control form-control-sm" placeholder="ejemplo@correo.com" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Serial Chasis <span class="text-danger">*</span></label>
                                    <input type="text" name="numero_chasis" class="form-control form-control-sm" required maxlength="17" placeholder="17 dígitos">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Serial Motor <span class="text-danger">*</span></label>
                                    <input type="text" name="numero_motor" class="form-control form-control-sm" required maxlength="17" placeholder="8-17 dígitos">
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Estado Operativo <span class="text-danger">*</span></label>
                                    <select name="id_estado" id="select_id_estado" class="form-select form-select-sm" required>
                                        <option value="" selected disabled>Seleccionar...</option>
                                        <option value="1">Activo</option>
                                        <option value="2">Inactivo</option>
                                    </select>
                                </div>

                                <div class="col-12 text-end mt-4">
                                    <button type="button" class="btn btn-sm btn-light px-3 fw-bold" data-bs-dismiss="modal">CANCELAR</button>
                                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm">GUARDAR BUS</button>
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
document.addEventListener('DOMContentLoaded', function() {
    const selectEst = document.getElementById('select_id_estado');
    if (selectEst && selectEst.options.length <= 1) {
        // Cargar estados si es necesario, o podemos asumir que se pasan?
        // Para simplificar, podemos cargarlos si el controlador los tiene.
        // O dejar que el usuario seleccione.
        // Pero el BusService devuelve estados.
    }
});

function cargarBuses() {
    const container = document.getElementById('buses_table_container');
    const search = document.getElementById('searchBuses').value;

    container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="text-muted mt-2">Cargando...</p></div>';

    fetch(`{{ route('auxiliar.buses.index') }}?ajax=1&search=${search}`)
        .then(r => r.text())
        .then(html => {
            container.innerHTML = html;
        });
}

document.getElementById('modalVehiculos').addEventListener('shown.bs.modal', function() {
    cargarBuses();

    // Cargar estados dinámicamente si está vacío
    const selectEst = document.getElementById('select_id_estado');
    if (selectEst && selectEst.options.length <= 1) {
        // Podríamos hacer una petición rápida o usar una variable inline de Blade si se pasa del dashboard.
    }
});
</script>
