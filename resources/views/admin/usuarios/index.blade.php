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

							<a href="{{ url('admin/usuarios/'.$u->doc_usuario.'/editar') }}" 
							class="btn btn-sm btn-outline-primary">
								Editar
							</a>

							<form action="{{ route('admin.usuarios.inactivar', $u->doc_usuario) }}" method="POST">
								@csrf
								@method('PATCH')
								<button class="btn btn-sm btn-outline-danger"
									onclick="return confirm('¿Inactivar este usuario?')">
									Inactivar
								</button>
							</form>

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
                            <input type="text" name="doc_usuario" class="form-control" required oninput="this.value = this.value.replace(/[^0-9]/g,'')" >
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
                            <input type="text" name="primer_nombre" class="form-control" required onkeydown="return event.key !== ' '" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,'')">
                        </div>

                        <!-- Segundo nombre -->
                        <div class="col-md-6">
                            <label class="form-label">Segundo Nombre</label>
                            <input type="text" name="segundo_nombre" class="form-control" onkeydown="return event.key !== ' '" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,'')" >
                        </div>

                        <!-- Primer apellido -->
                        <div class="col-md-6">
                            <label class="form-label">Primer Apellido</label>
                            <input type="text" name="primer_apellido" class="form-control" required onkeydown="return event.key !== ' '" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,'')" >
                        </div>

                        <!-- Segundo apellido -->
                        <div class="col-md-6">
                            <label class="form-label">Segundo Apellido</label>
                            <input type="text" name="segundo_apellido" class="form-control" onkeydown="return event.key !== ' '" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,'')" >
                        </div>

                        <!-- Correo -->
                        <div class="col-md-6">
                            <label class="form-label">Correo</label>
                            <input type="email" name="correo" class="form-control" required>
                        </div>

                        <!-- Teléfono -->
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" required maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g,'')"
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
@endsection
    