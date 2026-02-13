@extends('superadmin.layouts.admin')

@section('title', 'Planes de Licencia')

@section('content')
<div class="container-fluid sa-licencia-container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="sa-licencia-title">Planes de Licencia</h2>
            <p class="text-muted">Gestione los planes y precios disponibles para las empresas</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('superadmin.licencias.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a Licencias
            </a>
            <a href="{{ route('superadmin.planes.create') }}" class="btn btn-primary sa-licencia-btn-new">
                <i class="fas fa-plus me-2"></i>Nuevo Plan
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
                        <i class="fas fa-layer-group text-primary"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold">Total Planes</small>
                        <span class="h4 fw-bold mb-0">{{ $stats['total_planes'] }}</span>
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
                        <small class="text-muted d-block text-uppercase fw-bold">Planes Activos</small>
                        <span class="h4 fw-bold mb-0 text-success">{{ $stats['planes_activos'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card sa-licencia-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="sa-licencia-icon-box me-3" style="background-color: #fee2e2;">
                        <i class="fas fa-times-circle" style="color: #b91c1c;"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold">Inactivos</small>
                        <span class="h4 fw-bold mb-0 text-danger">{{ $stats['planes_inactivos'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card sa-licencia-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="sa-licencia-icon-box me-3" style="background-color: #e0e7ff;">
                        <i class="fas fa-certificate" style="color: #4338ca;"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold">Licencias Vendidas</small>
                        <span class="h4 fw-bold mb-0 text-primary">{{ $stats['total_licencias_vendidas'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Planes -->
    <div class="row g-4">
        @forelse($planes as $plan)
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="card sa-licencia-plan-card h-100 {{ $plan->id_estado == 1 ? '' : 'opacity-75' }}">
                <div class="card-body p-4">
                    <!-- Header del plan -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            @if($plan->id_estado == 1)
                                <span class="badge bg-success mb-2">
                                    <i class="fas fa-check-circle"></i> Activo
                                </span>
                            @else
                                <span class="badge bg-secondary mb-2">
                                    <i class="fas fa-ban"></i> Inactivo
                                </span>
                            @endif
                            <h4 class="fw-bold mb-0">{{ $plan->nombre_plan }}</h4>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('superadmin.planes.edit', $plan->id_plan) }}">
                                        <i class="fas fa-edit me-2"></i>Editar
                                    </a>
                                </li>
                                <li>
                                    <button class="dropdown-item" onclick="toggleEstado({{ $plan->id_plan }})">
                                        <i class="fas fa-power-off me-2"></i>
                                        {{ $plan->id_estado == 1 ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item text-danger" 
                                            onclick="confirmarEliminacion({{ $plan->id_plan }}, '{{ $plan->nombre_plan }}', {{ $plan->total_licencias }})">
                                        <i class="fas fa-trash me-2"></i>Eliminar
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Precio -->
                    <div class="text-center my-4">
                        <div class="sa-licencia-plan-price mb-2">
                            ${{ number_format($plan->precio, 0, ',', '.') }}
                        </div>
                        <small class="text-muted">{{ $plan->duracion_meses }} meses de servicio</small>
                    </div>

                    <!-- Descripción -->
                    <p class="text-muted small mb-4" style="min-height: 60px;">
                        {{ $plan->descripcion }}
                    </p>

                    <!-- Estadísticas del plan -->
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Total licencias:</small>
                            <strong>{{ $plan->total_licencias }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Activas:</small>
                            <strong class="text-success">{{ $plan->licencias_activas }}</strong>
                        </div>
                    </div>

                    <!-- Botón de acción -->
                    <div class="mt-4">
                        <a href="{{ route('superadmin.planes.edit', $plan->id_plan) }}" 
                           class="btn btn-outline-primary w-100">
                            <i class="fas fa-edit me-2"></i>Editar Plan
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-layer-group fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted mb-3">No hay planes de licencia</h4>
                    <p class="text-muted mb-4">Cree el primer plan para comenzar a asignar licencias</p>
                    <a href="{{ route('superadmin.planes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Crear Primer Plan
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="mensajeEliminar"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formEliminar" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Eliminar Plan
                    </button>
                </form>
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

// Confirmar eliminación
function confirmarEliminacion(idPlan, nombrePlan, totalLicencias) {
    const modal = new bootstrap.Modal(document.getElementById('modalEliminar'));
    const formEliminar = document.getElementById('formEliminar');
    const mensajeEliminar = document.getElementById('mensajeEliminar');
    
    formEliminar.action = `/superadmin/planes/${idPlan}`;
    
    if (totalLicencias > 0) {
        mensajeEliminar.innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>¡Atención!</strong> Este plan tiene <strong>${totalLicencias}</strong> licencia(s) asociada(s).
            </div>
            <p>No es posible eliminar el plan <strong>"${nombrePlan}"</strong> porque hay licencias asociadas.</p>
            <p class="mb-0">Puede <strong>desactivarlo</strong> en su lugar para que no esté disponible para nuevas licencias.</p>
        `;
        formEliminar.querySelector('button[type="submit"]').disabled = true;
    } else {
        mensajeEliminar.innerHTML = `
            <p>¿Está seguro de eliminar el plan <strong>"${nombrePlan}"</strong>?</p>
            <p class="text-muted mb-0">Esta acción no se puede deshacer.</p>
        `;
        formEliminar.querySelector('button[type="submit"]').disabled = false;
    }
    
    modal.show();
}

// Toggle estado (Activar/Desactivar)
function toggleEstado(idPlan) {
    if (!confirm('¿Está seguro de cambiar el estado de este plan?')) return;
    
    fetch(`/superadmin/planes/${idPlan}/toggle-estado`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error al cambiar el estado');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    });
}
</script>

<style>
.sa-licencia-plan-card {
    transition: all 0.3s ease;
    border: 2px solid #f3f4f6;
}

.sa-licencia-plan-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    border-color: #2563eb;
}

.opacity-75 {
    opacity: 0.75;
}

.opacity-75:hover {
    opacity: 1;
}
</style>
@endsection
