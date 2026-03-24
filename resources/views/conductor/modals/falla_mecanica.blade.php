<!-- MODAL REPORTE FALLA -->
<div class="modal fade" id="fallaModal" tabindex="-1" aria-labelledby="fallaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow rounded-4" action="{{ route('conductor.reportarFalla') }}" method="POST">
            @csrf
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="fallaModalLabel">
                    <span class="material-symbols-rounded text-warning">warning</span> Reportar Falla Mecánica
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <p class="text-muted small mb-4">Evidencie todo problema mecánico u operativo a los líderes. Esto permitirá asignar mantenimientos rápidamente.</p>
                
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small text-uppercase">Vehículo Implicado</label>
                    @if($asignacionActiva)
                        <input type="text" name="placa" class="form-control bg-light rounded-3 font-monospace fw-bold" value="{{ $asignacionActiva->placa }}" readonly required>
                    @else
                        <!-- Listamos todos los buses del sistema unicos de asignaciones para permitir el input -->
                        <select name="placa" class="form-select rounded-3" required>
                            <option value="" disabled selected>Seleccione placa del vehículo...</option>
                            @foreach($asignaciones->unique('placa') as $asig)
                                <option value="{{ $asig->placa }}">{{ $asig->placa }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small text-uppercase">Nivel de Urgencia</label>
                    <select name="nivel_urgencia" class="form-select rounded-3" required>
                        @foreach($nivelesUrgencia as $nivel)
                            <option value="{{ $nivel }}" {{ $nivel == 'Bajo' ? 'selected' : '' }}>{{ $nivel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small text-uppercase">Descripción Detallada y Contexto</label>
                    <textarea name="descripcion" class="form-control rounded-3 bg-light border-0 py-3 px-3" rows="4" placeholder="Explique la situación experimentada con detalle, qué sonido hace, la frecuencia, afectaciones al manejo, etc." required></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 pt-2">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Restablecer</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark shadow-sm">Registrar Envío</button>
            </div>
        </form>
    </div>
</div>
