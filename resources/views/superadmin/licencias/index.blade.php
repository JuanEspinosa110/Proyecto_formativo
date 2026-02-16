@extends('superadmin.layouts.admin')

@section('title', 'Gestión de Licencias')

@section('content')
<div class="container-fluid sa-licencia-container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="sa-licencia-title">Listado General de Licencias</h2>
            <p class="text-muted">Panel centralizado para el monitoreo, renovación y gestión de licencias B2B activas.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('superadmin.planes.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-layer-group me-2"></i>Gestionar Planes
            </a>
            <a href="{{ route('superadmin.licencias.create') }}" class="btn btn-primary sa-licencia-btn-new">
                <i class="fas fa-plus me-2"></i>Nueva Licencia
            </a>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card sa-licencia-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="sa-licencia-icon-box bg-blue-light me-3">
                        <i class="fas fa-certificate text-primary"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold">Total Licencias</small>
                        <span class="h4 fw-bold mb-0">{{ $stats['total'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card sa-licencia-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="sa-licencia-icon-box me-3" style="background-color: #dcfce7;">
                        <i class="fas fa-check-circle" style="color: #15803d;"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold">Activas</small>
                        <span class="h4 fw-bold mb-0 text-success">{{ $stats['activas'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card sa-licencia-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="sa-licencia-icon-box me-3" style="background-color: #fff3cd;">
                        <i class="fas fa-clock" style="color: #856404;"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold">Por Vencer</small>
                        <span class="h4 fw-bold mb-0 text-warning">{{ $stats['proximas_vencer'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card sa-licencia-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="sa-licencia-icon-box me-3" style="background-color: #fee2e2;">
                        <i class="fas fa-exclamation-triangle" style="color: #b91c1c;"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold">Vencidas</small>
                        <span class="h4 fw-bold mb-0 text-danger">{{ $stats['vencidas'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros Rápidos (Tabs) -->
    <div class="mb-3">
        <ul class="nav nav-pills gap-2">
            <li class="nav-item">
                <a class="nav-link {{ !request('filter') ? 'active' : '' }}"
                    href="{{ route('superadmin.licencias.index') }}">
                    Todas ({{ $stats['total'] }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') == 'activas' ? 'active' : '' }}"
                    href="{{ route('superadmin.licencias.index', ['filter' => 'activas']) }}">
                    <i class="fas fa-check-circle me-1"></i>
                    Activas ({{ $stats['activas'] }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') == 'por_vencer' ? 'active' : '' }}"
                    href="{{ route('superadmin.licencias.index', ['filter' => 'por_vencer']) }}">
                    <i class="fas fa-clock me-1"></i>
                    Por Vencer ({{ $stats['proximas_vencer'] }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') == 'vencidas' ? 'active' : '' }}"
                    href="{{ route('superadmin.licencias.index', ['filter' => 'vencidas']) }}">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Vencidas ({{ $stats['vencidas'] }})
                </a>
            </li>
        </ul>
    </div>

    <!-- Formulario de Filtros -->
    <div class="card sa-licencia-filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('superadmin.licencias.index') }}" id="filterForm">
                <!-- Mantener el filtro rápido si existe -->
                @if(request('filter'))
                <input type="hidden" name="filter" value="{{ request('filter') }}">
                @endif

                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Búsqueda</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text"
                                name="search"
                                class="form-control border-start-0"
                                placeholder="Empresa, NIT, ID licencia..."
                                value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-muted">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos los estados</option>
                            @foreach($estados as $estado)
                            <option value="{{ $estado->id_estado }}"
                                {{ request('estado') == $estado->id_estado ? 'selected' : '' }}>
                                {{ $estado->nombre_estado }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-muted">Plan</label>
                        <select name="plan" class="form-select">
                            <option value="">Todos los planes</option>
                            @foreach($planes as $plan)
                            <option value="{{ $plan->id_plan }}"
                                {{ request('plan') == $plan->id_plan ? 'selected' : '' }}>
                                {{ $plan->nombre_plan }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-filter me-1"></i>Filtrar
                            </button>
                            <a href="{{ route('superadmin.licencias.index') }}"
                                class="btn btn-outline-secondary"
                                title="Limpiar filtros">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-12">
                        <a href="{{ route('superadmin.licencias.export', request()->all()) }}"
                            class="btn btn-success">
                            <i class="fas fa-file-csv me-1"></i>Exportar CSV
                        </a>
                    </div>
            </form>
        </div>

    </div>

    <!-- Información de filtros aplicados -->
    @if(request()->hasAny(['search', 'estado', 'plan', 'filter']))
    <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
        <div>
            <i class="fas fa-info-circle me-2"></i>
            <strong>Filtros aplicados:</strong>
            @if(request('search'))
            Búsqueda: "{{ request('search') }}"
            @endif
            @if(request('estado'))
            , Estado: {{ $estados->firstWhere('id_estado', request('estado'))->nombre_estado ?? '' }}
            @endif
            @if(request('plan'))
            , Plan: {{ $planes->firstWhere('id_plan', request('plan'))->nombre_plan ?? '' }}
            @endif
            @if(request('filter'))
            , Filtro rápido: {{ ucfirst(request('filter')) }}
            @endif
        </div>
        <a href="{{ route('superadmin.licencias.index') }}" class="btn btn-sm btn-outline-secondary">
            Limpiar todos
        </a>
    </div>
    @endif

    <!-- Tabla de Licencias -->
    <div class="card sa-licencia-table-card">
        <div class="card-body">
            @if($licencias->count() > 0)
            <div class="table-responsive">
                <table class="table sa-licencia-table">
                    <thead>
                        <tr>
                            <th>Empresa</th>
                            <th>NIT / ID Licencia</th>
                            <th>Plan</th>
                            <th>Estado</th>
                            <th>Vigencia</th>
                            <th>Días Restantes</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($licencias as $licencia)
                        @php
                        $diasRestantes = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($licencia->fecha_vencimiento), false);
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="sa-licencia-avatar me-2">
                                        {{ strtoupper(substr($licencia->nombre_empresa, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $licencia->nombre_empresa }}</div>
                                        <small class="text-muted">{{ $licencia->correo_corporativo }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><strong>{{ number_format($licencia->NIT, 0, ',', '.') }}</strong></div>
                                <small class="text-muted">{{ $licencia->id_licencia }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    {{ $licencia->nombre_plan }}
                                </span>
                                <div class="small text-muted">${{ number_format($licencia->precio, 0, ',', '.') }}</div>
                            </td>
                            <td>
                                @if($licencia->id_estado == 1)
                                <span class="badge bg-success">{{ $licencia->nombre_estado }}</span>
                                @elseif($licencia->id_estado == 21)
                                <span class="badge bg-danger">{{ $licencia->nombre_estado }}</span>
                                @elseif($licencia->id_estado == 3)
                                <span class="badge bg-warning">{{ $licencia->nombre_estado }}</span>
                                @else
                                <span class="badge bg-info">{{ $licencia->nombre_estado }}</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ \Carbon\Carbon::parse($licencia->fecha_inicio)->format('d/m/Y') }}</div>
                                <div class="small text-muted">{{ \Carbon\Carbon::parse($licencia->fecha_vencimiento)->format('d/m/Y') }}</div>
                            </td>
                            <td>
                                @if($diasRestantes > 30)
                                <span class="text-success">
                                    <i class="fas fa-check-circle me-1"></i>{{ $diasRestantes }} días
                                </span>
                                @elseif($diasRestantes >= 0 && $diasRestantes <= 30)
                                    <span class="text-warning">
                                    <i class="fas fa-clock me-1"></i>{{ $diasRestantes }} días
                                    </span>
                                    @else
                                    <span class="text-danger">
                                        <i class="fas fa-exclamation-circle me-1"></i>Vencida
                                    </span>
                                    @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    <!--<button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="tooltip"
                                        title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>-->
                                    <!--<button class="btn btn-sm btn-outline-warning"
                                        data-bs-toggle="tooltip"
                                        title="Gestionar Estado">
                                        <i class="fas fa-shield-alt"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success"
                                        data-bs-toggle="tooltip"
                                        title="Renovar">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>-->
                                    <button class="btn btn-sm btn-outline-info"
                                        data-bs-toggle="tooltip"
                                        title="Ver Detalles"
                                        onclick="verDetalles({{ json_encode($licencia) }}, {{ $diasRestantes }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Mostrando {{ $licencias->firstItem() ?? 0 }} - {{ $licencias->lastItem() ?? 0 }}
                    de {{ $licencias->total() }} licencias
                </div>
                <div>
                    {{ $licencias->links() }}
                </div>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No se encontraron resultados</h5>
                <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                <a href="{{ route('superadmin.licencias.index') }}" class="btn btn-primary">
                    Ver todas las licencias
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Detalles -->
<div class="modal fade" id="modalDetalles" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de Licencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalDetallesBody">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-cerrar alertas
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Ver detalles
    function verDetalles(licencia, diasRestantes) {
        const modal = new bootstrap.Modal(document.getElementById('modalDetalles'));
        const body = document.getElementById('modalDetallesBody');

        body.innerHTML = `
        <div class="row g-3">
            <div class="col-md-6">
                <strong>Empresa:</strong><br>${licencia.nombre_empresa}
            </div>
            <div class="col-md-6">
                <strong>NIT:</strong><br>${new Intl.NumberFormat('es-CO').format(licencia.NIT)}
            </div>
            <div class="col-md-6">
                <strong>ID Licencia:</strong><br>${licencia.id_licencia}
            </div>
            <div class="col-md-6">
                <strong>Plan:</strong><br>${licencia.nombre_plan}
            </div>
            <div class="col-md-6">
                <strong>Precio:</strong><br>$${new Intl.NumberFormat('es-CO').format(licencia.precio)}
            </div>
            <div class="col-md-6">
                <strong>Estado:</strong><br>${licencia.nombre_estado}
            </div>
            <div class="col-md-6">
                <strong>Fecha Inicio:</strong><br>${new Date(licencia.fecha_inicio).toLocaleDateString('es-CO')}
            </div>
            <div class="col-md-6">
                <strong>Fecha Vencimiento:</strong><br>${new Date(licencia.fecha_vencimiento).toLocaleDateString('es-CO')}
            </div>
            <div class="col-md-6">
                <strong>Días Restantes:</strong><br>${diasRestantes > 0 ? diasRestantes + ' días' : 'Vencida'}
            </div>
            <div class="col-md-6">
                <strong>Correo Corporativo:</strong><br>${licencia.correo_corporativo}
            </div>
        </div>
    `;

        modal.show();
    }
</script>
@endsection