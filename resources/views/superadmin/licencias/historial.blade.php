@extends('superadmin.layouts.admin')
@section('title', 'Historial de Licencias')

@section('content')
<div class="container-fluid sa-licencia-container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold h3">Historial de licencias</h2>
            <p class="text-muted">Supervisión global de consumo de recursos por empresa y tipo de contrato.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <label class="text-muted small fw-bold text-uppercase">Total Active Licenses</label>
                        <h2 class="fw-bold mb-0">1,240</h2>
                        <span class="text-success small fw-bold"><i class="fas fa-arrow-up"></i> +5% este mes</span>
                    </div>
                    <div class="bg-primary-soft p-2 rounded">
                        <i class="fas fa-users text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <label class="text-muted small fw-bold text-uppercase">Avg. Global Usage</label>
                        <h2 class="fw-bold mb-0">68.4%</h2>
                        <span class="text-danger small fw-bold"><i class="fas fa-arrow-down"></i> -2% vs previo</span>
                    </div>
                    <div class="bg-info-soft p-2 rounded">
                        <i class="fas fa-chart-line text-info"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <label class="text-muted small fw-bold text-uppercase">Companies Over Limit</label>
                        <h2 class="fw-bold mb-0 text-warning">12</h2>
                        <span class="text-success small"><i class="fas fa-check-circle"></i> 3 resueltos hoy</span>
                    </div>
                    <div class="bg-warning-soft p-2 rounded">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" style="width: 150px;">
                    <option>Filtrar Estado</option>
                </select>
                <div class="input-group input-group-sm" style="width: 250px;">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control bg-light border-start-0" placeholder="Buscar empresa...">
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm"><i class="fas fa-download me-1"></i> Exportar PDF</button>
                <a href="{{ route('superadmin.licencias.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i> Añadir Empresa</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="small text-uppercase text-muted">
                        <th class="ps-4">Empresa</th>
                        <th>Estado</th>
                        <th>Usuarios</th>
                        <th>Autobuses</th>
                        <th>Rutas</th>
                        <th>Planes</th>
                        <th style="width: 200px;">Almacén</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($licencias as $lic)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $lic->nombre_empresa }}</div>
                            <div class="small text-muted">{{ $lic->nombre_plan }}</div>
                        </td>
                        <td>
                            @php
                            $statusClass = match($lic->id_estado) {
                            1 => 'bg-success-soft text-success',
                            3 => 'bg-warning-soft text-warning',
                            14 => 'bg-danger-soft text-danger',
                            default => 'bg-secondary-soft text-secondary'
                            };
                            @endphp
                            <span class="badge {{ $statusClass }} rounded-pill px-3">{{ $lic->nombre_estado }}</span>
                        </td>
                        <td class="fw-semibold">850</td>
                        <td class="fw-semibold">45</td>
                        <td class="fw-semibold">120</td>
                        <td><span class="badge border text-dark fw-normal">{{ $lic->nombre_plan }}</span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress w-100" style="height: 6px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 99%"></div>
                                </div>
                                <span class="small fw-bold">99%</span>
                            </div>
                            <div class="x-small text-muted">24.7Gb / 25Gb</div>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
            <span class="small text-muted">Total {{ $licencias->count() }} empresas registradas</span>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>
@endsection