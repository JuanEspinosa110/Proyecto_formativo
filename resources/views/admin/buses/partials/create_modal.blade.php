<!-- Modal CREAR -->
<div class="modal fade" id="modalCreateBus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small">
                    <span class="material-symbols-rounded text-primary me-2 fs-5">add_circle</span>
                    REGISTRAR NUEVO VEHÍCULO
                </h6>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formCreateBus" action="{{ $formAction ?? route('admin.buses.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    {{-- Errores de validación --}}
                    @if($errors->any() && !old('_method'))
                        <div class="alert alert-danger shadow-sm py-2 small mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Placa <span class="text-danger">*</span></label>
                            <input type="text" name="placa" class="form-control form-control-sm fw-bold" placeholder="ABC123" required style="text-transform:uppercase" maxlength="6">
                            <div class="real-time-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Modelo / Ref. <span class="text-danger">*</span></label>
                            <input type="text" name="modelo" class="form-control form-control-sm" placeholder="Ej: Toyota 2019" required>
                            <div class="real-time-error"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Pasajeros <span class="text-danger">*</span></label>
                            <input type="text" name="capacidad_pasajeros" class="form-control form-control-sm" required placeholder="00">
                            <div class="real-time-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Kilometraje <span class="text-danger">*</span></label>
                            <input type="text" name="kilometraje" class="form-control form-control-sm" required placeholder="0">
                            <div class="real-time-error"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Licencia Tránsito <span class="text-danger">*</span></label>
                            <input type="text" name="linc_transito" class="form-control form-control-sm" required maxlength="12" placeholder="8 dígitos">
                            <div class="real-time-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Doc. Propietario <span class="text-danger">*</span></label>
                            <input type="text" name="doc_propietario" class="form-control form-control-sm" required maxlength="15" placeholder="Máx. 10 dígitos">
                            <div class="real-time-error"></div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Nombre Propietario <span class="text-danger">*</span></label>
                            <input type="text" name="nombre_propietario" class="form-control form-control-sm" placeholder="Nombre completo" required>
                            <div class="real-time-error"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Teléfono <span class="text-danger">*</span></label>
                            <input type="text" name="telefono" class="form-control form-control-sm" required maxlength="10" placeholder="312...">
                            <div class="real-time-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Correo <span class="text-danger">*</span></label>
                            <input type="email" name="correo" class="form-control form-control-sm" placeholder="ejemplo@correo.com" required>
                            <div class="real-time-error"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Serial Chasis <span class="text-danger">*</span></label>
                            <input type="text" name="numero_chasis" class="form-control form-control-sm" required maxlength="17" placeholder="17 dígitos">
                            <small class="text-muted fs-xs">Debe contener exactamente 17 dígitos numéricos.</small>
                            <div class="real-time-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Serial Motor <span class="text-danger">*</span></label>
                            <input type="text" name="numero_motor" class="form-control form-control-sm" required maxlength="17" placeholder="8-17 dígitos">
                            <small class="text-muted fs-xs">Debe contener entre 8 y 17 dígitos numéricos según el fabricante.</small>
                            <div class="real-time-error"></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Estado Operativo <span class="text-danger">*</span></label>
                            <select name="id_estado" class="form-select form-select-sm" required>
                                <option value="" selected disabled>Seleccionar...</option>
                                @foreach($estados as $est)
                                <option value="{{ $est->id_estado }}">{{ $est->nombre_estado }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-sm btn-light px-3 fw-bold" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm">GUARDAR BUS</button>
                </div>
            </form>
        </div>
    </div>
</div>
