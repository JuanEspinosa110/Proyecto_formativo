@extends('gestor-setp.layouts.app')

@section('title', $empresa->nombre_empresa)

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:.83rem">
            <li class="breadcrumb-item">
                <a href="{{ route('gestor-setp.empresas.index') }}" style="color:var(--acc)">Empresas</a>
            </li>
            <li class="breadcrumb-item active">{{ $empresa->nombre_empresa }}</li>
        </ol>
    </nav>

    {{-- Banner --}}
    <div class="emp-show-header">
        <h1>{{ $empresa->nombre_empresa }}</h1>
        <p>NIT: {{ number_format($empresa->NIT, 0, '', '.') }} · {{ $empresa->ciudad->nombre_city ?? '—' }}</p>
    </div>

    <div class="emp-show-grid">

        {{-- Columna izquierda --}}
        <div>
            {{-- Datos generales --}}
            <div class="emp-show-card">
                <div class="emp-show-card-head">
                    <span class="material-symbols-rounded">info</span>
                    Información general
                </div>
                <div class="emp-show-card-body">
                    <div class="row g-0">
                        <div class="col-md-6">
                            <div class="info-field">
                                <label>Nombre empresa</label>
                                <span>{{ $empresa->nombre_empresa }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-field">
                                <label>NIT</label>
                                <span>{{ number_format($empresa->NIT, 0, '', '.') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-field">
                                <label>Representante legal</label>
                                <span>
                                    {{ $empresa->primer_nombre_repre }} {{ $empresa->segundo_nombre_repre }}
                                    {{ $empresa->primer_apellido_repre }} {{ $empresa->segundo_apellido_repre }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-field">
                                <label>Ciudad</label>
                                <span>{{ $empresa->ciudad->nombre_city ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-field">
                                <label>Teléfono empresa</label>
                                <span>{{ $empresa->telefono_empresa ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-field">
                                <label>Correo corporativo</label>
                                <span>{{ $empresa->correo_corporativo ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-field">
                                <label>Teléfono representante</label>
                                <span>{{ $empresa->telefono_representante ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-field">
                                <label>Correo representante</label>
                                <span>{{ $empresa->correo_representante ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-field">
                                <label>Registrada el</label>
                                <span>{{ \Carbon\Carbon::parse($empresa->fecha_creacion)->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Buses --}}
            <div class="emp-show-card">
                <div class="emp-show-card-head">
                    <span class="material-symbols-rounded">directions_bus</span>
                    Buses de la empresa ({{ $buses->count() }})
                </div>
                @if($buses->count())
                @foreach($buses as $bus)
                <div class="bus-mini-item">
                    <div class="d-flex align-items-center gap-2">
                        <span class="bus-mini-placa">{{ $bus->placa }}</span>
                        <span style="font-size:.82rem">{{ $bus->modelo ?? '—' }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if(($bus->docs_pendientes ?? 0) > 0)
                        <span style="background:var(--warn-bg);color:var(--warn);border-radius:var(--r-xl);padding:.15rem .5rem;font-size:.7rem;font-weight:600">
                            {{ $bus->docs_pendientes }} doc(s)
                        </span>
                        @endif
                        <a href="{{ route('gestor-setp.documentos.index', ['placa' => $bus->placa]) }}"
                           style="color:var(--acc);font-size:.78rem;display:flex;align-items:center;gap:.2rem;text-decoration:none;font-weight:600">
                            <span class="material-symbols-rounded" style="font-size:.9rem">folder_open</span> Docs
                        </a>
                    </div>
                </div>
                @endforeach
                @else
                <div style="padding:1.25rem;text-align:center;font-size:.85rem;color:var(--text-2)">
                    Esta empresa no tiene buses registrados.
                </div>
                @endif
            </div>
        </div>

        {{-- Columna derecha: estadísticas y rutas asignadas --}}
        <div>
            <div class="emp-show-card">
                <div class="emp-show-card-head">
                    <span class="material-symbols-rounded">bar_chart</span>
                    Estadísticas
                </div>
                <div class="emp-show-card-body">
                    <div class="stat-pill">
                        <span class="stat-pill-label">
                            <span class="material-symbols-rounded">directions_bus</span> Buses totales
                        </span>
                        <span class="stat-pill-val">{{ $buses->count() }}</span>
                    </div>
                    <div class="stat-pill">
                        <span class="stat-pill-label">
                            <span class="material-symbols-rounded">alt_route</span> Rutas operativas
                        </span>
                        <span class="stat-pill-val">{{ $rutas->total() }}</span>
                    </div>
                    <div class="stat-pill">
                        <span class="stat-pill-label">
                            <span class="material-symbols-rounded" style="color:var(--warn)">warning</span> Docs. pendientes
                        </span>
                        <span class="stat-pill-val" style="color:var(--warn)">{{ $docsPendientes ?? 0 }}</span>
                    </div>
                </div>
            </div>

            {{-- Rutas asignadas --}}
            <div class="emp-show-card">
                <div class="emp-show-card-head">
                    <span class="material-symbols-rounded">alt_route</span>
                    Rutas operativas ({{ $rutas->total() }})
                </div>
                @if($rutas->count())
                @foreach($rutas as $ruta)
                <div class="bus-mini-item">
                    <div>
                        <span style="font-family:var(--ff-d);font-weight:700;color:var(--acc)">
                            #{{ $ruta->codigo_ruta }}
                        </span>
                        <span style="font-size:.78rem;color:var(--text-2);margin-left:.35rem">
                            {{ $ruta->barrioOrigen->nombre ?? '?' }} → {{ $ruta->barrioDestino->nombre ?? '?' }}
                        </span>
                        @if($ruta->concesiones->where('NIT', $empresa->NIT)->where('id_estado', 1)->count())
                            <span class="badge bg-success-subtle text-success border border-success-subtle ms-1" style="font-size:.65rem">Ruta Autorizada</span>
                        @else
                            <span class="badge bg-light text-dark border ms-1" style="font-size:.65rem">Uso Público</span>
                        @endif
                    </div>
                    <span style="background:{{ $ruta->id_estado==1?'var(--ok-bg)':'var(--err-bg)' }};color:{{ $ruta->id_estado==1?'var(--ok)':'var(--err)' }};border-radius:var(--r-xl);padding:.15rem .5rem;font-size:.7rem;font-weight:600">
                        {{ $ruta->id_estado==1?'Activa':'Inactiva' }}
                    </span>
                </div>
                @endforeach

                {{-- Paginación de rutas --}}
                @if($rutas->hasPages())
                <div class="p-3 border-top d-flex justify-content-center">
                    {{ $rutas->fragment('rutas-list')->links() }}
                </div>
                @endif

                @else
                <div style="padding:1.25rem;text-align:center;font-size:.85rem;color:var(--text-2)">
                    Sin rutas operativas disponibles.
                </div>
                @endif
            </div>

        </div>
    </div>

</div>
@endsection
