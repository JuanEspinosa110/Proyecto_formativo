@extends('superadmin.layouts.admin')

@section('title', 'Nueva Empresa')

@section('content')

<div class="empresa-container">
    
    {{-- HEADER --}}
    <div class="empresa-header">
        <div class="empresa-header-title">
            <a href="{{ route('superadmin.empresas.index') }}" class="btn-back">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1><span class="material-symbols-outlined">add_business</span> Crear Empresa</h1>
                <p>Complete el formulario con los datos de la empresa</p>
            </div>
        </div>
    </div>

    {{-- FORMULARIO --}}
    <div class="empresa-form-container">

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Se encontraron errores:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif


        <form action="{{ route('superadmin.empresas.store') }}" method="POST" class="empresa-form">
            @csrf

            {{-- INFORMACIÓN DE LA EMPRESA --}}
            <div class="form-section">
                <div class="section-header">
                    <span class="material-symbols-outlined">business</span>
                    <h3>Información de la Empresa</h3>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="NIT" class="form-label required">NIT</label>
                        <input type="number" class="form-control @error('NIT') is-invalid @enderror" 
                               id="NIT" name="NIT" value="{{ old('NIT') }}" required>
                        @error('NIT')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="nombre_empresa" class="form-label required">Nombre de la Empresa</label>
                        <input type="text" class="form-control @error('nombre_empresa') is-invalid @enderror" 
                               id="nombre_empresa" name="nombre_empresa" value="{{ old('nombre_empresa') }}" required>
                        @error('nombre_empresa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="telefono_empresa" class="form-label">Teléfono Empresa</label>
                        <input type="text" class="form-control @error('telefono_empresa') is-invalid @enderror" 
                               id="telefono_empresa" name="telefono_empresa" value="{{ old('telefono_empresa') }}">
                        @error('telefono_empresa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="correo_corporativo" class="form-label">Correo Corporativo</label>
                        <input type="email" class="form-control @error('correo_corporativo') is-invalid @enderror" 
                               id="correo_corporativo" name="correo_corporativo" value="{{ old('correo_corporativo') }}">
                        @error('correo_corporativo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required">Tipo Empresa</label>
                        <select name="id_tipo_empresa" class="form-select @error('id_tipo_empresa') is-invalid @enderror" required>
                            <option value="">Seleccione tipo</option>
                            <option value="1">Transporte Urbano</option>
                            <option value="2">Transporte Intermunicipal</option>
                            <option value="3">Especial</option>
                        </select>
                        @error('id_tipo_empresa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- INFORMACIÓN DEL REPRESENTANTE LEGAL --}}
            <div class="form-section">
                <div class="section-header">
                    <span class="material-symbols-outlined">person</span>
                    <h3>Representante Legal</h3>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="doc_representante" class="form-label required">Documento de Identidad</label>
                        <input type="number" class="form-control @error('doc_representante') is-invalid @enderror" 
                               id="doc_representante" name="doc_representante" value="{{ old('doc_representante') }}" required>
                        @error('doc_representante')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="primer_nombre_repre" class="form-label required">Primer Nombre</label>
                        <input type="text" class="form-control @error('primer_nombre_repre') is-invalid @enderror" 
                               id="primer_nombre_repre" name="primer_nombre_repre" value="{{ old('primer_nombre_repre') }}" required>
                        @error('primer_nombre_repre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="segundo_nombre_repre" class="form-label">Segundo Nombre</label>
                        <input type="text" class="form-control @error('segundo_nombre_repre') is-invalid @enderror" 
                               id="segundo_nombre_repre" name="segundo_nombre_repre" value="{{ old('segundo_nombre_repre') }}">
                        @error('segundo_nombre_repre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="primer_apellido_repre" class="form-label required">Primer Apellido</label>
                        <input type="text" class="form-control @error('primer_apellido_repre') is-invalid @enderror" 
                               id="primer_apellido_repre" name="primer_apellido_repre" value="{{ old('primer_apellido_repre') }}" required>
                        @error('primer_apellido_repre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="segundo_apellido_repre" class="form-label">Segundo Apellido</label>
                        <input type="text" class="form-control @error('segundo_apellido_repre') is-invalid @enderror" 
                               id="segundo_apellido_repre" name="segundo_apellido_repre" value="{{ old('segundo_apellido_repre') }}">
                        @error('segundo_apellido_repre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="telefono_representante" class="form-label">Teléfono</label>
                        <input type="text" class="form-control @error('telefono_representante') is-invalid @enderror" 
                               id="telefono_representante" name="telefono_representante" value="{{ old('telefono_representante') }}">
                        @error('telefono_representante')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="correo_representante" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control @error('correo_representante') is-invalid @enderror" 
                               id="correo_representante" name="correo_representante" value="{{ old('correo_representante') }}">
                        @error('correo_representante')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- UBICACIÓN Y ESTADO --}}
            <div class="form-section">
                <div class="section-header">
                    <span class="material-symbols-outlined">location_on</span>
                    <h3>Ubicación y Estado</h3>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="id_departamento" class="form-label required">Departamento</label>
                        <select name="id_departamento"
                            class="form-select @error('id_departamento') is-invalid @enderror" 
                                id="id_departamento" required>


                            <option value="">Seleccione un departamento</option>
                            @foreach($departamentos as $departamento)
                                <option value="{{ $departamento->id_departamento }}">
                                    {{ $departamento->nombre_departamento }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="id_ciudad" class="form-label required">Ciudad</label>
                        <select class="form-select @error('id_ciudad') is-invalid @enderror" 
                                id="id_ciudad" name="id_ciudad" required>
                            <option value="">Seleccione primero un departamento</option>
                        </select>
                        @error('id_ciudad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    
                </div>
            </div>

            {{-- BOTONES --}}
            <div class="form-actions">
                <a href="{{ route('superadmin.empresas.index') }}" class="btn btn-secondary">
                    <span class="material-symbols-outlined">close</span>
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <span class="material-symbols-outlined">save</span>
                    Guardar Empresa
                </button>
            </div>

        </form>
    </div>

</div>

<script>

// =============================
// CARGAR CIUDADES POR DEPARTAMENTO
// =============================
document.getElementById('id_departamento').addEventListener('change', function() {
    const departamentoId = this.value;
    const ciudadSelect = document.getElementById('id_ciudad');
    
    ciudadSelect.innerHTML = '<option value="">Cargando ciudades...</option>';
    
    if (departamentoId) {
        fetch(`/superadmin/empresas/ciudades/${departamentoId}`)
            .then(response => response.json())
            .then(data => {
                ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad</option>';
                data.forEach(ciudad => {
                    const option = document.createElement('option');
                    option.value = ciudad.id_ciudad;
                    option.textContent = ciudad.nombre_city;
                    ciudadSelect.appendChild(option);
                });
            })
            .catch(() => {
                ciudadSelect.innerHTML = '<option value="">Error al cargar ciudades</option>';
            });
    } else {
        ciudadSelect.innerHTML = '<option value="">Seleccione primero un departamento</option>';
    }
});


// =============================
// FUNCIONES GENERALES
// =============================

// SOLO LETRAS
function soloLetras(input) {
    input.value = input.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúñÑ\s]/g, '');
}

// SOLO NÚMEROS
function soloNumeros(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
}



// =============================
// EVENTOS DE CAMPOS
// =============================

// NIT
document.getElementById('NIT').addEventListener('input', function() {
    soloNumeros(this);
});

// Documento representante
document.getElementById('doc_representante').addEventListener('input', function() {
    soloNumeros(this);
});

// Teléfonos
document.getElementById('telefono_empresa').addEventListener('input', function() {
    soloNumeros(this);
});

document.getElementById('telefono_representante').addEventListener('input', function() {
    soloNumeros(this);
});

// Nombres y apellidos
document.getElementById('primer_nombre_repre').addEventListener('input', function(){
    soloLetras(this);
});


document.getElementById('primer_apellido_repre').addEventListener('input', function(){
    soloLetras(this);
});


function validarCorreo(input) {
    const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        if (!regex.test(input.value)) {
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }

        function validarCorreo(input) 
        {
            const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            if (!regex.test(input.value)) {
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        }

}



</script>


@endsection
