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
                </div>
            </form>
        </div>
    </div>
</div>

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
        @endif
    });
</script>

@endsection