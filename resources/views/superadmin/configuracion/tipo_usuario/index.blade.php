@extends('superadmin.layouts.admin')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5>Crear Tipo de Usuario</h5>
    </div>
    <div class="card-body">

        <form action="{{ route('superadmin.tipo_usuario.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text"
                           name="nombre_tipo"
                           class="form-control @error('nombre_tipo') is-invalid @enderror"
                           value="{{ old('nombre_tipo') }}"
                           required>

                    @error('nombre_tipo')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-success">
                        Guardar
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

<form method="GET" class="mb-3">
    <div class="row">
        <div class="col-md-4">
            <input type="text"
                   name="buscar"
                   value="{{ request('buscar') }}"
                   class="form-control"
                   placeholder="Buscar tipo de usuario...">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary">
                Buscar
            </button>
        </div>
    </div>
</form>

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
                    <th width="150">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tipos as $tipo)
                <tr>
                    <td>{{ $tipo->id_tipo_usuario }}</td>
                    <td>{{ $tipo->nombre_tipo }}</td>
                    <td>
                        <button 
                            class="btn btn-warning btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#editarModal"
                            data-id="{{ $tipo->id_tipo_usuario }}"
                            data-nombre="{{ $tipo->nombre_tipo }}">
                            Editar
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $tipos->onEachSide(1)->links() }}
        </div>

    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="editarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formEditar" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Editar Tipo de Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text"
                               name="nombre_tipo"
                               id="editNombre"
                               class="form-control"
                               required>
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
        var button = event.relatedTarget; // Botón que activó el modal
        
        // Extraer info de los atributos data-*
        var id = button.getAttribute('data-id');
        var nombre = button.getAttribute('data-nombre');

        // Ajustar la acción del formulario
        var form = document.getElementById('formEditar');
        // Asegúrate de que la URL coincida con la definida en Web.php
        form.action = "{{ url('superadmin/tipo-usuario') }}/" + id;

        // Rellenar el input
        document.getElementById('editNombre').value = nombre;
    });
});
</script>

@endsection