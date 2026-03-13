@extends('admin.layouts.app')

@section('title', 'Crear Documento - SIGU')

@section('content')
<div class="sa-content-header">
    <div class="sa-content-title">
        <a href="{{ route('admin.documentos.index') }}" class="back-link">
            <span class="material-symbols-rounded">arrow_back</span>
        </a>
        <div>
            <h1>Crear Nuevo Documento</h1>
            <p>Empresa: {{ $empresa->nombre_empresa }}</p>
        </div>
    </div>
</div>

@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <span class="material-symbols-rounded">error</span>
    <div>
        <strong> Errores encontrados:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="form-container">
    <form action="{{ route('admin.documentos.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate id="formDocumento">
        @csrf

        <div class="form-section">
            <h2><span class="material-symbols-rounded">info</span> Información General</h2>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nombre" class="form-label">
                            <span class="text-danger">*</span> Nombre del Documento
                        </label>
                        <input type="text" name="nombre" id="nombre"
                            class="form-control @error('nombre') is-invalid @enderror"
                            placeholder="Ej: SOAT 2024 - Bus ABZ123"
                            value="{{ old('nombre') }}"
                            required
                            maxlength="150"
                            data-required="true">
                        <small class="form-text text-muted">Mínimo 3 caracteres, máximo 150</small>
                        @error('nombre')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="id_tipo_documento" class="form-label">
                            <span class="text-danger">*</span> Tipo de Documento
                        </label>
                        <select name="id_tipo_documento" id="id_tipo_documento"
                            class="form-select @error('id_tipo_documento') is-invalid @enderror"
                            required
                            data-required="true">
                            <option value="">-- Selecciona un tipo --</option>
                            @foreach ($tiposDocumento as $tipo)
                            <option value="{{ $tipo->id_tipo_documento }}"
                                data-requiere-doc="{{ $tipo->requiere_doc_usuario ? 'true' : 'false' }}"
                                data-requiere-placa="{{ $tipo->requiere_placa ? 'true' : 'false' }}"
                                {{ old('id_tipo_documento') == $tipo->id_tipo_documento ? 'selected' : '' }}>
                                {{ $tipo->nombre }}
                                @if($tipo->descripcion)
                                ({{ $tipo->descripcion }})
                                @endif
                            </option>
                            @endforeach
                        </select>
                        @error('id_tipo_documento')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fecha_expedicion" class="form-label">
                            <span class="text-danger">*</span> Fecha de Expedición
                        </label>
                        <input type="date" name="fecha_expedicion" id="fecha_expedicion"
                            class="form-control @error('fecha_expedicion') is-invalid @enderror"
                            value="{{ old('fecha_expedicion') }}"
                            required
                            data-required="true">
                        <small class="form-text text-muted">No puede ser en el futuro</small>
                        @error('fecha_expedicion')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fecha_vencimiento" class="form-label">
                            <span class="text-danger">*</span> Fecha de Vencimiento
                        </label>
                        <input type="date" name="fecha_vencimiento" id="fecha_vencimiento"
                            class="form-control @error('fecha_vencimiento') is-invalid @enderror"
                            value="{{ old('fecha_vencimiento') }}"
                            required
                            data-required="true">
                        <small class="form-text text-muted">Mínimo 30 días después de expedición, máximo 10 años</small>
                        @error('fecha_vencimiento')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="id_estado" class="form-label">
                            <span class="text-danger">*</span> Estado
                        </label>
                        <select name="id_estado" id="id_estado"
                            class="form-select @error('id_estado') is-invalid @enderror"
                            required
                            data-required="true">
                            <option value="">-- Selecciona un estado --</option>
                            @foreach ($estados as $estado)
                            <option value="{{ $estado->id_estado }}"
                                {{ old('id_estado') == $estado->id_estado ? 'selected' : '' }}>
                                {{ $estado->nombre_estado }}
                            </option>
                            @endforeach
                        </select>
                        @error('id_estado')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="archivo" class="form-label">
                            <span class="text-danger">*</span> Archivo
                        </label>
                        <input type="file" name="archivo" id="archivo"
                            class="form-control @error('archivo') is-invalid @enderror"
                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                            required
                            data-required="true">
                        <small class="form-text text-muted">
                             Formatos: PDF, JPG, PNG, DOC, DOCX | Máximo: 5MB
                        </small>
                        @error('archivo')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2><span class="material-symbols-rounded">link</span> Información Asociada</h2>

            <!--  CAMPO CONDICIONAL: Documento Usuario -->
            <div class="row" id="fila-asociados" style="display: flex; flex-wrap: wrap;">
                <div class="col-md-6" id="col-doc-usuario" style="display: none;">
                    <div class="form-group">
                        <label for="doc_usuario" class="form-label">
                            <span class="text-danger" id="asterisco-doc-usuario">*</span> Documento Usuario
                        </label>
                        <input type="text" name="doc_usuario" id="doc_usuario"
                            class="form-control @error('doc_usuario') is-invalid @enderror"
                            placeholder="Ej: 1098765567"
                            value="{{ old('doc_usuario') }}"
                            inputmode="numeric"
                            data-conditional="true"
                            data-tipo="doc_usuario">
                        <small class="form-text text-muted">Entre 6 y 15 dígitos, solo números</small>
                        <small class="form-text text-info" id="info-doc-usuario" style="display: none;">
                            Campo requerido para este tipo de documento
                        </small>
                        @error('doc_usuario')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6" id="col-fecha-nacimiento" style="display: none;">
                    <div class="form-group">
                        <label for="fecha_nacimiento" class="form-label">
                            <span class="text-danger">*</span> Fecha de Nacimiento del Usuario
                        </label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                            class="form-control"
                            placeholder="YYYY-MM-DD">
                        <small class="form-text text-muted">Obligatorio para Licencia. Usado para calcular vencimiento.</small>
                    </div>
                </div>
                <div class="col-md-6" id="col-placa" style="display: none;">
                    <div class="form-group">
                        <label for="placa" class="form-label">
                            <span class="text-danger" id="asterisco-placa">*</span> Placa del Bus
                        </label>
                        <input type="text" name="placa" id="placa"
                            class="form-control @error('placa') is-invalid @enderror"
                            placeholder="Ej: ABZ123 o ABZ-123"
                            value="{{ old('placa') }}"
                            maxlength="7"
                            data-conditional="true"
                            data-tipo="placa">
                        <small class="form-text text-muted">Formato: XXX000 o XXX-000 (mayúsculas automáticas)</small>
                        <small class="form-text text-info" id="info-placa" style="display: none;">
                            Campo requerido para este tipo de documento
                        </small>
                        @error('placa')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- CAMPO CONDICIONAL: Fecha de Nacimiento (solo para Licencia) -->
            <div class="row" id="fila-fecha-nacimiento" style="display: none;">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fecha_nacimiento" class="form-label">
                            <span class="text-danger">*</span> Fecha de Nacimiento del Usuario
                        </label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                            class="form-control"
                            placeholder="YYYY-MM-DD">
                        <small class="form-text text-muted">Obligatorio para Licencia. Usado para calcular vencimiento.</small>
                    </div>
                </div>
            </div>

            <!--  CAMPO CONDICIONAL: Placa del Bus -->
            <div class="row" id="fila-placa" style="display: none;">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="placa" class="form-label">
                            <span class="text-danger" id="asterisco-placa">*</span> Placa del Bus
                        </label>
                        <input type="text" name="placa" id="placa"
                            class="form-control @error('placa') is-invalid @enderror"
                            placeholder="Ej: ABZ123 o ABZ-123"
                            value="{{ old('placa') }}"
                            maxlength="7"
                            data-conditional="true"
                            data-tipo="placa">
                        <small class="form-text text-muted">Formato: XXX000 o XXX-000 (mayúsculas automáticas)</small>
                        <small class="form-text text-info" id="info-placa" style="display: none;">
                            Campo requerido para este tipo de documento
                        </small>
                        @error('placa')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Mensaje si no requiere campos adicionales -->
            <div id="sin-campos-adicionales" class="alert alert-info" style="display: none;">
                <span class="material-symbols-rounded">info</span>
                Este tipo de documento no requiere información adicional
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.documentos.index') }}" class="sigu-btn sigu-btn-ghost">
                <span class="material-symbols-rounded">close</span> Cancelar
            </a>
            <button type="submit" class="sigu-btn sigu-btn-primary" id="btn-submit">
                <span class="material-symbols-rounded">save</span> Guardar Documento
            </button>
        </div>
    </form>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/validaciones.css') }}">
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formDocumento');
        const btnSubmit = document.getElementById('btn-submit');
        const tipoDocumentoSelect = document.getElementById('id_tipo_documento');
        const docUsuarioInput = document.getElementById('doc_usuario');
        const placaInput = document.getElementById('placa');
        const filaAsociados = document.getElementById('fila-asociados');
        const colDocUsuario = document.getElementById('col-doc-usuario');
        const colFechaNacimiento = document.getElementById('col-fecha-nacimiento');
        const colPlaca = document.getElementById('col-placa');
        const sinCamposAdicionales = document.getElementById('sin-campos-adicionales');
        const fechaNacimientoInput = document.getElementById('fecha_nacimiento');
        const fechaExpedicionInput = document.getElementById('fecha_expedicion');
        const fechaVencimientoInput = document.getElementById('fecha_vencimiento');

        function actualizarCamposCondicionales() {
            const selectedOption = tipoDocumentoSelect.options[tipoDocumentoSelect.selectedIndex];
            const requiereDoc = selectedOption.getAttribute('data-requiere-doc') === 'true';
            const requierePlaca = selectedOption.getAttribute('data-requiere-placa') === 'true';
            // Detectar si es SOAT o Licencia (por nombre, puedes ajustar si tienes otro identificador)
            const nombreTipo = selectedOption.textContent.toLowerCase();
            const esSOAT = nombreTipo.includes('soat');
            const esLicencia = nombreTipo.includes('licencia');

            // Mostrar/ocultar y marcar como requerido: Documento Usuario

            // Mostrar/ocultar columnas de campos condicionales
            if (requiereDoc) {
                colDocUsuario.style.display = 'block';
                docUsuarioInput.setAttribute('required', 'required');
                docUsuarioInput.classList.add('required-field');
                document.getElementById('info-doc-usuario').style.display = 'block';
            } else {
                colDocUsuario.style.display = 'none';
                docUsuarioInput.removeAttribute('required');
                docUsuarioInput.classList.remove('required-field');
                document.getElementById('info-doc-usuario').style.display = 'none';
            }

            if (requierePlaca) {
                colPlaca.style.display = 'block';
                placaInput.setAttribute('required', 'required');
                placaInput.classList.add('required-field');
                document.getElementById('info-placa').style.display = 'block';
            } else {
                colPlaca.style.display = 'none';
                placaInput.removeAttribute('required');
                placaInput.classList.remove('required-field');
                document.getElementById('info-placa').style.display = 'none';
            }

            if (esLicencia) {
                colFechaNacimiento.style.display = 'block';
                fechaNacimientoInput.setAttribute('required', 'required');
            } else {
                colFechaNacimiento.style.display = 'none';
                fechaNacimientoInput.removeAttribute('required');
                fechaNacimientoInput.value = '';
            }

            // Mostrar mensaje si no requiere campos adicionales
            if (!requiereDoc && !requierePlaca) {
                sinCamposAdicionales.style.display = 'flex';
            } else {
                sinCamposAdicionales.style.display = 'none';
            }

            // Lógica automática de vencimiento para SOAT y Licencia
            if (esSOAT && fechaExpedicionInput.value) {
                // SOAT: vencimiento = expedición + 1 año
                const exp = new Date(fechaExpedicionInput.value);
                if (!isNaN(exp)) {
                    const venc = new Date(exp);
                    venc.setFullYear(venc.getFullYear() + 1);
                    fechaVencimientoInput.value = venc.toISOString().slice(0, 10);
                    fechaVencimientoInput.readOnly = true;
                }
            } else if (esLicencia && fechaExpedicionInput.value && fechaNacimientoInput.value) {
                // Licencia: vencimiento depende de edad
                const exp = new Date(fechaExpedicionInput.value);
                const nac = new Date(fechaNacimientoInput.value);
                if (!isNaN(exp) && !isNaN(nac)) {
                    const edad = exp.getFullYear() - nac.getFullYear() - (exp < new Date(exp.getFullYear(), nac.getMonth(), nac.getDate()) ? 1 : 0);
                    let añosVigencia = 10;
                    if (edad < 60) añosVigencia = 10;
                    else if (edad < 80) añosVigencia = 5;
                    else añosVigencia = 1;
                    const venc = new Date(exp);
                    venc.setFullYear(venc.getFullYear() + añosVigencia);
                    fechaVencimientoInput.value = venc.toISOString().slice(0, 10);
                    fechaVencimientoInput.readOnly = true;
                }
            } else {
                fechaVencimientoInput.readOnly = false;
            }
        }

        // Ejecutar al cambiar tipo de documento
        tipoDocumentoSelect.addEventListener('change', actualizarCamposCondicionales);
        // Ejecutar al cambiar fecha de expedición o nacimiento (para SOAT/Licencia)
        if (fechaExpedicionInput) fechaExpedicionInput.addEventListener('change', actualizarCamposCondicionales);
        if (fechaNacimientoInput) fechaNacimientoInput.addEventListener('change', actualizarCamposCondicionales);

        // Ejecutar al cargar la página (para mostrar campos si hay valor anterior)
        actualizarCamposCondicionales();

        // Prevenir envío duplicado
        form.addEventListener('submit', function(e) {
            btnSubmit.disabled = true;
            btnSubmit.classList.add('btn-loading');
            btnSubmit.innerHTML = '<span class="material-symbols-rounded">schedule</span> Guardando...';
        });

        // Auto-mayúsculas en placa
        placaInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Solo números en doc_usuario
        docUsuarioInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
</script>
@endpush

@endsection
