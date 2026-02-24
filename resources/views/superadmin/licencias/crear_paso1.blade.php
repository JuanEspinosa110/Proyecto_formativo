@extends('superadmin.layouts.admin')

@section('title', 'Crear Licencia - Paso 1')

@section('content')
<div class="container sa-licencia-container">
    <!-- Header con progreso -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="sa-licencia-title">Crear Nueva Licencia</h2>
                <p class="text-muted">Complete los datos de la empresa y asigne un administrador del sistema</p>
            </div>
            <div>
                <span class="badge bg-primary" style="font-size: 1rem; padding: 0.5rem 1rem;">
                    Paso 1 de 2
                </span>
            </div>
        </div>

        <!-- Barra de progreso -->
        <div class="progress" style="height: 4px;">
            <div class="progress-bar bg-primary" role="progressbar" style="width: 50%"></div>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Alert de validación de errores -->
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex">
            <i class="fas fa-exclamation-triangle me-3 fs-5"></i>
            <div>
                <h5 class="alert-heading">Errores de Validación</h5>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Alert de verificación NIT -->
    <div id="nitAlert" class="alert d-none mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle me-2 fs-4"></i>
            <div id="nitAlertMessage"></div>
        </div>
    </div>

    <form action="{{ route('superadmin.licencias.guardar-paso1') }}" method="POST" id="formPaso1">
        @csrf

        <!-- Paso 1: Datos de la Empresa -->
        <div class="card sa-licencia-card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-building me-2 text-primary"></i>
                    1. Datos de la Empresa
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="sa-licencia-label">NIT / Identificación Tributaria *</label>
                        <div class="input-group">
                            <input type="number"
                                id="nitInput"
                                name="NIT"
                                class="form-control sa-licencia-input @error('NIT') is-invalid @enderror"
                                value="{{ old('NIT') }}"
                                placeholder="900123456"
                                required
                                maxlength="15">
                            <button type="button" id="btnVerificarNit" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i> Verificar
                            </button>
                        </div>
                        @error('NIT')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Ingrese el NIT y haga clic en Verificar</small>
                    </div>

                    <div class="col-md-8">
                        <label class="sa-licencia-label">Nombre de la Empresa *</label>
                        <input type="text"
                            id="nombre_empresa"
                            name="nombre_empresa"
                            class="form-control sa-licencia-input @error('nombre_empresa') is-invalid @enderror"
                            value="{{ old('nombre_empresa') }}"
                            placeholder="Ej: Logística Nacional S.A.S"
                            readonly
                            required>
                        <small class="text-muted">Se carga automáticamente con la búsqueda del NIT</small>
                        @error('nombre_empresa')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="sa-licencia-label">Departamento *</label>
                        <input type="text" id="nombre_departamento" class="form-control sa-licencia-input" readonly value="{{ old('nombre_departamento') }}" placeholder="Carga automática...">
                        <input type="hidden" name="id_departamento" id="id_departamento" value="{{ old('id_departamento') }}">
                        @error('id_departamento')<div class="text-danger small">{{ $message }}</div>@enderror
                        <small class="text-muted">Se carga automáticamente con la búsqueda del NIT</small>
                    </div>

                    <div class="col-md-4">
                        <label class="sa-licencia-label">Ciudad *</label>
                        <input type="text" id="nombre_ciudad" class="form-control sa-licencia-input" readonly value="{{ old('nombre_ciudad') }}" placeholder="Carga automática...">
                        <input type="hidden" name="id_ciudad" id="id_ciudad" value="{{ old('id_ciudad') }}">
                        @error('id_ciudad')<div class="text-danger small">{{ $message }}</div>@enderror
                        <small class="text-muted">Se carga automáticamente con la búsqueda del NIT</small>
                    </div>

                    <div class="col-md-4">
                        <label class="sa-licencia-label">Teléfono Corporativo</label>
                        <input type="text"
                            id="telefono_empresa"
                            name="telefono_empresa"
                            class="form-control sa-licencia-input"
                            value="{{ old('telefono_empresa') }}"
                            placeholder="+57 300 000 0000"
                            readonly>
                        <small class="text-muted">Se carga automáticamente con la búsqueda del NIT</small>
                    </div>

                    <div class="col-md-6">
                        <label class="sa-licencia-label">Correo Electrónico Corporativo *</label>
                        <input type="email"
                            id="correo_corporativo"
                            name="correo_corporativo"
                            class="form-control sa-licencia-input @error('correo_corporativo') is-invalid @enderror"
                            value="{{ old('correo_corporativo') }}"
                            placeholder="contabilidad@empresa.com"
                            readonly
                            required>
                        <small class="text-muted">Se carga automáticamente con la búsqueda del NIT</small>
                        @error('correo_corporativo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Usuario Administrador -->
        <div class="card sa-licencia-card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-user-shield me-2 text-primary"></i>
                    2. Usuario Administrador del Sistema
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Este usuario</strong> será el encargado de administrar la licencia y acceder al sistema. Defina una contraseña segura.
                </div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="sa-licencia-label">Documento de Identidad *</label>
                        <input type="number"
                            id="doc_admin"
                            name="doc_admin"
                            class="form-control sa-licencia-input @error('doc_admin') is-invalid @enderror"
                            value="{{ old('doc_admin') }}"
                            placeholder="1098765432"
                            required>
                        @error('doc_admin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="sa-licencia-label">Primer Nombre *</label>
                        <input type="text"
                            id="primer_nombre_admin"
                            name="primer_nombre_admin"
                            class="form-control sa-licencia-input @error('primer_nombre_admin') is-invalid @enderror"
                            value="{{ old('primer_nombre_admin') }}"
                            placeholder="Juan"
                            required>
                        @error('primer_nombre_admin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="sa-licencia-label">Segundo Nombre</label>
                        <input type="text"
                            id="segundo_nombre_admin"
                            name="segundo_nombre_admin"
                            class="form-control sa-licencia-input @error('segundo_nombre_admin') is-invalid @enderror"
                            value="{{ old('segundo_nombre_admin') }}"
                            placeholder="Carlos">
                        @error('segundo_nombre_admin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="sa-licencia-label">Primer Apellido *</label>
                        <input type="text"
                            id="primer_apellido_admin"
                            name="primer_apellido_admin"
                            class="form-control sa-licencia-input @error('primer_apellido_admin') is-invalid @enderror"
                            value="{{ old('primer_apellido_admin') }}"
                            placeholder="González"
                            required>
                        @error('primer_apellido_admin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="sa-licencia-label">Segundo Apellido</label>
                        <input type="text"
                            id="segundo_apellido_admin"
                            name="segundo_apellido_admin"
                            class="form-control sa-licencia-input @error('segundo_apellido_admin') is-invalid @enderror"
                            value="{{ old('segundo_apellido_admin') }}"
                            placeholder="Martínez">
                        @error('segundo_apellido_admin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="sa-licencia-label">Teléfono</label>
                        <input type="number"
                            id="telefono_admin"
                            name="telefono_admin"
                            class="form-control sa-licencia-input"
                            value="{{ old('telefono_admin') }}"
                            placeholder="320 123 4567">
                    </div>

                    <div class="col-md-3">
                        <label class="sa-licencia-label">Correo de Acceso *</label>
                        <input type="email"
                            id="correo_admin"
                            name="correo_admin"
                            class="form-control sa-licencia-input @error('correo_admin') is-invalid @enderror"
                            value="{{ old('correo_admin') }}"
                            placeholder="admin@empresa.com"
                            required>
                        @error('correo_admin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="sa-licencia-label">Contraseña *</label>
                        <input type="password"
                            id="password_admin"
                            name="password_admin"
                            class="form-control sa-licencia-input @error('password_admin') is-invalid @enderror"
                            placeholder="Mínimo 8 caracteres"
                            required>
                        <small class="text-muted">Mín 8 caracteres, mayús, minús, números</small>
                        @error('password_admin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de navegación -->
        <div class="d-flex justify-content-between">
            <a href="{{ route('superadmin.licencias.index') }}" class="btn btn-light sa-licencia-btn-cancel">
                <i class="fas fa-times me-2"></i>Cancelar
            </a>
            <button type="submit" class="btn btn-primary sa-licencia-btn-next">
                Continuar al Paso 2
                <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nitInput = document.getElementById('nitInput');
        const btnVerificar = document.getElementById('btnVerificarNit');
        const nitAlert = document.getElementById('nitAlert');
        const nitAlertMessage = document.getElementById('nitAlertMessage');
        const formPaso1 = document.getElementById('formPaso1');

        // Verificar NIT
        btnVerificar.addEventListener('click', function() {
            const nit = nitInput.value.trim();
            if (!nit) {
                showAlert('warning', 'Por favor ingrese un NIT');
                return;
            }

            btnVerificar.disabled = true;
            btnVerificar.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            fetch(`/superadmin/licencias/verificar-nit/${nit}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showAlert('danger', data.mensaje);
                        formPaso1.querySelector('button[type="submit"]').disabled = true;
                        limpiarFormulario();
                    } else if (data.existe) {
                        // REQUISITO: Si existe pero tiene licencia, el controlador ya manda error.
                        // Si existe y no tiene licencia, llenamos todo.
                        showAlert('success', '✓ Empresa encontrada. Datos de ubicación vinculados.');
                        llenarFormulario(data.datos);
                        formPaso1.querySelector('button[type="submit"]').disabled = false;
                    } else {
                        // Si el NIT no existe en la tabla empresa
                        showAlert('danger', 'El NIT ingresado no existe en el sistema. Debe registrar la empresa primero.');
                        limpiarFormulario();
                        formPaso1.querySelector('button[type="submit"]').disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'Error de conexión al verificar NIT');
                })
                .finally(() => {
                    btnVerificar.disabled = false;
                    btnVerificar.innerHTML = '<i class="fas fa-search"></i> Verificar';
                });
        });

        /**
         * Validación de solo letras en campos de nombres
         */
        const camposLetras = [
            'primer_nombre_admin',
            'primer_apellido_admin',
            'segundo_apellido_admin'
        ];

        camposLetras.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('input', function(e) {
                    // Reemplaza cualquier cosa que NO sea letra (incluyendo tildes y ñ) por vacío
                    this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g, '');
                });
            }
        });

        const campotercernombre = [
            'segundo_nombre_admin'
        ];

        campotercernombre.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('input', function(e) {
                    // Reemplaza cualquier cosa que NO sea letra (incluyendo tildes y ñ) por vacío
                    this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
                });
            }
        });

        /**
         * Mostrar/ocultar alerta
         */
        function showAlert(type, message) {
            nitAlert.className = `alert alert-${type} d-block`;
            nitAlertMessage.innerHTML = message;
        }

        /**
         * Llenar formulario con datos de la empresa
         */
        function llenarFormulario(datos) {
            // Llenar campos de texto
            document.getElementById('nombre_empresa').value = datos.nombre_empresa || '';
            document.getElementById('telefono_empresa').value = datos.telefono_empresa || '';
            document.getElementById('correo_corporativo').value = datos.correo_corporativo || '';

            // Llenar Visualización de ubicación
            document.getElementById('nombre_departamento').value = datos.nombre_departamento || '';
            document.getElementById('nombre_ciudad').value = datos.nombre_city || '';

            // Llenar IDs ocultos para el envío del formulario
            document.getElementById('id_departamento').value = datos.id_departamento || '';
            document.getElementById('id_ciudad').value = datos.id_ciudad || '';
        }

        /**
         * Limpiar formulario
         */
        function limpiarFormulario() {
            const campos = [
                'nombre_empresa', 'telefono_empresa', 'correo_corporativo',
                'nombre_departamento', 'nombre_ciudad', 'id_departamento', 'id_ciudad'
            ];
            campos.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
        }
    });
</script>
@endsection