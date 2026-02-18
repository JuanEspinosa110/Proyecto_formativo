@extends('superadmin.layouts.admin')

@section('title', 'Editar Empresa')

@section('content')
<div class="empresa-container">
    
    {{-- HEADER --}}
    <div class="empresa-header">
        <div class="empresa-header-title">
            <a href="{{ route('superadmin.empresas.index') }}" class="btn-back">
                <span class="material-symbols-outlined"><i class="fa fa-arrow-left" aria-hidden="true"></i></span>
            </a>
            <div>
                <h1><span class="material-symbols-outlined"><i class="fa fa-edit" aria-hidden="true"></i></span> Editar Empresa</h1>
                <p>Actualice la información de {{ $empresa->nombre_empresa }}</p>
            </div>
        </div>
    </div>

    {{-- FORMULARIO --}}
    <div class="empresa-form-container">
        <form action="{{ route('superadmin.empresas.update', $empresa->NIT) }}" method="POST" class="empresa-form">
            @csrf
            @method('PUT')

            {{-- INFORMACIÓN DE LA EMPRESA --}}
            <div class="form-section">
                <div class="section-header">
                    <span class="material-symbols-outlined"><i class="fa fa-building" aria-hidden="true"></i></span>
                    <h3>Información de la Empresa</h3>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="NIT" class="form-label">NIT</label>
                        <input type="number" class="form-control" id="NIT" 
                               value="{{ $empresa->NIT }}" disabled>
                        <small class="text-muted">El NIT no se puede modificar</small>
                    </div>

                    <div class="col-md-6">
                        <label for="nombre_empresa" class="form-label required">
                            Nombre de la Empresa
                        </label>

                        {{-- Campo visible bloqueado --}}
                        <input type="text"
                            class="form-control bg-light"
                            value="{{ $empresa->nombre_empresa }}"
                            readonly>

                        {{-- Campo hidden para que se envíe al backend --}}
                        <input type="hidden"
                            name="nombre_empresa"
                            value="{{ $empresa->nombre_empresa }}">

                        <small class="text-muted">
                            El nombre de la empresa no se puede modificar.
                        </small>
                    </div>

                    <div class="col-md-6">
                        <label for="telefono_empresa" class="form-label">Teléfono Empresa</label>
                        <input type="text" class="form-control @error('telefono_empresa') is-invalid @enderror" 
                               id="telefono_empresa" name="telefono_empresa" 
                               value="{{ old('telefono_empresa', $empresa->telefono_empresa) }}">
                        @error('telefono_empresa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="correo_corporativo" class="form-label">Correo Corporativo</label>
                        <input type="email" class="form-control @error('correo_corporativo') is-invalid @enderror" 
                               id="correo_corporativo" name="correo_corporativo" 
                               value="{{ old('correo_corporativo', $empresa->correo_corporativo) }}">
                        @error('correo_corporativo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- INFORMACIÓN DEL REPRESENTANTE LEGAL --}}
            <div class="form-section">
                <div class="section-header">
                    <span class="material-symbols-outlined"><i class="fa fa-user" aria-hidden="true"></i></span>
                    <h3>Representante Legal</h3>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="doc_representante" class="form-label required">Documento de Identidad</label>
                        <input type="number" class="form-control @error('doc_representante') is-invalid @enderror" 
                               id="doc_representante" name="doc_representante" 
                               value="{{ old('doc_representante', $empresa->doc_representante) }}" required>
                        @error('doc_representante')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="primer_nombre_repre" class="form-label required">Primer Nombre</label>
                        <input type="text" class="form-control @error('primer_nombre_repre') is-invalid @enderror" 
                               id="primer_nombre_repre" name="primer_nombre_repre" 
                               value="{{ old('primer_nombre_repre', $empresa->primer_nombre_repre) }}" required>
                        @error('primer_nombre_repre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="segundo_nombre_repre" class="form-label">Segundo Nombre</label>
                        <input type="text" class="form-control @error('segundo_nombre_repre') is-invalid @enderror" 
                               id="segundo_nombre_repre" name="segundo_nombre_repre" 
                               value="{{ old('segundo_nombre_repre', $empresa->segundo_nombre_repre) }}">
                        @error('segundo_nombre_repre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="primer_apellido_repre" class="form-label required">Primer Apellido</label>
                        <input type="text" class="form-control @error('primer_apellido_repre') is-invalid @enderror" 
                               id="primer_apellido_repre" name="primer_apellido_repre" 
                               value="{{ old('primer_apellido_repre', $empresa->primer_apellido_repre) }}" required>
                        @error('primer_apellido_repre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="segundo_apellido_repre" class="form-label">Segundo Apellido</label>
                        <input type="text" class="form-control @error('segundo_apellido_repre') is-invalid @enderror" 
                               id="segundo_apellido_repre" name="segundo_apellido_repre" 
                               value="{{ old('segundo_apellido_repre', $empresa->segundo_apellido_repre) }}">
                        @error('segundo_apellido_repre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="telefono_representante" class="form-label">Teléfono</label>
                        <input type="text" class="form-control @error('telefono_representante') is-invalid @enderror" 
                               id="telefono_representante" name="telefono_representante" 
                               value="{{ old('telefono_representante', $empresa->telefono_representante) }}">
                        @error('telefono_representante')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="correo_representante" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control @error('correo_representante') is-invalid @enderror" 
                               id="correo_representante" name="correo_representante" 
                               value="{{ old('correo_representante', $empresa->correo_representante) }}">
                        @error('correo_representante')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- UBICACIÓN Y ESTADO --}}
            <div class="form-section">
                <div class="section-header">
                    <span class="material-symbols-outlined"><i class="fa fa-map" aria-hidden="true"></i></span>
                    <h3>Ubicación y Estado</h3>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="id_departamento" class="form-label required">Departamento</label>
                        <select class="form-select" id="id_departamento">
                            <option value="">Seleccione un departamento</option>
                            @foreach($departamentos as $departamento)
                                <option value="{{ $departamento->id_departamento }}"
                                        {{ $empresa->ciudad && $empresa->ciudad->id_departamento == $departamento->id_departamento ? 'selected' : '' }}>
                                    {{ $departamento->nombre_departamento }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="id_ciudad" class="form-label required">Ciudad</label>
                        <select class="form-select @error('id_ciudad') is-invalid @enderror" 
                                id="id_ciudad" name="id_ciudad" required>
                            @foreach($ciudades as $ciudad)
                                <option value="{{ $ciudad->id_ciudad }}" 
                                        {{ old('id_ciudad', $empresa->id_ciudad) == $ciudad->id_ciudad ? 'selected' : '' }}>
                                    {{ $ciudad->nombre_city }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_ciudad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="id_estado" class="form-label required">Estado</label>
                        <select class="form-select @error('id_estado') is-invalid @enderror" 
                                id="id_estado" name="id_estado" required>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->id_estado }}" 
                                        {{ old('id_estado', $empresa->id_estado) == $estado->id_estado ? 'selected' : '' }}>
                                    {{ $estado->nombre_estado }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- BOTONES --}}
            <div class="form-actions">
                <a href="{{ route('superadmin.empresas.index') }}" class="btn btn-secondary">
                    <span class="material-symbols-outlined"><i class="fa fa-times" aria-hidden="true"></i></span>
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <span class="material-symbols-outlined"><i class="fa fa-save" aria-hidden="true"></i></span>
                    Actualizar Empresa
                </button>
            </div>

        </form>
    </div>

</div>

<script>
// Cargar ciudades según departamento seleccionado
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
            .catch(error => {
                console.error('Error:', error);
                ciudadSelect.innerHTML = '<option value="">Error al cargar ciudades</option>';
            });
    } else {
        ciudadSelect.innerHTML = '<option value="">Seleccione primero un departamento</option>';
    }
});

// Validación de documento representante
document.getElementById('doc_representante').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>



@endsection
