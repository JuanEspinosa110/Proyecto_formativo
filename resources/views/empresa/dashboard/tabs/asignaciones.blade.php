<!-- Tab ASIGNACIONES (Gestion de Viajes) -->
<div class="tab-pane fade {{ $tab == 'asignaciones' ? 'show active' : '' }}" id="tab-asignaciones" role="tabpanel">
    <!-- Barra de Filtros (Asignaciones) -->
    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-body p-3 bg-white">
            <form method="GET" action="{{ route('empresa.dashboard') }}" class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="asignaciones">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0 ps-3">
                            <span class="material-symbols-rounded text-muted fs-5">search</span>
                        </span>
                        <input type="text" name="search_asignacion" class="form-control bg-light border-0 py-2" placeholder="Buscar por placa o conductor..." value="{{ request('search_asignacion') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="id_ruta" class="form-select form-select-sm bg-light border-0">
                        <option value="">Todas las rutas</option>
                        @foreach($rutas as $r)
                            <option value="{{ $r->id_ruta }}" {{ request('id_ruta') == $r->id_ruta ? 'selected' : '' }}>
                                {{ $r->nombre_ruta }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="fecha_viaje" class="form-control form-control-sm bg-light border-0" value="{{ request('fecha_viaje') }}" title="Filtrar por día">
                </div>
                <div class="col-md-4 text-end d-flex gap-1 justify-content-end">
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 fw-semibold shadow-sm w-100">Consultar</button>
                    <a href="{{ route('empresa.dashboard', ['tab' => 'asignaciones']) }}" class="btn btn-light btn-sm rounded-pill fw-semibold border shadow-sm" title="Limpiar">
                        <span class="material-symbols-rounded fs-6">filter_list_off</span>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Asignaciones -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light border-0">
                    <tr>
                        <th class="ps-4 border-0 small fw-bold text-muted text-uppercase ls-1">VEHÍCULO / RUTA</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase ls-1">CONDUCTOR</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase ls-1">FECHA INICIO</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase ls-1 text-center">ESTADO</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase ls-1 text-end pe-4">EXPEDIENTE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asignaciones as $as)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary text-white p-2 rounded-3 shadow-sm">
                                        <span class="material-symbols-rounded fs-4">route</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-black text-dark">{{ $as->bus->placa }}</h6>
                                        <span class="x-small text-muted fw-bold">{{ $as->ruta->nombre_ruta ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column small">
                                    <span class="fw-bold text-dark">{{ $as->conductor->primer_nombre }} {{ $as->conductor->primer_apellido }}</span>
                                    <span class="x-small text-muted">{{ $as->conductor->doc_usuario }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="small fw-medium text-muted">
                                    {{ \Carbon\Carbon::parse($as->fecha)->format('d/M - H:i') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill x-small px-3 py-1 shadow-sm {{ $as->id_estado == 1 ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $as->estado->nombre_estado ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-light border-0 shadow-sm rounded-pill px-3 fw-bold btn-ver-detalle-asignacion" 
                                            data-placa="{{ $as->bus->placa }}"
                                            data-modelo="{{ $as->bus->modelo }}"
                                            data-ruta="{{ $as->ruta->nombre_ruta ?? 'N/A' }}"
                                            data-conductor="{{ $as->conductor->primer_nombre ?? '---' }} {{ $as->conductor->primer_apellido ?? '' }}"
                                            data-doc-cond="{{ $as->conductor->doc_usuario ?? '---' }}"
                                            data-propietario="{{ $as->bus->propietario->primer_nombre ?? $as->bus->nombre_propietario ?? 'Particular' }} {{ $as->bus->propietario->primer_apellido ?? '' }}"
                                            data-tel-prop="{{ $as->bus->propietario->telefono ?? $as->bus->telefono ?? '---' }}"
                                            data-fecha="{{ \Carbon\Carbon::parse($as->fecha)->format('d/m/Y H:i') }}"
                                            data-estado="{{ $as->estado->nombre_estado ?? 'N/A' }}"
                                            data-estado-color="{{ $as->id_estado == 1 ? 'success' : 'warning' }}">
                                        Ver Ficha
                                    </button>
                                    
                                    @if($as->id_estado == 1 && !$as->recorridos()->exists())
                                        <form action="{{ route('empresa.asignaciones.inactivar', $as->id_viaje) }}" method="POST" class="d-inline form-inactivar-viaje">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0 shadow-sm rounded-pill px-3 fw-bold d-flex align-items-center gap-1">
                                                <span class="material-symbols-rounded fs-6">block</span>
                                                Inactivar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <span class="material-symbols-rounded display-4 opacity-25">event_busy</span>
                                    <p class="mt-2 fw-medium">No se encontraron asignaciones activas en este momento.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($asignaciones->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $asignaciones->appends(['tab' => 'asignaciones'])->links() }}
            </div>
        @endif
    </div>
</div>
