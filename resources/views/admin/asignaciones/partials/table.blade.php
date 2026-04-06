<div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3 text-uppercase small fw-bold text-muted border-0">ID</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Vehículo</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Ruta Asignada</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Conductor</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Fecha / Hora</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted border-0">Estado</th>
                    <th class="py-3 text-uppercase small fw-bold text-muted border-0 text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($asignaciones as $asig)
                <tr class="border-top">
                    <td class="ps-4 fw-bold text-muted">#{{ $asig->id_viaje }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark">{{ $asig->placa }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="lh-1">
                            <span class="d-block fw-medium text-dark">{{ $asig->ruta->nombre_ruta ?? 'Ruta #'.$asig->id_ruta }}</span>
                            <small class="text-muted" style="font-size: 0.7rem;">ID Sistema: {{ $asig->id_ruta }}</small>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="material-symbols-rounded text-muted fs-5">person</span>
                            <span class="text-dark">{{ optional($asig->conductor)->primer_nombre }} {{ optional($asig->conductor)->primer_apellido }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1 text-muted small">
                            <span class="material-symbols-rounded fs-6 opacity-50">calendar_today</span>
                            {{ \Carbon\Carbon::parse($asig->fecha)->format('d/m/Y H:i') }}
                        </div>
                    </td>
                    <td>
                        @php
                        $c = match((int)$asig->id_estado) {
                            1 => 'success',
                            2 => 'danger',
                            12 => 'info',
                            default => 'warning'
                        };
                        @endphp
                        <span class="badge bg-{{ $c }}-subtle text-{{ $c }} border border-{{ $c }} rounded-pill px-3">
                            {{ optional($asig->estado)->nombre_estado ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-3">
                            <a href="#" 
                               class="text-info text-decoration-none d-flex align-items-center view-asignacion"
                               title="Ver detalles"
                               data-json="{{ json_encode($asig) }}"
                               data-conductor="{{ optional($asig->conductor)->primer_nombre }} {{ optional($asig->conductor)->primer_apellido }}"
                               data-ruta="{{ $asig->ruta->nombre_ruta ?? 'Ruta #'.$asig->id_ruta }}"
                               data-estado="{{ optional($asig->estado)->nombre_estado }}">
                                <span class="material-symbols-rounded fs-5">visibility</span>
                            </a>

                            @if($asig->id_estado != 5)
                                <a href="#" 
                                   class="text-primary text-decoration-none d-flex align-items-center edit-asignacion"
                                   title="Editar asignación"
                                   data-bs-toggle="modal"
                                   data-bs-target="#modalEditAsignacion"
                                   data-json="{{ json_encode($asig) }}">
                                    <span class="material-symbols-rounded fs-5">edit</span>
                                </a>
                                <form action="{{ route('admin.asignaciones.destroy', $asig->id_viaje) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta asignación?')">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="form_type" value="delete">
                                    <button type="submit" class="p-0 border-0 bg-transparent text-danger d-flex align-items-center" title="Eliminar">
                                        <span class="material-symbols-rounded fs-5">delete</span>
                                    </button>
                                </form>
                            @else
                                <span class="text-muted opacity-50 d-flex align-items-center" title="Finalizado - No editable">
                                    <span class="material-symbols-rounded fs-5">lock</span>
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <span class="material-symbols-rounded display-4 opacity-25">assignment_late</span>
                        <p class="mt-2 fw-medium">No se encontraron asignaciones activas.</p>
                        <small>Comience vinculando un bus y un conductor a una ruta.</small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($asignaciones->hasPages())
    <div class="p-4 border-top">
        {{ $asignaciones->links() }}
    </div>
    @endif
</div>
