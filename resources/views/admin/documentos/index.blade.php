@extends('admin.layouts.app')

@section('title', 'Documentos - SIGU')

@section('content')
<div class="sa-content-header">
    <div class="sa-content-title">
        <h1><span class="material-symbols-rounded">description</span> Documentos</h1>
        <p>Gestiona todos los documentos de la empresa {{ $empresa->nombre_empresa }}</p>
    </div>
    <a href="{{ route('admin.documentos.create') }}" class="sigu-btn sigu-btn-primary">
        <span class="material-symbols-rounded">add</span> Nuevo Documento
    </a>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <span class="material-symbols-rounded">check_circle</span>
    <span>{{ $message }}</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <span class="material-symbols-rounded">error</span>
    <span>{{ $message }}</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Filtros -->
<div class="sa-filters-section">
    <form method="GET" action="{{ route('admin.documentos.index') }}" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label for="tipo" class="form-label">Tipo de Documento</label>
            <select name="tipo" id="tipo" class="form-select form-select-sm">
                <option value="">Todos</option>
                @foreach ($tiposDocumento as $tipo)
                <option value="{{ $tipo->id_tipo_documento }}"
                    {{ request('tipo') == $tipo->id_tipo_documento ? 'selected' : '' }}>
                    {{ $tipo->nombre }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" id="estado" class="form-select form-select-sm">
                <option value="">Todos</option>
                @foreach ($estados as $est)
                <option value="{{ $est->id_estado }}"
                    {{ request('estado') == $est->id_estado ? 'selected' : '' }}>
                    {{ $est->nombre_estado }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="search" class="form-label">Buscar</label>
            <input type="text" name="search" id="search" class="form-control form-control-sm"
                placeholder="Nombre, usuario o placa" value="{{ request('search') }}">
        </div>

        <div class="col-md-3">
            <button type="submit" class="sigu-btn sigu-btn-ghost w-100">
                <span class="material-symbols-rounded">search</span> Filtrar
            </button>
        </div>
    </form>
</div>

<!-- KPIs -->
<div class="sa-kpi-section">
    <div class="sa-kpi-card">
        <div class="kpi-left">
            <span class="kpi-title">Total de Documentos</span>
            <span class="kpi-value">{{ $documentos->total() }}</span>
        </div>
    </div>

    <div class="sa-kpi-card">
        <div class="kpi-left">
            <span class="kpi-title">Documentos Vigentes</span>
            <span class="kpi-value">
                {{ $documentos->filter(fn($d) => !$d->isVencido() && $d->id_estado == 1)->count() }}
            </span>
        </div>
    </div>

    <div class="sa-kpi-card">
        <div class="kpi-left">
            <span class="kpi-title">Documentos Vencidos</span>
            <span class="kpi-value">
                {{ $documentos->filter(fn($d) => $d->isVencido())->count() }}
            </span>
        </div>
    </div>
</div>

<!-- Tabla de Documentos -->
<div class="sa-chart-card">
    <div class="table-responsive">
        @if($documentos->count() > 0)
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Expedición</th>
                    <th>Vencimiento</th>
                    <th>Estado</th>
                    <th>Usuario/Placa</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($documentos as $documento)
                <tr>
                    <td>
                        <strong>{{ $documento->nombre }}</strong>
                    </td>
                    <td>
                        <span class="badge bg-info">
                            {{ $documento->tipoDocumento->nombre ?? 'N/A' }}
                        </span>
                    </td>
                    <td>
                        {{ $documento->fecha_expedicion->format('d/m/Y') }}
                    </td>
                    <td>
                        <span class="d-flex align-items-center gap-2">
                            {{ $documento->fecha_vencimiento->format('d/m/Y') }}
                            @if($documento->isVencido())
                            <span class="badge bg-danger">Vencido</span>
                            @elseif($documento->diasParaVencimiento() <= 30)
                                <span class="badge bg-warning">Por vencer</span>
                        @endif
                        </span>
                    </td>
                    <td>
                        @php
                        $badgeClass = match($documento->id_estado) {
                        1 => 'bg-success',
                        20 => 'bg-success',
                        21 => 'bg-danger',
                        default => 'bg-secondary'
                        };
                        @endphp
                        <span class="badge {{ $badgeClass }}">
                            {{ $documento->estado->nombre_estado ?? 'N/A' }}
                        </span>
                    </td>
                    <td>
                        @if($documento->doc_usuario)
                        <small class="d-block">Doc: {{ $documento->doc_usuario }}</small>
                        @endif
                        @if($documento->placa)
                        <small class="d-block">Bus: {{ $documento->placa }}</small>
                        @endif
                        @if(!$documento->doc_usuario && !$documento->placa)
                        <small class="text-muted">—</small>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('admin.documentos.download', $documento->id_documento) }}"
                                class="sigu-btn sigu-btn-ghost" title="Descargar">
                                <span class="material-symbols-rounded">download</span>
                            </a>
                            <a href="{{ route('admin.documentos.edit', $documento->id_documento) }}"
                                class="sigu-btn sigu-btn-ghost" title="Editar">
                                <span class="material-symbols-rounded">edit</span>
                            </a>
                            <!-- <form action="{{ route('admin.documentos.destroy', $documento->id_documento) }}"
                                method="POST" class="d-inline"
                                onsubmit="return confirm('¿Está seguro de eliminar este documento?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="sigu-btn sigu-btn-ghost sigu-btn-danger" title="Eliminar">
                                    <span class="material-symbols-rounded">delete</span>
                                </button>
                            </form> -->
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="mt-4">
            {{ $documentos->links('pagination::bootstrap-5') }}
        </div>
        @else
        <div class="alert alert-info">
            <span class="material-symbols-rounded">info</span>
            No hay documentos registrados. <a href="{{ route('admin.documentos.create') }}">Crear uno ahora</a>
        </div>
        @endif
    </div>
</div>

<style>
    .sa-content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .sa-content-title h1 {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
    }

    .sa-content-title p {
        margin: 0.25rem 0 0 0;
        opacity: 0.8;
    }

    .sa-filters-section {
        background: var(--card);
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        box-shadow: 0 6px 18px rgba(31, 36, 48, 0.04);
    }

    .sa-kpi-section {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .sa-kpi-card {
        flex: 1;
        min-width: 150px;
        padding: 1rem;
        background: linear-gradient(135deg, var(--p-light), var(--p-mid));
        border-radius: 8px;
        color: white;
    }

    .kpi-title {
        font-size: 0.85rem;
        opacity: 0.85;
    }

    .kpi-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 0.5rem;
    }

    .btn-group-sm .sigu-btn {
        padding: 0.35rem 0.75rem;
        font-size: 0.85rem;
    }

    @media (max-width: 768px) {
        .sa-content-header {
            flex-direction: column;
            gap: 1rem;
        }

        .sa-filters-section .row {
            grid-auto-flow: dense;
        }

        .sa-kpi-section {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection