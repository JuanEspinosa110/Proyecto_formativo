@extends('superadmin.layouts.admin')


@section('title', 'Tipos de Documentación — SIGU')

@section('content')
<div class="container-fluid py-4">
    <!-- Header de Página -->
    <div class="d-flex align-items-center justify-content-between mb-4 mt-2 px-1">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold">Tipos de Documentación</h1>
            <p class="text-muted small mb-0">Gestión de requisitos documentales para usuarios y vehículos</p>
        </div>
        <button class="btn btn-primary d-flex align-items-center gap-2 px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateTipo">
            <span class="material-symbols-rounded">add_circle</span>
            Nuevo Tipo
        </button>
    </div>

    <!-- Mensajes de Estado -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <span class="material-symbols-rounded align-middle me-2">check_circle</span>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    @endif

    <!-- Barra de Filtros -->
    <div class="card border-0 shadow-sm mb-4 rounded-3 pt-1">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('superadmin.configuracion.tipo-documento.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <span class="material-symbols-rounded">search</span>
                        </span>
                        <input type="text" name="buscar" class="form-control bg-light border-start-0" placeholder="Buscar por nombre..." value="{{ request('buscar') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-dark w-100 fw-semibold">Buscar</button>
                </div>
                @if(request('buscar'))
                <div class="col-md-1">
                    <a href="{{ route('superadmin.configuracion.tipo-documento.index') }}" class="btn btn-light w-100 text-muted" title="Limpiar filtro">
                        <span class="material-symbols-rounded">filter_list_off</span>
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted border-0">ID</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Nombre</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Requisitos</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Estado</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tipos as $tipo)
                    <tr class="border-top">
                        <td class="ps-4 text-muted small fw-bold">#{{ $tipo->id_tipo_documento }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $tipo->nombre }}</div>
                            <div class="text-muted small text-truncate" style="max-width: 250px;">{{ $tipo->descripcion ?? 'Sin descripción' }}</div>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @if($tipo->requiere_doc_usuario)
                                <span class="badge bg-info-subtle text-info border border-info rounded-pill px-2 py-1 fs-xs w-fit-content">
                                    <span class="material-symbols-rounded align-middle me-1" style="font-size: 0.9rem;">person</span>
                                    Requiere Doc. Usuario
                                </span>
                                @endif
                                @if($tipo->requiere_placa)
                                <span class="badge bg-purple-subtle text-purple border border-purple rounded-pill px-2 py-1 fs-xs w-fit-content">
                                    <span class="material-symbols-rounded align-middle me-1" style="font-size: 0.9rem;">directions_bus</span>
                                    Requiere Placa
                                </span>
                                @endif
                                @if(!$tipo->requiere_doc_usuario && !$tipo->requiere_placa)
                                <span class="text-muted small italic text-uppercase fw-bold opacity-50 pe-3">No requiere asociación</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @php
                            $statusClass = match($tipo->id_estado) {
                            1 => 'success',
                            2 => 'danger',
                            3 => 'warning',
                            default => 'secondary'
                            };
                            @endphp
                            <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }} border border-{{ $statusClass }} rounded-pill px-3 py-1 fw-bold fs-xs text-uppercase">
                                {{ optional($tipo->estado)->nombre_estado ?? 'Desconocido' }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-outline-primary btn-sm rounded-3 edit-tipo shadow-sm px-3 fw-semibold"
                                data-bs-toggle="modal" data-bs-target="#modalEditTipo"
                                data-json="{{ json_encode($tipo) }}">
                                <span class="material-symbols-rounded align-middle ps-1" style="font-size: 1.1rem;">edit_square</span>
                                Editar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted bg-light bg-opacity-50">
                            <span class="material-symbols-rounded display-4 opacity-25">description</span>
                            <p class="mt-2 fw-medium mb-0">No se encontraron tipos de documentos.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tipos->hasPages())
        <div class="p-3 border-top bg-white">
            <div class="d-flex justify-content-between align-items-center px-2">
                <small class="text-muted">Mostrando {{ $tipos->count() }} de {{ $tipos->total() }} resultados</small>
                <div>{{ $tipos->links() }}</div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal CREAR -->
<div class="modal fade" id="modalCreateTipo" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark">
                    <span class="material-symbols-rounded align-middle me-2 text-primary">add_circle</span>
                    Nuevo Tipo de Documento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('superadmin.configuracion.tipo-documento.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nombre del Documento <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control bg-light border-0 py-2 @error('nombre') is-invalid @enderror" required value="{{ old('nombre') }}" placeholder="Ej: SOAT, Licencia de Conducción">
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Descripción</label>
                            <textarea name="descripcion" class="form-control bg-light border-0 py-2" rows="2" placeholder="Opcional...">{{ old('descripcion') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase d-block mb-3">Asociación Requerida</label>
                            <div class="d-flex flex-wrap gap-4 px-2">
                                <div class="form-check form-switch custom-switch">
                                    <input class="form-check-input" type="checkbox" name="requiere_doc_usuario" id="checkUserCreate" value="1" {{ old('requiere_doc_usuario') ? 'checked' : '' }}>
                                    <label class="form-check-label ms-1" for="checkUserCreate">Requiere Doc. Usuario</label>
                                </div>
                                <div class="form-check form-switch custom-switch">
                                    <input class="form-check-input" type="checkbox" name="requiere_placa" id="checkPlateCreate" value="1" {{ old('requiere_placa') ? 'checked' : '' }}>
                                    <label class="form-check-label ms-1" for="checkPlateCreate">Requiere Placa</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Estado Inicial <span class="text-danger">*</span></label>
                            <select name="id_estado" class="form-select bg-light border-0 py-2" required>
                                @foreach($estados as $est)
                                <option value="{{ $est->id_estado }}" {{ old('id_estado') == $est->id_estado ? 'selected' : '' }}>{{ $est->nombre_estado }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 d-flex gap-2">
                    <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold flex-fill shadow-sm">Guardar Tipo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EDITAR -->
<div class="modal fade" id="modalEditTipo" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark">
                    <span class="material-symbols-rounded align-middle me-2 text-primary">edit_square</span>
                    Editar Tipo de Documento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditTipo" method="POST">
                @csrf @method('PUT')
                <div class="modal-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nombre del Documento <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control bg-light border-0 py-2 @error('nombre') is-invalid @enderror" required>
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Descripción</label>
                            <textarea name="descripcion" id="edit_descripcion" class="form-control bg-light border-0 py-2" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase d-block mb-3">Asociación Requerida</label>
                            <div class="d-flex flex-wrap gap-4 px-2">
                                <div class="form-check form-switch custom-switch">
                                    <input class="form-check-input" type="checkbox" name="requiere_doc_usuario" id="edit_checkUser">
                                    <label class="form-check-label ms-1" for="edit_checkUser">Requiere Doc. Usuario</label>
                                </div>
                                <div class="form-check form-switch custom-switch">
                                    <input class="form-check-input" type="checkbox" name="requiere_placa" id="edit_checkPlate">
                                    <label class="form-check-label ms-1" for="edit_checkPlate">Requiere Placa</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Estado <span class="text-danger">*</span></label>
                            <select name="id_estado" id="edit_id_estado" class="form-select bg-light border-0 py-2" required>
                                @foreach($estados as $est)
                                <option value="{{ $est->id_estado }}">{{ $est->nombre_estado }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 d-flex gap-2">
                    <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold flex-fill shadow-sm">Actualizar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Llenar Modal de Edición
        document.querySelectorAll('.edit-tipo').forEach(btn => {
            btn.addEventListener('click', function() {
                const data = JSON.parse(this.dataset.json);
                const form = document.getElementById('formEditRuta'); // Nota: el form tiene ID raro en el controller del user? no, arreglemoslo
                const formActual = document.getElementById('formEditTipo');

                formActual.action = `/superadmin/tipo-documento/${data.id_tipo_documento}`;
                formActual.querySelector('[name="nombre"]').value = data.nombre;
                formActual.querySelector('[name="descripcion"]').value = data.descripcion || '';
                formActual.querySelector('[name="requiere_doc_usuario"]').checked = data.requiere_doc_usuario == 1;
                formActual.querySelector('[name="requiere_placa"]').checked = data.requiere_placa == 1;
                formActual.querySelector('[name="id_estado"]').value = data.id_estado;
            });
        });

        @if($errors->any())
        const lastModal = '{{ old("_method") == "PUT" ? "#modalEditTipo" : "#modalCreateTipo" }}';
        const modal = new bootstrap.Modal(document.querySelector(lastModal));
        if (old('_method') == "PUT") {
            // Re-set action for edit modal if validation failed
            // This part is tricky with redirects, but usually Laravel preserves the URL
        }
        modal.show();
        @endif
    });
</script>

<style>
    .fs-xs {
        font-size: 0.75rem;
    }

    .w-fit-content {
        width: fit-content;
    }

    .text-purple {
        color: #6f42c1;
    }

    .bg-purple-subtle {
        background-color: #f1e6ff;
    }

    .border-purple {
        border-color: #d0bfff !important;
    }

    .custom-switch .form-check-input {
        width: 3em;
        height: 1.5em;
        cursor: pointer;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02) !important;
    }
</style>
@endpush
@endsection