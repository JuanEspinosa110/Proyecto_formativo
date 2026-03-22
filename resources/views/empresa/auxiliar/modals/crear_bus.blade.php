<!-- Modal Crear Bus (Auxiliar) -->
<div class="modal fade" id="modalCrearBus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-light border-0 py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded text-primary">directions_bus</span>
                    Registrar Nuevo Vehículo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('empresa.buses.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Placa</label>
                            <input type="text" name="placa" class="form-control rounded-3" required placeholder="Ej: ABC123" maxlength="6" style="text-transform: uppercase;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Modelo / Año</label>
                            <input type="text" name="modelo" class="form-control rounded-3" required placeholder="Ej: 2024" maxlength="4">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Capacidad Pasajeros</label>
                            <input type="number" name="capacidad_pasajeros" class="form-control rounded-3" required min="1" placeholder="Ej: 32">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Tarjeta Operación / Licencia</label>
                            <input type="text" name="linc_transito" class="form-control rounded-3" required placeholder="Número de licencia">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Propietario</label>
                            <select name="doc_propietario" class="form-select rounded-3" required>
                                <option value="" disabled selected>Seleccione un propietario...</option>
                                @if(isset($propietarios))
                                    @foreach($propietarios as $p)
                                        <option value="{{ $p->doc_usuario }}">{{ $p->primer_nombre }} {{ $p->primer_apellido }} ({{ $p->doc_usuario }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-3 gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Guardar Vehículo</button>
                </div>
            </form>
        </div>
    </div>
</div>
