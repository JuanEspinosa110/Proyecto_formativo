@extends('pasajero.layouts.app')
@section('title', 'Rutas disponibles')

@section('content')
<div class="container-fluid py-4 px-4">

    <div class="pas-header">
        <div>
            <h1><span class="material-symbols-rounded">alt_route</span> Rutas disponibles</h1>
            <p>Consulta las rutas activas en tu ciudad y las empresas que las operan.</p>
        </div>
        <a href="{{ route('pasajero.mapa') }}" class="pas-btn pas-btn-outline">
            <span class="material-symbols-rounded" style="font-size:1rem">map</span> Ver en mapa
        </a>
    </div>

    <form method="GET" action="{{ route('pasajero.rutas.index') }}" class="pas-filters">
        <div>
            <label class="form-label">Código</label>
            <input type="number" name="codigo" class="form-control" placeholder="Ej: 23"
                   value="{{ request('codigo') }}" style="width:110px">
        </div>
        <div>
            <label class="form-label">Origen</label>
            <select name="barrio_origen" class="form-select" style="min-width:155px">
                <option value="">Todos</option>
                @foreach($barrios as $b)
                <option value="{{ $b->id_barrio }}" {{ request('barrio_origen') == $b->id_barrio ? 'selected' : '' }}>
                    {{ $b->nombre }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Destino</label>
            <select name="barrio_destino" class="form-select" style="min-width:155px">
                <option value="">Todos</option>
                @foreach($barrios as $b)
                <option value="{{ $b->id_barrio }}" {{ request('barrio_destino') == $b->id_barrio ? 'selected' : '' }}>
                    {{ $b->nombre }}
                </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="pas-btn pas-btn-primary" style="align-self:flex-end">
            <span class="material-symbols-rounded" style="font-size:1rem">search</span> Filtrar
        </button>
        @if(request()->hasAny(['codigo','barrio_origen','barrio_destino']))
        <a href="{{ route('pasajero.rutas.index') }}" class="pas-btn pas-btn-outline" style="align-self:flex-end">
            <span class="material-symbols-rounded" style="font-size:1rem">close</span> Limpiar
        </a>
        @endif
    </form>

    @if($rutas->count())
    <div class="rut-grid">
        @foreach($rutas as $ruta)
        <div class="rut-card">
            <div class="rut-card-head">
                <span class="rut-codigo">Ruta #{{ $ruta->codigo_ruta }}</span>
                <span class="pas-badge pas-badge-active">
                    <span class="material-symbols-rounded" style="font-size:.8rem">circle</span> Activa
                </span>
            </div>
            <div class="rut-card-body">
                <div class="rut-trayecto">
                    <span class="rut-barrio">{{ $ruta->barrioOrigen->nombre ?? '—' }}</span>
                    <span class="material-symbols-rounded rut-arrow">arrow_forward</span>
                    <span class="rut-barrio">{{ $ruta->barrioDestino->nombre ?? '—' }}</span>
                </div>
                <div class="rut-meta">
                    <span>
                        <span class="material-symbols-rounded">corporate_fare</span>
                        {{ $ruta->asignaciones->count() }} empresa(s)
                    </span>
                </div>
                @if($ruta->asignaciones->count())
                <div class="rut-empresas">
                    @foreach($ruta->asignaciones->take(3) as $asig)
                        <span class="rut-empresa-chip">{{ $asig->empresa->nombre_empresa ?? $asig->NIT }}</span>
                    @endforeach
                    @if($ruta->asignaciones->count() > 3)
                        <span class="rut-empresa-chip">+{{ $ruta->asignaciones->count() - 3 }}</span>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @if($rutas->hasPages())
    <div class="d-flex justify-content-end mt-4">
        {{ $rutas->appends(request()->query())->links() }}
    </div>
    @endif
    @else
    <div class="pas-empty">
        <span class="material-symbols-rounded">alt_route</span>
        <p>No se encontraron rutas con los filtros aplicados.</p>
    </div>
    @endif

</div>
@endsection
