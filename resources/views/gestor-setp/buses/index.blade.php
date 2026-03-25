@extends('gestor-setp.layouts.app')

@section('title', 'Buses')

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- Alertas --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3">
        <span class="material-symbols-rounded">check_circle</span>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3">
        <span class="material-symbols-rounded">error</span>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Encabezado --}}
    <div class="bus-header">
        <div>
            <h1>
                <span class="material-symbols-rounded" style="color:var(--acc)">directions_bus</span>
                Buses
            </h1>
            <p>Buses asociados a las empresas de transporte de tu ciudad. Gestiona estados y documientos.</p>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('gestor-setp.buses.index') }}" class="bus-filters">
        <div>
            <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-2)">Placa</label>
            <input type="text" name="placa" class="form-control" placeholder="Ej: BUS567"
                   value="{{ request('placa') }}" style="width:130px">
        </div>
        <div>
            <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-2)">Empresa</label>
            <select name="nit" class="form-select" style="min-width:200px">
                <option value="">Todas las empresas</option>
                @foreach($empresas as $emp)
                <option value="{{ $emp->NIT }}" {{ request('nit') == $emp->NIT ? 'selected' : '' }}>
                    {{ $emp->nombre_empresa }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-2)">Estado</label>
            <select name="estado" class="form-select" style="min-width:130px">
                <option value="">Todos</option>
                <option value="1" {{ request('estado')=='1'?'selected':'' }}>Activo</option>
                <option value="2" {{ request('estado')=='2'?'selected':'' }}>Inactivo</option>
            </select>
        </div>
        <div>
            <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-2)">Documentos</label>
            <select name="docs" class="form-select" style="min-width:160px">
                <option value="">Todos</option>
                <option value="pendientes" {{ request('docs')=='pendientes'?'selected':'' }}>Con docs. pendientes</option>
                <option value="completos" {{ request('docs')=='completos'?'selected':'' }}>Documentación completa</option>
            </select>
        </div>
        <button type="submit" class="btn d-flex align-items-center gap-1"
                style="background:var(--acc);color:#fff;border-radius:var(--r-sm);align-self:flex-end;font-size:.875rem">
            <span class="material-symbols-rounded" style="font-size:1rem">search</span> Filtrar
        </button>
        @if(request()->hasAny(['placa','nit','estado','docs']))
        <a href="{{ route('gestor-setp.buses.index') }}"
           class="btn btn-outline-secondary d-flex align-items-center gap-1"
           style="border-radius:var(--r-sm);align-self:flex-end;font-size:.875rem">
            <span class="material-symbols-rounded" style="font-size:1rem">close</span> Limpiar
        </a>
        @endif
    </form>

    {{-- Tabla --}}
    <div class="bus-card">
        <div class="table-responsive">
            <table class="bus-table">
                <thead>
                    <tr>
                        <th>Placa</th>
                        <th>Empresa</th>
                        <th>Modelo</th>
                        <th>Capacidad</th>
                        <th>Km</th>
                        <th>Documentos</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($buses as $bus)
                    <tr>
                        <td><span class="bus-placa">{{ $bus->placa }}</span></td>
                        <td>
                            <div class="fw-semibold">{{ $bus->empresa->nombre_empresa ?? '—' }}</div>
                            <div style="font-size:.75rem;color:var(--text-2)">NIT: {{ $bus->NIT }}</div>
                        </td>
                        <td>{{ $bus->modelo ?? '—' }}</td>
                        <td>{{ $bus->capacidad_pasajeros ?? '—' }} pax</td>
                        <td>{{ number_format($bus->kilometraje ?? 0, 0, ',', '.') }} km</td>
                        <td>
                            @php $docsPend = $bus->documentos_pendientes ?? 0; @endphp
                            @if($docsPend > 0)
                                <span class="bus-docs-warn">
                                    <span class="material-symbols-rounded" style="font-size:1rem">warning</span>
                                    {{ $docsPend }} pendiente(s)
                                </span>
                            @else
                                <span class="bus-docs-ok">
                                    <span class="material-symbols-rounded" style="font-size:1rem">check_circle</span>
                                    Completos
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($bus->id_estado == 1)
                                <span class="bus-badge bus-badge-1">
                                    <span class="material-symbols-rounded" style="font-size:.8rem">circle</span> Activo
                                </span>
                            @else
                                <span class="bus-badge bus-badge-2">
                                    <span class="material-symbols-rounded" style="font-size:.8rem">cancel</span> Inactivo
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="bus-actions">
                                {{-- Ver detalle --}}
                                <a href="{{ route('gestor-setp.buses.show', $bus->placa) }}"
                                   class="bus-btn-icon" title="Ver detalle">
                                    <span class="material-symbols-rounded">visibility</span>
                                </a>

                                {{-- Cambiar estado --}}
                                <button class="bus-btn-icon {{ $bus->id_estado == 1 ? 'danger' : '' }}"
                                        title="{{ $bus->id_estado == 1 ? 'Inactivar bus' : 'Activar bus' }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEstado"
                                        data-placa="{{ $bus->placa }}"
                                        data-estado="{{ $bus->id_estado }}"
                                        data-nombre="{{ $bus->empresa->nombre_empresa ?? $bus->NIT }}">
                                    <span class="material-symbols-rounded">
                                        {{ $bus->id_estado == 1 ? 'directions_bus_filled' : 'no_transfer' }}
                                    </span>
                                </button>

                                {{-- Ver documentos --}}
                                <a href="{{ route('gestor-setp.documentos.index', ['placa' => $bus->placa]) }}"
                                   class="bus-btn-icon" title="Ver documentos">
                                    <span class="material-symbols-rounded">folder_open</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="bus-empty">
                                <span class="material-symbols-rounded">directions_bus</span>
                                <p>No se encontraron buses con los filtros aplicados.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($buses->hasPages())
        <div class="bus-pagination">
            {{ $buses->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>

{{-- ══ Modal: Cambiar estado de bus ══════════════════════════ --}}
<div class="modal fade" id="modalEstado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:var(--r-md);overflow:hidden">
            <div class="bus-modal-header d-flex align-items-center gap-2">
                <span class="material-symbols-rounded" style="color:var(--acc)">directions_bus</span>
                <h6 class="modal-title fw-bold mb-0">Cambiar estado del bus</h6>
            </div>
            <form id="formEstado" method="POST" action="">
                @csrf @method('PATCH')
                <div class="modal-body text-center p-4">
                    <div id="modal-estado-msg" style="font-size:.9rem;color:var(--text)"></div>
                    <input type="hidden" name="nuevo_estado" id="nuevo-estado">
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal"
                            style="border-radius:var(--r-sm)">Cancelar</button>
                    <button type="submit" id="btn-confirmar-estado"
                            class="btn btn-sm d-flex align-items-center gap-1"
                            style="background:var(--acc);color:#fff;border-radius:var(--r-sm)">
                        <span class="material-symbols-rounded" style="font-size:1rem">check</span>
                        Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('modalEstado').addEventListener('show.bs.modal', function (e) {
    const btn     = e.relatedTarget;
    const placa   = btn.getAttribute('data-placa');
    const estado  = parseInt(btn.getAttribute('data-estado'));
    const empresa = btn.getAttribute('data-nombre');

    const nuevo  = estado === 1 ? 2 : 1;
    const accion = estado === 1 ? 'inactivar' : 'activar';

    document.getElementById('modal-estado-msg').innerHTML =
        `¿Confirma <strong>${accion}</strong> el bus <strong>${placa}</strong> de la empresa <strong>${empresa}</strong>?`;
    document.getElementById('nuevo-estado').value = nuevo;
    document.getElementById('formEstado').action =
        '{{ url("/gestor-setp/buses") }}/' + placa + '/estado';

    const btnConfirmar = document.getElementById('btn-confirmar-estado');
    if (estado === 1) {
        btnConfirmar.style.background = 'var(--err)';
    } else {
        btnConfirmar.style.background = 'var(--acc)';
    }
});
</script>
@endpush
