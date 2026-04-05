@extends('empresa-recargas.layouts.app')

@section('title', 'Historial de Recargas y Titularidad')

@section('content')
<div class="admin-dashboard sigu-fade">
    <div class="sigu-page-hd d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="sigu-page-title">Historial</h1>
            <p class="sigu-page-sub">Consulta el registro de recargas y cambios de titularidad</p>
        </div>
    </div>

    <!-- Tabs de navegación -->
    <ul class="nav nav-pills mb-4 gap-2" id="historialTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active d-flex align-items-center gap-2 px-4 py-2 fw-medium" id="tab-recargas" data-bs-toggle="pill" data-bs-target="#panel-recargas" type="button" role="tab">
                <span class="material-symbols-rounded fs-5">payments</span> Recargas
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center gap-2 px-4 py-2 fw-medium" id="tab-titularidad" data-bs-toggle="pill" data-bs-target="#panel-titularidad" type="button" role="tab">
                <span class="material-symbols-rounded fs-5">swap_horiz</span> Cambios de Titularidad
            </button>
        </li>
    </ul>

    <!-- Filtros (compartidos) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
        <form method="GET" action="{{ route('gestor-recargas.historial') }}" class="row g-3 align-items-end">
            <!-- Preservar la pestaña activa -->
            <input type="hidden" name="tab" id="input-tab-activa" value="{{ request('tab', 'recargas') }}">
            <div class="col-md-3">
                <label class="form-label fw-medium text-muted small">ID/Código de Tarjeta</label>
                <input type="text" name="id_tarjeta" class="form-control" value="{{ request('id_tarjeta') }}" placeholder="Buscar por ID...">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium text-muted small">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium text-muted small">Fecha Fin</label>
                <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-medium">
                    <span class="material-symbols-rounded align-middle fs-5 me-1">search</span> Filtrar
                </button>
                <a href="{{ route('gestor-recargas.historial') }}" class="btn btn-light w-100 fw-medium">Limpiar</a>
            </div>
        </form>
        
        <div class="mt-3 pt-3 border-top d-flex justify-content-end">
            <a href="{{ route('gestor-recargas.historial', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success text-white fw-medium d-flex align-items-center gap-2">
                <span class="material-symbols-rounded fs-5">download</span> Descargar a Excel
            </a>
        </div>
    </div>

    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="historialTabsContent">

        <!-- Panel Recargas -->
        <div class="tab-pane fade show active" id="panel-recargas" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">ID Recarga</th>
                                    <th>Tarjeta</th>
                                    <th>Monto</th>
                                    <th>Fecha y Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recargas as $recarga)
                                <tr>
                                    <td class="ps-4 fw-medium">#{{ $recarga->id_recarga }}</td>
                                    <td>{{ $recarga->id_tarjeta }}</td>
                                    <td class="text-success fw-bold">${{ number_format($recarga->monto, 0, ',', '.') }}</td>
                                    <td class="text-muted">{{ $recarga->created_at->format('d/m/Y h:i A') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        No hay recargas registradas aún.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex justify-content-end">
                {{ $recargas->links() }}
            </div>
        </div>

        <!-- Panel Titularidad -->
        <div class="tab-pane fade" id="panel-titularidad" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Tarjeta</th>
                                    <th>Usuario</th>
                                    <th>Estado</th>
                                    <th>Motivo</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($titularidades as $t)
                                <tr>
                                    <td class="ps-4 fw-medium">#{{ $t->id_titularidad_tarjeta }}</td>
                                    <td>{{ $t->id_tarjeta }}</td>
                                    <td>
                                        @if($t->usuario)
                                            {{ $t->usuario->primer_nombre }} {{ $t->usuario->primer_apellido }}
                                            <br><small class="text-muted">{{ $t->doc_usuario }}</small>
                                        @else
                                            {{ $t->doc_usuario }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($t->id_estado == 1)
                                            <span class="badge bg-success">Activo</span>
                                        @elseif($t->id_estado == 2)
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @elseif($t->id_estado == 3)
                                            <span class="badge bg-warning text-dark">Suspendido</span>
                                        @else
                                            <span class="badge bg-light text-dark">{{ $t->estado->nombre_estado ?? $t->id_estado }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $t->motivo_cambio ?? '-' }}</td>
                                    <td class="text-muted">{{ $t->fecha_inicio ? \Carbon\Carbon::parse($t->fecha_inicio)->format('d/m/Y h:i A') : '-' }}</td>
                                    <td class="text-muted">{{ $t->fecha_fin ? \Carbon\Carbon::parse($t->fecha_fin)->format('d/m/Y h:i A') : '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        No hay cambios de titularidad registrados aún.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex justify-content-end">
                {{ $titularidades->links() }}
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Restaurar pestaña activa desde el parámetro URL
    const urlParams = new URLSearchParams(window.location.search);
    const tabActiva = urlParams.get('tab') || 'recargas';
    
    if (tabActiva === 'titularidad') {
        const tabTitularidad = document.getElementById('tab-titularidad');
        const tabRecargas = document.getElementById('tab-recargas');
        const panelTitularidad = document.getElementById('panel-titularidad');
        const panelRecargas = document.getElementById('panel-recargas');
        
        tabRecargas.classList.remove('active');
        panelRecargas.classList.remove('show', 'active');
        tabTitularidad.classList.add('active');
        panelTitularidad.classList.add('show', 'active');
    }

    // Actualizar el input oculto cuando cambias de pestaña
    document.querySelectorAll('#historialTabs button').forEach(function(btn) {
        btn.addEventListener('shown.bs.tab', function(e) {
            const tab = e.target.id === 'tab-titularidad' ? 'titularidad' : 'recargas';
            document.getElementById('input-tab-activa').value = tab;
        });
    });
});
</script>
@endpush
