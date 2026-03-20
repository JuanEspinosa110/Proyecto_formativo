<!-- Modal REPORTES (Configuración y Descarga) -->
<div class="modal fade" id="modalReportes" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center mb-0">
                    <span class="material-symbols-rounded me-2 fs-4 text-primary">analytics</span>
                    Generar Reportes
                </h5>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            
            <div class="modal-body p-4">
                <form method="POST" action="{{ route('auxiliar.reportes.export') }}" id="formReportes">
                    @csrf
                    
                    <div class="row g-3">
                        <!-- Tipo de Reporte -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Tipo de Reporte <span class="text-danger">*</span></label>
                            <select name="tipo_reporte" id="rep_tipo_reporte" class="form-select form-select-sm" required>
                                <option value="" selected disabled>Seleccione una opción...</option>
                                <option value="conductores">Listado de Conductores</option>
                                <option value="recorridos">Historial de Recorridos (Asignaciones)</option>
                                <option value="documentos">Inventario de Documentación</option>
                            </select>
                        </div>

                        <!-- Rango de Fechas (Condicional) -->
                        <div class="col-md-6 d-none" id="rep_wrapper_fecha_inicio">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6 d-none" id="rep_wrapper_fecha_fin">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Fecha Fin</label>
                            <input type="date" name="fecha_fin" class="form-control form-control-sm">
                        </div>

                        <!-- Formato -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Formato de Descarga</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="formno" id="formatExcel" value="excel" checked>
                                    <label class="form-check-label" for="formatExcel">
                                        Excel (.xlsx)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="formno" id="formatPdf" value="pdf">
                                    <label class="form-check-label" for="formatPdf">
                                        PDF (.pdf)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-4 d-grid">
                            <button type="submit" class="btn btn-success fw-bold rounded-pill shadow-sm py-2">
                                <span class="material-symbols-rounded fs-5 align-middle me-1">download_for_offline</span> GENERAR Y DESCARGAR
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectTipo = document.getElementById('rep_tipo_reporte');
    const wrapIni = document.getElementById('rep_wrapper_fecha_inicio');
    const wrapFin = document.getElementById('rep_wrapper_fecha_fin');

    selectTipo.addEventListener('change', function() {
        if (this.value === 'recorridos') {
            wrapIni.classList.remove('d-none');
            wrapFin.classList.remove('d-none');
        } else {
            wrapIni.classList.add('d-none');
            wrapFin.classList.add('d-none');
            wrapIni.querySelector('input').value = '';
            wrapFin.querySelector('input').value = '';
        }
    });

    // Validar antes de enviar (opcional, dompdf suele tardar)
});
</script>
