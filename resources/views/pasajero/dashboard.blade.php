@extends('pasajero.layouts.app')
@section('title', 'Inicio')

@section('content')
<div class="container-fluid py-4 px-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3">
        <span class="material-symbols-rounded">check_circle</span>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── Banner de bienvenida ─────────────────────────────── --}}
    <div class="pas-welcome">
        <div style="position:relative;z-index:1">
            <h2>¡Hola, {{ $user->primer_nombre }}!</h2>
            <p>Bienvenido a tu panel SIGU. Consulta tu saldo, rutas y movimientos.</p>
        </div>
        <span class="material-symbols-rounded"
              style="position:absolute;right:1.5rem;top:50%;transform:translateY(-50%);font-size:3.5rem;opacity:.18;font-variation-settings:var(--ms-on)">
            directions_bus
        </span>
    </div>

    {{-- ── Tarjeta virtual + stats ──────────────────────────── --}}
    <div class="pas-dash-row mb-4">

        {{-- Tarjeta virtual --}}
        <div>
            @if($tarjeta)
            <div class="tarjeta-virtual">
                <div class="tarjeta-virtual-top" style="position:relative;z-index:1">
                    <div>
                        <div class="tarjeta-label">Tarjeta SIGU</div>
                        <div class="tarjeta-id">{{ $tarjeta->id_tarjeta }}</div>
                    </div>
                    <div class="tarjeta-chip">
                        <span class="material-symbols-rounded" style="font-size:1rem">contactless</span>
                    </div>
                </div>
                <div style="position:relative;z-index:1">
                    <div class="tarjeta-label">Saldo disponible</div>
                    <div class="tarjeta-saldo">$ {{ number_format($tarjeta->saldo ?? 0, 2, ',', '.') }}</div>
                    <div class="tarjeta-nombre mt-2">
                        {{ $user->primer_nombre }} {{ $user->primer_apellido }}
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-2">
                <a href="{{ route('pasajero.saldo') }}" class="pas-btn pas-btn-outline flex-fill justify-content-center" style="font-size:.8rem">
                    <span class="material-symbols-rounded" style="font-size:.95rem">history</span> Ver movimientos
                </a>
            </div>
            @else
            <div class="pas-card h-100 d-flex align-items-center justify-content-center" style="min-height:180px">
                <div class="text-center">
                    <span class="material-symbols-rounded" style="font-size:2.5rem;color:var(--text-3);display:block;margin-bottom:.5rem">credit_card_off</span>
                    <p style="color:var(--text-2);font-size:.875rem;margin:0">Sin tarjeta activa</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Stats --}}
        <div class="pas-stats" style="margin-bottom:0;align-content:start">
            <div class="pas-stat">
                <div class="pas-stat-icon blue">
                    <span class="material-symbols-rounded">directions_bus</span>
                </div>
                <div>
                    <div class="pas-stat-val">{{ $totalViajes }}</div>
                    <div class="pas-stat-lbl">Viajes realizados</div>
                </div>
            </div>
            <div class="pas-stat">
                <div class="pas-stat-icon green">
                    <span class="material-symbols-rounded">payments</span>
                </div>
                <div>
                    <div class="pas-stat-val">{{ $totalRecargas }}</div>
                    <div class="pas-stat-lbl">Recargas hechas</div>
                </div>
            </div>
            <div class="pas-stat">
                <div class="pas-stat-icon warn">
                    <span class="material-symbols-rounded">monetization_on</span>
                </div>
                <div>
                    <div class="pas-stat-val">$ {{ number_format($gastoMes, 0, ',', '.') }}</div>
                    <div class="pas-stat-lbl">Gasto este mes</div>
                </div>
            </div>
            <div class="pas-stat">
                <div class="pas-stat-icon blue">
                    <span class="material-symbols-rounded">alt_route</span>
                </div>
                <div>
                    <div class="pas-stat-val">{{ $rutasDisponibles }}</div>
                    <div class="pas-stat-lbl">Rutas en tu ciudad</div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Últimos movimientos + accesos rápidos ────────────── --}}
    <div class="pas-dash-row">

        {{-- Últimos movimientos --}}
        <div class="pas-card">
            <div class="pas-card-head">
                <h3><span class="material-symbols-rounded">receipt_long</span> Últimos movimientos</h3>
                <a href="{{ route('pasajero.historial.index') }}"
                   style="font-size:.78rem;color:var(--pas);font-weight:600;text-decoration:none;display:flex;align-items:center;gap:.2rem">
                    Ver todo <span class="material-symbols-rounded" style="font-size:.9rem">chevron_right</span>
                </a>
            </div>
            @forelse($movimientos as $mov)
            <div class="pas-hist-item">
                <div class="pas-hist-item-icon {{ $mov['tipo'] }}">
                    <span class="material-symbols-rounded">
                        {{ $mov['tipo'] === 'recarga' ? 'add_card' : 'directions_bus' }}
                    </span>
                </div>
                <div class="flex-grow-1">
                    <div class="pas-hist-item-desc">{{ $mov['desc'] }}</div>
                    <div class="pas-hist-item-meta">
                        {{ \Carbon\Carbon::parse($mov['fecha'])->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div class="pas-hist-item-monto {{ $mov['tipo'] === 'recarga' ? 'positivo' : 'negativo' }}">
                    {{ $mov['tipo'] === 'recarga' ? '+' : '-' }}
                    $ {{ number_format($mov['monto'], 0, ',', '.') }}
                </div>
            </div>
            @empty
            <div class="pas-empty">
                <span class="material-symbols-rounded">receipt_long</span>
                <p>Aún no tienes movimientos registrados.</p>
            </div>
            @endforelse
        </div>

        {{-- Accesos rápidos --}}
        <div>
            <div class="pas-card mb-3">
                <div class="pas-card-head">
                    <h3><span class="material-symbols-rounded">apps</span> Accesos rápidos</h3>
                </div>
                <div class="pas-card-body">
                    <div class="pas-accesos">
                        <a href="{{ route('pasajero.rutas.index') }}" class="pas-acceso-item">
                            <span class="material-symbols-rounded">alt_route</span>
                            <span class="lbl">Rutas</span>
                        </a>
                        <a href="{{ route('pasajero.saldo') }}" class="pas-acceso-item">
                            <span class="material-symbols-rounded">credit_card</span>
                            <span class="lbl">Mi tarjeta</span>
                        </a>
                        <a href="{{ route('pasajero.recargas.index') }}" class="pas-acceso-item">
                            <span class="material-symbols-rounded">store</span>
                            <span class="lbl">Recargas</span>
                        </a>
                        <a href="{{ route('pasajero.mapa') }}" class="pas-acceso-item">
                            <span class="material-symbols-rounded">map</span>
                            <span class="lbl">Mapa</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Info tarjeta --}}
            @if($tarjeta)
            <div class="pas-alert info" style="margin-bottom:0">
                <span class="material-symbols-rounded" style="font-size:1rem;flex-shrink:0">info</span>
                <div>
                    Tu tarjeta <strong>{{ $tarjeta->id_tarjeta }}</strong> está activa desde
                    {{ \Carbon\Carbon::parse($titularidad->fecha_inicio)->format('d/m/Y') }}.
                </div>
            </div>
            @endif
        </div>

    </div>

</div>
@endsection
