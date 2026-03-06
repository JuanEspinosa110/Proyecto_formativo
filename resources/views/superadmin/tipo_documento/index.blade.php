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

    <!-- Barra de Filtros -->
    <div class="card border-0 shadow-sm mb-4 rounded-3 pt-1">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('superadmin.tipos-documentos.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <span class="material-symbols-rounded">search</span>
                        </span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Buscar por nombre o descripción..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="id_estado" class="form-select bg-light">
                        <option value="">Todos los estados</option>
                        @foreach($estados as $est)
                            <option value="{{ $est->id_estado }}" {{ request('id_estado') == $est->id_estado ? 'selected' : '' }}>
                                {{ $est->nombre_estado }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 fw-semibold">Filtrar</button>
                </div>
                @if(request()->hasAny(['search', 'id_estado']))
                <div class="col-md-1">
                    <a href="{{ route('superadmin.tipos-documentos.index') }}" class="btn btn-light w-100 text-muted" title="Limpiar filtros">
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
                                            Usuario
                                        </span>
                                    @endif
                                    @if($tipo->requiere_placa)
                                        <span class="badge bg-purple-subtle text-purple border border-purple rounded-pill px-2 py-1 fs-xs w-fit-content">
                                            <span class="material-symbols-rounded align-middle me-1" style="font-size: 0.9rem;">directions_bus</span>
                                            Vehículo (Placa)
                                        </span>
                                    @endif
                                    @if(!$tipo->requiere_doc_usuario && !$tipo->requiere_placa)
                                        <span class="text-muted small italic">General</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusClass = match($tipo->id_estado) {
                                        1 => 'success',
                                        2 => 'danger',
                                        default => 'warning'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }} border border-{{ $statusClass }} rounded-pill px-3 py-1 fw-bold fs-xs text-uppercase">
                                    {{ optional($tipo->estado)->nombre_estado ?? '—' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-outline-primary btn-sm rounded-3 edit-tipo shadow-sm px-3 fw-semibold" 
                                            data-bs-toggle="modal" data-bs-target="#modalEditTipo"
                                            data-json="{{ json_encode($tipo) }}">
                                        <span class="material-symbols-rounded align-middle ps-1" style="font-size: 1.1rem;">edit_square</span>
                                        Editar
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm rounded-3 delete-tipo px-2" 
                                            data-id="{{ $tipo->id_tipo_documento }}"
                                            data-nombre="{{ $tipo->nombre }}">
                                        <span class="material-symbols-rounded align-middle" style="font-size: 1.1rem;">delete</span>
                                    </button>
                                </div>
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
                <small class="text-muted">Mostrando {{ $tipos->count() }} resultados</small>
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
            <form id="formCreateTipo" action="{{ route('superadmin.tipos-documentos.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4 pb-4">
                    <div id="create-errors-alert" class="alert alert-danger d-none py-2 small mb-4"></div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nombre del Documento <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control bg-light border-0 py-2" required placeholder="Ej: SOAT, Licencia de Conducción">
                            <div class="invalid-feedback feedback-nombre"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Descripción</label>
                            <textarea name="descripcion" class="form-control bg-light border-0 py-2" rows="2" placeholder="Opcional..."></textarea>
                            <div class="invalid-feedback feedback-descripcion"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase d-block mb-3">Requisitos de Asociación</label>
                            <div class="d-flex flex-wrap gap-4 px-2">
                                <div class="form-check form-switch custom-switch">
                                    <input class="form-check-input" type="checkbox" name="requiere_doc_usuario" id="checkUserCreate">
                                    <label class="form-check-label ms-1" for="checkUserCreate">Asociar a Usuario</label>
                                </div>
                                <div class="form-check form-switch custom-switch">
                                    <input class="form-check-input" type="checkbox" name="requiere_placa" id="checkPlateCreate">
                                    <label class="form-check-label ms-1" for="checkPlateCreate">Asociar a Vehículo (Placa)</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Estado Inicial <span class="text-danger">*</span></label>
                            <select name="id_estado" class="form-select bg-light border-0 py-2" required>
                                @foreach($estados as $est)
                                    <option value="{{ $est->id_estado }}">{{ $est->nombre_estado }}</option>
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
                    <div id="edit-errors-alert" class="alert alert-danger d-none py-2 small mb-4"></div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nombre del Documento <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control bg-light border-0 py-2" required>
                            <div class="invalid-feedback feedback-nombre"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Descripción</label>
                            <textarea name="descripcion" id="edit_descripcion" class="form-control bg-light border-0 py-2" rows="2"></textarea>
                            <div class="invalid-feedback feedback-descripcion"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase d-block mb-3">Requisitos de Asociación</label>
                            <div class="d-flex flex-wrap gap-4 px-2">
                                <div class="form-check form-switch custom-switch">
                                    <input class="form-check-input" type="checkbox" name="requiere_doc_usuario" id="edit_checkUser">
                                    <label class="form-check-label ms-1" for="edit_checkUser">Asociar a Usuario</label>
                                </div>
                                <div class="form-check form-switch custom-switch">
                                    <input class="form-check-input" type="checkbox" name="requiere_placa" id="edit_checkPlate">
                                    <label class="form-check-label ms-1" for="edit_checkPlate">Asociar a Vehículo (Placa)</label>
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
                const form = document.getElementById('formEditTipo');
                
                form.action = `/superadmin/tipos-documentos/${data.id_tipo_documento}`;
                form.querySelector('[name="nombre"]').value = data.nombre;
                form.querySelector('[name="descripcion"]').value = data.descripcion || '';
                form.querySelector('[name="requiere_doc_usuario"]').checked = data.requiere_doc_usuario == 1;
                form.querySelector('[name="requiere_placa"]').checked = data.requiere_placa == 1;
                form.querySelector('[name="id_estado"]').value = data.id_estado;
                
                clearValidation('formEditTipo', 'edit-errors-alert');
            });
        });

        // Manejar envío AJAX
        function handleAjaxAction(formId, alertId) {
            const form = document.getElementById(formId);
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = e.submitter;
                const originalText = btn.innerHTML;
                
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Enviando...';
                clearValidation(formId, alertId);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (response.ok) {
                        btn.classList.replace('btn-primary', 'btn-success');
                        btn.innerHTML = '<span class="material-symbols-rounded align-middle">done</span> Listo';
                        setTimeout(() => location.reload(), 800);
                    } else if (response.status === 422) {
                        const data = await response.json();
                        showErrors(formId, alertId, data.errors);
                    } else {
                        throw new Error('Error en el servidor');
                    }
                } catch (err) {
                    alert('Error técnico occurred.');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });
        }

        // Eliminar
        document.querySelectorAll('.delete-tipo').forEach(btn => {
            btn.addEventListener('click', async function() {
                const id = this.dataset.id;
                const nombre = this.dataset.nombre;
                
                if (confirm(`¿Está seguro que desea eliminar el tipo "${nombre}"? Esta acción no se puede deshacer.`)) {
                    try {
                        const response = await fetch(`/superadmin/tipos-documentos/${id}`, {
                            method: 'DELETE',
                            headers: { 
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        
                        const data = await response.json();
                        if (response.ok) {
                            location.reload();
                        } else {
                            alert(data.message || 'Error al eliminar');
                        }
                    } catch (err) {
                        alert('Error de conexión');
                    }
                }
            });
        });

        function clearValidation(fId, aId) {
            const fm = document.getElementById(fId);
            fm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.getElementById(aId).classList.add('d-none');
        }

        function showErrors(fId, aId, errors) {
            const fm = document.getElementById(fId);
            const al = document.getElementById(aId);
            let firstMsg = "";
            
            Object.keys(errors).forEach(key => {
                const inp = fm.querySelector(`[name="${key}"]`);
                if (inp) inp.classList.add('is-invalid');
                if (!firstMsg) firstMsg = errors[key][0];
            });
            
            al.innerHTML = `<span>${firstMsg}</span>`;
            al.classList.remove('d-none');
        }

        handleAjaxAction('formCreateTipo', 'create-errors-alert');
        handleAjaxAction('formEditTipo', 'edit-errors-alert');
    });
</script>

<style>
    .fs-xs { font-size: 0.7rem; }
    .w-fit-content { width: fit-content; }
    .text-purple { color: #6f42c1; }
    .bg-purple-subtle { background-color: #f1e6ff; }
    .border-purple { border-color: #d0bfff !important; }
    .custom-switch .form-check-input { width: 3em; height: 1.5em; cursor: pointer; }
    .table-hover tbody tr:hover { background-color: rgba(0,0,0,0.02) !important; }
</style>
@endpush
@endsection
