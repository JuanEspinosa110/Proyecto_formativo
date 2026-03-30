@extends('propietario.layouts.app')

@section('title', 'Dashboard Propietario — SIGU')

@section('content')
    <div class="container-fluid pb-5">

        @if(isset($conteoVencidos) && ($conteoVencidos > 0 || $conteoProximos > 0))
            <div class="row mb-4 mt-2">
                <div class="col-12">
                    <div class="card border-0 shadow-lg rounded-4 p-4 mb-0 d-flex flex-row align-items-center gap-4 text-white"
                        style="background: linear-gradient(135deg, {{ $conteoVencidos > 0 ? '#ef4444 0%, #b91c1c' : '#f59e0b 0%, #d97706' }} 100%) !important;">
                        <div class="bg-white bg-opacity-20 p-3 rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 56px; height: 56px;">
                            <span
                                class="material-symbols-rounded fs-2 text-white">{{ $conteoVencidos > 0 ? 'gpp_maybe' : 'info' }}</span>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1">¡Acción Requerida! Alerta de Documentos</h5>
                            <p class="mb-0 opacity-90 small">
                                Tu flota registra <strong>{{ $conteoVencidos }}</strong> documentos vencidos y
                                <strong>{{ $conteoProximos }}</strong> próximos a vencer.
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('propietario.dashboard', ['section' => 'documentos']) }}"
                                class="btn btn-light fw-bold px-4 rounded-pill shadow-sm text-{{ $conteoVencidos > 0 ? 'danger' : 'warning' }}">Gestionar</a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="row mb-4 mt-2">
                <div class="col-12">
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 mb-0 d-flex align-items-center gap-4">
                        <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
                            <span class="material-symbols-rounded fs-1">error</span>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1">Error al procesar la solicitud</h5>
                            <ul class="mb-0 text-dark opacity-75 ps-3 py-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- 1. SECCIÓN DASHBOARD (GENERAL) -->
        @if(!request()->has('section') || request('section') == 'dashboard')
            <div id="section-dashboard">
                <div class="row mb-5">
                    <div class="col-12">
                        <span class="text-primary fw-bold text-uppercase small letter-spacing-1 mb-2 d-block">Panel del
                            Propietario</span>
                        <h1 class="display-5 fw-bold text-dark mb-2">Bienvenido, {{ auth()->user()->primer_nombre }}</h1>
                        <p class="text-muted fs-5">Gestión integral de flota, ingresos y cumplimiento documental.</p>
                    </div>
                </div>

                @if($buses->isEmpty())
                    <div class="alert alert-warning border-0 shadow-sm rounded-4 p-4 mb-5 d-flex align-items-center gap-4">
                        <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                            <span class="material-symbols-rounded fs-1">info</span>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1">Sin vehículos vinculados</h4>
                            <p class="mb-0 text-dark opacity-75">No tienes vehículos asociados a tu documento en este momento. Por
                                favor, contacta al administrador para vincular tus buses y poder visualizar tu operación.</p>
                        </div>
                    </div>
                @endif

                <div class="row g-4 mb-5">
                    <!-- Mi Bus -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                            <div class="card-body p-0">
                                <div class="d-flex flex-column gap-3">
                                    <div class="text-primary">
                                        <span class="material-symbols-rounded" style="font-size: 3rem;">directions_bus</span>
                                    </div>
                                    <div>
                                        <h1 class="fw-black mb-1 text-dark">{{ $buses->count() }}</h1>
                                        <p class="text-muted small fw-bold text-uppercase mb-0">Vehículos Registrados</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Ingresos -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                            <div class="card-body p-0">
                                <div class="d-flex flex-column gap-3">
                                    <div class="text-success">
                                        <span class="material-symbols-rounded" style="font-size: 3rem;">monetization_on</span>
                                    </div>
                                    <div>
                                        <h1 class="fw-black mb-1 text-dark">${{ number_format($ingresosTotales) }}</h1>
                                        <p class="text-muted small fw-bold text-uppercase mb-0">Ingresos Totales
                                            ({{ $conteoPasajeros }} Pax)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Viajes Realizados -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                            <div class="card-body p-0">
                                <div class="d-flex flex-column gap-3">
                                    <div class="text-warning">
                                        <span class="material-symbols-rounded" style="font-size: 3rem;">route</span>
                                    </div>
                                    <div>
                                        <h1 class="fw-black mb-1 text-dark">{{ $conteoAsignaciones }}</h1>
                                        <p class="text-muted small fw-bold text-uppercase mb-0">Viajes Realizados</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Documentos -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                            <div class="card-body p-0">
                                <div class="d-flex flex-column gap-3">
                                    <div class="text-info">
                                        <span class="material-symbols-rounded" style="font-size: 3rem;">folder_open</span>
                                    </div>
                                    <div>
                                        <h1 class="fw-black mb-1 text-dark">{{ $conteoDocumentos }}</h1>
                                        <p class="text-muted small fw-bold text-uppercase mb-0">Documentos Activos</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h5 class="fw-bold mb-0">Estado de la Última Operación</h5>
                                <a href="{{ route('propietario.dashboard', ['section' => 'asignaciones']) }}"
                                    class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm d-inline-flex align-items-center gap-1">
                                    Ver Todo <span class="material-symbols-rounded fs-5">arrow_forward</span>
                                </a>
                            </div>
                            @if($ultimaAsignacion)
                                <div class="bg-light p-4 rounded-4 border-start border-4 border-primary">
                                    <div class="row align-items-center">
                                        <div class="col-md-5">
                                            <span class="text-muted small text-uppercase fw-bold d-block mb-1">Ruta
                                                Actual/Última</span>
                                            <h4 class="fw-bold text-dark mb-0">
                                                {{ $ultimaAsignacion->ruta->nombre_ruta ?? 'Ruta Express' }}</h4>
                                            <div class="d-flex gap-2 align-items-center mt-1">
                                                <span class="badge bg-primary px-2 rounded-1">{{ $ultimaAsignacion->placa }}</span>
                                                <span
                                                    class="text-muted small">{{ $ultimaAsignacion->ruta->codigo_ruta ?? 'REG-001' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="text-muted small text-uppercase fw-bold d-block mb-1">Conductor</span>
                                            <span
                                                class="text-dark fw-medium small">{{ $ultimaAsignacion->conductor->primer_nombre }}
                                                {{ $ultimaAsignacion->conductor->primer_apellido }}</span>
                                        </div>
                                        <div class="col-md-4 text-md-end">
                                            <span
                                                class="badge bg-primary px-3 rounded-pill">{{ optional($ultimaAsignacion->estado)->nombre_estado }}</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <span class="material-symbols-rounded fs-1 opacity-25">pending_actions</span>
                                    <p class="text-muted mt-2">No se han registrado operaciones recientemente.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-lg rounded-4 p-4 h-100 text-white"
                            style="background: linear-gradient(135deg, #110726 0%, #2c1654 100%) !important;">
                            <h5 class="fw-bold mb-4 text-white">Vehículos Vinculados</h5>
                            <div class="d-flex flex-column gap-3 overflow-auto" style="max-height: 200px;">
                                @forelse($buses as $b)
                                    <div
                                        class="d-flex justify-content-between align-items-center text-white-50 small border-bottom border-secondary border-opacity-10 pb-2">
                                        <div>
                                            <span class="text-white fw-bold d-block">{{ $b->placa }}</span>
                                            <span class="x-small">{{ $b->modelo }}</span>
                                        </div>
                                        <span
                                            class="badge bg-{{ $b->id_estado == 1 ? 'success' : ($b->id_estado == 4 ? 'warning text-dark' : 'danger') }} rounded-pill"
                                            style="font-size: 0.6rem;">{{ $b->estado->nombre_estado }}</span>
                                    </div>
                                @empty
                                    <span class="text-white-50">Sin vehículos asociados</span>
                                @endforelse
                            </div>
                            <div class="text-center mt-auto pt-3">
                                <a href="{{ route('propietario.dashboard', ['section' => 'vehiculo']) }}"
                                    class="btn btn-outline-light btn-sm w-100 rounded-pill">Gestionar Flota</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- 2. SECCIÓN MI VEHÍCULO -->
        @if(request('section') == 'vehiculo')
            <div id="section-vehiculo">
                <div class="d-flex align-items-center justify-content-between mb-5">
                    <div>
                        <h1 class="h3 fw-bold text-dark mb-1">Mis Vehículos</h1>
                        <p class="text-muted mb-0">Detalles técnicos e información operativa de tu flota registrada.</p>
                    </div>
                    <button class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 fw-bold shadow-sm rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modalSubirDocumento">
                        <span class="material-symbols-rounded fs-5">description</span>
                        Subir Documento
                    </button>
                </div>

                <div class="row g-4">
                    @forelse($busesPaginated as $b)
                        <div class="col-lg-12 mb-4">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden border-start border-4 border-primary">
                                <div class="row g-0">
                                    <!-- Bloque de Visualización (25%) -->
                                    <div class="col-md-3 bg-light d-flex align-items-center justify-content-center p-4 border-end">
                                        <div class="text-center">
                                            <div class="bg-white shadow-sm rounded-circle p-4 mb-3 d-inline-flex align-items-center justify-content-center"
                                                style="width: 120px; height: 120px;">
                                                <span class="material-symbols-rounded text-primary"
                                                    style="font-size: 3.5rem !important;">directions_bus</span>
                                            </div>
                                            <h2
                                                class="fw-black text-dark mb-0 d-flex align-items-center justify-content-center gap-2">
                                                {{ $b->placa }}
                                                @php
                                                    $peorStatusColor = 'success';
                                                    if (isset($documentosAlerta)) {
                                                        $docsBus = $documentosAlerta->where('placa', $b->placa);
                                                        if ($docsBus->where('estado_expiracion', 'VENCIDO')->count() > 0) {
                                                            $peorStatusColor = 'danger';
                                                        } elseif ($docsBus->where('estado_expiracion', 'PRÓXIMO A VENCER')->count() > 0) {
                                                            $peorStatusColor = 'warning';
                                                        }
                                                    }
                                                @endphp
                                                <span class="material-symbols-rounded text-{{ $peorStatusColor }} fs-4"
                                                    title="Estado Documental">event_available</span>
                                            </h2>
                                            <span
                                                class="badge bg-primary-subtle text-primary px-3 rounded-pill mt-2">{{ $b->modelo }}</span>
                                        </div>
                                    </div>
                                    <!-- Bloque de Información (75%) -->
                                    <div class="col-md-9 p-4">
                                        <div class="row g-3">
                                            <div class="col-sm-6 col-md-3">
                                                <label
                                                    class="text-muted small fw-bold text-uppercase d-block mb-1">Capacidad</label>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="material-symbols-rounded text-muted fs-5">groups</span>
                                                    <span class="text-dark fw-bold">{{ $b->capacidad_pasajeros }} Pas.</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <label
                                                    class="text-muted small fw-bold text-uppercase d-block mb-1">Kilometraje</label>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="material-symbols-rounded text-muted fs-5">speed</span>
                                                    <span class="text-dark fw-bold">{{ number_format($b->kilometraje ?? 0) }}
                                                        KM</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <label class="text-muted small fw-bold text-uppercase d-block mb-1">Licencia</label>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="material-symbols-rounded text-muted fs-5">badge</span>
                                                    <span class="text-dark fw-bold small">{{ $b->linc_transito }}</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <label class="text-muted small fw-bold text-uppercase d-block mb-1">Estado</label>
                                                <span
                                                    class="badge bg-{{ $b->id_estado == 1 ? 'success' : ($b->id_estado == 4 ? 'warning' : 'danger') }}-subtle text-{{ $b->id_estado == 1 ? 'success' : ($b->id_estado == 4 ? 'warning' : 'danger') }} rounded-pill d-inline-block">
                                                    {{ optional($b->estado)->nombre_estado ?? 'Activo' }}
                                                </span>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="text-muted small fw-bold text-uppercase d-block mb-1">Chasis</label>
                                                <span
                                                    class="text-dark family-monospace small">{{ $b->numero_chasis ?? '---' }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="text-muted small fw-bold text-uppercase d-block mb-1">Motor</label>
                                                <span
                                                    class="text-dark family-monospace small">{{ $b->numero_motor ?? '---' }}</span>
                                            </div>
                                            <div class="col-12 mt-3 pt-3 border-top d-flex flex-wrap gap-2">
                                                <button
                                                    class="btn btn-primary d-flex align-items-center gap-2 px-4 rounded-pill fw-bold btn-ver-vehiculo"
                                                    data-placa="{{ $b->placa }}">
                                                    <span class="material-symbols-rounded fs-5">visibility</span>
                                                    Ver Ficha Completa
                                                </button>
                                                <button
                                                    class="btn btn-outline-dark d-flex align-items-center gap-2 px-4 rounded-pill fw-bold btn-historial-docs"
                                                    data-placa="{{ $b->placa }}">
                                                    <span class="material-symbols-rounded fs-5">history_edu</span>
                                                    Historial Documental
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info border-0 shadow-sm rounded-4 p-5 text-center">
                                <span class="material-symbols-rounded display-1 opacity-25 d-block mb-3">contact_support</span>
                                <h4 class="fw-bold">Sin vehículos asociados</h4>
                                <p class="text-muted mb-0">No se encontraron vehículos vinculados a tu cuenta de propietario.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
                <div class="mt-4">
                    {{ $busesPaginated->appends(['section' => 'vehiculo'])->links() }}
                </div>
            </div>
        @endif

        <!-- 3. SECCIÓN ASIGNACIONES -->
        @if(request('section') == 'asignaciones')
            <div id="section-asignaciones">
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 fw-bold text-dark">Mis Asignaciones</h1>
                        <p class="text-muted">Resumen de las últimas rutas programadas para tus vehículos.</p>
                    </div>
                </div>

                <!-- Filtro por Placa -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <form action="{{ route('propietario.dashboard') }}" method="GET" class="row g-3">
                        <input type="hidden" name="section" value="asignaciones">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Bus (Placa)</label>
                            <input type="text" name="placa" class="form-control rounded-3" placeholder="Ej: ABC-123"
                                value="{{ request('placa') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill fw-bold shadow-sm">Buscar</button>
                            <a href="{{ route('propietario.dashboard', ['section' => 'asignaciones']) }}"
                                class="btn btn-light rounded-circle shadow-sm">
                                <span class="material-symbols-rounded">restart_alt</span>
                            </a>
                        </div>
                    </form>
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
                                @forelse($asignacionesRecientes as $asig)
                                    <tr>
                                        <td class="ps-3"><span
                                                class="badge bg-primary bg-opacity-10 text-primary border-primary border px-2">{{ $asig->placa }}</span>
                                        </td>
                                        <td class="fw-bold text-dark">{{ $asig->ruta->nombre_ruta ?? 'N/A' }}</td>
                                        <td class="text-muted">{{ $asig->conductor->primer_nombre }}
                                            {{ $asig->conductor->primer_apellido }}</td>
                                        <td>
                                            <div class="d-flex flex-column small">
                                                <span
                                                    class="text-dark fw-bold">{{ \Carbon\Carbon::parse($asig->fecha)->format('H:i') }}
                                                    - {{ \Carbon\Carbon::parse($asig->fecha)->addHours(8)->format('H:i') }}</span>
                                                <span
                                                    class="text-muted">{{ \Carbon\Carbon::parse($asig->fecha)->format('d/m/Y') }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-{{ $asig->id_estado == 1 ? 'success' : 'warning' }} rounded-pill">{{ $asig->estado->nombre_estado }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <span class="material-symbols-rounded fs-1 opacity-25">pending_actions</span>
                                            <p class="text-muted mt-2 mb-0">No hay asignaciones recientes registradas.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4">
                    {{ $asignacionesRecientes->appends(['section' => 'asignaciones'])->links() }}
                </div>
            </div>
        @endif

        <!-- 4. SECCIÓN DOCUMENTOS -->
        @if(request('section') == 'documentos')
            <div id="section-documentos">
                <div class="d-flex align-items-center justify-content-between gap-4 mb-5">
                    <div>
                        <h1 class="h3 fw-bold text-dark mb-1">Documentos del Bus</h1>
                        <p class="text-muted mb-0">Control de legalidad y vencimientos de tu vehículo.</p>
                    </div>
                    <button class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 fw-bold shadow-sm rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modalSubirDocumento">
                        <span class="material-symbols-rounded">add</span>
                        Nuevo Documento
                    </button>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-uppercase small fw-bold text-muted border-0">Vehículo / Tipo</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Nombre del Archivo</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Fecha Carga</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Vencimiento</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Vigencia</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Validación</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted border-0 text-center pe-4">Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documentos as $doc)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div
                                                    class="bg-{{ $doc->status_color }} bg-opacity-10 text-{{ $doc->status_color }} p-2 rounded-3">
                                                    <span class="material-symbols-rounded">description</span>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark small">{{ $doc->tipoDocumento->nombre ?? 'N/A' }}
                                                    </div>
                                                    <span
                                                        class="badge bg-primary bg-opacity-10 text-primary x-small rounded-pill px-2 border border-primary border-opacity-10">{{ $doc->placa }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-muted small text-truncate" style="max-width: 150px;"
                                                title="{{ $doc->nombre }}">
                                                {{ $doc->nombre }}
                                            </div>
                                        </td>
                                        <td class="text-muted small">
                                            {{ $doc->created_at->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark small">
                                                {{ $doc->fecha_vencimiento->format('d/m/Y') }}
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $doc->status_color }}-subtle text-{{ $doc->status_color }} border border-{{ $doc->status_color }} rounded-pill px-3 py-1 x-small fw-bold">
                                                {{ $doc->estado_expiracion }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $valColor = 'secondary';
                                                $valText = 'Desconocido';
                                                if ($doc->id_estado == 1) {
                                                    $valColor = 'success';
                                                    $valText = 'Aprobado';
                                                } elseif ($doc->id_estado == 10) {
                                                    $valColor = 'danger';
                                                    $valText = 'Rechazado';
                                                } elseif ($doc->id_estado == 6) {
                                                    $valColor = 'warning';
                                                    $valText = 'Pendiente';
                                                } elseif ($doc->id_estado == 11) {
                                                    $valColor = 'info';
                                                    $valText = 'Archivado';
                                                }
                                            @endphp
                                            <span
                                                class="badge bg-{{ $valColor }}-subtle text-{{ $valColor }} border border-{{ $valColor }} rounded-pill px-3 py-1 x-small fw-bold">
                                                {{ $valText }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-center gap-2">
                                                @if($doc->archivo)
                                                    <button type="button"
                                                        class="btn btn-sm btn-light border p-2 rounded-circle text-dark"
                                                        onclick="mostrarVisor('{{ asset('storage/' . $doc->archivo) }}', '{{ $doc->tipoDocumento->nombre ?? 'Archivo' }}')"
                                                        title="Vista Previa">
                                                        <span class="material-symbols-rounded fs-6">visibility</span>
                                                    </button>
                                                    <a href="{{ asset('storage/' . $doc->archivo) }}" download
                                                        class="btn btn-sm btn-light border p-2 rounded-circle text-primary"
                                                        title="Descargar">
                                                        <span class="material-symbols-rounded fs-6">download</span>
                                                    </a>
                                                @endif
                                                <button
                                                    class="btn btn-sm btn-light border p-2 rounded-circle text-{{ $doc->status_color }}"
                                                    data-bs-toggle="modal" data-bs-target="#modalActualizarDocumento"
                                                    data-id="{{ $doc->id_documento }}" data-nombre="{{ $doc->nombre }}"
                                                    data-expedicion="{{ $doc->fecha_expedicion->format('Y-m-d') }}"
                                                    data-vencimiento="{{ $doc->fecha_vencimiento->format('Y-m-d') }}"
                                                    title="Actualizar / Renovar">
                                                    <span class="material-symbols-rounded fs-6">refresh</span>
                                                </button>
                                                <button
                                                    class="btn btn-sm btn-light border p-2 rounded-circle text-dark btn-historial-docs"
                                                    data-placa="{{ $doc->placa }}"
                                                    title="Ver expediente e historial de todos los documentos del vehículo">
                                                    <span class="material-symbols-rounded fs-6">history_edu</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <span class="material-symbols-rounded fs-1 opacity-25">folder_off</span>
                                            <p class="text-muted mt-2">No se encontraron documentos registrados.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4">
                    {{ $documentos->appends(['section' => 'documentos'])->links() }}
                </div>
            </div>
        @endif

        <!-- 5. SECCIÓN HISTORIAL -->
        @if(request('section') == 'historial')
            <div id="section-historial">
                <div class="d-flex align-items-center justify-content-between gap-4 mb-4">
                    <div>
                        <h1 class="h3 fw-bold text-dark mb-1">Historial de Operaciones</h1>
                        <p class="text-muted mb-0">Consulta detallada de viajes, pasajeros e ingresos por turno.</p>
                    </div>
                </div>

                <!-- Filtros Avanzados -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <form action="{{ route('propietario.dashboard') }}" method="GET" class="row g-3">
                        <input type="hidden" name="section" value="historial">
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Bus (Placa)</label>
                            <input type="text" name="placa" class="form-control rounded-3" placeholder="Ej: ABC-123"
                                value="{{ request('placa') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Conductor</label>
                            <input type="text" name="conductor" class="form-control rounded-3" placeholder="Nombre..."
                                value="{{ request('conductor') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Ruta</label>
                            <input type="text" name="ruta" class="form-control rounded-3" placeholder="Nombre ruta..."
                                value="{{ request('ruta') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Fecha</label>
                            <input type="date" name="fecha" class="form-control rounded-3" value="{{ request('fecha') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">En horario</label>
                            <input type="time" name="horario" class="form-control rounded-3" value="{{ request('horario') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm">Buscar</button>
                            <a href="{{ route('propietario.dashboard', ['section' => 'historial']) }}"
                                class="btn btn-light rounded-circle shadow-sm">
                                <span class="material-symbols-rounded">restart_alt</span>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Tabla Historial (Viajes/Asignaciones) -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-5">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3 border-0">ID</th>
                                    <th class="border-0">BUS</th>
                                    <th class="border-0">RUTA / CONDUCTOR</th>
                                    <th class="border-0">TURNO</th>
                                    <th class="border-0 text-center">PASAJEROS</th>
                                    <th class="border-0 text-end pe-3">INGRESOS</th>
                                    <th class="border-0 text-center">ANALIZAR</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historialAsignaciones as $asig)
                                    <tr>
                                        <td class="ps-3 fw-bold text-muted small">#{{ $asig->id_viaje }}</td>
                                        <td><span
                                                class="badge bg-primary bg-opacity-10 text-primary border-primary border px-2">{{ $asig->placa }}</span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $asig->ruta->nombre_ruta ?? 'N/A' }}</div>
                                            <small class="text-muted d-block">{{ $asig->conductor->primer_nombre }}
                                                {{ $asig->conductor->primer_apellido }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column small">
                                                <span
                                                    class="text-dark fw-bold">{{ \Carbon\Carbon::parse($asig->fecha)->format('H:i') }}
                                                    - {{ \Carbon\Carbon::parse($asig->fecha)->addHours(8)->format('H:i') }}</span>
                                                <span
                                                    class="text-muted">{{ \Carbon\Carbon::parse($asig->fecha)->format('d/m/Y') }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-light text-dark border rounded-pill px-3">{{ $asig->ventas->count() }}</span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="fw-bold text-success">
                                                ${{ number_format($asig->ventas->count() * $precioPasaje) }}</div>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-primary btn-sm rounded-pill px-3 fw-bold btn-detalle-asignacion"
                                                data-id="{{ $asig->id_viaje }}">
                                                Ver Detalle
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <span class="material-symbols-rounded fs-1 opacity-25">manage_search</span>
                                            <p class="text-muted mt-2 mb-0">No hay registros que coincidan con la búsqueda.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $historialAsignaciones->appends(['section' => 'historial'])->links() }}
                    </div>
                </div>

                <h5 class="fw-bold text-dark mb-3">Cambios Técnicos del Vehículo</h5>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3 border-0">FECHA CAMBIO</th>
                                    <th class="border-0">PLACA</th>
                                    <th class="border-0">TIPO DE CAMBIO</th>
                                    <th class="border-0">DETALLE DEL CAMBIO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historialCambios as $h)
                                    <tr>
                                        <td class="ps-3 text-muted small">
                                            {{ \Carbon\Carbon::parse($h->created_at)->format('d/m/Y H:i') }}
                                        </td>
                                        <td><span
                                                class="badge bg-secondary bg-opacity-10 text-secondary border px-2">{{ $h->placa }}</span>
                                        </td>
                                        <td class="fw-bold text-dark">{{ $h->tipo_cambio }}</td>
                                        <td class="text-muted small">{{ $h->detalle ?? 'Sin detalle registrado' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <span class="material-symbols-rounded fs-1 opacity-25">manage_search</span>
                                            <p class="text-muted mt-2 mb-0">Aún no se registran cambios históricos para este
                                                vehículo.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $historialCambios->appends(['section' => 'historial'])->links() }}
                    </div>
                </div>
            </div>
        @endif

        <!-- 6. SECCIÓN GANANCIAS (INGRESOS) -->
        @if(request('section') == 'ganancias')
                <style>
                    .hover-lift {
                        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
                    }

                    .hover-lift:hover {
                        transform: translateY(-4px) !important;
                        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
                    }

                    .btn-switch {
                        transition: all 0.2s;
                        color: rgba(255, 255, 255, 0.7);
                    }

                    .btn-switch:hover {
                        color: white;
                    }

                    .btn-switch.active {
                        background-color: white !important;
                        color: #0f172a !important;
                        font-weight: 700 !important;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
                    }

                    .card-gradient-earnings {
                        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%) !important;
                    }
                </style>

                <!-- 1. ENCABEZADO: TÍTULO + FILTRO -->
                <div class="d-flex align-items-center justify-content-between gap-4 mb-4 flex-wrap">
                    <div>
                        <h2 class="h4 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                            <span class="material-symbols-rounded text-primary">analytics</span>
                            Ganancias del Bus
                        </h2>
                        <p class="text-muted mb-0 small">Consulta detallada de los ingresos generados por la operación.</p>
                    </div>
                    <!-- Filtro por Mes Compacto -->
                    <div class="bg-white p-2 rounded-4 shadow-sm border">
                        <form action="{{ route('propietario.dashboard') }}" method="GET"
                            class="d-flex align-items-center gap-2 m-0">
                            <input type="hidden" name="section" value="ganancias">
                            <div class="d-flex align-items-center gap-2">
                                <label class="form-label x-small fw-bold text-muted text-uppercase mb-0 text-nowrap">Mes:</label>
                                <input type="month" name="mes_seleccionado"
                                    class="form-control form-control-sm border-0 bg-light rounded-3"
                                    value="{{ request('mes_seleccionado') }}" style="width: 140px;">
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">Filtrar</button>
                        </form>
                    </div>
                </div>

                <!-- 2. TARJETA RESUMEN CON TABS -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-12">
                        <div class="card border-0 shadow-lg rounded-5 overflow-hidden p-4 text-white card-gradient-earnings">
                            <div class="row align-items-center">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                                        <div>
                                            <span class="text-white-50 small fw-bold text-uppercase d-block mb-1"
                                                id="earnings_label">Ingresos de Hoy</span>
                                            <h1 class="display-5 fw-black mb-0 text-white" id="earnings_amount">
                                                ${{ number_format($gananciasHoy ?? 0) }}</h1>
                                        </div>
                                        <div class="d-flex gap-2 bg-white bg-opacity-10 p-1 rounded-pill">
                                            <button class="btn btn-sm rounded-pill px-4 fw-bold btn-switch active"
                                                data-amount="{{ number_format($gananciasHoy ?? 0) }}" data-label="Ingresos de Hoy"
                                                onclick="switchEarnings(this)">Hoy</button>
                                            <button class="btn btn-sm rounded-pill px-4 fw-bold btn-switch"
                                                data-amount="{{ number_format($gananciasSemana ?? 0) }}"
                                                data-label="Ingresos de la Semana" onclick="switchEarnings(this)">Semana</button>
                                            <button class="btn btn-sm rounded-pill px-4 fw-bold btn-switch"
                                                data-amount="{{ number_format($gananciasMes ?? 0) }}" data-label="Ingresos del Mes"
                                                onclick="switchEarnings(this)">Mes</button>
                                        </div>
                                    </div>

                                    <hr class="border-white border-opacity-10 my-4">

                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div
                                                class="bg-white bg-opacity-10 p-3 rounded-4 border border-white border-opacity-10 d-flex align-items-center gap-3 hover-lift">
                                                <div
                                                    class="bg-warning bg-opacity-25 p-2 rounded-3 text-warning d-flex align-items-center justify-content-center">
                                                    <span class="material-symbols-rounded">local_shipping</span>
                                                </div>
                                                <div>
                                                    <span class="small d-block text-white-50">Viajes Realizados</span>
                                                    <span class="h5 fw-black text-warning mb-0">{{ $conteoAsignaciones }}
                                                        viajes</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div
                                                class="bg-white bg-opacity-10 p-3 rounded-4 border border-white border-opacity-10 d-flex align-items-center gap-3 hover-lift">
                                                <div
                                                    class="bg-success bg-opacity-25 p-2 rounded-3 text-success d-flex align-items-center justify-content-center">
                                                    <span class="material-symbols-rounded">groups</span>
                                                </div>
                                                <div>
                                                    <span class="small d-block text-white-50">Pasajeros Transportados</span>
                                                    <span
                                                        class="h5 fw-black text-success mb-0">{{ number_format($conteoPasajeros) }}
                                                        pax</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div
                                                class="bg-white bg-opacity-10 p-3 rounded-4 border border-white border-opacity-10 d-flex align-items-center gap-3 hover-lift">
                                                <div
                                                    class="bg-info bg-opacity-25 p-2 rounded-3 text-info d-flex align-items-center justify-content-center">
                                                    <span class="material-symbols-rounded">payments</span>
                                                </div>
                                                <div>
                                                    <span class="small d-block text-white-50">Tarifa Promedio</span>
                                                    <span class="h5 fw-black text-info mb-0">$3,300</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. INGRESO CONSOLIDADO POR VEHÍCULO -->
                <h6 class="fw-bold text-muted text-uppercase small letter-spacing-1 mb-3 d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded fs-5 text-primary">directions_bus</span>
                    Ingreso Consolidado por Vehículo
                </h6>
                <div class="row g-3 mb-4">
                    @forelse($ingresosPorBus as $bus)
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white hover-lift">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span
                                        class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 rounded-pill fw-bold">{{ $bus->placa }}</span>
                                    <div class="d-flex align-items-center gap-1 text-muted small">
                                        <span class="material-symbols-rounded fs-6">groups</span>
                                        <span>{{ number_format($bus->total_pasajeros) }} Pax</span>
                                    </div>
                                </div>
                                <div class="mt-3 text-center">
                                    <span class="text-muted x-small text-uppercase fw-bold d-block">Ganancia Acumulada</span>
                                    <h3 class="fw-black mb-0 text-success">${{ number_format($bus->total_ingresos) }}</h3>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white">
                                <span class="material-symbols-rounded display-5 text-muted opacity-25 mb-2">money_off</span>
                                <p class="text-muted small mb-0">No se registran ganancias vinculadas a los vehículos.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- 4. TABLA DE INGRESOS POR TRAYECTO -->
                <div id="ganancias-table-container">
                    @include('propietario.partials.ganancias_table')
                </div>
            </div>
        @endif

    </div>

    <!-- Modales -->
    @if($buses->isNotEmpty())
        <!-- Modal Subir Documento -->
        <div class="modal fade" id="modalSubirDocumento" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 pt-4 px-4">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                            <span class="material-symbols-rounded text-primary">add_circle</span>
                            Nuevo Documento
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('propietario.subirDocumento') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Vehículo <span
                                            class="text-danger">*</span></label>
                                    <select name="placa" id="subir_placa" class="form-select rounded-3 shadow-sm border-light"
                                        required>
                                        <option value="" disabled selected>Seleccionar placa...</option>
                                        @foreach($buses as $b)
                                            <option value="{{ $b->placa }}"
                                                data-modelo="{{ preg_replace('/[^0-9]/', '', $b->modelo) }}">{{ $b->placa }} -
                                                {{ $b->modelo }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Tipo de Documento <span
                                            class="text-danger">*</span></label>
                                    <select name="id_tipo_documento" id="subir_id_tipo_documento"
                                        class="form-select rounded-3 shadow-sm border-light" required>
                                        <option value="" disabled selected>Elegir tipo...</option>
                                        @foreach($tiposDocumento as $tipo)
                                            <option value="{{ $tipo->id_tipo_documento }}">{{ $tipo->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 d-none" id="col_fecha_nacimiento">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Fecha de Nacimiento
                                        (Conductor) <span class="text-danger">*</span></label>
                                    <input type="date" name="fecha_nacimiento" id="subir_fecha_nacimiento"
                                        class="form-control rounded-3" placeholder="Para calcular vigencia">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Expedición</label>
                                    <input type="date" name="fecha_expedicion" id="subir_fecha_expedicion"
                                        class="form-control rounded-3" required>
                                </div>
                                <div class="col-6" id="div_fecha_vencimiento">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Vencimiento</label>
                                    <input type="date" name="fecha_vencimiento" id="subir_fecha_vencimiento"
                                        class="form-control rounded-3" required>
                                    <div class="invalid-feedback fw-bold">Fecha de vencimiento es inválida (ya expiró).</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Archivo digital
                                        (PDF/JPG)</label>
                                    <input type="file" name="archivo" class="form-control rounded-3"
                                        accept=".pdf,.jpg,.jpeg,.png" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="button" class="btn btn-light fw-bold px-4 rounded-pill"
                                data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm rounded-pill">Subir
                                archivo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Actualizar Documento -->
        <div class="modal fade" id="modalActualizarDocumento" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 pt-4 px-4">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                            <span class="material-symbols-rounded text-warning">edit_square</span>
                            Corregir Documento
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formActualizarDoc" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Documento</label>
                                    <input type="text" id="edit_nombre_display" class="form-control bg-light rounded-3" readonly
                                        disabled>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Expedición</label>
                                    <input type="date" name="fecha_expedicion" id="edit_expedicion"
                                        class="form-control rounded-3" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Vencimiento</label>
                                    <input type="date" name="fecha_vencimiento" id="edit_vencimiento"
                                        class="form-control rounded-3" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Nuevo Archivo
                                        (Opcional)</label>
                                    <input type="file" name="archivo" class="form-control rounded-3"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="button" class="btn btn-light fw-bold px-4 rounded-pill"
                                data-bs-dismiss="modal">Descartar</button>
                            <button type="submit" class="btn btn-warning fw-bold px-4 shadow-sm rounded-pill text-dark">Aplicar
                                cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal Registrar Gasto -->

        <!-- Modal Ver Vehículo (Ficha Completa) -->
        <div class="modal fade" id="modalVerVehiculo" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 p-4 bg-light">
                        <h5 class="modal-title fw-black text-dark d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                                <span class="material-symbols-rounded text-primary">analytics</span>
                            </div>
                            Expediente del Vehículo: <span id="ver_placa" class="text-primary">---</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 bg-light">
                        <!-- 1. Información General y Conductor -->
                        <div class="row g-4 mb-4">
                            <!-- Datos Técnicos -->
                            <div class="col-lg-8">
                                <div class="card border-0 shadow-sm rounded-4 h-100">
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold text-muted text-uppercase mb-4 d-flex align-items-center gap-2">
                                            <span class="material-symbols-rounded fs-5 text-primary">info</span>
                                            Información Técnica
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-md-3 col-6">
                                                <div class="p-3 rounded-4 bg-light border border-white h-100">
                                                    <label
                                                        class="text-muted x-small fw-bold text-uppercase d-block mb-1">Capacidad</label>
                                                    <span id="ver_capacidad" class="text-dark fw-bold">---</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-6">
                                                <div class="p-3 rounded-4 bg-light border border-white h-100">
                                                    <label
                                                        class="text-muted x-small fw-bold text-uppercase d-block mb-1">Kilometraje</label>
                                                    <span id="ver_kilometraje" class="text-dark fw-bold">---</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-6">
                                                <div class="p-3 rounded-4 bg-light border border-white h-100">
                                                    <label
                                                        class="text-muted x-small fw-bold text-uppercase d-block mb-1">Licencia</label>
                                                    <span id="ver_licencia" class="text-dark fw-bold">---</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-6">
                                                <div class="p-3 rounded-4 bg-light border border-white h-100">
                                                    <label
                                                        class="text-muted x-small fw-bold text-uppercase d-block mb-1">Estado</label>
                                                    <span id="ver_estado" class="badge rounded-pill px-3 py-2">---</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="p-3 rounded-4 bg-light border border-white">
                                                    <label class="text-muted x-small fw-bold text-uppercase d-block mb-1">Número
                                                        de Chasis</label>
                                                    <span id="ver_chasis"
                                                        class="text-dark family-monospace small fw-bold">---</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="p-3 rounded-4 bg-light border border-white">
                                                    <label class="text-muted x-small fw-bold text-uppercase d-block mb-1">Número
                                                        de Motor</label>
                                                    <span id="ver_motor"
                                                        class="text-dark family-monospace small fw-bold">---</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Conductor -->
                            <div class="col-lg-4">
                                <div class="card border-0 shadow-sm rounded-4 h-100">
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold text-muted text-uppercase mb-4 d-flex align-items-center gap-2">
                                            <span class="material-symbols-rounded fs-5 text-primary">person</span>
                                            Conductor Actual
                                        </h6>
                                        <div id="ver_conductor_box" class="h-100">
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                                                    <span class="material-symbols-rounded fs-2">person</span>
                                                </div>
                                                <div>
                                                    <h5 id="cond_nombre" class="fw-black mb-0">Cargando...</h5>
                                                    <span class="badge bg-primary-subtle text-primary x-small"
                                                        id="cond_ruta">---</span>
                                                </div>
                                            </div>
                                            <div class="p-3 rounded-4 bg-light border d-flex flex-column gap-2 small">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">Documento:</span>
                                                    <span id="cond_doc" class="fw-bold">---</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">Licencia:</span>
                                                    <span id="cond_lic" class="fw-bold">---</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="no_conductor_box"
                                            class="d-none alert alert-light text-center rounded-4 p-4 mb-0">
                                            <p class="mb-0 text-muted small">No hay conductores asignados actualmente.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Documentación del Vehículo -->
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-0 p-4">
                                <h6 class="fw-bold text-dark text-uppercase mb-0 d-flex align-items-center gap-2">
                                    <span class="material-symbols-rounded text-primary">folder_shared</span>
                                    Documentación del Vehículo
                                </h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 border-0 small fw-bold text-muted">TIPO DE DOCUMENTO</th>
                                            <th class="border-0 small fw-bold text-muted">FECHA CARGA</th>
                                            <th class="border-0 small fw-bold text-muted">VENCIMIENTO</th>
                                            <th class="border-0 small fw-bold text-muted">ESTADO</th>
                                            <th class="border-0 small fw-bold text-muted text-center pe-4">ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ver_docs_body">
                                        <!-- Los documentos se cargarán aquí -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 bg-light">
                        <button type="button" class="btn btn-dark fw-bold px-5 rounded-pill" data-bs-dismiss="modal">Cerrar
                            Expediente</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Bóveda Histórica -->
        <div class="modal fade" id="modalBovedaHistorial" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom p-4" style="background: #f8fafc;">
                        <h5 class="modal-title fw-bold d-flex align-items-center gap-3 text-dark">
                            <span class="material-symbols-rounded text-primary">history_edu</span>
                            Bóveda Histórica de Documentos: <span id="boveda_placa" class="text-primary">---</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 bg-light">
                        <div id="boveda_content" class="d-flex flex-column gap-4">
                            <!-- Dynamic content groups -->
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 bg-light">
                        <button type="button" class="btn btn-dark fw-bold px-5 rounded-pill" data-bs-dismiss="modal">Cerrar
                            Bóveda</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Visor de Documentos -->
        <div class="modal fade" id="modalVisorDocumento" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 pt-4 px-4 bg-dark text-white">
                        <h5 class="modal-title fw-bold" id="visor_titulo">Visualización de Documento</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 bg-secondary bg-opacity-10" style="height: 70vh;">
                        <iframe id="visor_iframe" class="w-100 h-100 d-none border-0" src=""></iframe>
                        <div id="visor_image_container"
                            class="w-100 h-100 d-none d-flex align-items-center justify-content-center p-3">
                            <img id="visor_img" src="" class="img-fluid rounded-3 shadow-sm" style="max-height: 100%;">
                        </div>
                        <div id="visor_error"
                            class="w-100 h-100 d-none d-flex flex-column align-items-center justify-content-center text-muted">
                            <span class="material-symbols-rounded display-1 mb-3">error</span>
                            <p class="fw-bold">No se puede previsualizar este archivo.</p>
                            <a id="visor_download" href="#" class="btn btn-primary rounded-pill px-4" download>Descargar
                                Archivo</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Detalle Asignación -->
    <div class="modal fade" id="modalDetalleAsignacion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom p-4" style="background: #f8fafc; border-color: #e5e7eb !important;">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-3 text-dark">
                        <span class="material-symbols-rounded text-primary">history_edu</span>
                        Detalle de la Asignación: <span id="det_id_viaje" class="text-primary">#---</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <!-- Información de la Asignación -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                                <label class="text-muted x-small fw-bold text-uppercase d-block mb-1">Bus</label>
                                <span id="det_placa" class="text-primary fw-black fs-5">---</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                                <label class="text-muted x-small fw-bold text-uppercase d-block mb-1">Conductor</label>
                                <span id="det_conductor" class="text-dark fw-bold">---</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                                <label class="text-muted x-small fw-bold text-uppercase d-block mb-1">Ruta Asignada</label>
                                <span id="det_ruta" class="text-dark fw-bold">---</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                                <label class="text-muted x-small fw-bold text-uppercase d-block mb-1">Horario Turno
                                    (8h)</label>
                                <span class="text-dark fw-bold">
                                    <span id="det_inicio">--:--</span> - <span id="det_fin">--:--</span>
                                </span>
                                <small id="det_fecha" class="text-muted d-block">--/--/----</small>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark text-uppercase mb-3 d-flex align-items-center gap-2">
                        <span class="material-symbols-rounded text-primary">route</span>
                        Viajes Realizados en el Turno
                    </h6>
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-white border-bottom">
                                    <tr>
                                        <th class="ps-3 border-0">TRAYECTO</th>
                                        <th class="border-0">SALIDA</th>
                                        <th class="border-0">LLEGADA</th>
                                        <th class="border-0 text-center">PASAJEROS</th>
                                        <th class="border-0 text-end pe-3">INGRESO</th>
                                    </tr>
                                </thead>
                                <tbody id="det_recorridos_body">
                                    <!-- Recorridos se cargarán aquí -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Resumen Estadístico -->
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 p-3 mb-0 bg-white">
                                <label class="text-muted x-small fw-bold text-uppercase d-block mb-1">Viajes O → D</label>
                                <h3 id="stat_od" class="fw-black text-dark mb-0">0</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 p-3 mb-0 bg-white">
                                <label class="text-muted x-small fw-bold text-uppercase d-block mb-1">Viajes D → O</label>
                                <h3 id="stat_do" class="fw-black text-dark mb-0">0</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 p-3 mb-0 bg-white">
                                <label class="text-muted x-small fw-bold text-uppercase d-block mb-1">Total
                                    Pasajeros</label>
                                <h3 id="stat_pax" class="fw-black text-success mb-0">0</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 p-3 mb-0 text-white"
                                style="background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;">
                                <label class="text-white-50 x-small fw-bold text-uppercase d-block mb-1">Total
                                    Ingresos</label>
                                <h3 id="stat_ingresos" class="fw-black mb-0">$0</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-light">
                    <button type="button" class="btn btn-dark fw-bold px-5 rounded-pill" data-bs-dismiss="modal">Cerrar
                        Detalle</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modalActualizar = document.getElementById('modalActualizarDocumento');
                if (modalActualizar) {
                    modalActualizar.addEventListener('show.bs.modal', function (event) {
                        const button = event.relatedTarget;
                        const id = button.getAttribute('data-id');
                        const nombre = button.getAttribute('data-nombre');
                        const expedicion = button.getAttribute('data-expedicion');
                        const vencimiento = button.getAttribute('data-vencimiento');

                        const form = document.getElementById('formActualizarDoc');
                        form.action = `/propietario/documento/${id}`;

                        document.getElementById('edit_nombre_display').value = nombre;
                        document.getElementById('edit_expedicion').value = expedicion;
                        document.getElementById('edit_vencimiento').value = vencimiento;
                    });
                }
                const modalKilometraje = document.getElementById('modalKilometraje');
                if (modalKilometraje) {
                    modalKilometraje.addEventListener('show.bs.modal', function (event) {
                        const button = event.relatedTarget;
                        const placa = button.getAttribute('data-placa');
                        const actual = button.getAttribute('data-actual');

                        const form = document.getElementById('formKilometraje');
                        form.action = `/propietario/bus/${placa}/kilometraje`;

                        document.getElementById('km_placa_display').value = placa;
                        const input = document.getElementById('km_input');
                        input.value = actual;
                        input.min = actual;

                        document.getElementById('km_hint').innerHTML = `Kilometraje actual: <strong>${new Intl.NumberFormat().format(actual)} KM</strong>`;
                    });
                }
                // 3. Ver Ficha Completa del Vehículo
                const modalVer = new bootstrap.Modal(document.getElementById('modalVerVehiculo'));
                document.querySelectorAll('.btn-ver-vehiculo').forEach(btn => {
                    btn.addEventListener('click', async function () {
                        const placa = this.getAttribute('data-placa');
                        try {
                            const response = await fetch(`/propietario/bus/${placa}/detalles`);
                            const data = await response.json();

                            // Llenar Info Técnica
                            document.getElementById('ver_placa').innerText = data.bus.placa;
                            document.getElementById('ver_capacidad').innerText = data.bus.capacidad_pasajeros + ' Pasajeros';
                            document.getElementById('ver_kilometraje').innerText = new Intl.NumberFormat().format(data.bus.kilometraje) + ' KM';
                            document.getElementById('ver_licencia').innerText = data.bus.linc_transito || '---';
                            document.getElementById('ver_chasis').innerText = data.bus.numero_chasis || '---';
                            document.getElementById('ver_motor').innerText = data.bus.numero_motor || '---';

                            const elEstado = document.getElementById('ver_estado');
                            elEstado.innerText = data.bus.estado.nombre_estado;
                            let statusClass = 'danger';
                            if (data.bus.id_estado == 1) statusClass = 'success';
                            else if (data.bus.id_estado == 4) statusClass = 'warning';

                            elEstado.className = 'badge rounded-pill px-3 py-2 bg-' + statusClass + '-subtle text-' + statusClass;

                            // Llenar Conductor
                            if (data.conductor) {
                                document.getElementById('cond_nombre').innerText = data.conductor.nombre;
                                document.getElementById('cond_doc').innerText = data.conductor.documento;
                                document.getElementById('cond_lic').innerText = data.conductor.licencia;
                                document.getElementById('cond_ruta').innerText = data.ruta || 'Sin ruta';
                                document.getElementById('ver_conductor_box').classList.remove('d-none');
                                document.getElementById('no_conductor_box').classList.add('d-none');
                            } else {
                                document.getElementById('ver_conductor_box').classList.add('d-none');
                                document.getElementById('no_conductor_box').classList.remove('d-none');
                            }

                            // Llenar Documentos
                            const docsBody = document.getElementById('ver_docs_body');
                            docsBody.innerHTML = '';

                            if (data.documentos.length === 0) {
                                docsBody.innerHTML = '<tr><td colspan="5" class="text-center py-5 text-muted">No hay documentos registrados profesionalmente</td></tr>';
                            }

                            data.documentos.forEach(doc => {
                                const tr = document.createElement('tr');

                                let renewBtn = '';
                                if (doc.status_vigencia !== 'VIGENTE') {
                                    renewBtn = `<button class="btn btn-sm btn-dark rounded-pill px-3 fw-bold x-small btn-renovar" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#modalSubirDocumento" data-placa="${placa}" data-tipo="${doc.id_tipo_documento}">Renovar</button>`;
                                }

                                // Formatear fechas
                                const fechaCarga = new Date(doc.created_at).toLocaleDateString();
                                const fechaVenc = new Date(doc.fecha_vencimiento).toLocaleDateString();

                                tr.innerHTML = `
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-${doc.status_color} bg-opacity-10 text-${doc.status_color} p-2 rounded-3">
                                                <span class="material-symbols-rounded">description</span>
                                            </div>
                                            <div class="fw-bold text-dark small">${doc.tipo_documento.nombre}</div>
                                        </div>
                                    </td>
                                    <td class="text-muted small">${fechaCarga}</td>
                                    <td class="fw-bold text-dark small">${fechaVenc}</td>
                                    <td>
                                        <span class="badge bg-${doc.status_color}-subtle text-${doc.status_color} rounded-pill px-3 x-small fw-bold border border-${doc.status_color}">${doc.status_vigencia}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="btn btn-sm btn-light border p-2 rounded-circle btn-visualizar-table" 
                                                    data-url="${doc.url_archivo}" 
                                                    data-nombre="${doc.tipo_documento.nombre}"
                                                    title="Vista Previa">
                                                <span class="material-symbols-rounded fs-6">visibility</span>
                                            </button>
                                            <a href="${doc.url_archivo}" 
                                               class="btn btn-sm btn-light border p-2 rounded-circle text-primary" 
                                               download
                                               title="Descargar">
                                                <span class="material-symbols-rounded fs-6">download</span>
                                            </a>
                                            ${renewBtn}
                                        </div>
                                    </td>
                                `;
                                docsBody.appendChild(tr);
                            });

                            // Activar botones de visualización dinámicos
                            document.querySelectorAll('.btn-visualizar-table').forEach(vBtn => {
                                vBtn.addEventListener('click', function (e) {
                                    e.preventDefault();
                                    const url = this.getAttribute('data-url');
                                    const nombre = this.getAttribute('data-nombre');
                                    mostrarVisor(url, nombre);
                                });
                            });

                            modalVer.show();
                        } catch (error) {
                            console.error('Error al cargar ficha:', error);
                            alert('No se pudo cargar la información del vehículo.');
                        }
                    });
                });

                // 4. Visor de Documentos
                window.mostrarVisor = function (url, nombre) {
                    const modalViewer = new bootstrap.Modal(document.getElementById('modalVisorDocumento'));
                    const iframe = document.getElementById('visor_iframe');
                    const imgContainer = document.getElementById('visor_image_container');
                    const img = document.getElementById('visor_img');
                    const error = document.getElementById('visor_error');
                    const download = document.getElementById('visor_download');

                    document.getElementById('visor_titulo').innerText = 'Documento: ' + nombre;

                    // Reset
                    iframe.classList.add('d-none');
                    imgContainer.classList.add('d-none');
                    error.classList.add('d-none');
                    iframe.src = '';
                    img.src = '';
                    download.href = url;

                    if (!url) {
                        error.classList.remove('d-none');
                    } else {
                        const ext = url.split('.').pop().toLowerCase();
                        if (ext === 'pdf') {
                            iframe.src = url;
                            iframe.classList.remove('d-none');
                        } else if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                            img.src = url;
                            imgContainer.classList.remove('d-none');
                        } else {
                            error.classList.remove('d-none');
                        }
                    }
                    modalViewer.show();
                };

                // 5. Atajo para Renovar (Preseleccionar el tipo en el modal de subida)
                document.addEventListener('click', function (e) {
                    if (e.target.classList.contains('btn-renovar')) {
                        const placa = e.target.getAttribute('data-placa');
                        const tipo = e.target.getAttribute('data-tipo');

                        const selectPlaca = document.querySelector('select[name="placa"]');
                        const selectTipo = document.getElementById('subir_id_tipo_documento');

                        if (selectPlaca) selectPlaca.value = placa;
                        if (selectTipo) {
                            selectTipo.value = tipo;
                            // Disparar evento change para recalcular si es necesario
                            selectTipo.dispatchEvent(new Event('change'));
                        }
                    }
                });

                // 6. Cálculo automático de vencimientos
                const tipoDocInput = document.getElementById('subir_id_tipo_documento');
                const fechaExpInput = document.getElementById('subir_fecha_expedicion');
                const fechaVencInput = document.getElementById('subir_fecha_vencimiento');
                const colFechaNac = document.getElementById('col_fecha_nacimiento');
                const fechaNacInput = document.getElementById('subir_fecha_nacimiento');

                function calcularVencimiento() {
                    const tipo = tipoDocInput.value;
                    const expedicionStr = fechaExpInput.value;
                    const divVenc = document.getElementById('div_fecha_vencimiento');

                    if (tipo === '6') {
                        if (divVenc) divVenc.classList.add('d-none');
                        fechaVencInput.removeAttribute('required');
                        fechaVencInput.value = ''; // Limpiar
                        return;
                    } else {
                        if (divVenc) divVenc.classList.remove('d-none');
                        fechaVencInput.setAttribute('required', 'required');
                    }
                    const placaSelect = document.getElementById('subir_placa');

                    if (!tipo || !expedicionStr) return;

                    const fechaExp = new Date(expedicionStr);
                    const fechaVenc = new Date(fechaExp);

                    let isAuto = false;

                    if (tipo == '1' || tipo == '4') {
                        // SOAT y Pólizas: 1 año estricto
                        fechaVenc.setFullYear(fechaVenc.getFullYear() + 1);
                        isAuto = true;
                    } else if (tipo == '2') {
                        // Tecnomecánica
                        let modeloAno = new Date().getFullYear();
                        if (placaSelect && placaSelect.selectedIndex > 0) {
                            const option = placaSelect.options[placaSelect.selectedIndex];
                            const modeloAttr = option.getAttribute('data-modelo');
                            if (modeloAttr && !isNaN(parseInt(modeloAttr))) {
                                modeloAno = parseInt(modeloAttr);
                            }
                        }

                        // Si la expedición es dentro de los primeros 5 años desde la matrícula (modelo)
                        if (fechaExp.getFullYear() <= modeloAno + 5) {
                            fechaVenc.setFullYear(modeloAno + 5);
                            // Si al sumar los 5 años, esa fecha ya pasó, entonces damos 1 año
                            if (fechaVenc < new Date()) {
                                fechaVenc.setFullYear(fechaExp.getFullYear() + 1);
                            }
                        } else {
                            // Vehículo usado / pasaron 5 años: Anual
                            fechaVenc.setFullYear(fechaVenc.getFullYear() + 1);
                        }
                        isAuto = true;
                    } else if (tipo == '3') {
                        // Licencia de Conducción Categoría C (Buseteros)
                        const birthDateStr = fechaNacInput.value;
                        if (birthDateStr) {
                            const birthDate = new Date(birthDateStr);
                            const age = new Date().getFullYear() - birthDate.getFullYear();

                            if (age < 60) {
                                fechaVenc.setFullYear(fechaVenc.getFullYear() + 3);
                            } else {
                                fechaVenc.setFullYear(fechaVenc.getFullYear() + 1);
                            }
                        } else {
                            // Si no hay fecha de nacimiento, poner 3 años por defecto
                            fechaVenc.setFullYear(fechaVenc.getFullYear() + 3);
                        }
                        isAuto = true;
                    }

                    // Formatear a YYYY-MM-DD para el input date
                    const yyyy = fechaVenc.getFullYear();
                    const mm = String(fechaVenc.getMonth() + 1).padStart(2, '0');
                    const dd = String(fechaVenc.getDate()).padStart(2, '0');

                    const vencimientoFormateado = `${yyyy}-${mm}-${dd}`;
                    fechaVencInput.value = vencimientoFormateado;

                    // Restricción visual de frontera
                    const hoy = new Date();
                    hoy.setHours(0, 0, 0, 0);

                    if (fechaVenc < hoy) {
                        fechaVencInput.classList.add('is-invalid');
                    } else {
                        fechaVencInput.classList.remove('is-invalid');
                    }

                    if (isAuto) {
                        fechaVencInput.setAttribute('readonly', 'readonly');
                        // Indicador visual
                        fechaVencInput.parentElement.querySelector('label').innerHTML = 'Vencimiento <span class="badge bg-success-subtle text-success py-0 px-2 fw-bold">Autocalculado</span>';
                    } else {
                        fechaVencInput.removeAttribute('readonly');
                        fechaVencInput.parentElement.querySelector('label').innerHTML = 'Vencimiento';
                    }
                }

                tipoDocInput.addEventListener('change', function () {
                    // Mostrar/ocultar fecha de nacimiento si es licencia
                    if (this.value == '3') {
                        colFechaNac.classList.remove('d-none');
                    } else {
                        colFechaNac.classList.add('d-none');
                    }
                    calcularVencimiento();
                });

                fechaExpInput.addEventListener('change', calcularVencimiento);
                fechaNacInput.addEventListener('change', calcularVencimiento);

                // 4. Ver Detalle de Asignación
                const modalDetalle = new bootstrap.Modal(document.getElementById('modalDetalleAsignacion'));
                document.querySelectorAll('.btn-detalle-asignacion').forEach(btn => {
                    btn.addEventListener('click', async function () {
                        const id = this.getAttribute('data-id');
                        try {
                            const response = await fetch(`/propietario/asignacion/${id}/detalle`);
                            const data = await response.json();

                            // Llenar Info General
                            document.getElementById('det_id_viaje').innerText = '#' + data.asignacion.id_viaje;
                            document.getElementById('det_placa').innerText = data.asignacion.placa;
                            document.getElementById('det_conductor').innerText = data.asignacion.conductor;
                            document.getElementById('det_ruta').innerText = data.asignacion.ruta;
                            document.getElementById('det_inicio').innerText = data.asignacion.inicio;
                            document.getElementById('det_fin').innerText = data.asignacion.fin;
                            document.getElementById('det_fecha').innerText = data.asignacion.fecha;

                            // Llenar Recorridos
                            const tbody = document.getElementById('det_recorridos_body');
                            tbody.innerHTML = '';
                            if (data.recorridos.length > 0) {
                                data.recorridos.forEach(rec => {
                                    const row = `
                                        <tr>
                                            <td class="ps-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="material-symbols-rounded text-muted small">${rec.es_regreso ? 'keyboard_return' : 'near_me'}</span>
                                                    <span class="fw-bold text-dark small">${rec.trayecto}</span>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-light text-dark fw-medium">${rec.hora_salida}</span></td>
                                            <td><span class="badge bg-light text-dark fw-medium">${rec.hora_llegada}</span></td>
                                            <td class="text-center"><span class="fw-bold">${rec.cantidad_pasajeros}</span></td>
                                            <td class="text-end pe-3 fw-bold text-success">$${new Intl.NumberFormat().format(rec.ingresos)}</td>
                                        </tr>
                                    `;
                                    tbody.innerHTML += row;
                                });
                            } else {
                                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted small text-uppercase">No se registraron viajes en este turno.</td></tr>';
                            }

                            // Llenar Resumen
                            document.getElementById('stat_od').innerText = data.resumen.total_origen_destino;
                            document.getElementById('stat_do').innerText = data.resumen.total_destino_origen;
                            document.getElementById('stat_pax').innerText = data.resumen.total_pasajeros;
                            document.getElementById('stat_ingresos').innerText = '$' + new Intl.NumberFormat().format(data.resumen.total_ingresos);

                            modalDetalle.show();
                        } catch (error) {
                            console.error('Error:', error);
                            alert('Error al cargar el detalle de la asignación.');
                        }
                    });
                });

                // 5. Bóveda Histórica
                const modalBoveda = new bootstrap.Modal(document.getElementById('modalBovedaHistorial'));
                document.querySelectorAll('.btn-historial-docs').forEach(btn => {
                    btn.addEventListener('click', async function () {
                        const placa = this.getAttribute('data-placa');
                        try {
                            const response = await fetch(`/propietario/bus/${placa}/historial-documental`);
                            if (!response.ok) throw new Error('Error en petición');
                            const data = await response.json();

                            document.getElementById('boveda_placa').innerText = data.placa;
                            const content = document.getElementById('boveda_content');
                            content.innerHTML = '';

                            const isObjectEmpty = (obj) => {
                                return Object.keys(obj).length === 0;
                            };

                            if (isObjectEmpty(data.grupos)) {
                                content.innerHTML = '<div class="alert alert-light text-center border-0 shadow-sm rounded-4 p-5"><span class="material-symbols-rounded display-1 opacity-25 d-block mb-3">folder_off</span><h5 class="fw-bold">Sin Historial</h5><p class="mb-0 text-muted">No hay documentos registrados para este vehículo.</p></div>';
                            } else {
                                for (const [tipo, docs] of Object.entries(data.grupos)) {
                                    let rows = '';
                                    docs.forEach(doc => {
                                        const isArchivado = doc.es_archivado;
                                        const trClass = isArchivado ? 'opacity-75 bg-light' : '';
                                        rows += `
                                        <tr class="${trClass}">
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark small text-truncate" style="max-width: 250px;" title="${doc.nombre}">${doc.nombre}</div>
                                                ${isArchivado ? '<span class="badge bg-secondary-subtle text-secondary x-small border border-secondary mt-1">Archivado</span>' : (doc.status_vigencia === 'VENCIDO' ? '<span class="badge bg-danger-subtle text-danger x-small border border-danger mt-1">Vencido</span>' : '<span class="badge bg-success-subtle text-success x-small border border-success mt-1">Activo</span>')}
                                            </td>
                                            <td class="text-muted small">${doc.fecha_carga}</td>
                                            <td class="fw-bold text-dark small">${doc.fecha_vencimiento}</td>
                                            <td><span class="badge bg-${doc.status_color}-subtle text-${doc.status_color} px-3 py-1 x-small border border-${doc.status_color} rounded-pill fw-bold">${doc.status_vigencia}</span></td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    ${doc.url_archivo ? `
                                                    <button class="btn btn-sm btn-light border p-2 rounded-circle text-dark" onclick="mostrarVisor('${doc.url_archivo}', '${tipo}')" title="Vista Previa"><span class="material-symbols-rounded fs-6">visibility</span></button>
                                                    <a href="${doc.url_archivo}" download class="btn btn-sm btn-light border text-primary p-2 rounded-circle" title="Descargar PDF"><span class="material-symbols-rounded fs-6">download</span></a>
                                                    ` : '<span class="text-muted small">N/A</span>'}
                                                </div>
                                            </td>
                                        </tr>`;
                                    });

                                    content.innerHTML += `
                                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                                        <div class="card-header bg-white border-0 p-4">
                                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2 text-uppercase letter-spacing-1 small">
                                                <span class="material-symbols-rounded text-primary fs-5">folder_open</span>
                                                ${tipo}
                                            </h6>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th class="ps-4 py-3 border-0 small text-muted fw-bold">NOMBRES Y ESTADO</th>
                                                        <th class="py-3 border-0 small text-muted fw-bold">CARGA</th>
                                                        <th class="py-3 border-0 small text-muted fw-bold">VENCIMIENTO</th>
                                                        <th class="py-3 border-0 small text-muted fw-bold">VIGENCIA</th>
                                                        <th class="py-3 border-0 text-end pe-4 small text-muted fw-bold">ACCIONES</th>
                                                    </tr>
                                                </thead>
                                                <tbody>${rows}</tbody>
                                            </table>
                                        </div>
                                    </div>`;
                                }
                            }
                            modalBoveda.show();
                        } catch (e) {
                            console.error(e);
                            alert("No se pudo cargar la información de la bóveda histórica.");
                        }
                    });
                });
                // 6. Cambiar Vista de Ganancias (Hoy/Semana/Mes)
                window.switchEarnings = function (btn) {
                    const container = btn.parentElement;
                    container.querySelectorAll('.btn-switch').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');

                    const amount = btn.getAttribute('data-amount');
                    const label = btn.getAttribute('data-label');

                    document.getElementById('earnings_amount').innerText = '$' + amount;
                    document.getElementById('earnings_label').innerText = label;
                };

                // 7. Paginación AJAX para la Tabla de Ganancias
                const earningsContainer = document.getElementById('ganancias-table-container');
                if (earningsContainer) {
                    earningsContainer.addEventListener('click', function (e) {
                        const link = e.target.closest('.pagination a');
                        if (link) {
                            e.preventDefault();
                            const url = link.href;
                            
                            earningsContainer.style.opacity = '0.5';
                            earningsContainer.style.pointerEvents = 'none';
                            
                            fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => {
                                if (!response.ok) throw new Error('Error en la red');
                                return response.text();
                            })
                            .then(html => {
                                earningsContainer.innerHTML = html;
                                earningsContainer.style.opacity = '1';
                                earningsContainer.style.pointerEvents = 'auto';
                            })
                            .catch(error => {
                                console.error('Error en paginación AJAX:', error);
                                earningsContainer.style.opacity = '1';
                                earningsContainer.style.pointerEvents = 'auto';
                                // Fallback: recargar si falla el AJAX
                                window.location.href = url;
                            });
                        }
                    });
                }
            });
        </script>

        <style>
            @import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;600;800&display=swap');

            .fw-black {
                font-weight: 800;
            }

            /* Estructura Especial para Secciones */
            .section-header {
                margin-bottom: 2rem;
            }

            /* Tarjetas Modernas con Efecto de Escala */
            .card {
                transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease !important;
                border-width: 0 !important;
            }

            .card:hover {
                transform: scale(1.02) translateY(-3px) !important;
                box-shadow: 0 20px 40px rgba(106, 81, 160, 0.12) !important;
            }

            /* Table hover */
            .table-hover tbody tr {
                transition: background-color 0.15s;
            }

            .table-hover tbody tr:hover {
                background-color: rgba(93, 84, 142, 0.03) !important;
            }

            /* Badges Rediseñados */
            .badge {
                font-weight: 600;
                letter-spacing: 0.2px;
                padding: 0.5em 0.9em;
                border-radius: 50px !important;
            }

            /* Primary Overrides (Purple) */
            .text-primary {
                color: #6a51a0 !important;
            }

            .bg-primary {
                background-color: #6a51a0 !important;
            }

            .border-primary {
                border-color: #6a51a0 !important;
            }

            /* Botones con Gradiente y Sombras Flotantes */
            .btn {
                transition: all 0.28s ease !important;
            }

            .btn:hover {
                transform: translateY(-2px) scale(1.03) !important;
            }

            .btn-primary {
                background: linear-gradient(135deg, #6a51a0 0%, #4c377a 100%) !important;
                border: none !important;
                color: white !important;
                box-shadow: 0 4px 12px rgba(106, 81, 160, 0.25) !important;
            }

            .btn-primary:hover {
                box-shadow: 0 6px 18px rgba(106, 81, 160, 0.35) !important;
            }

            .btn-success {
                background: linear-gradient(135deg, #10b981 0%, #047857 100%) !important;
                border: none !important;
                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2) !important;
            }

            .btn-danger {
                background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%) !important;
                border: none !important;
                box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2) !important;
            }

            .btn-warning {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
                border: none !important;
                color: white !important;
                box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2) !important;
            }

            /* Switch Buttons para Ganancias */
            .btn-switch {
                border: none !important;
                color: white !important;
                background: transparent !important;
                transition: all 0.22s ease !important;
            }

            .btn-switch:hover {
                background: rgba(255, 255, 255, 0.08) !important;
            }

            .btn-switch.active {
                background-color: white !important;
                color: #110726 !important;
                box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2) !important;
            }

            /* Scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
            }

            ::-webkit-scrollbar-track {
                background: transparent;
            }

            ::-webkit-scrollbar-thumb {
                background: #6a51a044;
                border-radius: 4px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: #6a51a099;
            }
        </style>
    @endpush
@endsection