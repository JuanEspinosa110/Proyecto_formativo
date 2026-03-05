@extends('superadmin.layouts.admin')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- FORM CREAR --}}
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5>Crear Estado</h5>
    </div>
    <div class="card-body">

        <form action="{{ route('superadmin.estados.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Nombre</label>
                    <input type="text"
                           name="nombre_estado"
                           class="form-control @error('nombre_estado') is-invalid @enderror"
                           value="{{ old('nombre_estado') }}"
                           required>

                    @error('nombre_estado')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Descripción</label>
                    <input type="text"
                           name="descripcion"
                           class="form-control"
                           value="{{ old('descripcion') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-success">
                        Guardar
                    </button>
                </div>
            </div>

        </form>

    </div>
</div>

{{-- BUSCADOR --}}
<form method="GET" class="mb-3">
    <div class="row">
        <div class="col-md-4">
            <input type="text"
                   name="buscar"
                   value="{{ request('buscar') }}"
                   class="form-control"
                   placeholder="Buscar estado...">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary">
                Buscar
            </button>
        </div>
    </div>
</form>

{{-- TABLA --}}
<div class="card shadow-sm">
    <div class="card-header">
        <h5>Listado</h5>
    </div>

    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th width="120">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($estados as $estado)
                <tr>
                    <td>{{ $estado->id_estado }}</td>
                    <td>{{ $estado->nombre_estado }}</td>
                    <td>{{ $estado->descripcion }}</td>
                    <td>
                        <button 
                            type="button"
                            class="btn btn-warning btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#editarModal"
                            data-id="{{ $estado->id_estado }}"
                            data-nombre="{{ $estado->nombre_estado }}"
                            data-descripcion="{{ $estado->descripcion }}">
                            Editar
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">No hay registros</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $estados->onEachSide(1)->links() }}
        </div>

    </div>
</div>

{{-- MODAL EDITAR --}}
<div class="modal fade" id="editarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formEditar" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Editar Estado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text"
                               name="nombre_estado"
                               id="editNombre"
                               class="form-control"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <input type="text"
                               name="descripcion"
                               id="editDescripcion"
                               class="form-control">
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

<script>
document.addEventListener('DOMContentLoaded', function () {

    var editarModal = document.getElementById('editarModal');

    editarModal.addEventListener('show.bs.modal', function (event) {

        var button = event.relatedTarget;

        var id = button.getAttribute('data-id');
        var nombre = button.getAttribute('data-nombre');
        var descripcion = button.getAttribute('data-descripcion');

        var form = document.getElementById('formEditar');
        form.action = "{{ url('superadmin/estados') }}/" + id;

        document.getElementById('editNombre').value = nombre;
        document.getElementById('editDescripcion').value = descripcion;
    });

});
</script>

@endsection