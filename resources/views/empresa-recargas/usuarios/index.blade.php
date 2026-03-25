@extends('empresa-recargas.layouts.app')

@section('title', 'Usuarios de la Empresa')

@section('content')
<div class="admin-dashboard sigu-fade">
    <div class="sigu-page-hd d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="sigu-page-title">Usuarios</h1>
            <p class="sigu-page-sub">Gestiona el personal de {{ Auth::user()->empresa?->nombre_empresa ?? 'tu empresa' }}</p>
        </div>
        <div>
            <a href="{{ route('gestor-recargas.usuarios.create') }}" class="btn btn-primary d-flex align-items-center gap-2 rounded-3">
                <span class="material-symbols-rounded">person_add</span>
                Nuevo Usuario
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Documento</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $user)
                        <tr>
                            <td class="ps-4 fw-medium">{{ $user->doc_usuario }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px">
                                        <span class="material-symbols-rounded fs-6">person</span>
                                    </div>
                                    <span>{{ $user->primer_nombre }} {{ $user->primer_apellido }}</span>
                                </div>
                            </td>
                            <td>{{ $user->correo }}</td>
                            <td>
                                @if($user->id_estado == 1)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">Activo</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ \Carbon\Carbon::parse($user->fecha_registro ?? now())->format('d/m/Y') }}</td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('gestor-recargas.usuarios.edit', $user->doc_usuario) }}" class="btn btn-sm btn-light border" title="Editar Usuario">
                                        <span class="material-symbols-rounded fs-6 align-middle text-primary">edit</span>
                                    </a>
                                    
                                    @if($user->doc_usuario !== Auth::user()->doc_usuario)
                                    <form action="{{ route('gestor-recargas.usuarios.toggle', $user->doc_usuario) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-light border" title="{{ $user->id_estado == 1 ? 'Desactivar Usuario' : 'Activar Usuario' }}">
                                            @if($user->id_estado == 1)
                                                <span class="material-symbols-rounded fs-6 align-middle text-danger">block</span>
                                            @else
                                                <span class="material-symbols-rounded fs-6 align-middle text-success">check_circle</span>
                                            @endif
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                No hay usuarios registrados aún.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end">
        {{ $usuarios->links() }}
    </div>
</div>
@endsection
