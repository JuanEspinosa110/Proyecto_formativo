@extends('gestor-setp.layouts.app')

@section('title', 'Detalle de Bus - ' . $bus->placa)

@push('styles')
<style>
.bus-detail-header {
    background: #fff;
    border-radius: var(--r-md);
    padding: 1.5rem;
    box-shadow: var(--sh-sm);
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 4px solid var(--acc);
}
.bus-detail-title h2 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.bus-detail-subtitle {
    font-size: 0.9rem;
    color: var(--text-2);
    margin-top: 0.25rem;
}
.card-section {
    background: #fff;
    border-radius: var(--r-md);
    padding: 1.5rem;
    box-shadow: var(--sh-sm);
    margin-bottom: 1.5rem;
    height: 100%;
}
.section-title {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: var(--text);
    border-bottom: 1px solid var(--border);
    padding-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.dev-list-item {
    padding: 0.75rem 0;
    border-bottom: 1px dashed var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.dev-list-item:last-child {
    border-bottom: none;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:.83rem;">
            <li class="breadcrumb-item">
                <a href="{{ route('gestor-setp.buses.index') }}" style="color:var(--acc)">Buses</a>
            </li>
            <li class="breadcrumb-item active">Detalle: {{ $bus->placa }}</li>
        </ol>
    </nav>

    {{-- Encabezado --}}
    <div class="bus-detail-header">
        <div class="bus-detail-title">
            <h2>
                <span class="material-symbols-rounded" style="color:var(--acc);">directions_bus</span>
                Bus {{ $bus->placa }}
            </h2>
            <div class="bus-detail-subtitle">
                Asignado a la empresa <strong>{{ $bus->empresa->nombre_empresa ?? '—' }}</strong> (NIT: {{ $bus->NIT }})
            </div>
        </div>
        <div>
            @if($bus->id_estado == 1)
                <span class="badge bg-success" style="font-size: 0.9rem; padding: 0.5em 1em;">
                    <span class="material-symbols-rounded" style="font-size:1rem; vertical-align: middle;">check_circle</span> Activo
                </span>
            @else
                <span class="badge bg-danger" style="font-size: 0.9rem; padding: 0.5em 1em;">
                    <span class="material-symbols-rounded" style="font-size:1rem; vertical-align: middle;">cancel</span> Inactivo/Suspendido
                </span>
            @endif
        </div>
    </div>

    <div class="row g-4">
        {{-- Información Técnica --}}
        <div class="col-md-5">
            <div class="card-section">
                <h3 class="section-title">
                    <span class="material-symbols-rounded" style="color:var(--acc)">info</span>
                    Información Técnica
                </h3>
                
                <div class="dev-list-item">
                    <span class="fw-semibold text-muted">Modelo</span>
                    <span>{{ $bus->modelo ?? 'No registrado' }}</span>
                </div>
                <div class="dev-list-item">
                    <span class="fw-semibold text-muted">Capacidad</span>
                    <span>{{ $bus->capacidad_pasajeros ?? '0' }} pasajeros</span>
                </div>
                <div class="dev-list-item">
                    <span class="fw-semibold text-muted">Kilometraje</span>
                    <span>{{ number_format($bus->kilometraje ?? 0, 0, ',', '.') }} km</span>
                </div>
                <div class="dev-list-item">
                    <span class="fw-semibold text-muted">Linc. Tránsito</span>
                    <span>{{ $bus->linc_transito ?? '—' }}</span>
                </div>
                <div class="dev-list-item">
                    <span class="fw-semibold text-muted">Número Chasis</span>
                    <span>{{ $bus->numero_chasis ?? '—' }}</span>
                </div>
                <div class="dev-list-item">
                    <span class="fw-semibold text-muted">Número Motor</span>
                    <span>{{ $bus->numero_motor ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- Estado de Documentación --}}
        <div class="col-md-7">
            <div class="card-section">
                <h3 class="section-title">
                    <span class="material-symbols-rounded" style="color:var(--acc)">folder_open</span>
                    Estado de Documentación
                </h3>

                @if($docsVencidos->count() > 0)
                    <div class="alert alert-danger mb-3 d-flex align-items-center gap-2">
                        <span class="material-symbols-rounded">error</span>
                        <div>
                            <strong>Atención:</strong> Existen <strong>{{ $docsVencidos->count() }}</strong> documento(s) vencido(s).
                        </div>
                    </div>
                @endif
                
                @if($docsPorVencer->count() > 0)
                    <div class="alert alert-warning mb-3 d-flex align-items-center gap-2">
                        <span class="material-symbols-rounded">warning</span>
                        <div>
                            Existen <strong>{{ $docsPorVencer->count() }}</strong> documento(s) próximos a vencer (30 días o menos).
                        </div>
                    </div>
                @endif
                
                @if($bus->documentos->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <span class="material-symbols-rounded" style="font-size:3rem; opacity: 0.5;">description</span>
                        <p class="mt-2 text-sm">Este bus no tiene documentos registrados en el sistema.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm mt-3">
                            <thead class="table-light">
                                <tr>
                                    <th>Documento</th>
                                    <th>Vencimiento</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bus->documentos as $doc)
                                    @php
                                        $dias = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($doc->fecha_vencimiento), false);
                                        if($dias < 0) {
                                            $estado = 'Vencido';
                                            $badge = 'bg-danger';
                                        } elseif($dias <= 30) {
                                            $estado = "Vence en {$dias} días";
                                            $badge = 'bg-warning text-dark';
                                        } else {
                                            $estado = 'Vigente';
                                            $badge = 'bg-success';
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-semibold" style="font-size: 0.9rem;">
                                                {{ $doc->tipoDocumento->nombre ?? 'Documento' }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                Ref: {{ $doc->nombre }}
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            {{ \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') }}
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge {{ $badge }}">{{ $estado }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="mt-4 text-end">
                    <a href="{{ route('gestor-setp.documentos.index', ['placa' => $bus->placa]) }}" class="btn btn-primary d-inline-flex align-items-center gap-2" style="background:var(--acc); border:none;">
                        <span class="material-symbols-rounded" style="font-size:1.1rem;">manage_search</span>
                        Gestionar Documentos Completos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
