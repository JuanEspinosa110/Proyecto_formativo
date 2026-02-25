@extends('superadmin.layouts.admin')

@section('content')

<div class="container-fluid px-4">
    <div class="row mt-4">
        <div class="col-12">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            {{-- HEADER Y BOTONES --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Tipos de Documento</h1>
                <div>
                    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#crearModal">
                        <i class="fas fa-plus me-1"></i> Crear Nuevo
                    </button>
                    <a href="{{ route('superadmin.configuracion.tipo-documento.export', ['buscar' => request('buscar')]) }}" class="btn btn-success shadow-sm ms-2">
                        <i class="fas fa-file-excel me-1"></i> Exportar Excel
                    </a>
                </div>
            </div>

            {{-- BUSCADOR --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('superadmin.configuracion.tipo-documento.index') }}" class="row g-3">
                        <div class="col-md-6 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control border-start-0" placeholder="Buscar por nombre...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100">Filtrar</button>
                        </div>
                        @if(request('buscar'))
                        <div class="col-md-2">
                            <a href="{{ route('superadmin.configuracion.tipo-documento.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
                        </div>
                        @endif
                    </form>
                </div>
            </div>

            {{-- TABLA --}}
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted text-uppercase small">
                                <tr>
                                    <th class="px-4 py-3" style="width: 100px;">ID</th>
                                    <th class="py-3">Nombre</th>
                                    <th class="py-3">Descripción</th>
                                    <th class="py-3 text-end px-4" style="width: 150px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse($tipos as $tipo)
                                <tr>
                                    <td class="px-4 fw-bold text-dark">{{ $tipo->id_tipo_documento }}</td>
                                    <td>{{ $tipo->nombre }}</td>
                                    <td class="text-muted">{{ $tipo->descripcion ?: 'Sin descripción' }}</td>
                                    <td class="text-end px-4">
                                        <button 
                                            type="button"
                                            class="btn btn-outline-warning btn-sm border-0"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editarModal"
                                            data-id="{{ $tipo->id_tipo_documento }}"
                                            data-nombre="{{ $tipo->nombre }}"
                                            data-descripcion="{{ $tipo->descripcion }}">
                                            <i class="fas fa-edit me-1"></i> Editar
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fas fa-folder-open d-block mb-2 fa-2x"></i>
                                        No se encontraron registros.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($tipos->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    <div class="d-flex justify-content-center">
                        {{ $tipos->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- MODAL CREAR --}}
<div class="modal fade" id="crearModal" tabindex="-1" aria-labelledby="crearModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="crearModalLabel">Nuevo Tipo de Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('superadmin.configuracion.tipo-documento.store') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" placeholder="Ej: Cédula de Ciudadanía" required value="{{ old('nombre') }}">
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="3" placeholder="Descripción opcional">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 text-white">Guardar Registro</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDITAR --}}
<div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="editarModalLabel">Editar Registro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditar" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="editNombre" class="form-control @error('nombre') is-invalid @enderror" required value="{{ old('nombre') }}">
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" id="editDescripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="2">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning px-4 text-dark">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var editarModal = document.getElementById('editarModal');
    if (editarModal) {
        editarModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            if (!button) return; // Prevent error if triggered manually
            
            var id = button.getAttribute('data-id');
            var nombre = button.getAttribute('data-nombre');
            var descripcion = button.getAttribute('data-descripcion');

            var form = document.getElementById('formEditar');
            form.action = "{{ url('superadmin/configuracion/tipo-documento') }}/" + id;

            document.getElementById('editNombre').value = nombre;
            document.getElementById('editDescripcion').value = descripcion;
            
            // Save ID for reopening on validation error
            sessionStorage.setItem('last_edit_id', id);
        });
    }

    // Redirect error to correct modal
    @if($errors->any())
        @if(old('_method') == 'PUT')
            var lastEditId = sessionStorage.getItem('last_edit_id');
            if (lastEditId) {
                var form = document.getElementById('formEditar');
                form.action = "{{ url('superadmin/configuracion/tipo-documento') }}/" + lastEditId;
                var myModal = new bootstrap.Modal(document.getElementById('editarModal'));
                myModal.show();
            }
        @else
            var myModal = new bootstrap.Modal(document.getElementById('crearModal'));
            myModal.show();
        @endif
    @endif
});
</script>

@endsection
