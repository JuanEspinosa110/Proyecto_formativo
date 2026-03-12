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

    <div class="saldo-wrap">

        {{-- Tarjeta visual --}}
        <div class="saldo-card-grande">
            <div style="position:relative;z-index:1">
                <div class="label">Saldo disponible</div>
                <div class="monto">$ {{ number_format($tarjeta->saldo ?? 0, 2, ',', '.') }}</div>
                <div class="sub">Tarjeta SIGU activa</div>
                <div class="id-tarjeta">{{ $tarjeta->id_tarjeta }}</div>
            </div>
        </div>

        {{-- Detalle --}}
        <div class="pas-card mb-4">
            <div class="pas-card-head">
                <h3><span class="material-symbols-rounded">info</span> Información de la tarjeta</h3>
            </div>
            <div class="pas-card-body">
                <div class="saldo-detalle-row">
                    <div class="key"><span class="material-symbols-rounded">tag</span> ID de tarjeta</div>
                    <div class="value" style="font-family:monospace">{{ $tarjeta->id_tarjeta }}</div>
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
            <div class="pas-empty">
                <span class="material-symbols-rounded">add_card</span>
                <p>No tienes recargas registradas aún.</p>
            </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
