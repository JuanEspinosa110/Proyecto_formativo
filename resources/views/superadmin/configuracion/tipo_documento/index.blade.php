@extends('superadmin.layouts.admin')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ===================== CREAR ===================== --}}
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5>Crear Tipo de Documento</h5>
    </div>

    <div class="card-body">
        <form action="{{ route('superadmin.tipo_documento.store') }}" method="POST">
            @csrf

            <div class="row">

                <div class="col-md-4">
                    <label class="form-label">Nombre</label>
                    <input type="text"
                           name="nombre"
                           class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre') }}"
                           required>

                    @error('nombre')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Descripción</label>
                    <input type="text"
                           name="descripcion"
                           class="form-control @error('descripcion') is-invalid @enderror"
                           value="{{ old('descripcion') }}">

                    @error('descripcion')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="id_estado"
                            class="form-select @error('id_estado') is-invalid @enderror"
                            required>
                        <option value="">Seleccione...</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id_estado }}">
                                {{ $estado->nombre_estado }}
                            </option>
                        @endforeach
                    </select>

                    @error('id_estado')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button class="btn btn-success w-100">
                        Guardar
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- ===================== BUSCADOR ===================== --}}
<form method="GET" class="mb-3">
    <div class="row">
        <div class="col-md-4">
            <input type="text"
                   name="buscar"
                   value="{{ request('buscar') }}"
                   class="form-control"
                   placeholder="Buscar tipo de documento...">
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary">
                Buscar
            </button>
        </div>
    </div>
</form>

{{-- ===================== TABLA ===================== --}}
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
                    <th>Estado</th>
                    <th width="150">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($tipos as $tipo)
                <tr>
                    <td>{{ $tipo->id_tipo_documento }}</td>
                    <td>{{ $tipo->nombre }}</td>
                    <td>{{ $tipo->descripcion }}</td>
                    <td>{{ $tipo->estado->nombre_estado ?? 'N/A' }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#editarModal"
                                data-id="{{ $tipo->id_tipo_documento }}"
                                data-nombre="{{ $tipo->nombre }}"
                                data-descripcion="{{ $tipo->descripcion }}"
                                data-estado="{{ $tipo->id_estado }}">
                            Editar
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">
                        No hay registros.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $tipos->onEachSide(1)->links() }}
        </div>
    </div>
</div>

{{-- ===================== MODAL EDITAR ===================== --}}
<div class="modal fade" id="editarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formEditar" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Editar Tipo de Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text"
                               name="nombre"
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

                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select name="id_estado"
                                id="editEstado"
                                class="form-select"
                                required>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->id_estado }}">
                                    {{ $estado->nombre_estado }}
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

{{-- ===================== SCRIPT MODAL ===================== --}}
<script>
var editarModal = document.getElementById('editarModal')

editarModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget

    var id = button.getAttribute('data-id')
    var nombre = button.getAttribute('data-nombre')
    var descripcion = button.getAttribute('data-descripcion')
    var estado = button.getAttribute('data-estado')

    var form = document.getElementById('formEditar')
    form.action = '/superadmin/tipo-documento/' + id

    document.getElementById('editNombre').value = nombre
    document.getElementById('editDescripcion').value = descripcion
    document.getElementById('editEstado').value = estado
})
</script>

@endsection