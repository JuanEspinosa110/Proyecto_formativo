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
        <a href="{{ route('superadmin.planes.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-layer-group me-2"></i>Gestionar Planes
        </a>
        <a href="{{ route('superadmin.licencias.create') }}" class="btn btn-primary sa-licencia-btn-new">
            <i class="fas fa-plus me-2"></i>Nueva Licencia
        </a>
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

    <!-- Filtros -->
    <div class="card sa-licencia-filter-card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="searchLicense" class="form-control border-start-0" placeholder="Filtrar por empresa, NIT o ID de licencia...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filterEstado">
                        <option value="">Todos los estados</option>
                        <option value="1">Activo</option>
                        <option value="3">Suspendido</option>
                        <option value="21">Vencido</option>
                        <option value="22">Renovado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filterPlan">
                        <option value="">Todos los planes</option>
                        <option value="1">Básico</option>
                        <option value="2">Profesional</option>
                        <option value="3">Premium</option>
                        <option value="4">Enterprise</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex justify-content-end align-items-center gap-2">
                    <!--<a href="{{ route('superadmin.licencias.historial') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-history me-1"></i> Historial
                    </a>-->
                    <button class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-download me-1"></i> Exportar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs de filtro rápido -->
    <div class="d-flex gap-2 mb-3">
        <button class="btn sa-licencia-tab active" data-filter="all">
            Todos <span class="badge bg-primary ms-1">{{ $stats['total'] }}</span>
        </button>
        <button class="btn sa-licencia-tab" data-filter="1">
            Activos <span class="badge bg-light text-success ms-1">{{ $stats['activas'] }}</span>
        </button>
        <button class="btn sa-licencia-tab" data-filter="proximas">
            Próximos a Vencer <span class="badge bg-light text-warning ms-1">{{ $stats['proximas_vencer'] }}</span>
        </button>
        <button class="btn sa-licencia-tab" data-filter="21">
            Vencidos <span class="badge bg-light text-danger ms-1">{{ $stats['vencidas'] }}</span>
        </button>
    </div>

    <!-- Tabla de licencias -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table sa-licencia-table align-middle mb-0" id="licenciasTable">
                <thead>
                    <tr>
                        <th>EMPRESA</th>
                        <th>NIT / ID LICENCIA</th>
                        <th>PLAN</th>
                        <th>ESTADO</th>
                        <th>VIGENCIA</th>
                        <th>DÍAS RESTANTES</th>
                        <th class="text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($licencias as $lic)
                    @php
                    $diasRestantes = (int)\Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($lic->fecha_vencimiento), false);
                    $vencida = $diasRestantes < 0;
                        $proximaVencer=!$vencida && $diasRestantes>= 0 && $diasRestantes <= 30 && $lic->id_estado == 1;

                            $badgeClass = match($lic->id_estado) {
                            1 => 'sa-licencia-status-active',
                            3 => 'badge bg-warning text-dark',
                            21 => 'sa-licencia-status-expired',
                            22 => 'badge bg-info text-dark',
                            default => 'badge bg-secondary'
                            };

                            $diasColor = $vencida ? 'text-danger fw-bold' : ($proximaVencer ? 'text-warning fw-bold' : 'text-success');
                            @endphp
                            <tr data-estado="{{ $lic->id_estado }}" data-plan="{{ $lic->id_plan }}" data-proxima="{{ $proximaVencer ? 'true' : 'false' }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="sa-licencia-avatar me-3">
                                            {{ strtoupper(substr($lic->nombre_empresa, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $lic->nombre_empresa }}</div>
                                            <small class="text-muted">{{ $lic->correo_corporativo }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ number_format($lic->NIT, 0, ',', '.') }}</div>
                                    <small class="text-muted font-monospace">{{ $lic->id_licencia }}</small>
                                </td>
                                <td>
                                    <span class="badge sa-licencia-badge-plan">{{ $lic->nombre_plan }}</span>
                                    <div class="small text-muted">${{ number_format($lic->precio, 0, ',', '.') }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $badgeClass }}">● {{ $lic->nombre_estado }}</span>
                                </td>
                                <td>
                                    <div class="small">{{ \Carbon\Carbon::parse($lic->fecha_inicio)->format('d M Y') }}</div>
                                    <div class="small text-muted">al {{ \Carbon\Carbon::parse($lic->fecha_vencimiento)->format('d M Y') }}</div>
                                </td>
                                <td>
                                    <span class="{{ $diasColor }}">
                                        @if($vencida)
                                        <i class="fas fa-exclamation-circle"></i> Vencida hace {{ abs($diasRestantes) }} días
                                        @else
                                        @if($proximaVencer)
                                        <i class="fas fa-clock"></i>
                                        @else
                                        <i class="fas fa-check-circle"></i>
                                        @endif
                                        {{ $diasRestantes }} días
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('superadmin.licencias.edit', $lic->id_licencia) }}"
                                            class="btn btn-sm text-primary"
                                            title="Editar licencia"
                                            data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('superadmin.licencias.gestionar-estado', $lic->id_licencia) }}"
                                            class="btn btn-sm text-warning"
                                            title="Gestionar estado"
                                            data-bs-toggle="tooltip">
                                            <i class="fas fa-shield-alt"></i>
                                        </a>
                                        <a href="{{ route('superadmin.licencias.renovar', $lic->id_licencia) }}"
                                            class="btn btn-sm text-success"
                                            title="Renovar licencia"
                                            data-bs-toggle="tooltip">
                                            <i class="fas fa-sync-alt"></i>
                                        </a>
                                        <button type="button"
                                            class="btn btn-sm text-info"
                                            title="Ver detalles"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalDetalle{{ $lic->id_licencia }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal de Detalles -->
                            <div class="modal fade" id="modalDetalle{{ $lic->id_licencia }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-file-contract me-2 text-primary"></i>
                                                Detalles de Licencia: {{ $lic->id_licencia }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="small text-muted fw-bold">EMPRESA</label>
                                                    <p class="mb-0">{{ $lic->nombre_empresa }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small text-muted fw-bold">NIT</label>
                                                    <p class="mb-0">{{ number_format($lic->NIT, 0, ',', '.') }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small text-muted fw-bold">PLAN CONTRATADO</label>
                                                    <p class="mb-0"><span class="badge sa-licencia-badge-plan">{{ $lic->nombre_plan }}</span></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small text-muted fw-bold">PRECIO</label>
                                                    <p class="mb-0 fw-bold text-success">${{ number_format($lic->precio, 0, ',', '.') }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small text-muted fw-bold">FECHA DE INICIO</label>
                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($lic->fecha_inicio)->format('d/m/Y') }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small text-muted fw-bold">FECHA DE VENCIMIENTO</label>
                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($lic->fecha_vencimiento)->format('d/m/Y') }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small text-muted fw-bold">ESTADO ACTUAL</label>
                                                    <p class="mb-0"><span class="badge {{ $badgeClass }}">{{ $lic->nombre_estado }}</span></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small text-muted fw-bold">DÍAS RESTANTES</label>
                                                    <p class="mb-0 {{ $diasColor }}">
                                                        @if($vencida)
                                                        Vencida hace {{ abs($diasRestantes) }} días
                                                        @else
                                                        {{ $diasRestantes }} días
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="col-12">
                                                    <label class="small text-muted fw-bold">CORREO CORPORATIVO</label>
                                                    <p class="mb-0">{{ $lic->correo_corporativo }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small text-muted fw-bold">FECHA DE CREACIÓN</label>
                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($lic->fecha_creacion)->format('d/m/Y H:i') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                            <a href="{{ route('superadmin.licencias.edit', $lic->id_licencia) }}" class="btn btn-primary">
                                                <i class="fas fa-edit me-2"></i>Editar Licencia
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-3">No hay licencias registradas en el sistema</p>
                                    <a href="{{ route('superadmin.licencias.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Crear Primera Licencia
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                </tbody>
            </table>
        </div>

        @if($licencias->count() > 0)
        <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
            <span class="small text-muted">
                Mostrando {{ $licencias->count() }} de {{ $stats['total'] }} licencias
            </span>
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Activar tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Filtro de búsqueda
        const searchInput = document.getElementById('searchLicense');
        const filterEstado = document.getElementById('filterEstado');
        const filterPlan = document.getElementById('filterPlan');
        const tableRows = document.querySelectorAll('#licenciasTable tbody tr');
        const tabs = document.querySelectorAll('.sa-licencia-tab');

        function filterTable() {
            const searchText = searchInput.value.toLowerCase();
            const estadoValue = filterEstado.value;
            const planValue = filterPlan.value;

            tableRows.forEach(row => {
                if (row.querySelector('td[colspan]')) return; // Skip empty state row

                const text = row.textContent.toLowerCase();
                const rowEstado = row.getAttribute('data-estado');
                const rowPlan = row.getAttribute('data-plan');

                const matchSearch = text.includes(searchText);
                const matchEstado = !estadoValue || rowEstado === estadoValue;
                const matchPlan = !planValue || rowPlan === planValue;

                if (matchSearch && matchEstado && matchPlan) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('keyup', filterTable);
        filterEstado.addEventListener('change', filterTable);
        filterPlan.addEventListener('change', filterTable);

        // Filtro por tabs
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Actualizar tab activo
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');

                // Resetear otros filtros
                searchInput.value = '';
                filterEstado.value = '';
                filterPlan.value = '';

                tableRows.forEach(row => {
                    if (row.querySelector('td[colspan]')) return;

                    const rowEstado = row.getAttribute('data-estado');
                    const isProxima = row.getAttribute('data-proxima') === 'true';

                    if (filter === 'all') {
                        row.style.display = '';
                    } else if (filter === 'proximas') {
                        row.style.display = isProxima ? '' : 'none';
                    } else {
                        row.style.display = rowEstado === filter ? '' : 'none';
                    }
                });
            });
        });

        // Auto-ocultar alertas después de 5 segundos
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });
</script>
@endsection