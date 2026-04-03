<!-- Modal Detalle de Asignación (Viaje) -->
<div class="modal fade" id="modalDetalleAsignacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-dark p-4">
                <h5 class="modal-title fw-black text-white d-flex align-items-center gap-3">
                    <div class="bg-white bg-opacity-10 p-2 rounded-circle">
                        <span class="material-symbols-rounded text-white">route</span>
                    </div>
                    Detalle de Asignación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Banner de Estado -->
                <div id="asignacion-status-banner" class="py-2 px-4 text-center fw-bold small text-uppercase ls-1">
                    ESTADO: <span id="display-asig-estado">---</span>
                </div>

                <div class="p-4">
                    <div class="row g-4">
                        <!-- Vehículo y Ruta -->
                        <div class="col-12">
                            <div class="card border-0 bg-light rounded-4 p-3">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-primary text-white p-2 rounded-3 shadow-sm">
                                        <span class="material-symbols-rounded">directions_bus</span>
                                    </div>
                                    <div>
                                        <h6 class="fw-black text-dark mb-0" id="display-asig-placa">---</h6>
                                        <small class="text-muted" id="display-asig-modelo">---</small>
                                    </div>
                                    <div class="ms-auto text-end">
                                        <label class="text-muted x-small fw-bold text-uppercase d-block mb-0">Ruta</label>
                                        <span class="text-primary fw-bold" id="display-asig-ruta">---</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Conductor -->
                        <div class="col-md-6">
                            <label class="text-muted x-small fw-bold text-uppercase d-block mb-2">Conductor Asignado</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-secondary text-white p-2 rounded-circle shadow-sm">
                                    <span class="material-symbols-rounded fs-5">person</span>
                                </div>
                                <div>
                                    <span class="d-block fw-bold text-dark small" id="display-asig-conductor">---</span>
                                    <small class="text-muted" id="display-asig-doc-cond">---</small>
                                </div>
                            </div>
                        </div>

                        <!-- Propietario -->
                        <div class="col-md-6">
                            <label class="text-muted x-small fw-bold text-uppercase d-block mb-2">Propietario del Bus</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-info text-white p-2 rounded-circle shadow-sm">
                                    <span class="material-symbols-rounded fs-5">person_pin</span>
                                </div>
                                <div>
                                    <span class="d-block fw-bold text-dark small" id="display-asig-propietario">---</span>
                                    <small class="text-muted" id="display-asig-tel-prop">---</small>
                                </div>
                            </div>
                        </div>

                        <!-- Fecha y Hora -->
                        <div class="col-12 border-top pt-3">
                            <div class="row text-center">
                                <div class="col-6 border-end">
                                    <label class="text-muted x-small fw-bold text-uppercase d-block mb-1">Fecha Operación</label>
                                    <span class="text-dark fw-bold" id="display-asig-fecha">---</span>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted x-small fw-bold text-uppercase d-block mb-1">Turno/Hora</label>
                                    <span class="text-dark fw-bold">Programado</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-light">
                <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold w-100" data-bs-dismiss="modal">CERRAR DETALLE</button>
            </div>
        </div>
    </div>
</div>
