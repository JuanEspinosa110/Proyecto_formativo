@extends('gestor-setp.layouts.app')

@section('title', 'Rutas')

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
    <div class="rt-header">
        <div>
            <h1><span class="material-symbols-rounded" style="color:var(--acc)">alt_route</span>Rutas</h1>
            <p>Gestión y asignación de rutas a empresas de transporte urbano de tu ciudad.</p>
        </div>
        <a href="{{ route('gestor-setp.rutas.create') }}" class="rt-btn rt-btn-primary">
            <span class="material-symbols-rounded" style="font-size:1.1rem">add_road</span>
            Nueva ruta
        </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('gestor-setp.rutas.index') }}" class="rt-filters">
        <div>
            <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-2)">Código</label>
            <input type="number" name="codigo" class="form-control" placeholder="Ej: 23"
                   value="{{ request('codigo') }}" style="width:110px">
        </div>
        <div>
            <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-2)">Estado</label>
            <select name="estado" class="form-select" style="min-width:130px">
                <option value="">Todos</option>
                <option value="1" {{ request('estado')=='1'?'selected':'' }}>Activa</option>
                <option value="2" {{ request('estado')=='2'?'selected':'' }}>Inactiva</option>
            </select>
        </div>
        <button type="submit" class="rt-btn rt-btn-primary" style="align-self:flex-end">
            <span class="material-symbols-rounded" style="font-size:1rem">search</span> Filtrar
        </button>
        @if(request()->hasAny(['codigo','estado']))
        <a href="{{ route('gestor-setp.rutas.index') }}" class="rt-btn rt-btn-outline" style="align-self:flex-end">
            <span class="material-symbols-rounded" style="font-size:1rem">close</span> Limpiar
        </a>
        @endif
    </form>

    {{-- Grid de rutas --}}
    @if($rutas->count())
    <div class="rt-grid">
        @foreach($rutas as $ruta)
        <div class="rt-card">
            <div class="rt-card-head">
                <div>
                    <span class="rt-code">Ruta #{{ $ruta->codigo_ruta }}</span>
                    <div style="font-size:.75rem;color:var(--text-2);margin-top:.1rem">
                        ID: {{ $ruta->id_ruta }}
                    </div>
                </div>
                @if($ruta->id_estado == 1)
                    <span class="rt-badge rt-badge-active">
                        <span class="material-symbols-rounded" style="font-size:.8rem">circle</span> Activa
                    </span>
                @else
                    <span class="rt-badge rt-badge-inactive">
                        <span class="material-symbols-rounded" style="font-size:.8rem">cancel</span> Inactiva
                    </span>
                @endif
            </div>

            <div class="rt-card-body">
                {{-- Recorrido --}}
                <div class="rt-route-arrow">
                    <span class="rt-barrio">{{ $ruta->barrioOrigen->nombre ?? 'N/A' }}</span>
                    <span class="material-symbols-rounded rt-arrow">arrow_forward</span>
                    <span class="rt-barrio">{{ $ruta->barrioDestino->nombre ?? 'N/A' }}</span>
                </div>

                @php
                    $empresasUnicas = $ruta->asignaciones->pluck('empresa')->unique('NIT')->filter();
                @endphp
                <div class="rt-meta">
                    <span><span class="material-symbols-rounded" style="font-size:.9rem">location_city</span>
                        {{ $ruta->ciudad->nombre_city ?? '—' }}
                    </span>
                    <span><span class="material-symbols-rounded" style="font-size:.9rem">corporate_fare</span>
                        @if($empresasUnicas->isEmpty())
                            Todas las empresas (Uso libre)
                        @else
                            {{ $empresasUnicas->count() }} empresa(s)
                        @endif
                    </span>
                    <span><span class="material-symbols-rounded" style="font-size:.9rem">directions_bus</span>
                        {{ $ruta->buses_asignados_count }} buses asig.
                    </span>
                </div>

                {{-- Empresas autorizadas/asignadas para usar esta ruta --}}
                @if($empresasUnicas->isNotEmpty())
                <div class="rt-emp-list">
                    @foreach($empresasUnicas->take(3) as $empresa)
                        <span class="rt-emp-chip">{{ $empresa->nombre_empresa ?? $empresa->NIT }}</span>
                    @endforeach
                    @if($empresasUnicas->count() > 3)
                        <span class="rt-emp-chip">+{{ $empresasUnicas->count() - 3 }} más</span>
                    @endif
                </div>
                @else
                <div class="rt-emp-list">
                    <span class="rt-emp-chip" style="background-color: var(--primary-100); color: var(--primary); border: 1px dotted var(--primary);">
                        <span class="material-symbols-rounded" style="font-size: .9rem;">lock_open</span> No restringida
                    </span>
                </div>
                @endif
            </div>

            <div class="rt-card-footer">
                <div style="display:flex;gap:.4rem">
                    {{-- Asignar empresa --}}
                    <button class="rt-btn rt-btn-outline"
                            data-bs-toggle="modal"
                            data-bs-target="#modalAsignar"
                            data-ruta-id="{{ $ruta->id_ruta }}"
                            data-ruta-codigo="{{ $ruta->codigo_ruta }}">
                        <span class="material-symbols-rounded" style="font-size:.95rem">add_business</span>
                        Asignar empresa
                    </button>

                    {{-- Editar --}}
                    <a href="{{ route('gestor-setp.rutas.edit', $ruta->id_ruta) }}" class="rt-btn rt-btn-outline">
                        <span class="material-symbols-rounded" style="font-size:.95rem">edit</span>
                    </a>
                </div>

                {{-- Toggle estado --}}
                <form method="POST" action="{{ route('gestor-setp.rutas.toggle-estado', $ruta->id_ruta) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="rt-btn {{ $ruta->id_estado == 1 ? 'rt-btn-danger' : 'rt-btn-outline' }}"
                            onclick="return confirm('¿Confirma cambiar el estado de esta ruta?')">
                        <span class="material-symbols-rounded" style="font-size:.95rem">
                            {{ $ruta->id_estado == 1 ? 'do_not_disturb_on' : 'check_circle' }}
                        </span>
                        {{ $ruta->id_estado == 1 ? 'Inactivar' : 'Activar' }}
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Paginación --}}
    @if($rutas->hasPages())
    <div class="d-flex justify-content-end mt-4">
        {{ $rutas->appends(request()->query())->links() }}
    </div>
    @endif

    @else
    <div class="rt-empty">
        <span class="material-symbols-rounded">alt_route</span>
        <p>No se encontraron rutas con los filtros aplicados.</p>
    </div>
    @endif

</div>

{{-- ══ Modal: Asignar empresa a ruta ══════════════════════════ --}}
<div class="modal fade" id="modalAsignar" tabindex="-1" aria-labelledby="modalAsignarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--r-md);overflow:hidden">
            <div class="rt-modal-header d-flex align-items-center gap-2">
                <span class="material-symbols-rounded" style="color:var(--acc)">add_business</span>
                <h5 class="modal-title fw-bold mb-0" id="modalAsignarLabel">
                    Asignar empresa — Ruta <span id="modal-ruta-codigo"></span>
                </h5>
            </div>
            <form id="formAsignar" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="id_tipo_asignacion" value="1">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Empresa de transporte <span style="color:var(--err)">*</span></label>
                        <select name="Nit" class="form-select" required>
                            <option value="" disabled selected>— Seleccione empresa —</option>
                            @foreach($empresasTransporte as $emp)
                            <option value="{{ $emp->NIT }}">{{ $emp->nombre_empresa }}</option>
                            @endforeach
                        </select>
                        <p style="font-size:.76rem;color:var(--text-2);margin-top:.3rem">
                            Solo se muestran empresas de transporte urbano de tu ciudad.
                        </p>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Fecha inicio <span style="color:var(--err)">*</span></label>
                            <input type="date" name="fecha_inicio" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Fecha fin</label>
                            <input type="date" name="fecha_fin" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                            style="border-radius:var(--r-sm)">Cancelar</button>
                    <button type="submit" class="rt-btn rt-btn-primary">
                        <span class="material-symbols-rounded" style="font-size:1rem">save</span>
                        Asignar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Inyectar id de ruta en el modal de asignación
document.getElementById('modalAsignar').addEventListener('show.bs.modal', function (e) {
    const btn    = e.relatedTarget;
    const id     = btn.getAttribute('data-ruta-id');
    const codigo = btn.getAttribute('data-ruta-codigo');
    document.getElementById('modal-ruta-codigo').textContent = '#' + codigo;
    document.getElementById('formAsignar').action =
        '{{ url("/gestor-setp/rutas") }}/' + id + '/asignar';
});
</script>
@endpush
