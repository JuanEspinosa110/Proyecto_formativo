@if(isset($asignacionActiva) && !$recorridoActivo)
<!-- MODAL INICIAR RUTA (SENTIDO) -->
<div class="modal fade" id="modalIniciarRuta" tabindex="-1" aria-labelledby="modalIniciarRutaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow-lg rounded-4" action="{{ route('conductor.iniciarRecorrido', $asignacionActiva->id_viaje) }}" method="POST">
            @csrf
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-primary text-white rounded-top-4 pb-3">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded">route</span> Configurar Trayecto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-4">
                <div class="text-center mb-4">
                    <h5 class="fw-bold text-dark mb-1">Ruta: {{ $asignacionActiva->ruta->nombre_ruta ?? 'N/A' }}</h5>
                    <p class="text-muted small">Seleccione el sentido del recorrido a realizar. Cada sentido se guardará como un viaje/recorrido individual e independiente de la meta fiscal para su cierre total.</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="sentido" id="sentidoIda" value="IDA" required>
                        <label class="btn btn-outline-primary w-100 p-3 rounded-4 d-flex flex-column align-items-center" for="sentidoIda">
                            <span class="material-symbols-rounded fs-1 mb-2">trending_flat</span>
                            <span class="fw-bold">Viaje de IDA</span>
                            <small class="opacity-75 d-block mt-1">Origen &rarr; Destino</small>
                        </label>
                    </div>
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="sentido" id="sentidoVuelta" value="VUELTA" required>
                        <label class="btn btn-outline-primary w-100 p-3 rounded-4 d-flex flex-column align-items-center" for="sentidoVuelta">
                            <span class="material-symbols-rounded fs-1 mb-2">sync_alt</span>
                            <span class="fw-bold">Viaje VUELTA</span>
                            <small class="opacity-75 d-block mt-1">Destino &rarr; Origen</small>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 pt-1">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold fs-5 shadow-sm w-100 py-3">Comenzar y Marcar Salida</button>
            </div>
        </form>
    </div>
</div>
@endif
