<!-- Modal CREAR -->
<div class="modal fade @if($errors->any() && old('form_type') == 'create') show @endif" id="modalCreateAsignacion" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small">
                    <span class="material-symbols-rounded text-primary me-2 fs-5">add_circle</span>
                    NUEVA ASIGNACIÓN
                </h6>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formCreateAsignacion" action="{{ $formAction ?? route('admin.asignaciones.store') }}" method="POST">
                @csrf
                <input type="hidden" name="form_type" value="create">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- 1. Ruta -->
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Ruta <span class="text-danger">*</span></label>
                            <select name="id_ruta" class="form-select form-select-sm @error('id_ruta') is-invalid @enderror" required>
                                <option value="" selected disabled>Seleccionar...</option>
                                @foreach($rutas as $ruta)
                                <option value="{{ $ruta->id_ruta }}" @if(old('id_ruta') == $ruta->id_ruta) selected @endif>
                                    #{{ $ruta->codigo_ruta }} - {{ $ruta->nombre_ruta ?? 'Ruta' }}
                                    @if($ruta->concesiones->isEmpty()) (Uso Público) @endif
                                </option>
                                @endforeach
                            </select>
                            @error('id_ruta') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <!-- 2. Tiempo -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Hora Inicio <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="fecha" id="create_fecha" class="form-control form-control-sm @error('fecha') is-invalid @enderror" value="{{ old('fecha') }}" required>
                            @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Hora Fin (Estimada)</label>
                            <input type="text" id="create_hora_fin" class="form-control form-control-sm bg-light" readonly placeholder="Calculado +8h">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1 d-block mb-2">Franjas Rápidas (Turnos 8h)</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-xs btn-outline-primary py-1 px-3 small quick-time" data-time="04:30">04:30 - 12:30</button>
                                <button type="button" class="btn btn-xs btn-outline-primary py-1 px-3 small quick-time" data-time="12:30">12:30 - 20:30</button>
                            </div>
                        </div>

                        <!-- 3. Vehículo (Placa) -->
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Vehículo (Placa) <span class="text-danger">*</span></label>
                            <select name="placa" id="create_placa" class="form-select form-select-sm @error('placa') is-invalid @enderror" required disabled title="Fije fecha primero">
                                <option value="" selected disabled>Seleccionar...</option>
                                {{-- Se llena vía AJAX --}}
                            </select>
                            @error('placa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- 4. Conductor -->
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Conductor <span class="text-danger">*</span></label>
                            <select name="doc_us" id="create_doc_us" class="form-select form-select-sm @error('doc_us') is-invalid @enderror" required disabled title="Fije fecha primero">
                                <option value="" selected disabled>Seleccionar...</option>
                                {{-- Se llena vía AJAX --}}
                            </select>
                            @error('doc_us') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- 5. Estado -->
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Estado <span class="text-danger">*</span></label>
                            <select name="id_estado" class="form-select form-select-sm @error('id_estado') is-invalid @enderror" required>
                                @foreach($estados as $est)
                                <option value="{{ $est->id_estado }}" @if(old('id_estado', 1) == $est->id_estado) selected @endif>{{ $est->nombre_estado }}</option>
                                @endforeach
                            </select>
                            @error('id_estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-sm btn-light px-3 fw-bold" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm">GUARDAR ASIGNACIÓN</button>
                </div>
            </form>
        </div>
    </div>
</div>
