@extends('superadmin.layouts.admin')

@section('title', 'Tipos de Mantenimiento')

@section('content')

<div class="container-fluid">

    {{-- Mensaje éxito --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Listado de Tipos de Mantenimientos</h5>

            <div class="d-flex gap-2">

                {{-- Botón Exportar --}}
                <a href="{{ route('superadmin.tipo_mantenimiento.export.excel', request()->query()) }}"
                   class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-excel"></i> Excel
                </a>

                {{-- Botón Crear --}}
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="bi bi-plus-circle"></i> Nuevo
                </button>

            </div>
        </div>

        <div class="card-body">

            {{-- Buscador --}}
            <form method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text"
                           name="buscar"
                           value="{{ request('buscar') }}"
                           class="form-control"
                           placeholder="Buscar tipo de mantenimiento...">

                    <button class="btn btn-outline-secondary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            {{-- Tabla --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th width="120">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tipos as $tipo)
                            <tr>
                                <td>{{ $tipo->id_tipo_mantenimiento }}</td>
                                <td>{{ $tipo->nombre }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editarModal{{ $tipo->id_tipo_mantenimiento }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>

                            {{-- Modal Editar --}}
                            <div class="modal fade"
                                 id="editarModal{{ $tipo->id_tipo_mantenimiento }}"
                                 tabindex="-1">

                                <div class="modal-dialog">
                                    <div class="modal-content">

                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Editar Tipo de Mantenimiento</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <form method="POST"
                                              action="{{ route('superadmin.tipo_mantenimiento.update', $tipo->id_tipo_mantenimiento) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-body">

                                                <div class="mb-3">
                                                    <label class="form-label">Nombre</label>
                                                    <input type="text"
                                                           name="nombre"
                                                           class="form-control"
                                                           value="{{ $tipo->nombre }}"
                                                           required>
                                                </div>

                                            </div>

                                            <div class="modal-footer">
                                                <button class="btn btn-success">
                                                    Guardar Cambios
                                                </button>
                                                <button type="button"
                                                        class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                    Cancelar
                                                </button>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No hay registros</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="d-flex justify-content-end">
                {{ $tipos->links() }}
            </div>

        </div>
    </div>
</div>

{{-- Modal Crear --}}
<div class="modal fade" id="crearModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Nuevo Tipo de Mantenimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST"
                  action="{{ route('superadmin.tipo_mantenimiento.store') }}">
                @csrf

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text"
                            name="nombre"
                            value="{{ old('nombre') }}"
                            class="form-control @error('nombre') is-invalid @enderror">

                        @error('nombre')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Guardar</button>
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var myModal = new bootstrap.Modal(document.getElementById('modalCrear'));
        myModal.show();
    });
</script>
@endif
@endsection