<!-- Modal Detalle de Documento -->
<div class="modal fade" id="modalDetalleDocumento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-light p-4">
                <h5 class="modal-title fw-black text-dark d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                        <span class="material-symbols-rounded text-primary">description</span>
                    </div>
                    Información Documental
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <!-- Icono Descriptivo del Documento -->
                <div class="mb-4">
                    <div id="doc-type-icon" class="bg-primary bg-opacity-10 p-4 rounded-circle d-inline-flex mx-auto mb-3">
                        <span class="material-symbols-rounded text-primary fs-1">folder_shared</span>
                    </div>
                    <h5 id="display-doc-nombre" class="fw-black text-dark mb-1">---</h5>
                    <p class="text-muted small mb-0" id="display-doc-placa">Ficha Vehicular: ---</p>
                </div>

                <!-- Detalles Legales -->
                <div class="card border-0 bg-light rounded-4 p-4 mb-4">
                    <div class="row g-3">
                        <div class="col-6 border-end">
                            <label class="text-muted x-small fw-bold text-uppercase d-block mb-1">Vencimiento</label>
                            <span id="display-doc-venc" class="text-dark fw-bold">---</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted x-small fw-bold text-uppercase d-block mb-1">Estado Legal</label>
                            <span id="display-doc-estado" class="badge rounded-pill px-3 py-1">---</span>
                        </div>
                    </div>
                </div>

                <!-- Acciones de Descarga/Ver -->
                <div class="row g-2">
                    <div class="col-6">
                        <a href="#" id="btn-descargar-doc" class="btn btn-dark w-100 rounded-pill py-2 fw-bold d-flex align-items-center justify-content-center gap-2" download>
                            <span class="material-symbols-rounded fs-5">download</span>
                            Descargar
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" id="btn-previsualizar-doc" target="_blank" class="btn btn-primary w-100 rounded-pill py-2 fw-bold d-flex align-items-center justify-content-center gap-2">
                            <span class="material-symbols-rounded fs-5">visibility</span>
                            Ver Online
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold w-100" data-bs-dismiss="modal">CERRAR DETALLE</button>
            </div>
        </div>
    </div>
</div>
