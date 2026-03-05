@extends('superadmin.layouts.admin')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ============================= --}}
{{-- CREAR CIUDAD --}}
{{-- ============================= --}}
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5>Crear Ciudad</h5>
    </div>
    <div class="card-body">

        <form action="{{ route('superadmin.ciudades.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Nombre Ciudad</label>
                    <input type="text"
                           name="nombre_city"
                           class="form-control @error('nombre_city') is-invalid @enderror"
                           value="{{ old('nombre_city') }}"
                           required>

                    @error('nombre_city')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Departamento</label>
                    <select name="id_departamento"
                            class="form-select @error('id_departamento') is-invalid @enderror"
                            required>

                        <option value="">Seleccione un departamento</option>

                        @foreach($departamentos as $dep)
                            <option value="{{ $dep->id_departamento }}"
                                {{ old('id_departamento') == $dep->id_departamento ? 'selected' : '' }}>
                                {{ $dep->nombre_departamento }}
                            </option>
                        @endforeach
                    </select>

                    @error('id_departamento')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-success w-100">
                        Guardar Ciudad
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

{{-- ============================= --}}
{{-- BUSCADOR --}}
{{-- ============================= --}}
<form method="GET" class="mb-3">
    <div class="row g-2">
        <div class="col-md-4">
            <input type="text"
                   name="buscar"
                   value="{{ request('buscar') }}"
                   class="form-control"
                   placeholder="Buscar ciudad o departamento...">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">
                Buscar
            </button>
        </div>
    </div>
</form>

{{-- ============================= --}}
{{-- TABLA --}}
{{-- ============================= --}}
<div class="card shadow-sm">
    <div class="card-header">
        <h5>Listado</h5>
    </div>

    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Ciudad</th>
                    <th>Departamento</th>
                    <th width="150">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ciudades as $ciudad)
                <tr>
                    <td>{{ $ciudad->id_ciudad }}</td>
                    <td>{{ $ciudad->nombre_city }}</td>
                    <td>{{ $ciudad->departamento?->nombre_departamento }}</td>
                    <td>
                        <button 
                            class="btn btn-warning btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#editarCiudadModal"
                            data-id="{{ $ciudad->id_ciudad }}"
                            data-nombre="{{ $ciudad->nombre_city }}"
                            data-departamento="{{ $ciudad->id_departamento }}">
                            Editar
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- PAGINACIÓN BONITA --}}
        <div class="d-flex justify-content-center">
            {{ $ciudades->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>

    </div>
</div>

{{-- ============================= --}}
{{-- MODAL EDITAR --}}
{{-- ============================= --}}
<div class="modal fade" id="editarCiudadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formEditarCiudad" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Editar Ciudad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nombre Ciudad</label>
                        <input type="text" name="nombre_city" id="editNombre"
                               class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Departamento</label>
                        <select name="id_departamento" id="editDepartamento"
                                class="form-select" required>

                            @foreach($departamentos as $dep)
                                <option value="{{ $dep->id_departamento }}">
                                    {{ $dep->nombre_departamento }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        Guardar Cambios
                    </button>
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    const editarModal = document.getElementById('editarCiudadModal');
    const form = document.getElementById('formEditarCiudad');

    editarModal.addEventListener('show.bs.modal', function(event) {

        const button = event.relatedTarget;

        const id = button.getAttribute('data-id');
        const nombre = button.getAttribute('data-nombre');
        const departamento = button.getAttribute('data-departamento');

        document.getElementById('editNombre').value = nombre;
        document.getElementById('editDepartamento').value = departamento;

        form.action = "{{ route('superadmin.ciudades.update', ':id') }}".replace(':id', id);
    });

});
</script>
@endpush