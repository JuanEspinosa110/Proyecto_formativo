<!-- Modal Gestionar Documento (Auxiliar) -->
<div class="modal fade" id="modalGestionarDocumento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-light border-0 py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded text-primary">gavel</span>
                    Revisión de Documento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Visor de Documento -->
                    <div class="col-md-7 border-end">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Vista Previa</label>
                        <div id="visorDocumento" class="bg-light rounded-4 d-flex align-items-center justify-content-center border" style="height: 350px; overflow: hidden;">
                            <span class="text-muted small">Cargando archivo...</span>
                        </div>
                    </div>
                    <!-- Detalles y Acciones -->
                    <div class="col-md-5 d-flex flex-column justify-content-between">
                        <div>
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Información</label>
                            <div class="p-3 bg-light rounded-3 mb-3">
                                <div class="small fw-bold text-dark" id="txtTipoDoc">Tipo: ---</div>
                                <div class="text-muted small" id="txtPlacaDoc">Placa: ---</div>
                            </div>

                            <p class="text-muted small">Al aprobar este documento, si el vehículo cumple con todos los requisitos regulatorios, pasará a estado **ACTIVO**.</p>
                        </div>

                        <div class="d-flex flex-column gap-2 mt-4">
                            <!-- Formulario Aprobar -->
                            <form id="formAprobarDocumento" action="#" method="POST" class="w-100">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-2 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                    <span class="material-symbols-rounded">check_circle</span>
                                    Aprobar Documento
                                </button>
                            </form>
                            <!-- Formulario Rechazar -->
                            <form id="formRechazarDocumento" action="#" method="POST" class="w-100">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold py-2 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                    <span class="material-symbols-rounded">cancel</span>
                                    Rechazar Documento
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('modalGestionarDocumento');
        if (modal) {
            modal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const tipo = button.getAttribute('data-tipo');
                const placa = button.getAttribute('data-placa');
                const archivo = button.getAttribute('data-archivo');

                // Actualizar textos
                document.getElementById('txtTipoDoc').innerText = 'Tipo: ' + tipo;
                document.getElementById('txtPlacaDoc').innerText = 'Placa: ' + placa;

                // Actualizar forms action
                const routeAprobar = "{{ url('empresa/documentos') }}/" + id + "/aprobar";
                const routeRechazar = "{{ url('empresa/documentos') }}/" + id + "/rechazar";
                document.getElementById('formAprobarDocumento').setAttribute('action', routeAprobar);
                document.getElementById('formRechazarDocumento').setAttribute('action', routeRechazar);

                // Cargar visor
                const visor = document.getElementById('visorDocumento');
                const baseUrl = "{{ asset('storage') }}";
                if (archivo) {
                    const ext = archivo.split('.').pop().toLowerCase();
                    if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                        visor.innerHTML = `<img src="${baseUrl}/${archivo}" class="img-fluid rounded-3" style="max-height: 100%; object-fit: contain;">`;
                    } else if (ext === 'pdf') {
                        visor.innerHTML = `<iframe src="${baseUrl}/${archivo}" width="100%" height="100%" class="border-0 rounded-3"></iframe>`;
                    } else {
                        visor.innerHTML = `<span class="text-muted small">No se puede previsualizar este formato. <a href="${baseUrl}/${archivo}" target="_blank" class="text-primary">Ver externo</a></span>`;
                    }
                } else {
                    visor.innerHTML = `<span class="text-muted small">Sin archivo asociado</span>`;
                }
            });
        }
    });
</script>
