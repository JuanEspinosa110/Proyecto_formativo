<!-- Modal Asignar Conductor (Admin-Style para Auxiliar) -->
<div class="modal fade" id="modalAsignarConductor" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-light border-0 py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small">
                    <span class="material-symbols-rounded text-primary me-2 fs-5">event_available</span>
                    NUEVA ASIGNACIÓN
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCreateAsignacion" action="{{ route('empresa.asignaciones.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- 1. Ruta -->
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Ruta <span
                                    class="text-danger">*</span></label>
                            <select name="id_ruta"
                                class="form-select form-select-sm rounded-3 @error('id_ruta') is-invalid @enderror"
                                required>
                                <option value="" selected disabled>Seleccionar ruta...</option>
                                @foreach($rutas as $r)
                                    <option value="{{ $r->id_ruta }}" {{ old('id_ruta') == $r->id_ruta ? 'selected' : '' }}>
                                        {{ $r->nombre_ruta }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_ruta') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <!-- 2. Tiempo -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Hora Inicio <span
                                    class="text-danger">*</span></label>
                            <input type="datetime-local" name="fecha" id="create_fecha"
                                class="form-control form-control-sm rounded-3 @error('fecha') is-invalid @enderror"
                                value="{{ old('fecha') }}" required min="{{ now()->format('Y-m-d\TH:i') }}">
                            @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Hora Fin
                                (Estimada)</label>
                            <input type="text" id="create_hora_fin"
                                class="form-control form-control-sm bg-light rounded-3" readonly
                                placeholder="Calculado +8h">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1 d-block mb-1">Franjas
                                Rápidas (Turnos 8h)</label>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <button type="button"
                                    class="btn btn-xs btn-outline-primary py-1 px-3 small quick-time rounded-pill"
                                    data-time="04:30">04:30 - ...</button>
                                <button type="button"
                                    class="btn btn-xs btn-outline-primary py-1 px-3 small quick-time rounded-pill"
                                    data-time="12:30">12:30 - ...</button>
                            </div>
                        </div>

                        <!-- 3. Vehículo (Placa) -->
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Vehículo (Disponible)
                                <span class="text-danger">*</span></label>
                            <select name="placa" id="create_placa"
                                class="form-select form-select-sm rounded-3 @error('placa') is-invalid @enderror"
                                required disabled title="Seleccione fecha primero">
                                <option value="" selected disabled>Seleccionar primero la fecha...</option>
                            </select>
                            @error('placa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- 4. Conductor -->
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Conductor
                                (Disponible) <span class="text-danger">*</span></label>
                            <select name="doc_us" id="create_doc_us"
                                class="form-select form-select-sm rounded-3 @error('doc_us') is-invalid @enderror"
                                required disabled title="Seleccione fecha primero">
                                <option value="" selected disabled>Seleccionar primero la fecha...</option>
                            </select>
                            @error('doc_us') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- 5. Estado -->
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Estado de la
                                Asignación <span class="text-danger">*</span></label>
                            <select name="id_estado" class="form-select form-select-sm rounded-3" required>
                                @foreach($estados as $est)
                                    <option value="{{ $est->id_estado }}" {{ old('id_estado', 1) == $est->id_estado ? 'selected' : '' }}>{{ $est->nombre_estado }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-3 gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                        data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">GUARDAR
                        ASIGNACIÓN</button>
                </div>
            </form>
        </div>
    </div>
</div>