<!-- Modal Reportes (Auxiliar) -->
<div class="modal fade" id="modalReportes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-light border-0 py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded text-primary">analytics</span>
                    Descarga de Reportes
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formReportesAuxiliar" action="{{ route('empresa.reportes.export') }}" method="GET">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Tipo de Reporte</label>
                            <select name="tipo_reporte" class="form-select rounded-3" required>
                                <option value="general">Reporte General de Operación</option>
                                <option value="usuarios">Listado de Usuarios (Personal)</option>
                                <option value="buses">Estado de la Flota (Buses)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Formato</label>
                            <div class="d-flex gap-2">
                                <input type="radio" class="btn-check" name="formato" id="formatExcel" value="excel" checked>
                                <label class="btn btn-outline-success w-100 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2" for="formatExcel">
                                    <span class="material-symbols-rounded">table_chart</span> Excel
                                </label>

                                <input type="radio" class="btn-check" name="formato" id="formatPDF" value="pdf">
                                <label class="btn btn-outline-danger w-100 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2" for="formatPDF">
                                    <span class="material-symbols-rounded">picture_as_pdf</span> PDF
                                </label>
                            </div>
                        </div>
                        <div class="col-6">
                             <label class="form-label small fw-bold text-muted text-uppercase">Rango</label>
                             <select name="rango" class="form-select rounded-3">
                                 <option value="hoy">Hoy</option>
                                 <option value="semana">Esta Semana</option>
                                 <option value="mes">Este Mes</option>
                             </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-3 gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center gap-2">
                        <span class="material-symbols-rounded fs-5">download</span>
                        Generar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
