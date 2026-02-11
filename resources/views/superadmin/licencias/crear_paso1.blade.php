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
                            <input type="text" 
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
                               required>
                        @error('nombre_empresa')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label class="sa-licencia-label">Departamento *</label>
                        <select name="id_departamento" 
                                id="id_departamento" 
                                class="form-select sa-licencia-input @error('id_departamento') is-invalid @enderror" 
                                required>
                            <option value="">Seleccionar departamento</option>
                            @foreach($departamentos as $dep)
                            <option value="{{ $dep->id_departamento }}" {{ old('id_departamento') == $dep->id_departamento ? 'selected' : '' }}>
                                {{ $dep->nombre_departamento }}
                            </option>
                            @endforeach
                        </select>
                        @error('id_departamento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label class="sa-licencia-label">Ciudad *</label>
                        <select name="id_ciudad" 
                                id="id_ciudad" 
                                class="form-select sa-licencia-input @error('id_ciudad') is-invalid @enderror" 
                                required>
                            <option value="">Primero seleccione departamento</option>
                        </select>
                        @error('id_ciudad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label class="sa-licencia-label">Teléfono Corporativo</label>
                        <input type="text" 
                               id="telefono_empresa"
                               name="telefono_empresa" 
                               class="form-control sa-licencia-input" 
                               value="{{ old('telefono_empresa') }}" 
                               placeholder="+57 300 000 0000">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="sa-licencia-label">Correo Electrónico Corporativo *</label>
                        <input type="email" 
                               id="correo_corporativo"
                               name="correo_corporativo" 
                               class="form-control sa-licencia-input @error('correo_corporativo') is-invalid @enderror" 
                               value="{{ old('correo_corporativo') }}" 
                               placeholder="contabilidad@empresa.com" 
                               required>
                        @error('correo_corporativo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Representante Legal -->
        <div class="card sa-licencia-card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-user-tie me-2 text-primary"></i> 
                    2. Representante Legal de la Empresa
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="sa-licencia-label">Documento de Identidad *</label>
                        <input type="text" 
                               id="doc_representante"
                               name="doc_representante" 
                               class="form-control sa-licencia-input @error('doc_representante') is-invalid @enderror" 
                               value="{{ old('doc_representante') }}" 
                               placeholder="1098765432" 
                               required>
                        @error('doc_representante')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-3">
                        <label class="sa-licencia-label">Primer Nombre *</label>
                        <input type="text" 
                               id="primer_nombre_repre"
                               name="primer_nombre_repre" 
                               class="form-control sa-licencia-input @error('primer_nombre_repre') is-invalid @enderror" 
                               value="{{ old('primer_nombre_repre') }}" 
                               required>
                        @error('primer_nombre_repre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-3">
                        <label class="sa-licencia-label">Segundo Nombre</label>
                        <input type="text" 
                               id="segundo_nombre_repre"
                               name="segundo_nombre_repre" 
                               class="form-control sa-licencia-input" 
                               value="{{ old('segundo_nombre_repre') }}">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="sa-licencia-label">Primer Apellido *</label>
                        <input type="text" 
                               id="primer_apellido_repre"
                               name="primer_apellido_repre" 
                               class="form-control sa-licencia-input @error('primer_apellido_repre') is-invalid @enderror" 
                               value="{{ old('primer_apellido_repre') }}" 
                               required>
                        @error('primer_apellido_repre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-3">
                        <label class="sa-licencia-label">Segundo Apellido</label>
                        <input type="text" 
                               id="segundo_apellido_repre"
                               name="segundo_apellido_repre" 
                               class="form-control sa-licencia-input" 
                               value="{{ old('segundo_apellido_repre') }}">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="sa-licencia-label">Teléfono</label>
                        <input type="text" 
                               id="telefono_representante"
                               name="telefono_representante" 
                               class="form-control sa-licencia-input" 
                               value="{{ old('telefono_representante') }}" 
                               placeholder="320 123 4567">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="sa-licencia-label">Correo Electrónico *</label>
                        <input type="email" 
                               id="correo_representante"
                               name="correo_representante" 
                               class="form-control sa-licencia-input @error('correo_representante') is-invalid @enderror" 
                               value="{{ old('correo_representante') }}" 
                               placeholder="representante@empresa.com" 
                               required>
                        @error('correo_representante')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Administrador del Sistema -->
        <div class="card sa-licencia-card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-user-shield me-2 text-primary"></i> 
                    3. Administrador del Sistema
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Este usuario tendrá acceso al sistema como administrador de la empresa
                </div>
                
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="sa-licencia-label">Documento de Identidad *</label>
                        <input type="text" 
                               name="doc_admin" 
                               class="form-control sa-licencia-input @error('doc_admin') is-invalid @enderror" 
                               value="{{ old('doc_admin') }}" 
                               placeholder="1234567890" 
                               required>
                        @error('doc_admin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-3">
                        <label class="sa-licencia-label">Primer Nombre *</label>
                        <input type="text" 
                               name="primer_nombre_admin" 
                               class="form-control sa-licencia-input @error('primer_nombre_admin') is-invalid @enderror" 
                               value="{{ old('primer_nombre_admin') }}" 
                               required>
                        @error('primer_nombre_admin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-3">
                        <label class="sa-licencia-label">Segundo Nombre</label>
                        <input type="text" 
                               name="segundo_nombre_admin" 
                               class="form-control sa-licencia-input" 
                               value="{{ old('segundo_nombre_admin') }}">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="sa-licencia-label">Primer Apellido *</label>
                        <input type="text" 
                               name="primer_apellido_admin" 
                               class="form-control sa-licencia-input @error('primer_apellido_admin') is-invalid @enderror" 
                               value="{{ old('primer_apellido_admin') }}" 
                               required>
                        @error('primer_apellido_admin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-3">
                        <label class="sa-licencia-label">Segundo Apellido</label>
                        <input type="text" 
                               name="segundo_apellido_admin" 
                               class="form-control sa-licencia-input" 
                               value="{{ old('segundo_apellido_admin') }}">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="sa-licencia-label">Teléfono</label>
                        <input type="text" 
                               name="telefono_admin" 
                               class="form-control sa-licencia-input" 
                               value="{{ old('telefono_admin') }}" 
                               placeholder="320 123 4567">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="sa-licencia-label">Correo de Acceso *</label>
                        <input type="email" 
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
                               name="password_admin" 
                               class="form-control sa-licencia-input @error('password_admin') is-invalid @enderror" 
                               placeholder="Mínimo 8 caracteres" 
                               required>
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

        // Mostrar loading
        btnVerificar.disabled = true;
        btnVerificar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';

        fetch(`/superadmin/licencias/verificar-nit/${nit}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    // Empresa tiene licencia activa
                    showAlert('danger', data.mensaje);
                    formPaso1.querySelector('button[type="submit"]').disabled = true;
                } else if (data.existe && !data.tiene_licencia) {
                    // Empresa existe, autocompletar datos
                    showAlert('success', '✓ Empresa encontrada. Datos cargados automáticamente.');
                    llenarFormulario(data.datos);
                } else {
                    // NIT nuevo
                    showAlert('info', 'NIT no registrado. Complete los datos para crear la empresa.');
                    limpiarFormulario();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'Error al verificar el NIT');
            })
            .finally(() => {
                btnVerificar.disabled = false;
                btnVerificar.innerHTML = '<i class="fas fa-search"></i> Verificar';
            });
    });

    // Cargar ciudades al cambiar departamento
    document.getElementById('id_departamento').addEventListener('change', function() {
        const deptoId = this.value;
        const ciudadSelect = document.getElementById('id_ciudad');
        
        if (!deptoId) {
            ciudadSelect.innerHTML = '<option value="">Primero seleccione departamento</option>';
            return;
        }
        
        fetch(`/superadmin/licencias/ciudades/${deptoId}`)
            .then(response => response.json())
            .then(ciudades => {
                ciudadSelect.innerHTML = '<option value="">Seleccionar ciudad</option>';
                ciudades.forEach(ciudad => {
                    ciudadSelect.innerHTML += `<option value="${ciudad.id_ciudad}">${ciudad.nombre_city}</option>`;
                });
            });
    });

    function showAlert(type, message) {
        nitAlert.className = `alert alert-${type} d-block`;
        nitAlertMessage.textContent = message;
    }

    function llenarFormulario(datos) {
        // Datos de empresa
        document.getElementById('nombre_empresa').value = datos.nombre_empresa || '';
        document.getElementById('telefono_empresa').value = datos.telefono_empresa || '';
        document.getElementById('correo_corporativo').value = datos.correo_corporativo || '';
        
        // Departamento y ciudad
        if (datos.id_departamento) {
            document.getElementById('id_departamento').value = datos.id_departamento;
            
            // Cargar ciudades y seleccionar
            fetch(`/superadmin/licencias/ciudades/${datos.id_departamento}`)
                .then(response => response.json())
                .then(ciudades => {
                    const ciudadSelect = document.getElementById('id_ciudad');
                    ciudadSelect.innerHTML = '<option value="">Seleccionar ciudad</option>';
                    ciudades.forEach(ciudad => {
                        const selected = ciudad.id_ciudad == datos.id_ciudad ? 'selected' : '';
                        ciudadSelect.innerHTML += `<option value="${ciudad.id_ciudad}" ${selected}>${ciudad.nombre_city}</option>`;
                    });
                });
        }
        
        // Datos de representante
        document.getElementById('doc_representante').value = datos.doc_representante || '';
        document.getElementById('primer_nombre_repre').value = datos.primer_nombre_repre || '';
        document.getElementById('segundo_nombre_repre').value = datos.segundo_nombre_repre || '';
        document.getElementById('primer_apellido_repre').value = datos.primer_apellido_repre || '';
        document.getElementById('segundo_apellido_repre').value = datos.segundo_apellido_repre || '';
        document.getElementById('telefono_representante').value = datos.telefono_representante || '';
        document.getElementById('correo_representante').value = datos.correo_representante || '';
    }

    function limpiarFormulario() {
        // Mantener solo el NIT, limpiar el resto
        const campos = ['nombre_empresa', 'telefono_empresa', 'correo_corporativo',
                       'doc_representante', 'primer_nombre_repre', 'segundo_nombre_repre',
                       'primer_apellido_repre', 'segundo_apellido_repre', 
                       'telefono_representante', 'correo_representante'];
        
        campos.forEach(campo => {
            const elem = document.getElementById(campo);
            if (elem) elem.value = '';
        });
        
        document.getElementById('id_departamento').value = '';
        document.getElementById('id_ciudad').innerHTML = '<option value="">Primero seleccione departamento</option>';
    }
});
</script>
@endsection
