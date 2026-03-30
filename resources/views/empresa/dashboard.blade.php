@extends('empresa.layouts.app')

@section('title', 'Dashboard Empresa — SIGU')

@section('content')
<div class="container-fluid pb-5">
    
    <!-- Alertas -->
    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center gap-2">
        <span class="material-symbols-rounded">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center gap-2">
        <span class="material-symbols-rounded">error</span>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Menú Superior de Secciones -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-2 bg-light">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('empresa.dashboard') }}" class="btn btn-{{ $section == 'dashboard' ? 'primary' : 'light' }} rounded-pill px-4 d-flex align-items-center gap-2 fw-bold">
                        <span class="material-symbols-rounded">dashboard</span> Panel
                    </a>
                    <a href="{{ route('empresa.dashboard', ['section' => 'usuarios']) }}" class="btn btn-{{ $section == 'usuarios' ? 'primary' : 'light' }} rounded-pill px-4 d-flex align-items-center gap-2 fw-bold">
                        <span class="material-symbols-rounded">group</span> Usuarios
                    </a>
                    <a href="{{ route('empresa.dashboard', ['section' => 'buses']) }}" class="btn btn-{{ $section == 'buses' ? 'primary' : 'light' }} rounded-pill px-4 d-flex align-items-center gap-2 fw-bold">
                        <span class="material-symbols-rounded">directions_bus</span> Buses
                    </a>
                    <a href="{{ route('empresa.dashboard', ['section' => 'documentos']) }}" class="btn btn-{{ $section == 'documentos' ? 'primary' : 'light' }} rounded-pill px-4 d-flex align-items-center gap-2 fw-bold">
                        <span class="material-symbols-rounded">description</span> Documentos
                    </a>
                    <a href="{{ route('empresa.dashboard', ['section' => 'asignaciones']) }}" class="btn btn-{{ $section == 'asignaciones' ? 'primary' : 'light' }} rounded-pill px-4 d-flex align-items-center gap-2 fw-bold">
                        <span class="material-symbols-rounded">event_available</span> Asignaciones
                    </a>
                    <button class="btn btn-light rounded-pill px-4 d-flex align-items-center gap-2 ms-auto fw-bold" data-bs-toggle="modal" data-bs-target="#modalReportes">
                        <span class="material-symbols-rounded text-primary">analytics</span> Reportes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================
         1. SECCIÓN DEFAULT (DASHBOARD stats)
         ========================================== -->
    @if($section == 'dashboard')
    <div class="row mb-5">
        <div class="col-12">
            <span class="text-primary fw-bold text-uppercase small letter-spacing-1 mb-2 d-block">Panel de Auxiliar</span>
            <h1 class="display-5 fw-bold text-dark mb-2">Bienvenido, {{ auth()->user()->primer_nombre }}</h1>
            <p class="text-muted fs-5">Monitoreo y administración de la operación de transporte.</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-inline-flex mx-auto mb-3" style="width: 60px; height: 60px; align-items: center; justify-content: center;">
                    <span class="material-symbols-rounded fs-2">group</span>
                </div>
                <h5 class="fw-bold mb-1" id="kpiUsuarios">{{ $stats['usuarios'] }}</h5>
                <p class="text-muted small mb-0">Usuarios (Personal)</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle d-inline-flex mx-auto mb-3" style="width: 60px; height: 60px; align-items: center; justify-content: center;">
                    <span class="material-symbols-rounded fs-2">directions_bus</span>
                </div>
                <h5 class="fw-bold mb-1" id="kpiBuses">{{ $stats['buses'] }}</h5>
                <p class="text-muted small mb-0">Buses Registrados</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle d-inline-flex mx-auto mb-3" style="width: 60px; height: 60px; align-items: center; justify-content: center;">
                    <span class="material-symbols-rounded fs-2">description</span>
                </div>
                <h5 class="fw-bold mb-1" id="kpiDocumentos">{{ $stats['documentos_pendientes'] }}</h5>
                <p class="text-muted small mb-0">Docs Pendientes</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle d-inline-flex mx-auto mb-3" style="width: 60px; height: 60px; align-items: center; justify-content: center;">
                    <span class="material-symbols-rounded fs-2">assignment_turned_in</span>
                </div>
                <h5 class="fw-bold mb-1">{{ $stats['asignaciones_hoy'] }}</h5>
                <p class="text-muted small mb-0">Asignaciones Hoy</p>
            </div>
        </div>
    </div>
    <!-- SECCIÓN DE GRÁFICAS (Espejo de Admin) -->
    <div class="row g-4 mb-5">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded text-primary">pie_chart</span>
                    Distribución Usuarios vs Docs
                </h5>
                <div style="height: 250px;">
                    <canvas id="chartUsersDocs"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded text-success">bar_chart</span>
                    Buses por Estado
                </h5>
                <div style="height: 250px;">
                    <canvas id="chartBusesEstado"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded text-info">timeline</span>
                    Viajes por Ruta
                </h5>
                <div style="height: 250px;">
                    <canvas id="chartViajesRuta"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ==========================================
         2. SECCIÓN USUARIOS
         ========================================== -->
    @if($section == 'usuarios')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="fw-bold text-dark mb-0">Gestión de Personal</h3>
        <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
            <span class="material-symbols-rounded">person_add</span> Registrar Usuario
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3 border-0">DOCUMENTO</th>
                        <th class="border-0">NOMBRE</th>
                        <th class="border-0">CORREO</th>
                        <th class="border-0">ROL</th>
                        <th class="border-0 text-center">ESTADO</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $u)
                    <tr>
                        <td class="ps-3 fw-bold text-muted small">{{ $u->doc_usuario }}</td>
                        <td class="fw-bold text-dark">{{ $u->primer_nombre }} {{ $u->primer_apellido }}</td>
                        <td>{{ $u->correo }}</td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-2">{{ $u->id_tipo_usuario == 3 ? 'Conductor' : 'Propietario' }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $u->id_estado == 1 ? 'success' : 'danger' }} rounded-pill">{{ $u->id_estado == 1 ? 'Activo' : 'Inactivo' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No hay usuarios registrados</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $usuarios->links() }}</div>
    </div>
    @endif

    <!-- ==========================================
         3. SECCIÓN BUSES
         ========================================== -->
    @if($section == 'buses')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="fw-bold text-dark mb-0">Flota de Vehículos</h3>
        <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalCrearBus">
            <span class="material-symbols-rounded">directions_bus</span> Registrar Bus
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3 border-0">PLACA</th>
                        <th class="border-0">MODELO</th>
                        <th class="border-0">CAPACIDAD</th>
                        <th class="border-0">LICENCIA</th>
                        <th class="border-0 text-center">ESTADO</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($buses as $b)
                    <tr>
                        <td class="ps-3 fw-bold"><span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2">{{ $b->placa }}</span></td>
                        <td>{{ $b->modelo }}</td>
                        <td>{{ $b->capacidad_pasajeros }} Pas.</td>
                        <td><small>{{ $b->linc_transito }}</small></td>
                        <td class="text-center">
                            <span class="badge bg-{{ $b->id_estado == 1 ? 'success' : 'danger' }} rounded-pill">{{ $b->id_estado == 1 ? 'Activo' : 'Inactivo' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No hay buses registrados</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $buses->links() }}</div>
    </div>
    @endif

    <!-- ==========================================
         4. SECCIÓN DOCUMENTOS
         ========================================== -->
    @if($section == 'documentos')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="fw-bold text-dark mb-0">Control de Documentos</h3>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3 border-0">BUS / TIPO</th>
                        <th class="border-0">CARGADO</th>
                        <th class="border-0">VENCIMIENTO</th>
                        <th class="border-0">VALIDACIÓN</th>
                        <th class="border-0 text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documentos as $doc)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold text-dark small">{{ $doc->tipoDocumento->nombre ?? 'N/A' }}</div>
                            <span class="badge bg-primary bg-opacity-10 text-primary x-small px-2 border">{{ $doc->placa }}</span>
                        </td>
                        <td class="small text-muted">{{ $doc->created_at->format('d/m/Y') }}</td>
                        <td class="small fw-bold">{{ $doc->fecha_vencimiento->format('d/m/Y') }}</td>
                        <td>
                            @php
                                $statusColors = [5 => 'warning', 1 => 'success', 8 => 'danger', 2 => 'danger'];
                                $valText = optional($doc->estado)->nombre_estado ?? 'Desconocido';
                                $valColor = $statusColors[$doc->id_estado] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $valColor }}-subtle text-{{ $valColor }} border px-3 rounded-pill">
                                {{ $valText }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($doc->id_estado == 5)
                            <button class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalGestionarDocumento"
                                    data-id="{{ $doc->id_documento }}"
                                    data-tipo="{{ $doc->tipoDocumento->nombre ?? 'Docs' }}"
                                    data-placa="{{ $doc->placa }}"
                                    data-archivo="{{ $doc->archivo }}">
                                Gestionar
                            </button>
                            @else
                                <span class="text-muted x-small">Revisado</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No hay documentos pendientes</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $documentos->links() }}</div>
    </div>
    @endif

    <!-- ==========================================
         5. SECCIÓN ASIGNACIONES
         ========================================== -->
    @if($section == 'asignaciones')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="fw-bold text-dark mb-0">Asignaciones & Turnos</h3>
        <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalAsignarConductor">
            <span class="material-symbols-rounded">event_available</span> Crear Asignación
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3 border-0">PLACA</th>
                        <th class="border-0">RUTA</th>
                        <th class="border-0">CONDUCTOR</th>
                        <th class="border-0">FECHA Y TURNO</th>
                        <th class="border-0 text-center">ESTADO</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asignaciones as $asig)
                    <tr>
                        <td class="ps-3 fw-bold"><span class="badge bg-primary bg-opacity-10 text-primary border px-2">{{ $asig->placa }}</span></td>
                        <td class="fw-bold text-dark">{{ $asig->ruta->nombre_ruta ?? 'N/A' }}</td>
                        <td>{{ $asig->conductor->primer_nombre }} {{ $asig->conductor->primer_apellido }}</td>
                        <td>
                            <div class="d-flex flex-column small">
                                <span class="text-dark fw-bold">{{ \Carbon\Carbon::parse($asig->fecha)->format('H:i') }} - {{ \Carbon\Carbon::parse($asig->fecha)->addHours(8)->format('H:i') }}</span>
                                <span class="text-muted">{{ \Carbon\Carbon::parse($asig->fecha)->format('d/m/Y') }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $asig->id_estado == 1 ? 'success' : 'warning' }} rounded-pill">{{ $asig->estado->nombre_estado }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No hay asignaciones registradas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $asignaciones->links() }}</div>
    </div>
    @endif

</div>

<!-- Inclusión Modular de Modales -->
@include('empresa.auxiliar.modals.crear_usuario')
@include('empresa.auxiliar.modals.crear_bus')
@include('empresa.auxiliar.modals.asignar_conductor')
@include('empresa.auxiliar.modals.gestionar_documentos')
@include('empresa.auxiliar.modals.reportes')

@push('scripts')
<script>
    window.ADMIN_STATS_URL = "{{ route('empresa.dashboard.stats') }}";
</script>
<script src="{{ asset('js/dashboard/admin.js') }}" defer></script>
@endpush

@endsection
