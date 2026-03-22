<!-- Modal Asignar Conductor (Auxiliar) -->
<div class="modal fade" id="modalAsignarConductor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-light border-0 py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded text-primary">event_available</span>
                    Crear Nueva Asignación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('empresa.asignaciones.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Bus (Placa)</label>
                            <select name="placa" class="form-select rounded-3" required>
                                <option value="" disabled selected>Seleccione un vehículo operable...</option>
                                @if(isset($busesDisponibles))
                                    @foreach($busesDisponibles as $b)
                                        <option value="{{ $b->placa }}">{{ $b->placa }} ({{ $b->modelo }})</option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="text-muted d-block mt-1">Solo se muestran buses con todos sus documentos vigentes.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Ruta</label>
                            <select name="id_ruta" class="form-select rounded-3" required>
                                <option value="" disabled selected>Seleccione una ruta...</option>
                                @if(isset($rutas))
                                    @foreach($rutas as $r)
                                        <option value="{{ $r->id_ruta }}">{{ $r->nombre_ruta }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Conductor</label>
                            <select name="doc_us" class="form-select rounded-3" required>
                                <option value="" disabled selected>Seleccione un conductor...</option>
                                @if(isset($conductores))
                                    @foreach($conductores as $c)
                                        <option value="{{ $c->doc_usuario }}">{{ $c->primer_nombre }} {{ $c->primer_apellido }} ({{ $c->doc_usuario }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Fecha y Hora de Inicio</label>
                            <input type="datetime-local" name="fecha" class="form-control rounded-3" required min="{{ now()->format('Y-m-d\TH:i') }}">
                            <small class="text-muted d-block mt-1">La jornada será de 8 horas a partir del inicio.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-3 gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Confirmar Asignación</button>
                </div>
            </form>
        </div>
    </div>
</div>
