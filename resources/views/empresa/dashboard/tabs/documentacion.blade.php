<!-- Tab DOCUMENTACIÓN (Vigilancia de Documentos) -->
<div class="tab-pane fade {{ $tab == 'documentacion' ? 'show active' : '' }}" id="tab-documentacion" role="tabpanel">
    <!-- Barra de Filtros (Documentación) -->
    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-body p-3 bg-white">
            <form method="GET" action="{{ route('empresa.dashboard') }}" class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="documentacion">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0 ps-3">
                            <span class="material-symbols-rounded text-muted fs-5">search</span>
                        </span>
                        <input type="text" name="search_doc" class="form-control bg-light border-0 py-2" placeholder="Buscar por placa o documento..." value="{{ request('search_doc') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status_doc" class="form-select form-select-sm bg-light border-0">
                        <option value="">Cualquier estado</option>
                        <option value="vigente" {{ request('status_doc') == 'vigente' ? 'selected' : '' }}>Al día (Vigentes)</option>
                        <option value="proximo" {{ request('status_doc') == 'proximo' ? 'selected' : '' }}>Próximo a vencer</option>
                        <option value="vencido" {{ request('status_doc') == 'vencido' ? 'selected' : '' }}>Vencidos</option>
                    </select>
                </div>
                <div class="col-md-3 ms-auto text-end d-flex gap-1 justify-content-end">
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 fw-semibold shadow-sm w-100">Consultar</button>
                    <a href="{{ route('empresa.dashboard', ['tab' => 'documentacion']) }}" class="btn btn-light btn-sm rounded-pill fw-semibold border shadow-sm">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Documentación -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 border-0 small fw-bold text-muted text-uppercase ls-1">DOCUMENTO / VEHÍCULO</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase ls-1">PROPIETARIO</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase ls-1 text-center">VENCIMIENTO</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase ls-1 text-center">VIGENCIA</th>
                        <th class="border-0 small fw-bold text-muted text-uppercase ls-1 text-end pe-4">ARCHIVOS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documentos as $doc)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-{{ $doc->status_color }} text-{{ $doc->status_color == 'warning' ? 'dark' : 'white' }} p-2 rounded-3 shadow-sm">
                                        <span class="material-symbols-rounded fs-4">description</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-black text-dark">{{ $doc->tipoDocumento->nombre ?? 'Documento' }}</h6>
                                        <span class="x-small text-muted fw-bold">PLACA: {{ $doc->placa }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column small">
                                    <span class="fw-bold text-dark">{{ $doc->bus->propietario->primer_nombre ?? 'N/A' }} {{ $doc->bus->propietario->primer_apellido ?? '' }}</span>
                                    <span class="x-small text-muted">{{ $doc->bus->propietario->doc_usuario ?? '' }}</span>
                                </div>
                            </td>
                            <td class="text-center small fw-medium text-muted">
                                {{ $doc->fecha_vencimiento->format('d/m/Y') }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $doc->status_color }}-subtle text-{{ $doc->status_color }} border border-{{ $doc->status_color }} rounded-pill x-small px-3 py-1 shadow-sm fw-bold">
                                    {{ $doc->estado_expiracion }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-light border-0 shadow-sm rounded-pill px-3 fw-bold btn-ver-detalle-documento" 
                                            data-nombre="{{ $doc->tipoDocumento->nombre }}"
                                            data-placa="{{ $doc->placa }}"
                                            data-venc="{{ $doc->fecha_vencimiento->format('d/m/Y') }}"
                                            data-estado="{{ $doc->estado_expiracion }}"
                                            data-color="{{ $doc->status_color }}"
                                            data-archivo="{{ asset('storage/' . $doc->archivo) }}">
                                        Ver Archivo
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <span class="material-symbols-rounded display-4 opacity-25">folder_off</span>
                                    <p class="mt-2 fw-medium">No se encontraron documentos en observación.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($documentos->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $documentos->appends(['tab' => 'documentacion'])->links() }}
            </div>
        @endif
    </div>
</div>
