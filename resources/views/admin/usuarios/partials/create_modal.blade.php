<!-- Modal CREAR USUARIO -->
<div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small">
                    <span class="material-symbols-rounded me-2 fs-5 text-primary">person_add</span>
                    REGISTRAR NUEVO USUARIO
                </h6>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form method="POST" action="{{ $formAction ?? route('admin.usuarios.store') }}" id="formCrearUsuario" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    {{-- Errores de validación (Solo para campos sin validación en tiempo real) --}}
                    @if($errors->any() && old('form_type') == 'create')
                        @php
                            $realTimeFields = ['primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido'];
                            $hasOtherErrors = false;
                            foreach($errors->keys() as $key) {
                                if(!in_array($key, $realTimeFields)) {
                                    $hasOtherErrors = true;
                                    break;
                                }
                            }
                        @endphp
                        
                        @if($hasOtherErrors)
                            <div class="alert alert-danger shadow-sm py-2 small mb-4">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endif

                    <div class="row g-3">
                        <div class="col-md-12 text-input-validate" data-type="number">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Documento <span class="text-danger">*</span></label>
                            <input type="text" name="doc_usuario" class="form-control form-control-sm @error('doc_usuario') is-invalid @enderror" required minlength="6" maxlength="10" pattern="[1-9][0-9]{5,9}" placeholder="Documento..." value="{{ old('doc_usuario') }}"
                                oninvalid="if(this.validity.valueMissing){this.setCustomValidity('Este campo es obligatorio')}else if(this.validity.patternMismatch){this.setCustomValidity('El documento debe tener entre 6 y 10 dígitos y no puede iniciar en 0')}else{this.setCustomValidity('')}"
                                oninput="this.setCustomValidity('')">
                            @error('doc_usuario') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted fs-xs">Mín. 6, máx. 10 dígitos (Sin 0 inicial).</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Rol Operativo <span class="text-danger">*</span></label>
                            <select name="id_tipo_usuario" id="select_id_tipo_usuario" class="form-select form-select-sm @error('id_tipo_usuario') is-invalid @enderror" required
                                oninvalid="this.setCustomValidity('Este campo es obligatorio')"
                                oninput="this.setCustomValidity('')">
                                <option value="" selected disabled>Seleccione...</option>
                                @foreach($roles as $r)
                                    @if(!isset($restrictedRoles) || in_array($r->id_tipo_usuario, $restrictedRoles))
                                        <option value="{{ $r->id_tipo_usuario }}" {{ old('id_tipo_usuario') == $r->id_tipo_usuario ? 'selected' : '' }}>{{ $r->nombre_tipo }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('id_tipo_usuario') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Foto de Perfil</label>
                            <input type="file" name="foto_usuario" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-md-6 text-input-validate" data-type="text">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Primer Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="primer_nombre" class="form-control form-control-sm @error('primer_nombre') is-invalid @enderror" required minlength="2" maxlength="30" pattern="[a-zA-ZÁÉÍÓÚáéíóúÑñ]+(\s[a-zA-ZÁÉÍÓÚáéíóúÑñ]+)?" value="{{ old('primer_nombre') }}"
                                oninvalid="if(this.validity.valueMissing){this.setCustomValidity('Este campo es obligatorio')}else if(this.validity.patternMismatch || this.validity.tooShort){this.setCustomValidity('Solo letras, máximo dos palabras, mín 2 chars')}else{this.setCustomValidity('')}"
                                oninput="this.setCustomValidity('')">
                            <div class="invalid-feedback real-time-error">@error('primer_nombre') {{ $message }} @enderror</div>
                        </div>
                        <div class="col-md-6 text-input-validate" data-type="text">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Segundo Nombre</label>
                            <input type="text" name="segundo_nombre" class="form-control form-control-sm @error('segundo_nombre') is-invalid @enderror" minlength="2" maxlength="30" pattern="[a-zA-ZÁÉÍÓÚáéíóúÑñ]+(\s[a-zA-ZÁÉÍÓÚáéíóúÑñ]+)?" value="{{ old('segundo_nombre') }}"
                                oninvalid="if(this.validity.patternMismatch || this.validity.tooShort){this.setCustomValidity('Solo letras, máximo dos palabras, mín 2 caracteres')}else{this.setCustomValidity('')}"
                                oninput="this.setCustomValidity('')">
                            <div class="invalid-feedback real-time-error">@error('segundo_nombre') {{ $message }} @enderror</div>
                        </div>

                        <div class="col-md-6 text-input-validate" data-type="text">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Primer Apellido <span class="text-danger">*</span></label>
                            <input type="text" name="primer_apellido" class="form-control form-control-sm @error('primer_apellido') is-invalid @enderror" required minlength="2" maxlength="30" pattern="[a-zA-ZÁÉÍÓÚáéíóúÑñ]+(\s[a-zA-ZÁÉÍÓÚáéíóúÑñ]+)?" value="{{ old('primer_apellido') }}"
                                oninvalid="if(this.validity.valueMissing){this.setCustomValidity('Este campo es obligatorio')}else if(this.validity.patternMismatch || this.validity.tooShort){this.setCustomValidity('Solo letras, máximo dos palabras, mín 2 chars')}else{this.setCustomValidity('')}"
                                oninput="this.setCustomValidity('')">
                            <div class="invalid-feedback real-time-error">@error('primer_apellido') {{ $message }} @enderror</div>
                        </div>
                        <div class="col-md-6 text-input-validate" data-type="text">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Segundo Apellido <span class="text-danger">*</span></label>
                            <input type="text" name="segundo_apellido" class="form-control form-control-sm @error('segundo_apellido') is-invalid @enderror" required minlength="2" maxlength="30" pattern="[a-zA-ZÁÉÍÓÚáéíóúÑñ]+(\s[a-zA-ZÁÉÍÓÚáéíóúÑñ]+)?" value="{{ old('segundo_apellido') }}"
                                oninvalid="if(this.validity.valueMissing){this.setCustomValidity('Este campo es obligatorio')}else if(this.validity.patternMismatch || this.validity.tooShort){this.setCustomValidity('Solo letras, máximo dos palabras, mín 2 chars')}else{this.setCustomValidity('')}"
                                oninput="this.setCustomValidity('')">
                            <div class="invalid-feedback real-time-error">@error('segundo_apellido') {{ $message }} @enderror</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Correo <span class="text-danger">*</span></label>
                            <input type="email" name="correo" class="form-control form-control-sm @error('correo') is-invalid @enderror" required placeholder="usuario@sigu.com" value="{{ old('correo') }}"
                                oninvalid="if(this.validity.valueMissing){this.setCustomValidity('Este campo es obligatorio')}else{this.setCustomValidity('Ingrese un correo válido')}"
                                oninput="this.setCustomValidity('')">
                            @error('correo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 text-input-validate" data-type="number">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Teléfono <span class="text-danger">*</span></label>
                            <input type="text" name="telefono" class="form-control form-control-sm @error('telefono') is-invalid @enderror" required minlength="10" maxlength="10" pattern="[0-9]{10}" value="{{ old('telefono') }}"
                                oninvalid="if(this.validity.valueMissing){this.setCustomValidity('Este campo es obligatorio')}else{this.setCustomValidity('El teléfono debe tener 10 dígitos')}"
                                oninput="this.setCustomValidity('')">
                            @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12" id="wrapper_password_crear" style="display: none;">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Contraseña de Acceso <span class="text-info" id="msg_pass_obligatorio">(Opcional para otros roles)</span></label>
                            <div class="input-group input-group-sm">
                                <input type="password" name="password" id="pass_crear" class="form-control @error('password') is-invalid @enderror" placeholder="Mínimo 8 caracteres">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('pass_crear')">
                                    <span class="material-symbols-rounded fs-6 align-middle">visibility</span>
                                </button>
                            </div>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted fs-xs">Si se deja vacío, se generará una contraseña aleatoria.</small>
                        </div>

                        <!-- SECCIÓN EXCLUSIVA PARA CONDUCTORES -->
                        <div class="col-12" id="wrapper_licencia_crear" style="display: none;">
                            <hr class="my-3 border-light">
                            <h6 class="text-primary small fw-bold mb-3 d-flex align-items-center">
                                <span class="material-symbols-rounded fs-5 me-1">id_card</span>
                                INFORMACIÓN DE LICENCIA (SOLO CONDUCTORES)
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Fecha de Nacimiento <span class="text-danger">*</span></label>
                                    <input type="date" name="fecha_nacimiento" id="fecha_nac_crear" class="form-control form-control-sm @error('fecha_nacimiento') is-invalid @enderror" value="{{ old('fecha_nacimiento') }}" max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                                    @error('fecha_nacimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Fecha de Expedición <span class="text-danger">*</span></label>
                                    <input type="date" name="fecha_expedicion" id="fecha_exp_crear" class="form-control form-control-sm @error('fecha_expedicion') is-invalid @enderror" value="{{ old('fecha_expedicion') }}" max="{{ date('Y-m-d') }}">
                                    @error('fecha_expedicion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Fecha de Vencimiento <span class="text-danger">*</span></label>
                                    <input type="date" name="fecha_vencimiento" id="fecha_venc_crear" class="form-control form-control-sm bg-light @error('fecha_vencimiento') is-invalid @enderror" value="{{ old('fecha_vencimiento') }}" readonly placeholder="Calculado automáticamente">
                                    @error('fecha_vencimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted fs-xs">Automático según ley (<60: 3 años, ≥60: 1 año)</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">Archivo Licencia (PDF/PNG) <span class="text-danger">*</span></label>
                                    <input type="file" name="archivo_licencia" id="archivo_lic_crear" class="form-control form-control-sm @error('archivo_licencia') is-invalid @enderror" accept=".pdf,.png">
                                    @error('archivo_licencia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-sm btn-light px-3 fw-bold" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm">GUARDAR USUARIO</button>
                </div>
            </form>
        </div>
    </div>
</div>
