@extends('controlador-tiempo.layouts.app')

@section('title', 'Panel — Controlador de Tiempo')

@section('content')
<div class="sigu-fade">

    {{-- Saludo --}}
    <div class="sigu-page-hd">
        <h1 class="sigu-page-title">Panel de Control</h1>
        <p class="sigu-page-sub">Bienvenido, {{ Auth::user()->primer_nombre }}. Aquí tienes el resumen operativo de la flota.</p>
    </div>

    {{-- ─── KPIs ─────────────────────────────────────────────────── --}}
    <div class="row g-3 mt-2">

        <div class="col-6 col-md-3">
            <div class="bg-white rounded-3 shadow-sm p-4 h-100 d-flex flex-column ct-kpi">
                <span class="material-symbols-rounded mb-2 ct-kpi-icon" style="font-size:1.8rem;">directions_bus</span>
                <span class="fs-1 fw-bold lh-1">{{ $totalBuses }}</span>
                <span class="text-muted small mt-1">Buses en flota</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="bg-white rounded-3 shadow-sm p-4 h-100 d-flex flex-column ct-kpi">
                <span class="material-symbols-rounded mb-2 ct-kpi-icon" style="font-size:1.8rem;">radar</span>
                <span class="fs-1 fw-bold lh-1">{{ $busesEnRuta }}</span>
                <span class="text-muted small mt-1">Buses activos</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="bg-white rounded-3 shadow-sm p-4 h-100 d-flex flex-column ct-kpi">
                <span class="material-symbols-rounded mb-2 ct-kpi-icon" style="font-size:1.8rem;">alt_route</span>
                <span class="fs-1 fw-bold lh-1">{{ $rutasActivas }}</span>
                <span class="text-muted small mt-1">Rutas operativas</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="bg-white rounded-3 shadow-sm p-4 h-100 d-flex flex-column ct-kpi">
                <span class="material-symbols-rounded mb-2" style="font-size:1.8rem; color:#e53e3e;">warning</span>
                <span class="fs-1 fw-bold lh-1">{{ $busesInactivos }}</span>
                <span class="text-muted small mt-1">Buses inactivos / taller</span>
            </div>
        </div>

    </div>

    {{-- ─── Estado de Frecuencias ────────────────────────────────── --}}
    <div class="row g-3 mt-2">
        <div class="col-12">
            <div class="bg-white rounded-3 shadow-sm p-4">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <span class="material-symbols-rounded text-primary">timer</span>
                    <h6 class="fw-bold mb-0">Monitor de Frecuencias (Hoy)</h6>
                </div>
                
                <div class="row g-3">
                    @forelse($rutasDetalle as $ruta)
                        @php
                            $min = $ruta->minutos_desde_salida;
                            $status = 'text-success';
                            $bg = 'bg-success';
                            $msg = 'Frecuencia óptima';
                            
                            if($min === null) {
                                $status = 'text-muted'; $bg = 'bg-secondary'; $msg = 'Sin despachos hoy';
                            } elseif($min > 15) {
                                $status = 'text-danger'; $bg = 'bg-danger'; $msg = 'Retraso crítico (>15 min)';
                            } elseif($min > 10) {
                                $status = 'text-warning'; $bg = 'bg-warning text-dark'; $msg = 'Alerta de intervalo (>10 min)';
                            }
                        @endphp
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 border rounded-3 h-100 shadow-none border-light-subtle bg-light bg-opacity-10">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="small fw-extrabold text-uppercase tracking-wider text-muted">{{ $ruta->nombre_ruta ?? ('Ruta #'.$ruta->id_ruta) }}</div>
                                    <span class="badge {{ $bg }} rounded-pill ps-2 pe-3 py-1 fw-bold" style="font-size: 0.65rem;">
                                        <i class="opacity-75 me-1">•</i> {{ $msg }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-baseline gap-2 mt-1">
                                    <span class="fs-2 fw-bold {{ $status }}">{{ is_numeric($min) ? round($min) : '—' }}</span>
                                    <span class="text-dark fw-medium" style="opacity: 0.6;">minutos</span>
                                </div>
                                <div class="mt-2 pt-2 border-top border-light d-flex justify-content-between align-items-center">
                                    <div class="small text-muted">
                                        {{ $ruta->barrioOrigen->nombre ?? '?' }} ↔ {{ $ruta->barrioDestino->nombre ?? '?' }}
                                    </div>
                                    @if($ruta->ultimo_bus)
                                        <div class="badge bg-light text-dark border fw-bold" style="font-size: 0.7rem;">🚌 {{ $ruta->ultimo_bus }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-4">
                            <p class="text-muted">No hay rutas configuradas para esta empresa.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Accesos rápidos ──────────────────────────────────────── --}}
    <div class="row g-3 mt-2">
        <div class="col-md-4">
            <a href="{{ route('controlador-tiempo.despacho.index') }}" class="text-decoration-none">
                <div class="bg-white rounded-3 shadow-sm p-4 h-100 d-flex align-items-center gap-3 border border-transparent" style="transition:.2s; cursor:pointer;" onmouseover="this.style.borderColor='var(--ct-accent)'" onmouseout="this.style.borderColor='transparent'">
                    <span class="material-symbols-rounded fs-2" style="color:var(--ct-accent);">directions_bus</span>
                    <div>
                        <div class="fw-bold text-dark">Módulo de Despacho</div>
                        <div class="text-muted small">Turnos, intervalos y relevos</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('controlador-tiempo.monitoreo.index') }}" class="text-decoration-none">
                <div class="bg-white rounded-3 shadow-sm p-4 h-100 d-flex align-items-center gap-3 border border-transparent" style="transition:.2s; cursor:pointer;" onmouseover="this.style.borderColor='var(--ct-accent)'" onmouseout="this.style.borderColor='transparent'">
                    <span class="material-symbols-rounded fs-2" style="color:var(--ct-accent);">radar</span>
                    <div>
                        <div class="fw-bold text-dark">Monitoreo en Vivo</div>
                        <div class="text-muted small">Estado y ubicación de buses</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('controlador-tiempo.planillas.index') }}" class="text-decoration-none">
                <div class="bg-white rounded-3 shadow-sm p-4 h-100 d-flex align-items-center gap-3 border border-transparent" style="transition:.2s; cursor:pointer;" onmouseover="this.style.borderColor='var(--ct-accent)'" onmouseout="this.style.borderColor='transparent'">
                    <span class="material-symbols-rounded fs-2" style="color:var(--ct-accent);">assignment</span>
                    <div>
                        <div class="fw-bold text-dark">Planillas de Despacho</div>
                        <div class="text-muted small">Documentación y novedades</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- ─── Últimas asignaciones ─────────────────────────────────── --}}
    <div class="row g-3 mt-2">
        <div class="col-12">
            <div class="bg-white rounded-3 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Asignaciones Recientes</h6>
                    <a href="{{ route('controlador-tiempo.despacho.index') }}" class="small" style="color:var(--ct-accent); text-decoration:none;">Ver todas →</a>
                </div>

                @forelse($asignaciones as $asig)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <span class="material-symbols-rounded text-muted" style="font-size:1.2rem;">directions_bus</span>
                            <div>
                                <span class="fw-semibold">{{ $asig->placa ?? $asig->bus->placa ?? '—' }}</span>
                                <span class="text-muted small ms-2">
                                    {{ $asig->ruta->barrioOrigen->nombre ?? '?' }} → {{ $asig->ruta->barrioDestino->nombre ?? '?' }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <span class="badge-ct">Activa</span>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small text-center py-3">No hay asignaciones registradas.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
