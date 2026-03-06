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
							@php
								$estado = $estados->firstWhere('id_estado', $u->id_estado);
							@endphp
							@if($estado && $estado->id_estado == 1)
								<span class="badge bg-success">{{ $estado->nombre_estado }}</span>
							@elseif($estado)
								<span class="badge bg-secondary">{{ $estado->nombre_estado }}</span>
							@else
								<span class="badge bg-secondary">Desconocido</span>
							@endif
						</td>
						<td class="d-flex gap-1">
							<button 
								type="button"
								class="btn btn-sm btn-outline-info"
								data-bs-toggle="modal"
								data-bs-target="#modalVerUsuario"
								data-doc="{{ $u->doc_usuario }}"
								data-primer-nombre="{{ $u->primer_nombre }}"
								data-segundo-nombre="{{ $u->segundo_nombre }}"
								data-primer-apellido="{{ $u->primer_apellido }}"
								data-segundo-apellido="{{ $u->segundo_apellido }}"
								data-correo="{{ $u->correo }}"
								data-telefono="{{ $u->telefono }}"
								data-rol="{{ $u->nombre_tipo }}"
								data-estado="{{ $u->nombre_estado }}"
								data-ciudad="{{ $u->nombre_city }}"
							>
								Ver
							</button>
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
								data-estado_id="{{ $u->id_estado }}"
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
                            <input type="text" name="doc_usuario" class="form-control" required minlength="7" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g,''); if(this.value.startsWith('0')) this.value = this.value.replace(/^0+/, '');" >
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
                            <input type="text" name="doc_usuario" id="editDoc" class="form-control" required minlength="7" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g,''); if(this.value.startsWith('0')) this.value = this.value.replace(/^0+/, '');" >
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
                            <input type="text" name="primer_nombre" id="editPrimerNombre" class="form-control" required maxlength="50" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g,'')" >
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Segundo Nombre</label>
                            <input type="text" name="segundo_nombre" id="editSegundoNombre" class="form-control" maxlength="50" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g,'')" >
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Primer Apellido</label>
                            <input type="text" name="primer_apellido" id="editPrimerApellido" class="form-control" required maxlength="50" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g,'')" >
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Segundo Apellido</label>
                            <input type="text" name="segundo_apellido" id="editSegundoApellido" class="form-control" maxlength="50" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g,'')" >
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo</label>
                            <input type="email" name="correo" id="editCorreo" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" id="editTelefono" class="form-control" required minlength="10" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g,'')"
 >
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <select name="id_estado" id="editEstado" class="form-select" required>
                                <option value="">Seleccione</option>
                                @foreach($estados as $estado)
                                    <option value="{{ $estado->id_estado }}">{{ $estado->nombre_estado }}</option>
                                @endforeach
                            </select>
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

<!-- Modal Ver Usuario -->
<div class="modal fade" id="modalVerUsuario" tabindex="-1" aria-labelledby="modalVerUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerUsuarioLabel">Información del Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Documento</label>
                        <span id="verDoc" class="form-control bg-light"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Rol</label>
                        <span id="verRol" class="form-control bg-light"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Primer Nombre</label>
                        <span id="verPrimerNombre" class="form-control bg-light"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Segundo Nombre</label>
                        <span id="verSegundoNombre" class="form-control bg-light"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Primer Apellido</label>
                        <span id="verPrimerApellido" class="form-control bg-light"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Segundo Apellido</label>
                        <span id="verSegundoApellido" class="form-control bg-light"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Correo</label>
                        <span id="verCorreo" class="form-control bg-light"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <span id="verTelefono" class="form-control bg-light"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ciudad</label>
                        <span id="verCiudad" class="form-control bg-light"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Estado</label>
                        <span id="verEstado" class="form-control bg-light"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>
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
        document.getElementById('editEstado').value = button.getAttribute('data-estado_id');
        var form = document.getElementById('formEditarUsuario');
        form.action = '/admin/usuarios/' + button.getAttribute('data-doc');
    });

    var modalVer = document.getElementById('modalVerUsuario');
    modalVer.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('verDoc').textContent = button.getAttribute('data-doc');
        document.getElementById('verPrimerNombre').textContent = button.getAttribute('data-primer-nombre');
        document.getElementById('verSegundoNombre').textContent = button.getAttribute('data-segundo-nombre');
        document.getElementById('verPrimerApellido').textContent = button.getAttribute('data-primer-apellido');
        document.getElementById('verSegundoApellido').textContent = button.getAttribute('data-segundo-apellido');
        document.getElementById('verCorreo').textContent = button.getAttribute('data-correo');
        document.getElementById('verTelefono').textContent = button.getAttribute('data-telefono');
        document.getElementById('verRol').textContent = button.getAttribute('data-rol');
        document.getElementById('verEstado').textContent = button.getAttribute('data-estado');
        document.getElementById('verCiudad').textContent = button.getAttribute('data-ciudad');
    });
});
</script>
@endsection
