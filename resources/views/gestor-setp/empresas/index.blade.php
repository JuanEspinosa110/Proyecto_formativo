@extends('gestor-setp.layouts.app')

@section('title', 'Empresas de Transporte')

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- Alertas --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3">
        <span class="material-symbols-rounded">check_circle</span>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Encabezado --}}
    <div class="emp-header">
        <div>
            <h1>
                <span class="material-symbols-rounded" style="color:var(--acc)">business</span>
                Empresas de Transporte
            </h1>
            <p>Empresas de transporte urbano en tu ciudad con información relevante.</p>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('gestor-setp.empresas.index') }}" class="emp-filters">
        <div>
            <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-2)">Buscar</label>
            <input type="text" name="q" class="form-control" placeholder="Nombre o NIT…"
                   value="{{ request('q') }}" style="min-width:200px">
        </div>
        <div>
            <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-2)">Estado</label>
            <select name="estado" class="form-select" style="min-width:130px">
                <option value="">Todos</option>
                <option value="1" {{ request('estado')=='1'?'selected':'' }}>Activa</option>
                <option value="2" {{ request('estado')=='2'?'selected':'' }}>Inactiva</option>
            </select>
        </div>
        <button type="submit" class="emp-btn" style="align-self:flex-end">
            <span class="material-symbols-rounded" style="font-size:1rem">search</span> Filtrar
        </button>
        @if(request()->hasAny(['q','estado']))
        <a href="{{ route('gestor-setp.empresas.index') }}"
           class="btn btn-outline-secondary d-flex align-items-center gap-1"
           style="border-radius:var(--r-sm);align-self:flex-end;font-size:.82rem">
            <span class="material-symbols-rounded" style="font-size:1rem">close</span> Limpiar
        </a>
        @endif
    </form>

    {{-- Grid --}}
    @if($empresas->count())
    <div class="emp-grid">
        @foreach($empresas as $empresa)
        <div class="emp-card">
            <div class="emp-card-head">
                <div class="d-flex align-items-center gap-3">
                    <div class="emp-logo">
                        <span class="material-symbols-rounded">directions_bus</span>
                    </div>
                    <div>
                        <p class="emp-name">{{ $empresa->nombre_empresa }}</p>
                        <p class="emp-nit">NIT: {{ number_format($empresa->NIT, 0, '', '.') }}</p>
                    </div>
                </div>
                @if($empresa->id_estado == 1)
                    <span class="emp-badge emp-badge-active">Activa</span>
                @else
                    <span class="emp-badge emp-badge-inactive">Inactiva</span>
                @endif
            </div>

            <div class="emp-card-body">
                <div class="emp-info-row">
                    <span class="material-symbols-rounded">person</span>
                    Representante:
                    <strong>{{ $empresa->primer_nombre_repre }} {{ $empresa->primer_apellido_repre }}</strong>
                </div>
                <div class="emp-info-row">
                    <span class="material-symbols-rounded">phone</span>
                    <strong>{{ $empresa->telefono_empresa ?? '—' }}</strong>
                    &nbsp;·&nbsp;
                    <strong>{{ $empresa->correo_corporativo ?? '—' }}</strong>
                </div>
                <div class="emp-info-row">
                    <span class="material-symbols-rounded">location_city</span>
                    Ciudad:
                    <strong>{{ $empresa->ciudad->nombre_city ?? '—' }}</strong>
                </div>
                <div class="emp-info-row">
                    <span class="material-symbols-rounded">calendar_today</span>
                    Registrada:
                    <strong>{{ \Carbon\Carbon::parse($empresa->fecha_creacion)->format('d/m/Y') }}</strong>
                </div>

                {{-- Estadísticas --}}
                <div class="emp-stats">
                    <div class="emp-stat">
                        <div class="emp-stat-val">{{ $empresa->buses_count ?? 0 }}</div>
                        <div class="emp-stat-lbl">Buses</div>
                    </div>
                    <div class="emp-stat">
                        <div class="emp-stat-val">{{ $empresa->rutas_count ?? 0 }}</div>
                        <div class="emp-stat-lbl">Rutas</div>
                    </div>
                    <div class="emp-stat">
                        <div class="emp-stat-val">{{ $empresa->docs_pendientes ?? 0 }}</div>
                        <div class="emp-stat-lbl" style="{{ ($empresa->docs_pendientes ?? 0) > 0 ? 'color:var(--err)' : '' }}">
                            Docs. pendientes
                        </div>
                    </div>
                </div>
            </div>

            <div class="emp-card-footer">
                <a href="{{ route('gestor-setp.empresas.show', $empresa->NIT) }}" class="emp-btn">
                    <span class="material-symbols-rounded" style="font-size:1rem">visibility</span>
                    Ver detalle
                </a>
            </div>
        </div>
        @endforeach
    </div>

    @if($empresas->hasPages())
    <div class="d-flex justify-content-end mt-4">
        {{ $empresas->appends(request()->query())->links() }}
    </div>
    @endif

    @else
    <div class="emp-empty">
        <span class="material-symbols-rounded">business</span>
        <p>No se encontraron empresas de transporte en tu ciudad.</p>
    </div>
    @endif

</div>
@endsection
