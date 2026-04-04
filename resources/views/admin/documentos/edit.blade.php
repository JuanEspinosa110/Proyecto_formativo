@extends(auth()->user()->id_tipo_usuario == 1 ? 'admin.layouts.app' : 'empresa.layouts.app')

@section('title', 'Editar Documento - SIGU')

@section('content')
<div class="sa-content-header">
    <div class="sa-content-title">
        <a href="{{ auth()->user()->id_tipo_usuario == 1 ? route('admin.documentos.index') : route('empresa.dashboard', ['tab' => 'documentacion']) }}" class="back-link">
            <span class="material-symbols-rounded">arrow_back</span>
        </a>
        <div>
            <h1>Editar Documento</h1>
            <p>Empresa: {{ $empresa->nombre_empresa }}</p>
        </div>
    </div>
</div>

@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <span class="material-symbols-rounded">error</span>
    <div>
        <strong>Errores en el formulario:</strong>
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
    <form action="{{ route('admin.documentos.update', $documento->id_documento) }}" method="POST" enctype="multipart/form-data" class="needs-validation" id="formDocumento" novalidate>
        @csrf
        @method('PUT')

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
                            placeholder="Ej: SOAT 2024"
                            value="{{ old('nombre', $documento->nombre) }}"
                            maxlength="150"
                            required>
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
                            required>
                            <option value="">Selecciona un tipo</option>
                            @foreach ($tiposDocumento as $tipo)
                            <option value="{{ $tipo->id_tipo_documento }}"
                                data-requiere-doc="{{ $tipo->requiere_doc_usuario ? 'true' : 'false' }}"
                                data-requiere-placa="{{ $tipo->requiere_placa ? 'true' : 'false' }}"
                                {{ old('id_tipo_documento', $documento->id_tipo_documento) == $tipo->id_tipo_documento ? 'selected' : '' }}>
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
                            value="{{ old('fecha_expedicion', $documento->fecha_expedicion->format('Y-m-d')) }}"
                            required>
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
                            value="{{ old('fecha_vencimiento', $documento->fecha_vencimiento->format('Y-m-d')) }}"
                            required>
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
                            required>
                            <option value="">Selecciona un estado</option>
                            @foreach ($estados as $estado)
                            <option value="{{ $estado->id_estado }}"
                                {{ old('id_estado', $documento->id_estado) == $estado->id_estado ? 'selected' : '' }}>
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
                            <small>(Dejar en blanco para mantener el actual)</small>
                        </label>
                        <input type="file" name="archivo" id="archivo"
                            class="form-control @error('archivo') is-invalid @enderror"
                            accept=".pdf,.jpg,.jpeg,.png">
                        <small class="form-text text-muted">
                             Formatos: PDF, JPG, PNG | Máximo: 2MB
                        </small>
                        @if($documento->archivo)
                        <div class="file-info mt-2">
                            <span class="material-symbols-rounded">description</span>
                            <div>
                                <small class="d-block text-muted">Archivo actual:</small>
                                <small>{{ basename($documento->archivo) }}</small>
                            </div>
                        </div>
                        @endif
                        @error('archivo')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2><span class="material-symbols-rounded">link</span> Información Asociada</h2>

            <!-- CAMPO CONDICIONAL: Documento Usuario -->
            <div class="row" id="fila-doc-usuario" style="display: none;">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="doc_usuario" class="form-label">
                            <span class="text-danger" id="asterisco-doc-usuario">*</span> Documento Usuario
                        </label>
                        <input type="text" name="doc_usuario" id="doc_usuario"
                            class="form-control @error('doc_usuario') is-invalid @enderror"
                            placeholder="Ej: 1098765567"
                            value="{{ old('doc_usuario', $documento->doc_usuario) }}"
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
            </div>

            <!-- CAMPO CONDICIONAL: Placa del Bus -->
            <div class="row" id="fila-placa" style="display: none;">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="placa" class="form-label">
                            <span class="text-danger" id="asterisco-placa">*</span> Placa del Bus
                        </label>
                        <input type="text" name="placa" id="placa"
                            class="form-control @error('placa') is-invalid @enderror"
                            placeholder="Ej: ABZ123 o ABZ-123"
                            value="{{ old('placa', $documento->placa) }}"
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

        <!-- Información de Auditoría -->
        <div class="form-section audit-info">
            <h3>Información de Auditoría</h3>
            <div class="row">
                <div class="col-md-6">
                    <small>
                        <strong>Creado:</strong> {{ $documento->created_at->format('d/m/Y H:i:s') }}
                    </small>
                </div>
                <div class="col-md-6">
                    <small>
                        <strong>Última actualización:</strong> {{ $documento->updated_at->format('d/m/Y H:i:s') }}
                    </small>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ auth()->user()->id_tipo_usuario == 1 ? route('admin.documentos.index') : route('empresa.dashboard', ['tab' => 'documentacion']) }}" class="sigu-btn sigu-btn-ghost">
                <span class="material-symbols-rounded">close</span> Cancelar
            </a>
            <button type="submit" class="sigu-btn sigu-btn-primary" id="btn-submit">
                <span class="material-symbols-rounded">save</span> Guardar Cambios
            </button>
        </div>
    </form>
</div>

@push('styles')
<style>
    .text-danger {
        color: #dc2626;
        font-weight: 600;
    }

    .text-info {
        color: #3b82f6 !important;
        font-weight: 500;
    }

    .alert-info {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.3);
        color: #1e3a8a;
        border-radius: 8px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .alert-info .material-symbols-rounded {
        font-size: 20px;
    }

    .sa-content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .sa-content-title {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 8px;
        background: var(--card);
        color: var(--text);
        text-decoration: none;
        transition: background 0.12s ease;
        cursor: pointer;
    }

    .back-link:hover {
        background: var(--p-light);
        color: var(--p);
    }

    .sa-content-title h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
    }

    .sa-content-title p {
        margin: 0.5rem 0 0 0;
        opacity: 0.8;
    }

    .form-container {
        background: var(--card);
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 6px 18px rgba(31, 36, 48, 0.04);
    }

    .form-section {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid rgba(31, 36, 48, 0.08);
    }

    .form-section:last-of-type {
        border-bottom: none;
    }

    .form-section.audit-info {
        background: var(--bg);
        padding: 1rem;
        border-radius: 8px;
        border: none;
        margin-bottom: 1.5rem;
    }

    .form-section h2 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: var(--p);
    }

    .form-section h2 .material-symbols-rounded {
        font-size: 1.3rem;
    }

    .form-section h3 {
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--muted);
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control,
    .form-select {
        border: 1px solid rgba(31, 36, 48, 0.12);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        background: var(--bg);
        color: var(--text);
        transition: border-color 0.12s ease, box-shadow 0.12s ease;
        font-size: 1rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--p);
        box-shadow: 0 0 0 3px rgba(106, 76, 197, 0.1);
        outline: none;
    }

    .form-control:disabled,
    .form-select:disabled {
        background-color: rgba(31, 36, 48, 0.04);
        opacity: 0.6;
    }

    .form-text {
        font-size: 0.85rem;
        opacity: 0.7;
        margin-top: 0.35rem;
    }

    .file-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: var(--bg);
        border-radius: 6px;
        margin-top: 0.5rem;
    }

    .file-info .material-symbols-rounded {
        color: var(--p);
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(31, 36, 48, 0.08);
    }

    .sigu-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.12s ease;
        text-decoration: none;
    }

    .sigu-btn-primary {
        background: linear-gradient(135deg, var(--p), var(--p-mid));
        color: white;
        box-shadow: 0 8px 20px rgba(106, 76, 197, 0.12);
    }

    .sigu-btn-primary:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(106, 76, 197, 0.16);
    }

    .sigu-btn-primary:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .sigu-btn-ghost {
        background: transparent;
        color: var(--p);
        border: 1px solid rgba(106, 76, 197, 0.2);
    }

    .sigu-btn-ghost:hover {
        background: var(--p-light);
    }

    .invalid-feedback {
        color: #dc2626;
        font-size: 0.85rem;
        margin-top: 0.25rem;
        font-weight: 500;
    }

    .d-block {
        display: block;
    }

    .is-invalid {
        border-color: #dc2626 !important;
        background-color: rgba(220, 38, 38, 0.03) !important;
    }

    .is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
    }

    @media (max-width: 768px) {
        .form-container {
            padding: 1.5rem;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .form-actions .sigu-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formDocumento');
        const btnSubmit = document.getElementById('btn-submit');
        const tipoDocumentoSelect = document.getElementById('id_tipo_documento');
        const docUsuarioInput = document.getElementById('doc_usuario');
        const placaInput = document.getElementById('placa');
        const filaDocUsuario = document.getElementById('fila-doc-usuario');
        const filaPlaca = document.getElementById('fila-placa');
        const sinCamposAdicionales = document.getElementById('sin-campos-adicionales');

        /**
         * CONTROLAR VISIBILIDAD DE CAMPOS CONDICIONALES
         */
        function actualizarCamposCondicionales() {
            const selectedOption = tipoDocumentoSelect.options[tipoDocumentoSelect.selectedIndex];
            const requiereDoc = selectedOption.getAttribute('data-requiere-doc') === 'true';
            const requierePlaca = selectedOption.getAttribute('data-requiere-placa') === 'true';

            // Mostrar/ocultar y marcar como requerido: Documento Usuario
            if (requiereDoc) {
                filaDocUsuario.style.display = 'flex';
                docUsuarioInput.setAttribute('required', 'required');
                docUsuarioInput.classList.add('required-field');
                document.getElementById('info-doc-usuario').style.display = 'block';
            } else {
                filaDocUsuario.style.display = 'none';
                docUsuarioInput.removeAttribute('required');
                docUsuarioInput.classList.remove('required-field');
                document.getElementById('info-doc-usuario').style.display = 'none';
            }

            // Mostrar/ocultar y marcar como requerido: Placa del Bus
            if (requierePlaca) {
                filaPlaca.style.display = 'flex';
                placaInput.setAttribute('required', 'required');
                placaInput.classList.add('required-field');
                document.getElementById('info-placa').style.display = 'block';
            } else {
                filaPlaca.style.display = 'none';
                placaInput.removeAttribute('required');
                placaInput.classList.remove('required-field');
                document.getElementById('info-placa').style.display = 'none';
            }

            // Mostrar mensaje si no requiere campos adicionales
            if (!requiereDoc && !requierePlaca) {
                sinCamposAdicionales.style.display = 'flex';
            } else {
                sinCamposAdicionales.style.display = 'none';
            }
        }

        // Ejecutar al cambiar tipo de documento
        tipoDocumentoSelect.addEventListener('change', actualizarCamposCondicionales);

        // Ejecutar al cargar la página (para mostrar campos si hay valor anterior)
        actualizarCamposCondicionales();

        // Auto-mayúsculas en placa
        placaInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Solo números en doc_usuario
        docUsuarioInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Prevenir envío duplicado
        form.addEventListener('submit', function(e) {
            btnSubmit.disabled = true;
            btnSubmit.classList.add('btn-loading');
            btnSubmit.innerHTML = '<span class="material-symbols-rounded">schedule</span> Guardando...';
        });
    });
</script>
@endpush

@endsection