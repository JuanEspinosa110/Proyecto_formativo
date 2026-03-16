@extends('jefemantenimiento.layouts.app')

@section('title', 'Inicio — Jefe de Mantenimiento')

@section('content')
<div class="sigu-fade">

    {{-- Saludo --}}
    <div class="sigu-page-hd">
        <h1 class="sigu-page-title">Panel de Mantenimiento</h1>
        <p class="sigu-page-sub">Bienvenido, {{ Auth::user()->primer_nombre }}. Aquí tienes el resumen del taller.</p>
    </div>

    {{-- ─── Tarjetas de métricas ─── --}}
    <div class="row g-3 mt-2">

        <div class="col-6 col-md-3">
            <div class="bg-white rounded-lg shadow-sm p-4 h-100 d-flex flex-column">
                <span class="material-symbols-rounded mb-2" style="font-size:1.8rem; color:#f6820c;">directions_bus</span>
                <span class="fs-1 fw-bold lh-1">{{ $busesEnTaller }}</span>
                <span class="text-muted small mt-1">Buses en taller</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="bg-white rounded-lg shadow-sm p-4 h-100 d-flex flex-column">
                <span class="material-symbols-rounded mb-2" style="font-size:1.8rem; color:#e53e3e;">notification_important</span>
                <span class="fs-1 fw-bold lh-1">{{ $reportesPendientes }}</span>
                <span class="text-muted small mt-1">Reportes pendientes</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="bg-white rounded-lg shadow-sm p-4 h-100 d-flex flex-column">
                <span class="material-symbols-rounded mb-2" style="font-size:1.8rem; color:#3182ce;">engineering</span>
                <span class="fs-1 fw-bold lh-1">{{ $trabajosEnCurso }}</span>
                <span class="text-muted small mt-1">Trabajos en curso</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="bg-white rounded-lg shadow-sm p-4 h-100 d-flex flex-column">
                <span class="material-symbols-rounded mb-2" style="font-size:1.8rem; color:#38a169;">check_circle</span>
                <span class="fs-1 fw-bold lh-1">{{ $trabajosFinalizados }}</span>
                <span class="text-muted small mt-1">Trabajos finalizados</span>
            </div>
        </div>

    </div>

    {{-- ─── Alertas de Mantenimiento Preventivo ─── --}}
    @if(count($alertasMantenimiento) > 0)
    <div class="row g-3 mt-1 mb-2">
        <div class="col-12">
            <div class="bg-white rounded-lg shadow-sm p-4 border-start border-4 border-warning">
                <div class="d-flex align-items-center mb-3">
                    <span class="material-symbols-rounded text-warning me-2" style="font-size:1.5rem;">warning</span>
                    <h6 class="fw-bold mb-0 text-dark">Alertas de Mantenimiento Preventivo (Próximos o Vencidos)</h6>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th>Bus</th>
                                <th>Fecha Requerida</th>
                                <th>KMs Requeridos</th>
                                <th>KM Actual</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alertasMantenimiento as $alerta)
                                <tr>
                                    <td class="fw-bold">{{ $alerta->placa }}</td>
                                    <td>
                                        @if($alerta->fecha_proximo)
                                            @php $vencidoFecha = strtotime($alerta->fecha_proximo) < strtotime(date('Y-m-d')); @endphp
                                            <span class="{{ $vencidoFecha ? 'text-danger fw-bold' : 'text-warning text-dark' }}">
                                                {{ date('d/m/Y', strtotime($alerta->fecha_proximo)) }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($alerta->km_proximo)
                                            @php $vencidoKm = $alerta->km_proximo <= $alerta->km_actual; @endphp
                                            <span class="{{ $vencidoKm ? 'text-danger fw-bold' : 'text-warning text-dark' }}">
                                                {{ number_format($alerta->km_proximo, 0, ',', '.') }} km
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($alerta->km_actual, 0, ',', '.') }} km</td>
                                    <td>
                                        @if(
                                            ($alerta->fecha_proximo && strtotime($alerta->fecha_proximo) < strtotime(date('Y-m-d'))) || 
                                            ($alerta->km_proximo && $alerta->km_proximo <= $alerta->km_actual)
                                        )
                                            <span class="badge bg-danger rounded-pill">Vencido</span>
                                        @else
                                            <span class="badge bg-warning text-dark rounded-pill">Próximo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('jefemantenimiento.create', ['placa' => $alerta->placa]) }}" class="btn btn-sm" style="background:var(--p); color:white; border-radius:0.5rem; padding:0.2rem 0.6rem; font-size:0.8rem;">
                                            Programar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ─── Dos columnas: en curso + reportes recientes ─── --}}
    <div class="row g-3 mt-1">

        {{-- Buses en taller actualmente --}}
        <div class="col-md-6">
            <div class="bg-white rounded-lg shadow-sm p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Trabajos en curso</h6>
                    <a href="{{ route('jefemantenimiento.index') }}" class="small" style="color:var(--p); text-decoration:none;">Ver todos →</a>
                </div>

                @forelse($enCurso as $mant)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <span class="fw-semibold">{{ $mant->placa }}</span>
                            <span class="text-muted small ms-2">{{ $mant->bus->modelo ?? '' }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning text-dark">En Taller</span>
                            <a href="{{ route('jefemantenimiento.show', $mant->id_mantenimiento) }}"
                               class="btn btn-sm" style="border:1px solid var(--p); color:var(--p); border-radius:6px; padding:2px 8px; font-size:0.78rem; text-decoration:none;">
                                Ver
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small text-center py-3">No hay trabajos en curso.</p>
                @endforelse
            </div>
        </div>

        {{-- Reportes recientes --}}
        <div class="col-md-6">
            <div class="bg-white rounded-lg shadow-sm p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Reportes recientes</h6>
                    <a href="{{ route('jefemantenimiento.reportes') }}" class="small" style="color:var(--p); text-decoration:none;">Ver todos →</a>
                </div>

                @forelse($reportesRecientes as $rep)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <span class="fw-semibold">{{ $rep->placa }}</span>
                            <span class="text-muted small ms-2">{{ Str::limit($rep->descripcion, 35) }}</span>
                        </div>
                        <div>
                            @php $urg = strtoupper($rep->urgencia ?? 'BAJA'); @endphp
                            @if($urg === 'ALTA' || $urg === 'CRITICA')
                                <span class="badge bg-danger">{{ $urg }}</span>
                            @elseif($urg === 'MEDIA')
                                <span class="badge bg-warning text-dark">{{ $urg }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $urg }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted small text-center py-3">No hay reportes recientes.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
