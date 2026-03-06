@extends('admin.layouts.app')

@section('title', 'Usuarios — SIGU')

@section('content')
<div class="sigu-page-hd d-flex justify-content-between align-items-center">
	<div>
		<h1 class="sigu-page-title">Usuarios</h1>
		<p class="sigu-page-sub">Usuarios de tu empresa</p>
	</div>

	<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
		<span class="material-symbols-rounded">person_add</span>
		Nuevo Usuario
	</button>
</div>

<div class="card" style="padding:1rem; margin-top:1rem;">
	<form method="GET" action="" class="d-flex gap-2 align-items-center mb-3 admin-filter">
		<label class="mb-0">Filtrar por rol:</label>
		<select name="role" class="form-select form-select-sm" style="max-width:220px">
			<option value="">Todos</option>
			@foreach($roles as $r)
				<option value="{{ $r->id_tipo_usuario }}" {{ (string)($selectedRole ?? '') === (string)$r->id_tipo_usuario ? 'selected' : '' }}>
					{{ $r->nombre_tipo }}
				</option>
			@endforeach
		</select>
		<button class="btn btn-primary btn-sm">Filtrar</button>
	</form>

	<div class="d-flex justify-content-between align-items-center mb-3">
		<div>
			<h1 class="sigu-page-title">Usuarios</h1>
			<p class="sigu-page-sub">Usuarios de tu empresa</p>
		</div>

	</div>

	<div class="table-responsive">
		<table class="table table-striped table-sm">
			<thead>
				<tr>
					<th>Documento</th>
					<th>Nombre</th>
					<th>Correo</th>
					<th>Teléfono</th>
					<th>Rol</th>
					<th>Estado</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				@foreach($usuarios as $u)
					<tr>
						<td>{{ $u->doc_usuario }}</td>
						<td>{{ $u->primer_nombre }} {{ $u->primer_apellido }}</td>
						<td>{{ $u->correo }}</td>
						<td>{{ $u->telefono }}</td>
						<td>{{ $u->nombre_tipo ?? 'N/A' }}</td>
						<td>
							@if($u->nombre_estado == 'Activo')
								<span class="badge bg-success">Activo</span>
							@else
								<span class="badge bg-secondary">Inactivo</span>
							@endif
						</td>
						<td class="d-flex gap-1">

							<a href="{{ url('admin/usuarios/'.$u->doc_usuario) }}" 
							class="btn btn-sm btn-outline-secondary">
								Ver
							</a>

							<!-- Botón Editar (dentro de la tabla) -->
							<button 
								type="button"
								class="btn btn-sm btn-outline-primary"
								data-bs-toggle="modal"
								data-bs-target="#modalEditarUsuario"
								data-doc="{{ $u->doc_usuario }}"
								data-primer-nombre="{{ $u->primer_nombre }}"
								data-segundo-nombre="{{ $u->segundo_nombre }}"
								data-primer-apellido="{{ $u->primer_apellido }}"
								data-segundo-apellido="{{ $u->segundo_apellido }}"
								data-correo="{{ $u->correo }}"
								data-telefono="{{ $u->telefono }}"
								data-rol="{{ $u->id_tipo_usuario }}"
							>
								Editar
							</button>

						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	<div class="mt-2">{{ $usuarios->links() }}</div>
</div>


<!-- Modal Crear Usuario -->
<div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-labelledby="modalCrearUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="POST" action="{{ route('admin.usuarios.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="modalCrearUsuarioLabel">Crear Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <!-- Documento -->
                        <div class="col-md-6">
                            <label class="form-label">Documento</label>
                            <input type="text" name="doc_usuario" class="form-control" required minlength="7" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g,'')" >
                        </div>

                        <!-- Rol -->
                        <div class="col-md-6">
                            <label class="form-label">Rol</label>
                            <select name="id_tipo_usuario" class="form-select" required>
                                <option value="">Seleccione</option>

                                @foreach($roles as $rol)
                                    @if(in_array($rol->id_tipo_usuario,[1,3,4]))
                                        <option value="{{ $rol->id_tipo_usuario }}">
                                            {{ $rol->nombre_tipo }}
                                        </option>
                                    @endif
                                @endforeach

                            </select>
                        </div>

                        <!-- Primer nombre -->
                        <div class="col-md-6">
                            <label class="form-label">Primer Nombre</label>
                            <input type="text" name="primer_nombre" class="form-control" required maxlength="50" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g,'')" >
                        </div>

                        <!-- Segundo nombre -->
                        <div class="col-md-6">
                            <label class="form-label">Segundo Nombre</label>
                            <input type="text" name="segundo_nombre" class="form-control" maxlength="50" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g,'')" >
                        </div>

                        <!-- Primer apellido -->
                        <div class="col-md-6">
                            <label class="form-label">Primer Apellido</label>
                            <input type="text" name="primer_apellido" class="form-control" required maxlength="50" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g,'')" >
                        </div>

                        <!-- Segundo apellido -->
                        <div class="col-md-6">
                            <label class="form-label">Segundo Apellido</label>
                            <input type="text" name="segundo_apellido" class="form-control" maxlength="50" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g,'')" >
                        </div>

                        <!-- Correo -->
                        <div class="col-md-6">
                            <label class="form-label">Correo</label>
                            <input type="email" name="correo" class="form-control" required>
                        </div>

                        <!-- Teléfono -->
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" required minlength="10" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g,'')"
 >
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Crear Usuario
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


<!-- Modal Editar Usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="formEditarUsuario">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarUsuarioLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Documento</label>
                            <input type="text" name="doc_usuario" id="editDoc" class="form-control" required minlength="7" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g,'')">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rol</label>
                            <select name="id_tipo_usuario" id="editRol" class="form-select" required>
                                <option value="">Seleccione</option>
                                @foreach($roles as $rol)
                                    @if(in_array($rol->id_tipo_usuario,[1,3,4]))
                                        <option value="{{ $rol->id_tipo_usuario }}">
                                            {{ $rol->nombre_tipo }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Primer Nombre</label>
                            <input type="text" name="primer_nombre" id="editPrimerNombre" class="form-control" required maxlength="50" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g,'')">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Segundo Nombre</label>
                            <input type="text" name="segundo_nombre" id="editSegundoNombre" class="form-control" maxlength="50" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g,'')">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Primer Apellido</label>
                            <input type="text" name="primer_apellido" id="editPrimerApellido" class="form-control" required maxlength="50" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g,'')">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Segundo Apellido</label>
                            <input type="text" name="segundo_apellido" id="editSegundoApellido" class="form-control" maxlength="50" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g,'')">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo</label>
                            <input type="email" name="correo" id="editCorreo" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" id="editTelefono" class="form-control" required minlength="10" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g,'')">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEditar = document.getElementById('modalEditarUsuario');
    modalEditar.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('editDoc').value = button.getAttribute('data-doc');
        document.getElementById('editPrimerNombre').value = button.getAttribute('data-primer-nombre');
        document.getElementById('editSegundoNombre').value = button.getAttribute('data-segundo-nombre');
        document.getElementById('editPrimerApellido').value = button.getAttribute('data-primer-apellido');
        document.getElementById('editSegundoApellido').value = button.getAttribute('data-segundo-apellido');
        document.getElementById('editCorreo').value = button.getAttribute('data-correo');
        document.getElementById('editTelefono').value = button.getAttribute('data-telefono');
        document.getElementById('editRol').value = button.getAttribute('data-rol');
        var form = document.getElementById('formEditarUsuario');
        form.action = '/admin/usuarios/' + button.getAttribute('data-doc');
    });
});
</script>
@endsection
