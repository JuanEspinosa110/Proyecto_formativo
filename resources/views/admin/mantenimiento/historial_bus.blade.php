@extends('admin.layouts.app')

@section('title', 'Historial del Bus {{ $bus->placa }} — SIGU')

@section('content')
<div class="sigu-fade">
    <div class="sigu-page-hd d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('admin.buses.index') }}" class="text-muted small" style="text-decoration:none;">← Volver a Buses</a>
            <h1 class="sigu-page-title mt-1">Historial del Bus: {{ $bus->placa }}</h1>
            <p class="sigu-page-sub">{{ $bus->modelo }} — Estado actual:
                <span class="badge @if($bus->id_estado == 4) bg-warning text-dark @elseif($bus->id_estado == 1) bg-success @else bg-secondary @endif">
                    {{ $bus->estado->nombre_estado ?? 'Sin estado' }}
                </span>
            </p>
        </div>
        <div>
            <a href="{{ route('admin.mantenimiento.create', ['placa' => $bus->placa, 'origen' => 'admin']) }}" class="btn" style="background:var(--p); color:white; border-radius:0.5rem; padding:0.5rem 1rem; text-decoration:none;">
                + Nuevo Mantenimiento
            </a>
        </div>
    </div>

    {{-- Resumen financiero --}}
    <div class="row g-3 mt-2 mb-4">
        <div class="col-sm-4">
            <div class="bg-white rounded-lg shadow-sm p-4 text-center">
                <p class="small text-muted mb-1">Gasto este mes</p>
                <h3 class="text-success">${{ number_format($gastoEsteMes, 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="bg-white rounded-lg shadow-sm p-4 text-center">
                <p class="small text-muted mb-1">Gasto este año</p>
                <h3>${{ number_format($gastoEsteAnio, 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="bg-white rounded-lg shadow-sm p-4 text-center">
                <p class="small text-muted mb-1">Total histórico</p>
                <h3>${{ number_format($totalGastado, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    {{-- Tabla de registros --}}
    <div class="bg-white rounded-lg shadow-sm p-4">
        <table class="table sigu-table w-100 table-hover">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tareas</th>
                    <th>Costo</th>
                    <th>KM</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($mantenimientos as $m)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($m->fecha_mantenimiento)->format('d/m/Y') }}</td>
                        <td>
                            @foreach($m->detalles->take(2) as $d)
                                <span class="badge bg-light text-dark me-1">{{ $d->tipoMantenimiento->nombre ?? '—' }}</span>
                            @endforeach
                            @if($m->detalles->count() > 2)
                                <span class="text-muted small">+{{ $m->detalles->count() - 2 }} más</span>
                            @endif
                        </td>
                        <td><strong>${{ number_format($m->costo_total, 0, ',', '.') }}</strong></td>
                        <td>{{ number_format($m->kilometraje) }}</td>
                        <td>
                            @if((int)$m->id_estado === 7)
                                <span class="badge bg-warning text-dark">En Taller</span>
                            @else
                                <span class="badge bg-success">Finalizado</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.mantenimiento.show', [$m->id_mantenimiento, 'origen'=>'admin']) }}" style="color:var(--p); text-decoration:none; font-size:0.85rem;">Ver detalle</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Este bus no tiene registros de mantenimiento.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
