<!-- Modal DOCUMENTOS (Revisión) -->
<div class="modal fade" id="modalDocumentos" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center mb-0">
                    <span class="material-symbols-rounded me-2 fs-4 text-primary">folder_shared</span>
                    Revisión de Documentos
                </h5>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            
            <div class="modal-body p-4">
                <!-- Filtros -->
                <div class="d-flex gap-2 mb-3">
                    <div class="input-group input-group-sm w-75">
                        <span class="input-group-text bg-light border-0"><span class="material-symbols-rounded fs-6 text-muted">search</span></span>
                        <input type="text" id="searchDocs" class="form-control bg-light border-0" placeholder="Buscar por placa o documento...">
                    </div>
                    <button class="btn btn-sm btn-dark px-3 fw-bold" onclick="cargarDocumentos()">Filtrar</button>
                </div>

                <!-- Contenedor -->
                <div class="table-responsive" id="documentos_table_container">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="text-muted mt-2 mb-0">Cargando...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cargarDocumentos() {
    const container = document.getElementById('documentos_table_container');
    const search = document.getElementById('searchDocs').value;

    container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="text-muted mt-2">Cargando...</p></div>';

    fetch(`{{ route('auxiliar.documentos.index') }}?ajax=1&search=${search}`)
        .then(r => r.text())
        .then(html => {
            container.innerHTML = html;
        });
}

function procesarDocumento(id, accion) {
    if(!confirm(`¿Está seguro de ${accion} este documento?`)) return;

    fetch(`/auxiliar/documentos/${id}/${accion}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            alert(data.message);
            cargarDocumentos(); // Recargar la tabla
        } else {
            alert(data.message || 'Error al procesar.');
        }
    })
    .catch(err => {
        alert('Error en la petición.');
    });
}

document.getElementById('modalDocumentos').addEventListener('shown.bs.modal', function() {
    cargarDocumentos();
});
</script>
