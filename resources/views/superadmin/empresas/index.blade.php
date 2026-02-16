@extends('superadmin.layouts.admin')

@section('title', 'Gestión de Empresas')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif



@section('content')
<div class="empresa-container">
    
    {{-- HEADER --}}
    <div class="empresa-header">
        <div class="empresa-header-title">
            <h1><span class="material-symbols-outlined">business</span> Gestión de Empresas</h1>
            <p>Administra las empresas registradas en el sistema</p>

            <a href="{{ route('superadmin.empresas.create') }}" class="btn btn-success">
                <span class="material-symbols-outlined">add</span>
                Nueva Empresa
            </a>

        </div>
        
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FILTROS Y BÚSQUEDA --}}
    <div class="empresa-filters">
        <form action="{{ route('superadmin.empresas.index') }}" method="GET" class="filters-form">
            <div class="filter-group">
                <div class="search-box">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" name="search" placeholder="Buscar por nombre, NIT o correo..." 
                           value="{{ request('search') }}" class="form-control">
                </div>
            </div>

            <div class="filter-group">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->id_estado }}" 
                                {{ request('estado') == $estado->id_estado ? 'selected' : '' }}>
                            {{ $estado->nombre_estado }}
                        </option>
                    @endforeach
                </select>

                <select name="ciudad" class="form-select">
                    <option value="">Todas las ciudades</option>
                    @foreach($ciudades as $ciudad)
                        <option value="{{ $ciudad->id_ciudad }}" 
                                {{ request('ciudad') == $ciudad->id_ciudad ? 'selected' : '' }}>
                            {{ $ciudad->nombre_city }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn-filter">
                    <span class="material-symbols-outlined">filter_list</span>
                    Filtrar
                </button>

                @if(request('search') || request('estado') || request('ciudad'))
                    <a href="{{ route('superadmin.empresas.index') }}" class="btn-clear">
                        <span class="material-symbols-outlined">clear</span>
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ESTADÍSTICAS --}}
    <div class="empresa-stats">
        <div class="stat-card">
            <div class="stat-icon activo">
                <span class="material-symbols-outlined">business_center</span>
            </div>
            <div class="stat-info">
                <h3>{{ $empresas->total() }}</h3>
                <p>Total Empresas</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon activo">
                <span class="material-symbols-outlined">check_circle</span>
            </div>
            <div class="stat-info">
                <h3>{{ \App\Models\Empresa::where('id_estado', 1)->count() }}</h3>
                <p>Activas</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon proceso">
                <span class="material-symbols-outlined">pending</span>
            </div>
            <div class="stat-info">
                <h3>{{ \App\Models\Empresa::where('id_estado', 6)->count() }}</h3>
                <p>En Proceso</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon inactivo">
                <span class="material-symbols-outlined">cancel</span>
            </div>
            <div class="stat-info">
                <h3>{{ \App\Models\Empresa::where('id_estado', 2)->count() }}</h3>
                <p>Inactivas</p>
            </div>
        </div>
    </div>

    {{-- TABLA DE EMPRESAS --}}
    <div class="empresa-table-container">
        @if($empresas->count() > 0)
            <table class="empresa-table">
                <thead>
                    <tr>
                        <th>NIT</th>
                        <th>Empresa</th>
                        <th>Representante Legal</th>
                        <th>Contacto</th>
                        <th>Ubicación</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($empresas as $empresa)
                        <tr>
                            <td>
                                <strong>{{ number_format($empresa->NIT, 0, '', '.') }}</strong>
                            </td>
                            <td>
                                <div class="empresa-info">
                                    <h4>{{ $empresa->nombre_empresa }}</h4>
                                    <small>{{ $empresa->correo_corporativo }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="representante-info">
                                    <p><strong>{{ $empresa->nombre_completo_representante }}</strong></p>
                                    <small>CC: {{ number_format($empresa->doc_representante, 0, '', '.') }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="contacto-info">
                                    @if($empresa->telefono_empresa)
                                        <p><i class="bi bi-telephone-fill"></i> {{ $empresa->telefono_empresa }}</p>
                                    @endif
                                    @if($empresa->correo_corporativo)
                                        <p><i class="bi bi-envelope-fill"></i> {{ $empresa->correo_corporativo }}</p>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($empresa->ciudad)
                                    <span class="badge bg-secondary">
                                        {{ $empresa->ciudad->nombre_city }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($empresa->estado)
                                    <span class="badge-estado {{ strtolower($empresa->estado->nombre_estado) }}">
                                        {{ $empresa->estado->nombre_estado }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="{{ route('superadmin.empresas.show', $empresa->NIT) }}" 
                                       class="btn-action view" title="Ver detalles">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </a>
                                    <a href="{{ route('superadmin.empresas.edit', $empresa->NIT) }}" 
                                       class="btn-action edit" title="Editar">
                                        <span class="material-symbols-outlined">edit</span>
                                    </a>
                                    
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- PAGINACIÓN --}}
            <div class="empresa-pagination">
                {{ $empresas->appends(request()->query())->links() }}
            </div>
        @else
            <div class="empty-state">
                <span class="material-symbols-outlined">inbox</span>
                <h3>No se encontraron empresas</h3>
                <p>No hay empresas registradas que coincidan con los criterios de búsqueda.</p>
                
            </div>
        @endif
    </div>

</div>

{{-- MODAL DE CONFIRMACIÓN --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar esta empresa?</p>
                <p class="text-danger"><strong>Esta acción cambiará el estado de la empresa a "ELIMINADO".</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarEliminacion(nit) {
    const form = document.getElementById('deleteForm');
    form.action = `/superadmin/empresas/${nit}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>

@endsection
