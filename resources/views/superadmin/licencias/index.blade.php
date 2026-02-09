@extends('superadmin.layouts.admin')

@section('content')
<div class="container-fluid sa-licencia-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="sa-licencia-title">Listado General de Licencias</h2>
            <p class="text-muted">Panel centralizado para el monitoreo, renovación y gestión de licencias B2B activas.</p>
        </div>
        <a href="{{ route('superadmin.licencias.create') }}" class="btn btn-primary sa-licencia-btn-new">
            <i class="fas fa-plus me-2"></i> + Nueva Licencia
        </a>
    </div>

    <div class="card sa-licencia-filter-card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Filtrar por empresa, NIT o ID...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option selected>Estado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option selected>Plan</option>
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    <button class="btn btn-outline-secondary"><i class="fas fa-download"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mb-3">
        <button class="btn sa-licencia-tab active">Todos <span class="badge bg-primary ms-1">128</span></button>
        <button class="btn sa-licencia-tab">Activos <span class="badge bg-light text-success ms-1">112</span></button>
        <button class="btn sa-licencia-tab">Próximos a Vencer <span class="badge bg-light text-warning ms-1">8</span></button>
        <button class="btn sa-licencia-tab">Expirados <span class="badge bg-light text-danger ms-1">5</span></button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table sa-licencia-table align-middle">
                <thead>
                    <tr>
                        <th>EMPRESA</th>
                        <th>NIT / ID</th>
                        <th>PLAN</th>
                        <th>ESTADO</th>
                        <th>VIGENCIA</th>
                        <th>LÍMITES (U/B)</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="sa-licencia-avatar me-3">TC</div>
                                <div>
                                    <div class="fw-bold">Transportes Central S.A.</div>
                                    <small class="text-muted">Bogotá, Colombia</small>
                                </div>
                            </div>
                        </td>
                        <td>900.123.456-1</td>
                        <td><span class="badge sa-licencia-badge-plan">Enterprise</span></td>
                        <td><span class="badge sa-licencia-status-active">● Activo</span></td>
                        <td>
                            <div class="small">01 Ene 2026</div>
                            <div class="small text-muted">al 31 Dic 2026</div>
                        </td>
                        <td>
                            <small><i class="fas fa-user me-1"></i> 50</small>
                            <small class="ms-2"><i class="fas fa-bus me-1"></i> 120</small>
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm text-primary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('superadmin.licencias.edit', 1) }}" class="btn btn-sm text-secondary"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card sa-licencia-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="sa-licencia-icon-box bg-blue-light me-3"><i class="fas fa-check-circle text-primary"></i></div>
                    <div>
                        <small class="text-muted d-block">LICENCIAS TOTALES</small>
                        <span class="h4 fw-bold">1,280</span>
                    </div>
                </div>
            </div>
        </div>
        </div>
</div>
@endsection