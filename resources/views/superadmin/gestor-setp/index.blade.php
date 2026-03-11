@extends('superadmin.layouts.admin')

@section('title', 'Gestores SETP')

@push('styles')
<style>
/* ── Encabezado de sección ──────────────────────────────── */
.gs-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.75rem;
}
.gs-header-left h1 {
    font-family: var(--ff-d);
    font-size: 1.45rem;
    font-weight: 700;
    color: var(--text);
    margin: 0 0 .2rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}
.gs-header-left p {
    color: var(--text-2);
    font-size: .875rem;
    margin: 0;
}

/* ── Filtros ────────────────────────────────────────────── */
.gs-filters {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r-md);
    padding: 1rem 1.25rem;
    display: flex;
    gap: .75rem;
    flex-wrap: wrap;
    align-items: flex-end;
    margin-bottom: 1.5rem;
    box-shadow: var(--sh-xs);
}
.gs-filters .form-control,
.gs-filters .form-select {
    border-color: var(--border);
    border-radius: var(--r-sm);
    font-size: .875rem;
    color: var(--text);
}
.gs-filters .form-control:focus,
.gs-filters .form-select:focus {
    border-color: var(--p);
    box-shadow: 0 0 0 3px rgba(94,84,142,.12);
}

/* ── Tabla ──────────────────────────────────────────────── */
.gs-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r-md);
    box-shadow: var(--sh-sm);
    overflow: hidden;
}
.gs-table { width: 100%; border-collapse: collapse; }
.gs-table thead tr {
    background: var(--p-xlight);
    border-bottom: 2px solid var(--border);
}
.gs-table th {
    padding: .8rem 1rem;
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--text-2);
    white-space: nowrap;
}
.gs-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .15s ease;
}
.gs-table tbody tr:last-child { border-bottom: none; }
.gs-table tbody tr:hover { background: var(--p-xlight); }
.gs-table td {
    padding: .85rem 1rem;
    font-size: .875rem;
    color: var(--text);
    vertical-align: middle;
}

/* ── Avatar ─────────────────────────────────────────────── */
.gs-avatar {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--p), var(--p-mid));
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    font-size: .95rem;
    font-weight: 700;
    font-family: var(--ff-d);
    flex-shrink: 0;
}

/* ── Badges ─────────────────────────────────────────────── */
.gs-badge {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    padding: .25rem .65rem;
    border-radius: var(--r-xl);
    font-size: .75rem;
    font-weight: 600;
    white-space: nowrap;
}
.gs-badge-active  { background: var(--ok-bg);   color: var(--ok); }
.gs-badge-inactive{ background: var(--err-bg);  color: var(--err); }

/* ── Acciones ────────────────────────────────────────────── */
.gs-actions { display: flex; gap: .35rem; align-items: center; }
.gs-btn-icon {
    width: 32px; height: 32px;
    border: 1px solid var(--border);
    background: var(--surface);
    border-radius: var(--r-sm);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: all .15s ease;
    text-decoration: none;
    color: var(--text-2);
}
.gs-btn-icon:hover {
    border-color: var(--p);
    color: var(--p);
    background: var(--p-light);
}
.gs-btn-icon.danger:hover {
    border-color: var(--err);
    color: var(--err);
    background: var(--err-bg);
}
.gs-btn-icon .material-symbols-rounded { font-size: 1rem; }

/* ── Empty state ─────────────────────────────────────────── */
.gs-empty {
    text-align: center;
    padding: 3.5rem 1rem;
}
.gs-empty .material-symbols-rounded {
    font-size: 3rem;
    color: var(--text-3);
    margin-bottom: .75rem;
    display: block;
}
.gs-empty p {
    color: var(--text-2);
    font-size: .9rem;
    margin: 0;
}

/* ── Paginación ──────────────────────────────────────────── */
.gs-pagination {
    padding: 1rem 1.25rem;
    display: flex;
    justify-content: flex-end;
    border-top: 1px solid var(--border);
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- ── Alertas ──────────────────────────────────────────────── --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
        <span class="material-symbols-rounded">check_circle</span>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
        <span class="material-symbols-rounded">error</span>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── Encabezado ───────────────────────────────────────────── --}}
    <div class="gs-header">
        <div class="gs-header-left">
            <h1>
                <span class="material-symbols-rounded" style="color:var(--p)">manage_accounts</span>
                Gestores SETP
            </h1>
            <p>Usuarios con rol <strong>Gestor SETP</strong> asignados a empresas de tipo Setp.</p>
        </div>
        <a href="{{ route('superadmin.gestores-setp.create') }}" class="btn btn-primary d-flex align-items-center gap-2"
           style="background:var(--p);border-color:var(--p);border-radius:var(--r-sm);">
            <span class="material-symbols-rounded" style="font-size:1.1rem">person_add</span>
            Nuevo Gestor SETP
        </a>
    </div>

    {{-- ── Filtros ───────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('superadmin.gestores-setp.index') }}" class="gs-filters">
        <div>
            <label class="form-label fw-semibold" style="font-size:.8rem;color:var(--text-2)">Buscar</label>
            <input type="text" name="q" class="form-control" placeholder="Nombre o documento…"
                   value="{{ request('q') }}" style="min-width:220px;">
        </div>
        <div>
            <label class="form-label fw-semibold" style="font-size:.8rem;color:var(--text-2)">Empresa SETP</label>
            <select name="nit" class="form-select">
                <option value="">Todas</option>
                @foreach($empresasSetp as $emp)
                <option value="{{ $emp->NIT }}" {{ request('nit') == $emp->NIT ? 'selected' : '' }}>
                    {{ $emp->nombre_empresa }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label fw-semibold" style="font-size:.8rem;color:var(--text-2)">Estado</label>
            <select name="estado" class="form-select">
                <option value="">Todos</option>
                <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activo</option>
                <option value="2" {{ request('estado') == '2' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>
        <button type="submit" class="btn btn-sm d-flex align-items-center gap-1"
                style="background:var(--p);color:#fff;border-radius:var(--r-sm);align-self:flex-end;">
            <span class="material-symbols-rounded" style="font-size:1rem">search</span> Filtrar
        </button>
        @if(request()->hasAny(['q','nit','estado']))
        <a href="{{ route('superadmin.gestores-setp.index') }}"
           class="btn btn-sm d-flex align-items-center gap-1 align-self-end"
           style="border:1px solid var(--border);border-radius:var(--r-sm);color:var(--text-2);">
            <span class="material-symbols-rounded" style="font-size:1rem">close</span> Limpiar
        </a>
        @endif
    </form>

    {{-- ── Tabla ─────────────────────────────────────────────────── --}}
    <div class="gs-card">
        <div class="table-responsive">
            <table class="gs-table">
                <thead>
                    <tr>
                        <th>Gestor</th>
                        <th>Documento</th>
                        <th>Empresa SETP</th>
                        <th>Ciudad</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gestores as $g)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="gs-avatar">
                                    {{ strtoupper(substr($g->primer_nombre ?? 'G', 0, 1)) }}{{ strtoupper(substr($g->primer_apellido ?? '', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">
                                        {{ $g->primer_nombre }} {{ $g->segundo_nombre }} {{ $g->primer_apellido }} {{ $g->segundo_apellido }}
                                    </div>
                                    <div style="font-size:.78rem;color:var(--text-2)">{{ $g->correo }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ number_format($g->doc_usuario, 0, '', '') }}</td>
                        <td>
                            @if($g->empresa)
                                <div class="fw-semibold">{{ $g->empresa->nombre_empresa }}</div>
                                <div style="font-size:.78rem;color:var(--text-2)">NIT: {{ $g->NIT }}</div>
                            @else
                                <span style="color:var(--text-3);font-style:italic">Sin empresa asignada</span>
                            @endif
                        </td>
                        <td>{{ $g->ciudad->nombre_city ?? '—' }}</td>
                        <td>
                            <div style="font-size:.82rem">{{ $g->telefono ?? '—' }}</div>
                        </td>
                        <td>
                            @if($g->id_estado == 1)
                                <span class="gs-badge gs-badge-active">
                                    <span class="material-symbols-rounded" style="font-size:.85rem">circle</span> Activo
                                </span>
                            @else
                                <span class="gs-badge gs-badge-inactive">
                                    <span class="material-symbols-rounded" style="font-size:.85rem">cancel</span> Inactivo
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="gs-actions">
                                <a href="{{ route('superadmin.gestores-setp.edit', $g->doc_usuario) }}"
                                   class="gs-btn-icon" title="Editar">
                                    <span class="material-symbols-rounded">edit</span>
                                </a>

                                <form method="POST"
                                      action="{{ route('superadmin.gestores-setp.toggle-estado', $g->doc_usuario) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="gs-btn-icon {{ $g->id_estado == 1 ? 'danger' : '' }}"
                                            title="{{ $g->id_estado == 1 ? 'Inactivar' : 'Activar' }}"
                                            onclick="return confirm('¿Confirma cambiar el estado de este gestor?')">
                                        <span class="material-symbols-rounded">
                                            {{ $g->id_estado == 1 ? 'person_off' : 'person_check' }}
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="gs-empty">
                                <span class="material-symbols-rounded">manage_accounts</span>
                                <p>No se encontraron gestores SETP con los filtros aplicados.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($gestores->hasPages())
        <div class="gs-pagination">
            {{ $gestores->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
