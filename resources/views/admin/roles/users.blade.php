@extends('superadmin.layouts.admin')

@section('title', 'Usuarios del Tipo: ' . $tipoUsuario->nombre_tipo)

@section('content')
<div class="sa-roles-container">
    <!-- Header -->
    <div class="sa-roles-header">
        <div>
            <h1 class="sa-roles-title">Usuarios - {{ $tipoUsuario->nombre_tipo }}</h1>
            <p class="sa-roles-subtitle">Listado de usuarios asignados a este tipo</p>
        </div>
        <a href="{{ route('superadmin.roles.index') }}" class="sa-roles-btn sa-roles-btn-secondary">
            <span class="material-symbols-outlined">arrow_back</span>
            Volver a Tipos
        </a>
    </div>

    <!-- Alertas -->
    @if(session('success'))
    <div class="sa-roles-alert sa-roles-alert-success">
        <span class="material-symbols-outlined">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="sa-roles-alert sa-roles-alert-error">
        <span class="material-symbols-outlined">error</span>
        {{ session('error') }}
    </div>
    @endif

    <!-- Estadísticas -->
    <div class="sa-roles-stats">
        <div class="sa-roles-stat-card">
            <div class="sa-roles-stat-label">Total de Usuarios</div>
            <div class="sa-roles-stat-value">{{ $usuarios->total() }}</div>
        </div>
        <div class="sa-roles-stat-card">
            <div class="sa-roles-stat-label">Tipo de Usuario</div>
            <div class="sa-roles-stat-value">{{ $tipoUsuario->nombre_tipo }}</div>
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="sa-roles-table-container">
        <table class="sa-roles-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Ciudad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->id_usuario }}</td>
                    <td>{{ $usuario->nombre_usuario ?? 'N/A' }}</td>
                    <td>{{ $usuario->email_usuario ?? 'N/A' }}</td>
                    <td>{{ $usuario->nombre_city ?? 'N/A' }}</td>
                    <td>
                        <span class="sa-roles-badge sa-roles-badge-{{ $usuario->nombre_estado === 'activo' ? 'success' : 'danger' }}">
                            {{ ucfirst($usuario->nombre_estado ?? 'N/A') }}
                        </span>
                    </td>
                    <td class="sa-roles-actions">
                        <button class="sa-roles-btn sa-roles-btn-sm sa-roles-btn-info" title="Ver detalles">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                        <a href="{{ route('superadmin.usuarios.index') }}" class="sa-roles-btn sa-roles-btn-sm sa-roles-btn-warning" title="Editar">
                            <span class="material-symbols-outlined">edit</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No hay usuarios asignados a este tipo</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    @if($usuarios->hasPages())
    <div class="sa-roles-pagination">
        {{ $usuarios->links() }}
    </div>
    @endif
</div>

<style>
.sa-roles-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.sa-roles-badge-success {
    background-color: #d4edda;
    color: #155724;
}

.sa-roles-badge-danger {
    background-color: #f8d7da;
    color: #721c24;
}

.sa-roles-actions {
    display: flex;
    gap: 8px;
}

.sa-roles-btn-sm {
    padding: 6px 10px;
    font-size: 12px;
}
</style>
@endsection
