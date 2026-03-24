@extends('pasajero.layouts.app')
@section('title', 'Puntos de recarga')

@section('content')
<div class="container-fluid py-4 px-4">

    <div class="pas-header">
        <div>
            <h1><span class="material-symbols-rounded">store</span> Puntos de recarga</h1>
            <p>Encuentra los puntos autorizados para recargar tu tarjeta SIGU.</p>
        </div>
    </div>

    <div class="pas-alert info">
        <span class="material-symbols-rounded" style="font-size:1.1rem;flex-shrink:0">info</span>
        Dirígete a cualquiera de estos puntos con tu tarjeta SIGU y tu documento de identidad para realizar recargas en efectivo.
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('pasajero.recargas.index') }}" class="pas-filters">
        <div style="flex:1;min-width:200px">
            <label class="form-label">Buscar punto</label>
            <input type="text" name="q" class="form-control"
                   placeholder="Nombre o NIT del punto..."
                   value="{{ request('q') }}">
        </div>
        <div>
            <label class="form-label">Ciudad</label>
            <select name="ciudad" class="form-select" style="min-width:160px">
                @foreach($ciudades as $c)
                <option value="{{ $c->id_ciudad }}" {{ $ciudadFiltro == $c->id_ciudad ? 'selected' : '' }}>
                    {{ $c->nombre_city }}
                </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="pas-btn pas-btn-primary" style="align-self:flex-end">
            <span class="material-symbols-rounded" style="font-size:1rem">search</span> Buscar
        </button>
        @if(request()->hasAny(['q']))
        <a href="{{ route('pasajero.recargas.index') }}" class="pas-btn pas-btn-outline" style="align-self:flex-end">
            <span class="material-symbols-rounded" style="font-size:1rem">close</span> Limpiar
        </a>
        @endif
    </form>

    {{-- Grid de puntos --}}
    @if($puntos->count())
    <div class="rec-grid">
        @foreach($puntos as $punto)
        <div class="rec-card">
            <div class="rec-card-head">
                <div class="rec-logo">
                    <span class="material-symbols-rounded">store</span>
                </div>
                <div>
                    <div class="rec-nombre">{{ $punto->nombre_empresa }}</div>
                    <div class="rec-tipo">NIT: {{ number_format($punto->NIT, 0, '', '.') }}</div>
                </div>
            </div>
            <div class="rec-card-body">
                @if($punto->ciudad)
                <div class="rec-info-row">
                    <span class="material-symbols-rounded">location_city</span>
                    <span>{{ $punto->ciudad->nombre_city }}</span>
                </div>
                @endif
                @if($punto->telefono_empresa)
                <div class="rec-info-row">
                    <span class="material-symbols-rounded">phone</span>
                    <strong>{{ $punto->telefono_empresa }}</strong>
                </div>
                @endif
                @if($punto->correo_corporativo)
                <div class="rec-info-row">
                    <span class="material-symbols-rounded">mail</span>
                    <span>{{ $punto->correo_corporativo }}</span>
                </div>
                @endif
                @if($punto->nombres_repre)
                <div class="rec-info-row">
                    <span class="material-symbols-rounded">person</span>
                    <span>{{ $punto->nombres_repre }}</span>
                </div>
                @endif
                <div class="rec-info-row mt-1">
                    @if($punto->id_estado == 1)
                        <span class="pas-badge pas-badge-active">
                            <span class="material-symbols-rounded" style="font-size:.8rem">circle</span> Habilitado
                        </span>
                    @else
                        <span class="pas-badge pas-badge-inactive">No disponible</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @if($puntos->hasPages())
    <div class="d-flex justify-content-end mt-4">
        {{ $puntos->appends(request()->query())->links() }}
    </div>
    @endif
    @else
    <div class="pas-empty">
        <span class="material-symbols-rounded">store</span>
        <p>No se encontraron puntos de recarga con los filtros aplicados.</p>
    </div>
    @endif

</div>
@endsection
