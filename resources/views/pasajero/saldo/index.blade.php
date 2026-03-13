@extends('pasajero.layouts.app')
@section('title', 'Mi tarjeta')

@section('content')
<div class="container-fluid py-4 px-4">

    <div class="pas-header mb-4">
        <div>
            <h1><span class="material-symbols-rounded">credit_card</span> Mi tarjeta</h1>
            <p>Consulta tu saldo y las últimas recargas realizadas.</p>
        </div>
    </div>

    <div class="row">
        {{-- Columna Izquierda: Tarjeta y Accesos Rápida --}}
        <div class="col-lg-6 mb-4">
            {{-- Tarjeta visual --}}
            <div class="saldo-card-grande w-100">
                <div style="position:relative;z-index:1">
                    <div class="label">Saldo disponible</div>
                    <div class="monto">$ {{ number_format($tarjeta->saldo ?? 0, 2, ',', '.') }}</div>
                    <div class="sub">Tarjeta SIGU activa</div>
                    <div class="id-tarjeta">{{ $tarjeta->codigo_tarjeta }}</div>
                </div>
            </div>

            {{-- Accesos Rápidos (Nuevo bloque para hacer la vista más rica) --}}
            <div class="pas-accesos mt-4">
                <a href="{{ route('pasajero.recargas.index') }}" class="pas-acceso-item">
                    <span class="material-symbols-rounded">store</span>
                    <span class="lbl">Puntos de recarga</span>
                </a>
                <a href="{{ route('pasajero.historial.index') }}" class="pas-acceso-item">
                    <span class="material-symbols-rounded">history</span>
                    <span class="lbl">Ver Historial</span>
                </a>
                <a href="{{ route('pasajero.rutas.index') }}" class="pas-acceso-item">
                    <span class="material-symbols-rounded">alt_route</span>
                    <span class="lbl">Explorar Rutas</span>
                </a>
                <a href="{{ route('pasajero.mapa') }}" class="pas-acceso-item">
                    <span class="material-symbols-rounded">map</span>
                    <span class="lbl">Mapa de Paradas</span>
                </a>
            </div>

            {{-- Bloque de Cambio de Tarjeta --}}
            <div class="pas-card mt-4 support-banner">
                <div class="pas-card-body d-flex align-items-center gap-3">
                    <div class="support-icon text-danger bg-danger bg-opacity-10 border border-danger border-opacity-25" style="box-shadow: none;">
                        <span class="material-symbols-rounded">find_replace</span>
                    </div>
                    <div>
                        <h4 class="support-title">¿Tarjeta robada o perdida?</h4>
                        <p class="support-text">Inactiva esta tarjeta y transfiere tu saldo a un nuevo plástico.</p>
                        <a href="{{ route('pasajero.tarjeta.cambiar') }}" class="support-link text-danger mt-2">
                            Iniciar cambio <span class="material-symbols-rounded">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna Derecha: Detalles y Últimos Movimientos --}}
        <div class="col-lg-6">
            {{-- Detalle --}}
            <div class="pas-card mb-4">
                <div class="pas-card-head">
                    <h3><span class="material-symbols-rounded">info</span> Información de la tarjeta</h3>
                </div>
                <div class="pas-card-body">
                    <div class="saldo-detalle-row">
                        <div class="key"><span class="material-symbols-rounded">tag</span> Código de tarjeta</div>
                        <div class="value" style="font-family:monospace">{{ $tarjeta->codigo_tarjeta }}</div>
                    </div>
                    <div class="saldo-detalle-row">
                        <div class="key"><span class="material-symbols-rounded">calendar_today</span> Activa desde</div>
                        <div class="value">{{ \Carbon\Carbon::parse($titularidad->fecha_inicio)->format('d/m/Y') }}</div>
                    </div>
                    <div class="saldo-detalle-row">
                        <div class="key"><span class="material-symbols-rounded">payments</span> Total recargado</div>
                        <div class="value" style="color:var(--ok)">$ {{ number_format($totalRecargado, 2, ',', '.') }}</div>
                    </div>
                    <div class="saldo-detalle-row">
                        <div class="key"><span class="material-symbols-rounded">radio_button_checked</span> Estado</div>
                        <div class="value">
                            @if($tarjeta->id_estado == 1)
                                <span class="pas-badge pas-badge-active">
                                    <span class="material-symbols-rounded" style="font-size:.8rem">circle</span> Activa
                                </span>
                            @else
                                <span class="pas-badge pas-badge-inactive">Inactiva</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Últimas recargas --}}
            <div class="pas-card">
                <div class="pas-card-head">
                    <h3><span class="material-symbols-rounded">history</span> Últimas recargas</h3>
                    <a href="{{ route('pasajero.historial.index', ['tab' => 'recargas']) }}"
                       style="font-size:.78rem;color:var(--pas);font-weight:600;text-decoration:none;display:flex;align-items:center;gap:.2rem">
                        Ver todas <span class="material-symbols-rounded" style="font-size:.9rem">chevron_right</span>
                    </a>
                </div>
                @forelse($recargas as $rec)
                <div class="pas-hist-item">
                    <div class="pas-hist-item-icon recarga">
                        <span class="material-symbols-rounded">add_card</span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="pas-hist-item-desc">Recarga de saldo</div>
                        <div class="pas-hist-item-meta">
                            {{ \Carbon\Carbon::parse($rec->created_at)->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    <div class="pas-hist-item-monto positivo">
                        +$ {{ number_format($rec->monto, 0, ',', '.') }}
                    </div>
                </div>
                @empty
                <div class="pas-empty pb-4 pt-4">
                    <span class="material-symbols-rounded">add_card</span>
                    <p>No tienes recargas registradas aún.</p>
                </div>
                @endforelse
            </div>

            {{-- Resumen de Gastos o Progreso --}}
            <div class="pas-card mt-4">
                <div class="pas-card-head">
                    <h3><span class="material-symbols-rounded">monitoring</span> Resumen del Mes</h3>
                </div>
                <div class="pas-card-body">
                    <div class="d-flex gap-3 align-items-center">
                        <div class="flex-grow-1">
                            <span class="d-block text-uppercase fw-bold text-muted" style="font-size:.75rem;">Viajes en el mes</span>
                            <span style="font-family: var(--ff-d); font-size: 1.5rem; font-weight: 700; color: var(--text);">{{ $viajesMes->count() ?? 0 }} <span class="fw-medium text-secondary" style="font-size: .8rem;">viajes</span></span>
                        </div>
                        <div class="bg-secondary opacity-25" style="width: 1px; height: 40px;"></div>
                        <div class="flex-grow-1">
                            <span class="d-block text-uppercase fw-bold text-muted" style="font-size:.75rem;">Total gastado</span>
                            <span style="font-family: var(--ff-d); font-size: 1.5rem; font-weight: 700; color: var(--err);">$ {{ number_format($totalGastadoMes ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="mt-3 bg-light rounded p-3 d-flex align-items-start gap-2 text-muted" style="font-size: .8rem;">
                        <span class="material-symbols-rounded text-info" style="font-size: 1.1rem;">info</span>
                        Al usar tu tarjeta SIGU en lugar de efectivo, contribuyes a un transporte más ágil y seguro.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
