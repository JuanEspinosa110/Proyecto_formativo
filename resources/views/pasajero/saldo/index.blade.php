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

                {{-- Accesos Rápidos (mejorados) --}}
                <div class="d-grid gap-2 d-md-flex justify-content-md-start pas-accesos mt-4">
                    <a href="{{ route('pasajero.recargas.index') }}"
                        class="btn btn-outline-primary btn-lg d-flex align-items-center gap-2">
                        <span class="material-symbols-rounded">store</span> Puntos de recarga
                    </a>
                    <a href="{{ route('pasajero.historial.index') }}"
                        class="btn btn-outline-secondary btn-lg d-flex align-items-center gap-2">
                        <span class="material-symbols-rounded">history</span> Ver Historial
                    </a>
                    <a href="{{ route('pasajero.rutas.index') }}"
                        class="btn btn-outline-success btn-lg d-flex align-items-center gap-2">
                        <span class="material-symbols-rounded">alt_route</span> Explorar Rutas
                    </a>
                    <a href="{{ route('pasajero.mapa') }}"
                        class="btn btn-outline-info btn-lg d-flex align-items-center gap-2">
                        <span class="material-symbols-rounded">map</span> Mapa de Paradas
                    </a>
                </div>

                {{-- Botón de recarga con Stripe --}}
                <a href="{{ route('pasajero.tarjeta.recargar') }}"
                    class="btn btn-success btn-lg w-100 fw-bold py-2 d-flex justify-content-center align-items-center gap-2 mt-3">
                    Recargar con Stripe <span class="material-symbols-rounded">credit_score</span>
                </a>

                {{-- Bloque de Cambio de Tarjeta --}}
                <div class="pas-card mt-4 support-banner">
                    <div class="pas-card-body d-flex align-items-center gap-3">
                        <div class="support-icon text-danger bg-danger bg-opacity-10 border border-danger border-opacity-25"
                            style="box-shadow: none;">
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
                            <div class="value">{{ \Carbon\Carbon::parse($titularidad->fecha_inicio)->format('d/m/Y') }}
                            </div>
                        </div>
                        <div class="saldo-detalle-row">
                            <div class="key"><span class="material-symbols-rounded">payments</span> Total recargado</div>
                            <div class="value" style="color:var(--ok)">$ {{ number_format($totalRecargado, 2, ',', '.') }}
                            </div>
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
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="m-0"
                            style="font-family:var(--ff-d); font-size:1.1rem; font-weight:700; color:var(--text); display:flex; align-items:center; gap:.5rem">
                            <span class="material-symbols-rounded text-primary">history</span> Últimas recargas
                        </h3>
                        <a href="{{ route('pasajero.historial.index', ['tab' => 'recargas']) }}"
                            style="font-size:.78rem;color:var(--pas);font-weight:600;text-decoration:none;display:flex;align-items:center;gap:.2rem">
                            Ver todas <span class="material-symbols-rounded" style="font-size:.9rem">chevron_right</span>
                        </a>
                    </div>

                    @forelse($recargas as $rec)
                        <div class="pas-recarga-item">
                            <div class="pas-recarga-item-icon">
                                <span class="material-symbols-rounded">add_card</span>
                            </div>
                            <div class="pas-recarga-info">
                                <div class="pas-recarga-title">Recarga de saldo</div>
                                <div class="pas-recarga-date">
                                    {{ \Carbon\Carbon::parse($rec->created_at)->translatedFormat('d M, Y • H:i') }}
                                </div>
                            </div>
                            <div class="pas-recarga-monto">
                                +${{ number_format($rec->monto, 0, ',', '.') }}
                            </div>
                        </div>
                    @empty
                        <div class="pas-empty pb-4 pt-4 bg-white rounded-4 border">
                            <span class="material-symbols-rounded">add_card</span>
                            <p>No tienes recargas registradas aún.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Resumen del Mes (Premium) --}}
                <div class="pas-card">
                    <div class="pas-card-head">
                        <h3><span class="material-symbols-rounded">monitoring</span> Resumen de
                            {{ \Carbon\Carbon::now()->translatedFormat('F') }}</h3>
                    </div>
                    <div class="pas-card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="pas-stat-card">
                                    <span class="pas-stat-label">Viajes</span>
                                    <span class="pas-stat-value">{{ $viajesMes->count() }}</span>
                                    @if(isset($conteoViajesMesAnterior) && $conteoViajesMesAnterior > 0)
                                        <span
                                            class="pas-stat-sub {{ $viajesMes->count() >= $conteoViajesMesAnterior ? 'up' : 'down' }}">
                                            <span class="material-symbols-rounded"
                                                style="font-size:1rem">{{ $viajesMes->count() >= $conteoViajesMesAnterior ? 'trending_up' : 'trending_down' }}</span>
                                            {{ $conteoViajesMesAnterior }} el mes pasado
                                        </span>
                                    @else
                                        <span class="pas-stat-sub neutral" style="font-size: .65rem;">Sin datos previos</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="pas-stat-card">
                                    <span class="pas-stat-label">Gastado</span>
                                    <span class="pas-stat-value">${{ number_format($totalGastadoMes, 0, ',', '.') }}</span>
                                    @if(isset($totalGastadoMesAnterior) && $totalGastadoMesAnterior > 0)
                                        <span
                                            class="pas-stat-sub {{ $totalGastadoMes <= $totalGastadoMesAnterior ? 'up' : 'down' }}">
                                            <span class="material-symbols-rounded"
                                                style="font-size:1rem">{{ $totalGastadoMes <= $totalGastadoMesAnterior ? 'savings' : 'payments' }}</span>
                                            ${{ number_format($totalGastadoMesAnterior, 0, ',', '.') }} ant.
                                        </span>
                                    @else
                                        <span class="pas-stat-sub neutral" style="font-size: .65rem;">Mes de estreno</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($viajesMes->isEmpty())
                            <div class="mt-4 p-3 rounded-4 bg-light border d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-circle d-flex shadow-sm">
                                    <span class="material-symbols-rounded"
                                        style="font-variation-settings: 'FILL' 1;">rocket_launch</span>
                                </div>
                                <div style="font-size: .82rem; line-height: 1.4;">
                                    <strong class="d-block text-primary">¡Bienvenido a
                                        {{ \Carbon\Carbon::now()->translatedFormat('F') }}!</strong>
                                    Aún no has registrado viajes este mes. ¡Sigue moviéndote con SIGU!
                                </div>
                            </div>
                        @else
                            <div class="mt-3 bg-light rounded p-3 d-flex align-items-start gap-2 text-muted"
                                style="font-size: .8rem;">
                                <span class="material-symbols-rounded text-info" style="font-size: 1.1rem;">info</span>
                                Al usar tu tarjeta SIGU en lugar de efectivo, contribuyes a un transporte más ágil y seguro.
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection