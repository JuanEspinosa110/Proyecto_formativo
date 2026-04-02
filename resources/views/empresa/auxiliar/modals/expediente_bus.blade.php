<!-- Modal VER BUS (Igual a Admin) -->
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
                                    <span class="material-symbols-rounded fs-5 text-primary fw-bold">info</span>
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
                                    <span class="material-symbols-rounded fs-5 text-primary fw-bold">person</span>
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

                <!-- 2. Documentación Legaly Bóveda -->
                <div class="row g-4">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold text-dark text-uppercase mb-0 d-flex align-items-center gap-2">
                                    <span class="material-symbols-rounded text-primary fw-bold">folder_shared</span>
                                    Documentación Legal Activa
                                </h6>
                                <button type="button" class="btn btn-sm btn-outline-dark d-flex align-items-center gap-1 fw-bold rounded-pill px-3" id="btn_abrir_boveda_auxiliar" data-placa="">
                                    <span class="material-symbols-rounded fs-6">history_edu</span>
                                    Bóveda Histórica
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 border-0 small fw-bold text-muted text-uppercase ls-1">Documento</th>
                                            <th class="border-0 small fw-bold text-muted text-uppercase ls-1 text-center">Vencimiento</th>
                                            <th class="border-0 small fw-bold text-muted text-uppercase ls-1 text-center font-bold">Estado</th>
                                            <th class="border-0 small fw-bold text-muted text-uppercase ls-1 text-end pe-4">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="view_bus_docs_body">
                                        <!-- Vía AJAX -->
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
