@extends('superadmin.layouts.admin')

<<<<<<< HEAD
@section('title', 'Tipos de Documentación — SIGU')

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
                <input type="hidden" name="id_estado" value="1">
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
>>>>>>> origin/develop
                </div>
            </form>
        </div>
    </div>
</div>

<<<<<<< HEAD
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
=======
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
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
                        <select name="id_estado" id="editEstado" class="form-select @error('id_estado') is-invalid @enderror" required>
                            @foreach($estados as $estado)
                            <option value="{{ $estado->id_estado }}">{{ $estado->nombre_estado }}</option>
                            @endforeach
                        </select>
                        @error('id_estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning px-4 text-dark">Guardar Cambios</button>
>>>>>>> origin/develop
                </div>
            </form>
        </div>
    </div>
</div>

<<<<<<< HEAD
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
            if(old('_method') == "PUT") {
               // Re-set action for edit modal if validation failed
               // This part is tricky with redirects, but usually Laravel preserves the URL
            }
            modal.show();
=======
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editarModal = document.getElementById('editarModal');
        if (editarModal) {
            editarModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;

                var id = button.getAttribute('data-id');
                var nombre = button.getAttribute('data-nombre');
                var descripcion = button.getAttribute('data-descripcion');
                var estado = button.getAttribute('data-id-estado'); // Capturar estado

                var form = document.getElementById('formEditar');
                form.action = "{{ url('superadmin/configuracion/tipo-documento') }}/" + id;

                document.getElementById('editNombre').value = nombre;
                document.getElementById('editDescripcion').value = descripcion;
                document.getElementById('editEstado').value = estado; // Asignar al select

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
>>>>>>> origin/develop
        @endif
    });
</script>

<<<<<<< HEAD
<style>
    .fs-xs { font-size: 0.75rem; }
    .w-fit-content { width: fit-content; }
    .text-purple { color: #6f42c1; }
    .bg-purple-subtle { background-color: #f1e6ff; }
    .border-purple { border-color: #d0bfff !important; }
    .custom-switch .form-check-input { width: 3em; height: 1.5em; cursor: pointer; }
    .table-hover tbody tr:hover { background-color: rgba(0,0,0,0.02) !important; }
</style>
@endpush
=======
>>>>>>> origin/develop
@endsection