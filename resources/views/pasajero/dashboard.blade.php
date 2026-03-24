@extends('pasajero.layouts.app')
@section('title', 'Inicio')

@section('content')
<div class="container-fluid py-4 px-3 px-lg-4">

    {{-- ── HERO ── --}}
    <div class="dash-hero">
        <div class="dash-hero-bg" style="background-image: url('{{ asset('images/dashboard_hero_bg.png') }}')"></div>
        <div class="dash-hero-overlay"></div>
        <div class="dash-hero-content">
            <div class="dash-hero-badge">
                <span class="material-symbols-rounded" style="font-size:1rem">directions_transit</span>
                Sistema Integrado de Gestión Urbana
            </div>
            <h1 class="dash-hero-title">
                Bienvenido, <span>{{ $user->primer_nombre }}</span>
            </h1>
            <p class="dash-hero-sub">
                Con SIGU viajas de forma inteligente, segura y conectada.
                Explora rutas, gestiona tu tarjeta y descubre la ciudad.
            </p>
            <div class="dash-hero-btns">
                <a href="{{ route('pasajero.rutas.index') }}" class="dash-hero-btn dash-hero-btn-primary">
                    <span class="material-symbols-rounded">alt_route</span> Ver Rutas
                </a>
                <a href="{{ route('pasajero.mapa') }}" class="dash-hero-btn dash-hero-btn-outline">
                    <span class="material-symbols-rounded">map</span> Ver Mapa
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- ── Columna izquierda ── --}}
        <div class="col-lg-5 col-xl-4">

            @if($tieneTarjeta)
            {{-- Card estado de tarjeta + stats --}}
            <div class="dash-tarjeta-card">
                <div style="position:relative;z-index:1">
                    <div class="dash-tarjeta-label">Saldo disponible</div>
                    <div class="dash-tarjeta-saldo">$ {{ number_format($tarjeta->saldo ?? 0, 2, ',', '.') }}</div>
                    <div class="dash-tarjeta-cod">{{ $tarjeta->codigo_tarjeta }}</div>
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <a href="{{ route('pasajero.saldo') }}" class="dash-tarjeta-btn">
                            <span class="material-symbols-rounded" style="font-size:1rem">credit_card</span> Mi tarjeta
                        </a>
                        <a href="{{ route('pasajero.historial.index') }}" class="dash-tarjeta-btn">
                            <span class="material-symbols-rounded" style="font-size:1rem">history</span> Historial
                        </a>
                    </div>

                    {{-- Divisor --}}
                    <div style="border-top:1px solid rgba(255,255,255,.18);margin:1.2rem 0"></div>

                    {{-- Stats dentro de la card --}}
                    <div class="dash-card-stats">
                        <div class="dash-card-stat">
                            <span class="material-symbols-rounded dash-card-stat-icon">swap_vert</span>
                            <div class="dash-card-stat-num">{{ $totalViajes }}</div>
                            <div class="dash-card-stat-lbl">Viajes</div>
                        </div>
                        <div class="dash-card-stat">
                            <span class="material-symbols-rounded dash-card-stat-icon">add_card</span>
                            <div class="dash-card-stat-num">{{ $totalRecargas }}</div>
                            <div class="dash-card-stat-lbl">Recargas</div>
                        </div>
                        <div class="dash-card-stat">
                            <span class="material-symbols-rounded dash-card-stat-icon">payments</span>
                            <div class="dash-card-stat-num">$ {{ number_format($gastoMes, 0, ',', '.') }}</div>
                            <div class="dash-card-stat-lbl">Gasto del mes</div>
                        </div>
                    </div>
                </div>
            </div>

            @else
            {{-- Usuario sin tarjeta + stats --}}
            <div class="dash-no-card">
                <div class="dash-no-card-icon">
                    <span class="material-symbols-rounded">credit_card_off</span>
                </div>
                <h3 class="fw-bold mb-2" style="font-family:var(--ff-d)">Aún no tienes tarjeta</h3>
                <p style="color:rgba(255,255,255,.72);font-size:.9rem;margin-bottom:1.4rem">
                    Activa o vincula tu tarjeta SIGU para desbloquear pagos, historial de viajes y mucho más.
                </p>
                <a href="{{ route('pasajero.tarjeta.sin-tarjeta') }}" class="dash-hero-btn dash-hero-btn-primary">
                    <span class="material-symbols-rounded">add_card</span> Activar mi tarjeta
                </a>

                {{-- Divisor --}}
                <div style="border-top:1px solid rgba(255,255,255,.18);margin:1.2rem 0"></div>

                {{-- Stats de la red --}}
                <div class="dash-card-stats">
                    <div class="dash-card-stat">
                        <span class="material-symbols-rounded dash-card-stat-icon">alt_route</span>
                        <div class="dash-card-stat-num">{{ $rutasDisponibles }}</div>
                        <div class="dash-card-stat-lbl">Rutas</div>
                    </div>
                    <div class="dash-card-stat">
                        <span class="material-symbols-rounded dash-card-stat-icon">storefront</span>
                        <div class="dash-card-stat-num">500+</div>
                        <div class="dash-card-stat-lbl">Puntos de Recarga</div>
                    </div>
                    <div class="dash-card-stat">
                        <span class="material-symbols-rounded dash-card-stat-icon">group</span>
                        <div class="dash-card-stat-num">Gratis</div>
                        <div class="dash-card-stat-lbl">Explorar</div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- ── Columna derecha ── --}}
        <div class="col-lg-7 col-xl-8">

            {{-- Accesos rápidos --}}
            <p class="dash-features-title">
                <span class="material-symbols-rounded" style="color:var(--pas)">grid_view</span>
                Accesos rápidos
            </p>
            <div class="row g-3 mb-4">
                <div class="col-6 col-sm-3">
                    <a href="{{ route('pasajero.rutas.index') }}" class="dash-feature">
                        <div class="dash-feature-icon fi-purple">
                            <span class="material-symbols-rounded">alt_route</span>
                        </div>
                        <div class="dash-feature-title">Rutas</div>
                        <div class="dash-feature-desc">Consulta los recorridos disponibles</div>
                    </a>
                </div>
                <div class="col-6 col-sm-3">
                    <a href="{{ route('pasajero.recargas.index') }}" class="dash-feature">
                        <div class="dash-feature-icon fi-green">
                            <span class="material-symbols-rounded">store</span>
                        </div>
                        <div class="dash-feature-title">Recargas</div>
                        <div class="dash-feature-desc">Puntos autorizados de recarga</div>
                    </a>
                </div>
                <div class="col-6 col-sm-3">
                    <a href="{{ route('pasajero.mapa') }}" class="dash-feature">
                        <div class="dash-feature-icon fi-blue">
                            <span class="material-symbols-rounded">map</span>
                        </div>
                        <div class="dash-feature-title">Mapa</div>
                        <div class="dash-feature-desc">Paradas y rutas en el mapa</div>
                    </a>
                </div>
                <div class="col-6 col-sm-3">
                    <a href="{{ route('pasajero.tarjeta.index') }}" class="dash-feature">
                        <div class="dash-feature-icon fi-orange">
                            <span class="material-symbols-rounded">credit_card</span>
                        </div>
                        <div class="dash-feature-title">Mi tarjeta</div>
                        <div class="dash-feature-desc">Gestiona tu tarjeta SIGU</div>
                    </a>
                </div>
            </div>

            {{-- Beneficios de SIGU --}}
            <p class="dash-features-title">
                <span class="material-symbols-rounded" style="color:var(--pas)">star</span>
                ¿Qué te ofrece SIGU?
            </p>
            <div class="d-flex flex-column gap-3">
                <div class="dash-benefit">
                    <div class="dash-benefit-icon bi-purple">
                        <span class="material-symbols-rounded">account_balance_wallet</span>
                    </div>
                    <div>
                        <div class="dash-benefit-title">Protección de saldo</div>
                        <p class="dash-benefit-desc">Si pierdes tu tarjeta registrada, el saldo permanece seguro y puedes transferirlo a una nueva tarjeta fácilmente.</p>
                    </div>
                </div>
                <div class="dash-benefit">
                    <div class="dash-benefit-icon bi-green">
                        <span class="material-symbols-rounded">alt_route</span>
                    </div>
                    <div>
                        <div class="dash-benefit-title">Integración de rutas</div>
                        <p class="dash-benefit-desc">Explora todas las rutas de la ciudad, consulta paradas y planea tus viajes con facilidad desde cualquier dispositivo.</p>
                    </div>
                </div>
                <div class="dash-benefit">
                    <div class="dash-benefit-icon bi-orange">
                        <span class="material-symbols-rounded">storefront</span>
                    </div>
                    <div>
                        <div class="dash-benefit-title">Más de 500 puntos de red</div>
                        <p class="dash-benefit-desc">Recarga en droguerías, tiendas autorizadas y centros de servicio distribuidos por toda la ciudad.</p>
                    </div>
                </div>
                <div class="dash-benefit">
                    <div class="dash-benefit-icon bi-red">
                        <span class="material-symbols-rounded">speed</span>
                    </div>
                    <div>
                        <div class="dash-benefit-title">Pago ágil y sin filas</div>
                        <p class="dash-benefit-desc">Aborda el bus rápidamente con solo acercar tu tarjeta al lector. Sin efectivo, sin complicaciones, sin retrasos.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

    {{-- ── Footer ── --}}
    <footer class="dash-footer">
        <div class="dash-footer-inner">
            <div class="dash-footer-brand">
                <span class="material-symbols-rounded" style="font-variation-settings:'FILL' 1">directions_transit</span>
                <span class="fw-bold">SIGU</span>
            </div>
            <p class="dash-footer-copy">
                &copy; {{ date('Y') }} Sistema Integrado de Gestión Urbana.
                Todos los derechos reservados.
            </p>
            <div class="dash-footer-links">
                <a href="{{ route('pasajero.rutas.index') }}">Rutas</a>
                <a href="{{ route('pasajero.recargas.index') }}">Recargas</a>
                <a href="{{ route('pasajero.mapa') }}">Mapa</a>
                <a href="{{ route('pasajero.perfil.edit') }}">Mi perfil</a>
            </div>
        </div>
    </footer>
</div>
@endsection
