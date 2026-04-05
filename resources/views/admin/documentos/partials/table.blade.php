@php
$routePrefix = Auth::user()->id_tipo_usuario == 1 ? 'admin' : 'empresa';
@endphp

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        @if($documentos->count() > 0)
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3 text-uppercase small fw-bold text-muted border-0">Vehículo / Placa</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Propietario</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Tipo</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Vencimiento</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Estado</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted border-0 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($documentos as $documento)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            @if($documento->placa)
                                <div class="bg-dark text-white p-2 rounded-3">
                                    <span class="material-symbols-rounded">directions_bus</span>
                                </div>
                                <div>
                                    <strong class="d-block text-dark fs-6">{{ $documento->placa }}</strong>
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 150px;">{{ $documento->nombre }}</small>
                                </div>
                            @else
                                <div class="bg-dark text-white p-2 rounded-3">
                                    <span class="material-symbols-rounded">person</span>
                                </div>
                                <div>
                                    <strong class="d-block text-dark fs-6 text-uppercase">Personal</strong>
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 150px;">{{ $documento->nombre }}</small>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($documento->placa)
                            <div class="text-dark fw-medium small">{{ $documento->bus->nombre_propietario ?? 'No asignado' }}</div>
                            <small class="text-muted">ID: {{ $documento->bus->doc_propietario ?? '---' }}</small>
                        @else
                            <div class="text-dark fw-medium small">
                                {{ $documento->usuario->primer_nombre ?? 'N/A' }} {{ $documento->usuario->primer_apellido ?? '' }}
                            </div>
                            <small class="text-muted">CC: {{ $documento->doc_usuario ?? '---' }}</small>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border fw-medium px-3 rounded-pill">
                            {{ $documento->tipoDocumento->nombre ?? 'N/A' }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">
                            {{ $documento->fecha_vencimiento->format('d/m/Y') }}
                        </div>
                        <span class="badge bg-{{ $documento->status_color }}-subtle text-{{ $documento->status_color }} small rounded-pill px-2 border border-{{ $documento->status_color }}" style="font-size: 0.65rem; font-weight: 700;">
                            {{ $documento->estado_expiracion }}
                        </span>
                    </td>
                    <td>
                        @php
                        $badgeClass = match($documento->id_estado) {
                            1 => 'bg-success',
                            8 => 'bg-danger',
                            5 => 'bg-warning text-dark',
                            default => 'bg-secondary'
                        };
                        @endphp
                        <span class="badge {{ $badgeClass }} rounded-pill px-3">
                            {{ $documento->estado->nombre_estado ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <button class="btn btn-sm btn-primary rounded-pill px-2 fw-bold btn-visor" 
                                data-url="{{ asset($documento->archivo) }}" 
                                data-nombre="{{ $documento->nombre }}"
                                title="Previsualizar">
                                <span class="material-symbols-rounded fs-5">visibility</span>
                            </button>

                            @if($documento->id_estado == 5) <!-- PENDIENTE (Nuevo ID) -->
                                <form action="{{ route($routePrefix . '.documentos.aprobar', $documento->id_documento) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-2 fw-bold" title="Aprobar">
                                        <span class="material-symbols-rounded fs-5">check</span>
                                    </button>
                                </form>
                                <form action="{{ route($routePrefix . '.documentos.rechazar', $documento->id_documento) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-2 fw-bold" title="Rechazar">
                                        <span class="material-symbols-rounded fs-5">close</span>
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route($routePrefix . '.documentos.download', $documento->id_documento) }}"
                                class="btn btn-sm btn-light rounded-pill px-2 fw-bold" title="Descargar">
                                <span class="material-symbols-rounded fs-5">download</span>
                            </a>

                            @if($documento->estado_expiracion != 'VIGENTE')
                                <a href="{{ route($routePrefix . '.documentos.edit', $documento->id_documento) }}"
                                    class="btn btn-sm btn-warning text-dark rounded-pill px-3 fw-bold d-flex align-items-center gap-1" title="Renovar Doc">
                                    <span class="material-symbols-rounded fs-5">edit_note</span>
                                    <span class="small">Renovar</span>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="mt-4 p-3 border-top">
            {{ $documentos->links('pagination::bootstrap-5') }}
        </div>
        @else
        <div class="alert alert-info m-3">
            <span class="material-symbols-rounded">info</span>
            No hay documentos registrados.
        </div>
        @endif
    </div>
</div>
