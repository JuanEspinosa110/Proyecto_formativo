@extends('superadmin.layouts.admin')

@section('title', 'Gestores de Recargas')

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- Alertas --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
        <span class="material-symbols-rounded">check_circle</span>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Header --}}
    <div class="gs-header">
        <div class="gs-header-left">
            <h1>
                <span class="material-symbols-rounded gs-icon-primary">payments</span>
                Gestores de Recargas
            </h1>
            <p>Usuarios con acceso al panel de recargas asignados a empresas de recarga.</p>
        </div>
        <a href="{{ route('superadmin.gestores-recargas.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <span class="material-symbols-rounded">person_add</span>
            Nuevo Gestor
        </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('superadmin.gestores-recargas.index') }}" class="gs-filters">
        <div>
            <label class="form-label fw-semibold">Buscar</label>
            <input type="text" name="q" class="form-control" placeholder="Nombre o documento…"
                   value="{{ request('q') }}">
        </div>
        <div>
            <label class="form-label fw-semibold">Empresa de Recargas</label>
            <select name="nit" class="form-select">
                <option value="">Todas</option>
                @foreach($empresasRecarga as $emp)
                <option value="{{ $emp->NIT }}" {{ request('nit') == $emp->NIT ? 'selected' : '' }}>
                    {{ $emp->nombre_empresa }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label fw-semibold">Estado</label>
            <select name="estado" class="form-select">
                <option value="">Todos</option>
                <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activo</option>
                <option value="2" {{ request('estado') == '2' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>
        <button type="submit" class="btn btn-sm btn-primary d-flex align-items-center gap-1 align-self-end">
            <span class="material-symbols-rounded">search</span> Filtrar
        </button>
        @if(request()->hasAny(['q','nit','estado']))
        <a href="{{ route('superadmin.gestores-recargas.index') }}"
           class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 align-self-end">
            <span class="material-symbols-rounded">close</span> Limpiar
        </a>
        @endif
    </form>

    {{-- Tabla --}}
    <div class="gs-card">
        <div class="table-responsive">
            <table class="gs-table">
                <thead>
                    <tr>
                        <th>Gestor</th>
                        <th>Documento</th>
                        <th>Empresa</th>
                        <th>Ciudad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gestores as $gestor)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="gs-avatar">
                                    {{ strtoupper(substr($gestor->primer_nombre ?? 'G', 0, 1)) }}{{ strtoupper(substr($gestor->primer_apellido ?? '', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $gestor->primer_nombre }} {{ $gestor->primer_apellido }}</div>
                                    <div class="text-muted small text-truncate" style="max-width: 150px;">{{ $gestor->correo }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="fw-semibold">{{ $gestor->doc_usuario }}</td>
                        <td>{{ optional($gestor->empresa)->nombre_empresa ?? '—' }}</td>
                        <td>{{ optional($gestor->ciudad)->nombre_city ?? '—' }}</td>
                        <td>
                            @if($gestor->id_estado == 1)
                                <span class="gs-badge gs-badge-active">
                                    <span class="material-symbols-rounded" style="font-size:.85rem">circle</span> Activo
                                </span>
                            @else
                                <span class="gs-badge gs-badge-inactive">
                                    <span class="material-symbols-rounded" style="font-size:.85rem">cancel</span> Inactivo
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="gs-actions">
                                <a href="{{ route('superadmin.gestores-recargas.edit', $gestor->doc_usuario) }}"
                                   class="gs-btn-icon" title="Editar">
                                    <span class="material-symbols-rounded">edit</span>
                                </a>
                                <form method="POST"
                                      action="{{ route('superadmin.gestores-recargas.toggle-estado', $gestor->doc_usuario) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="gs-btn-icon {{ $gestor->id_estado == 1 ? 'danger' : '' }}"
                                            title="{{ $gestor->id_estado == 1 ? 'Inactivar' : 'Activar' }}">
                                        <span class="material-symbols-rounded">
                                            {{ $gestor->id_estado == 1 ? 'person_off' : 'person_check' }}
                                        </span>
                                    </button>
                                </form>
                                <form method="POST"
                                      action="{{ route('superadmin.gestores-recargas.destroy', $gestor->doc_usuario) }}"
                                      onsubmit="return confirm('¿Eliminar este gestor?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="gs-btn-icon danger" title="Eliminar">
                                        <span class="material-symbols-rounded">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="gs-empty">
                                <span class="material-symbols-rounded">payments</span>
                                <p>No se encontraron gestores de recargas.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($gestores->hasPages())
        <div class="gs-pagination">
            {{ $gestores->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

