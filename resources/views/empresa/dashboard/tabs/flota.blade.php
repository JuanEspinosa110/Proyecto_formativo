<!-- Tab FLOTA (Gestion de Buses) -->
<div class="tab-pane fade {{ $tab == 'flota' ? 'show active' : '' }}" id="tab-flota" role="tabpanel">
    <!-- Barra de Filtros (Flota) -->
    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-body p-3 bg-white">
            <form method="GET" action="{{ route('empresa.dashboard') }}" class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="flota">
                <div class="col-md-7">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0 ps-3">
                            <span class="material-symbols-rounded text-muted fs-5">search</span>
                        </span>
                        <input type="text" name="search_bus" class="form-control bg-light border-0 py-2" placeholder="Buscar por placa o modelo..." value="{{ request('search_bus') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="estado_bus" class="form-select form-select-sm bg-light border-0">
                        <option value="">Cualquier estado</option>
                        @foreach($estadosBus as $eb)
                            <option value="{{ $eb->id_estado }}" {{ request('estado_bus') == $eb->id_estado ? 'selected' : '' }}>
                                {{ $eb->nombre_estado }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 text-end d-flex gap-1 justify-content-end">
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 fw-semibold shadow-sm w-100">Consultar</button>
                    <a href="{{ route('empresa.dashboard', ['tab' => 'flota']) }}" class="btn btn-light btn-sm rounded-pill fw-semibold border shadow-sm" title="Limpiar">
                        <span class="material-symbols-rounded fs-6">filter_list_off</span>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Flota -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 border-0 small fw-bold text-muted text-uppercase ls-1">Vehículo</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase ls-1">Propietario</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase ls-1 text-center">Estado</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase ls-1 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($buses as $bus)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary text-white p-2 rounded-3 shadow-sm">
                                        <span class="material-symbols-rounded fs-4">directions_bus</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-black text-dark letter-spacing-1">{{ $bus->placa }}</h6>
                                        <span class="x-small text-muted text-uppercase fw-bold">{{ $bus->modelo }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="small fw-bold text-dark">{{ $bus->nombre_propietario }}</span>
                                    <span class="x-small text-muted">{{ $bus->doc_propietario }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill x-small px-3 py-1 shadow-sm {{ $bus->id_estado == 1 ? 'bg-success bg-opacity-75' : ($bus->id_estado == 2 ? 'bg-danger bg-opacity-75' : 'bg-warning text-dark') }}">
                                    {{ $bus->estado->nombre_estado ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-light border-0 shadow-sm rounded-pill px-3 fw-bold btn-ver-expediente-aux" data-placa="{{ $bus->placa }}">
                                        Ver Ficha
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-muted">
                                    <span class="material-symbols-rounded display-4 opacity-25">directions_bus</span>
                                    <p class="mt-2 fw-medium">No hay vehículos registrados para esta empresa.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($buses->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $buses->appends(['tab' => 'flota'])->links() }}
            </div>
        @endif
    </div>
</div>
