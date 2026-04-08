<div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3 text-uppercase small fw-bold text-muted border-0">Vehículo / Propietario</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Datos Técnicos</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Capacidad</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Kilometraje</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Estado</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted border-0 text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($buses as $bus)
                    <tr class="border-top">
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    <span class="d-block fw-bold text-dark fs-6">{{ $bus->placa }}</span>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                                        <span class="material-symbols-rounded fs-xs align-middle">person</span>
                                        {{ $bus->doc_propietario ?? 'Sin propietario' }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="lh-sm">
                                <span class="fw-medium d-block text-dark">{{ $bus->modelo ?? 'N/D' }}</span>
                                <small class="text-muted" style="font-size: 0.7rem;">
                                    <strong>Chasis:</strong> {{ $bus->numero_chasis ?? '—' }} |
                                    <strong>Motor:</strong> {{ $bus->numero_motor ?? '—' }}
                                </small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 fw-medium">
                                <span class="material-symbols-rounded fs-6 align-middle me-1">group</span>
                                {{ $bus->capacidad_pasajeros }} pasj.
                            </span>
                        </td>
                        <td class="text-muted small">
                            <div class="d-flex align-items-center gap-1">
                                <span class="material-symbols-rounded fs-6 opacity-50">speed</span>
                                {{ number_format($bus->kilometraje) }} km
                            </div>
                            @if($bus->linc_transito)
                                <small class="text-primary d-block mt-1" style="font-size: 0.65rem;">
                                    <strong>LIC:</strong> {{ $bus->linc_transito }}
                                </small>
                            @endif
                        </td>
                        <td>
                            @php
                                $c = match ((int) $bus->id_estado) {
                                    1 => 'success', // Activo
                                    2 => 'danger', // Inactivo
                                    7 => 'info', // En mantenimiento
                                    default => 'warning'
                                };
                            @endphp
                            <span class="badge bg-{{ $c }}-subtle text-{{ $c }} border border-{{ $c }} rounded-pill px-3">
                                {{ optional($bus->estado)->nombre_estado ?? 'Desconocido' }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-3">
                                <a href="#" class="text-info text-decoration-none d-flex align-items-center"
                                    title="Ver expediente" data-bs-toggle="modal" data-bs-target="#modalViewBus"
                                    data-json='@json($bus)' data-estado="{{ optional($bus->estado)->nombre_estado }}">
                                    <span class="material-symbols-rounded fs-5">visibility</span>
                                </a>

                                {{-- Condicional para Auxiliar: Solo Admin puede editar/eliminar --}}
                                {{-- Botón Editar visible para ambos --}}
                                <a href="#" class="text-primary text-decoration-none d-flex align-items-center"
                                    title="Editar vehículo" data-bs-toggle="modal" data-bs-target="#modalEditBus"
                                    data-json='@json($bus)'>
                                    <span class="material-symbols-rounded fs-5">edit</span>
                                </a>

                                {{-- Condicional para Auxiliar: Solo Admin puede eliminar --}}
@php
    $routePrefix = auth()->user()->id_tipo_usuario == 1 ? 'admin' : 'empresa';
@endphp
                                    <form id="delete-bus-{{ $bus->placa }}"
                                        action="{{ route($routePrefix . '.buses.destroy', $bus->placa) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                            class="p-0 border-0 bg-transparent text-danger d-flex align-items-center"
                                            title="Eliminar" data-confirm-form="delete-bus-{{ $bus->placa }}"
                                            data-confirm-title="¿Eliminar vehículo {{ $bus->placa }}?"
                                            data-confirm-msg="Esta acción es irreversible y afecta las asignaciones y registros vinculados.">
                                            <span class="material-symbols-rounded fs-5">delete</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <span class="material-symbols-rounded display-4 opacity-25">directions_bus</span>
                            <p class="mt-2 fw-medium">No se encontraron buses que coincidan con los criterios.</p>
                            <small>Verifica los filtros activos o agrega un nuevo registro.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">
        {{ $buses->links() }}
    </div>
</div>