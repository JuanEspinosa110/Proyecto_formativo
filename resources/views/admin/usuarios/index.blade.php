@extends('admin.layouts.app')

@section('title', 'Usuarios — SIGU')

@section('content')
<div class="sigu-page-hd">
	<div>
		<h1 class="sigu-page-title">Usuarios</h1>
		<p class="sigu-page-sub">Usuarios de tu empresa</p>
	</div>
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
						<td>{{ $u->nombre_estado ?? 'N/A' }}</td>
						<td>
							<a href="{{ url('admin/usuarios/'.$u->doc_usuario) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
							<a href="{{ url('admin/usuarios/'.$u->doc_usuario.'/editar') }}" class="btn btn-sm btn-outline-primary">Editar</a>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	<div class="mt-2">{{ $usuarios->links() }}</div>
</div>

@endsection
    