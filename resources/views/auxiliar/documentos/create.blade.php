@extends('auxiliar.layouts.app')

@section('title', 'Cargar Documento — Auxiliar')

@section('content')
<div class="container-fluid pt-0 pb-4">
    
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold">Cargar Documento</h1>
            <p class="text-muted small mb-0">Suba un nuevo documento asociado a un vehículo o conductor.</p>
        </div>
        <a href="{{ route('auxiliar.documentos.index') }}" class="btn btn-light btn-sm d-flex align-items-center gap-2 px-3 rounded-pill border shadow-sm">
            <span class="material-symbols-rounded fs-5">arrow_back</span> Volver
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger shadow-sm py-2 mb-4">{{ session('error') }}</div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <form method="POST" action="{{ route('auxiliar.documentos.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row g-3">
                        <!-- Nombre -->
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nombre del Documento <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" required placeholder="Ej: SOAT 2026, Revisión Técnico Mecánica" value="{{ old('nombre') }}">
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Tipo -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Tipo de Documento <span class="text-danger">*</span></label>
                            <select name="id_tipo_documento" id="id_tipo_documento" class="form-select @error('id_tipo_documento') is-invalid @enderror" required>
                                <option value="" selected disabled>Seleccione...</option>
                                @foreach($tiposDocumento as $t)
                                    <option value="{{ $t->id_tipo_documento }}" {{ old('id_tipo_documento') == $t->id_tipo_documento ? 'selected' : '' }} data-requiere-placa="{{ $t->requiere_placa }}" data-requiere-usuario="{{ $t->requiere_doc_usuario }}">
                                        {{ $t->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_tipo_documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Estado <span class="text-danger">*</span></label>
                            <select name="id_estado" class="form-select @error('id_estado') is-invalid @enderror" required>
                                @foreach($estados as $e)
                                    <option value="{{ $e->id_estado }}" {{ old('id_estado') == $e->id_estado ? 'selected' : '' }}>
                                        {{ $e->nombre_estado }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Fechas -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Fecha Expedición <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_expedicion" class="form-control @error('fecha_expedicion') is-invalid @enderror" required value="{{ old('fecha_expedicion') }}">
                            @error('fecha_expedicion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Fecha Vencimiento <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror" required value="{{ old('fecha_vencimiento') }}">
                            @error('fecha_vencimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Asociaciones Relacionales Condicionales -->
                        <div class="col-md-6" id="wrapper_placa">
                            <label class="form-label small fw-bold text-muted text-uppercase">Vehículo (Placa)</label>
                            <select name="placa" class="form-select @error('placa') is-invalid @enderror">
                                <option value="">Ninguno</option>
                                @foreach($buses as $b)
                                    <option value="{{ $b->placa }}" {{ old('placa') == $b->placa ? 'selected' : '' }}>
                                        {{ $b->placa }}
                                    </option>
                                @endforeach
                            </select>
                            @error('placa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6" id="wrapper_doc_usuario">
                            <label class="form-label small fw-bold text-muted text-uppercase">Conductor/Usuario</label>
                            <select name="doc_usuario" class="form-select @error('doc_usuario') is-invalid @enderror">
                                <option value="">Ninguno</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->doc_usuario }}" {{ old('doc_usuario') == $u->doc_usuario ? 'selected' : '' }}>
                                        {{ $u->primer_nombre }} {{ $u->primer_apellido }} ({{ $u->doc_usuario }})
                                    </option>
                                @endforeach
                            </select>
                            @error('doc_usuario') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Archivo -->
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Archivo Documento <span class="text-danger">*</span></label>
                            <input type="file" name="archivo" class="form-control @error('archivo') is-invalid @enderror" required accept=".pdf,.png,.jpg,.jpeg">
                            @error('archivo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 mt-4 d-grid">
                            <button type="submit" class="btn btn-primary fw-bold rounded-pill shadow-sm py-2">
                                <span class="material-symbols-rounded fs-5 align-middle me-1">upload</span> Cargar y Registrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectTipo = document.getElementById('id_tipo_documento');
    const wrapPlaca = document.getElementById('wrapper_placa');
    const wrapDoc = document.getElementById('wrapper_doc_usuario');

    function toggleFields() {
        const selected = selectTipo.options[selectTipo.selectedIndex];
        if (selected) {
            const reqPlaca = selected.getAttribute('data-requiere-placa') == 1 || selected.getAttribute('data-requiere-placa') === 'true';
            const reqDoc = selected.getAttribute('data-requiere-usuario') == 1 || selected.getAttribute('data-requiere-usuario') === 'true';

            // No los ocultamos del todo, solo les ponemos un indicador visual o habilitamos
            // Pero para UX limpia, podemos alternar disabled o required
            const inputPlaca = wrapPlaca.querySelector('select');
            const inputDoc = wrapDoc.querySelector('select');

            if (reqPlaca) {
                inputPlaca.setAttribute('required', 'required');
                wrapPlaca.querySelector('label').innerHTML = 'Vehículo (Placa) <span class="text-danger">*</span>';
            } else {
                inputPlaca.removeAttribute('required');
                wrapPlaca.querySelector('label').innerHTML = 'Vehículo (Placa)';
            }

            if (reqDoc) {
                inputDoc.setAttribute('required', 'required');
                wrapDoc.querySelector('label').innerHTML = 'Conductor/Usuario <span class="text-danger">*</span>';
            } else {
                inputDoc.removeAttribute('required');
                wrapDoc.querySelector('label').innerHTML = 'Conductor/Usuario';
            }
        }
    }

    selectTipo.addEventListener('change', toggleFields);
    toggleFields(); // Init
});
</script>
@endpush

@endsection
