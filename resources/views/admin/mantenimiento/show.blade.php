@extends('admin.layouts.app')

@section('title', 'Detalle Mantenimiento — SIGU')

@push('styles')
<style>
/* ─── Estilos de impresión ─────────────────────────────── */
@media print {
    /* Ocultar todo el chrome de la app */
    .sigu-sidebar,
    .sigu-topbar,
    .sigu-page-hd .d-print-none,
    .d-print-none,
    nav, aside { display: none !important; }

    /* Quitar márgenes del layout */
    body, .sigu-main, .sigu-content, .sigu-fade {
        margin: 0 !important;
        padding: 0 !important;
        background: white !important;
    }

    /* Que la orden ocupe toda la hoja */
    .print-order {
        display: block !important;
        width: 100%;
        padding: 0 !important;
    }

    /* Ocultar la tarjeta de pantalla normal */
    .screen-only { display: none !important; }

    /* Ajustes de fuente para impresión */
    * { font-family: 'Arial', sans-serif; font-size: 11pt; color: #000 !important; }
    .text-muted { color: #555 !important; }
    .badge { border: 1px solid #888 !important; background: none !important; padding: 2px 6px; }

    /* Evitar saltos de página en las tareas */
    .print-task-row { break-inside: avoid; }

    /* Firma */
    .print-sign-row { margin-top: 60px; }
    .print-sign-line { border-top: 1px solid #000; width: 200px; text-align: center; padding-top: 4px; font-size: 9pt; }
}

/* ─── Vista de pantalla normal: ocultar bloque de impresión ── */
@media screen {
    .print-order { display: none; }
}
</style>
@endpush

@section('content')
<div class="sigu-fade">
    {{-- ─── Encabezado (sólo pantalla) ─── --}}
    <div class="sigu-page-hd d-flex justify-content-between">
        <div>
            <a href="{{ route('admin.mantenimiento.index') }}" class="text-muted small" style="text-decoration:none;">← Historial</a>
            <h1 class="sigu-page-title mt-1">Detalle de Mantenimiento</h1>
            <p class="sigu-page-sub">Orden #{{ str_pad($mantenimiento->id_mantenimiento, 5, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div class="d-print-none d-flex gap-2 align-items-center pt-2">

            {{-- Imprimir: contorno del color primario --}}
            <button onclick="window.print()" class="btn btn-sm d-flex align-items-center gap-1"
                    style="border:1.5px solid var(--p); color:var(--p); border-radius:0.5rem; padding:0.35rem 0.9rem; background:transparent;">
                <span class="material-symbols-rounded" style="font-size:1rem;">print</span>
                Imprimir
            </button>

            @if($mantenimiento->bus)
            {{-- Historial: neutro --}}
            <a href="{{ route('admin.buses.historial', $mantenimiento->placa) }}"
               class="btn btn-sm d-flex align-items-center gap-1"
               style="border:1.5px solid #cbd5e0; color:#4a5568; border-radius:0.5rem; padding:0.35rem 0.9rem; background:transparent; text-decoration:none;">
                <span class="material-symbols-rounded" style="font-size:1rem;">history</span>
                Historial del bus
            </a>
            @endif

            @if((int)$mantenimiento->id_estado === 4)
            {{-- Finalizar: acción principal, verde relleno --}}
            <form id="formFinalizar" action="{{ route('admin.mantenimiento.finalizar', $mantenimiento->id_mantenimiento) }}" method="POST">
                @csrf
                <button type="button" class="btn btn-sm d-flex align-items-center gap-1"
                        style="background:#38a169; color:#fff; border:none; border-radius:0.5rem; padding:0.35rem 1rem;"
                        data-confirm-form="formFinalizar"
                        data-confirm-title="Finalizar y liberar bus"
                        data-confirm-msg="El bus ser&aacute; marcado como disponible nuevamente.">
                    <span class="material-symbols-rounded" style="font-size:1rem;">check_circle</span>
                    Finalizar y Liberar Bus
                </button>
            </form>
            @endif

        </div>

    </div>

    {{-- ─── Tarjeta pantalla ─── --}}
    <div class="row mt-4 screen-only">
        <div class="col-md-4">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h6 class="text-uppercase small fw-bold text-muted mb-3">Bus</h6>
                <h3 class="mb-0">{{ $mantenimiento->placa }}</h3>
                <p class="text-muted mb-3">{{ $mantenimiento->bus->modelo ?? '—' }}</p>
                <span class="badge @if((int)$mantenimiento->bus?->id_estado === 4) bg-warning text-dark @else bg-success @endif">
                    {{ $mantenimiento->bus?->estado?->nombre_estado ?? 'Sin estado' }}
                </span>
                <hr class="my-3">
                <div class="mb-3">
                    <label class="small text-muted d-block">Fecha de ingreso</label>
                    <span>{{ \Carbon\Carbon::parse($mantenimiento->fecha_mantenimiento)->format('d/m/Y') }}</span>
                </div>
                <div class="mb-3">
                    <label class="small text-muted d-block">Kilometraje al ingresar</label>
                    <span>{{ number_format($mantenimiento->kilometraje) }} KM</span>
                </div>
                <div class="mb-3">
                    <label class="small text-muted d-block">Costo total</label>
                    <span class="fs-4 fw-bold text-success">${{ number_format($mantenimiento->costo_total, 0, ',', '.') }}</span>
                </div>
                <div>
                    <label class="small text-muted d-block">Estado del registro</label>
                    @if((int)$mantenimiento->id_estado === 4)
                        <span class="badge bg-warning text-dark">En Taller</span>
                    @else
                        <span class="badge bg-success">Finalizado</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h6 class="text-uppercase small fw-bold text-muted mb-3">Tareas realizadas</h6>
                    @forelse($mantenimiento->detalles as $detalle)
                        <div class="list-group-item px-0 py-3">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 fw-bold">{{ $detalle->tipoMantenimiento->nombre ?? 'General' }}</h6>
                            </div>
                            <p class="mb-1 text-muted">{{ $detalle->descripcion }}</p>
                            @if($detalle->evidencia_foto)
                                <div class="mt-2 text-start d-print-none">
                                    <a href="{{ asset('storage/' . $detalle->evidencia_foto) }}" target="_blank" class="d-inline-flex align-items-center gap-1 text-primary small text-decoration-none">
                                        <span class="material-symbols-rounded" style="font-size:1.1rem;">image</span>
                                        Ver Evidencia
                                    </a>
                                </div>
                            @endif
                        </div>
                    @empty
                    <p class="text-center py-4 text-muted">Sin tareas registradas.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         ORDEN DE TRABAJO — solo visible al imprimir
    ════════════════════════════════════════════════════ --}}
    <div class="print-order">

        {{-- Encabezado de la orden --}}
        <table style="width:100%; border-bottom:2px solid #000; padding-bottom:12px; margin-bottom:16px;">
            <tr>
                <td style="width:60%;">
                    <p style="margin:0; font-size:18pt; font-weight:bold;">SIGU</p>
                    <p style="margin:0; font-size:9pt; color:#555;">Sistema de Gestión de Unidades</p>
                </td>
                <td style="text-align:right; vertical-align:bottom;">
                    <p style="margin:0; font-size:14pt; font-weight:bold;">ORDEN DE TRABAJO</p>
                    <p style="margin:0; font-size:10pt;">#{{ str_pad($mantenimiento->id_mantenimiento, 5, '0', STR_PAD_LEFT) }}</p>
                </td>
            </tr>
        </table>

        {{-- Datos principales --}}
        <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
            <tr>
                <td style="width:25%; padding:6px 0; font-weight:bold; color:#555; font-size:9pt;">VEHÍCULO (PLACA)</td>
                <td style="width:25%; padding:6px 8px; font-size:12pt; font-weight:bold;">{{ $mantenimiento->placa }}</td>
                <td style="width:25%; padding:6px 0; font-weight:bold; color:#555; font-size:9pt;">FECHA DE INGRESO</td>
                <td style="width:25%; padding:6px 8px;">{{ \Carbon\Carbon::parse($mantenimiento->fecha_mantenimiento)->format('d/m/Y') }}</td>
            </tr>
            <tr style="background:#f5f5f5;">
                <td style="padding:6px 0; font-weight:bold; color:#555; font-size:9pt;">MODELO</td>
                <td style="padding:6px 8px;">{{ $mantenimiento->bus->modelo ?? '—' }}</td>
                <td style="padding:6px 0; font-weight:bold; color:#555; font-size:9pt;">KILOMETRAJE</td>
                <td style="padding:6px 8px;">{{ number_format($mantenimiento->kilometraje) }} KM</td>
            </tr>
            <tr>
                <td style="padding:6px 0; font-weight:bold; color:#555; font-size:9pt;">ESTADO</td>
                <td style="padding:6px 8px;">
                    @if((int)$mantenimiento->id_estado === 4) En Taller @else Finalizado @endif
                </td>
                <td style="padding:6px 0; font-weight:bold; color:#555; font-size:9pt;">COSTO TOTAL</td>
                <td style="padding:6px 8px; font-weight:bold; font-size:12pt;">${{ number_format($mantenimiento->costo_total, 0, ',', '.') }}</td>
            </tr>
        </table>

        {{-- Tabla de tareas --}}
        <p style="font-weight:bold; text-transform:uppercase; font-size:9pt; color:#555; margin-bottom:8px; border-bottom:1px solid #ccc; padding-bottom:4px;">
            Tareas realizadas
        </p>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#e8e8e8;">
                    <th style="padding:6px 8px; text-align:left; border:1px solid #ccc; font-size:9pt;">#</th>
                    <th style="padding:6px 8px; text-align:left; border:1px solid #ccc; font-size:9pt;">Tipo</th>
                    <th style="padding:6px 8px; text-align:left; border:1px solid #ccc; font-size:9pt;">Descripción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mantenimiento->detalles as $i => $detalle)
                    <tr class="print-task-row" style="{{ $i % 2 === 0 ? 'background:#fafafa;' : '' }}">
                        <td style="padding:6px 8px; border:1px solid #ccc;">{{ $i + 1 }}</td>
                        <td style="padding:6px 8px; border:1px solid #ccc; font-weight:bold;">{{ $detalle->tipoMantenimiento->nombre ?? 'General' }}</td>
                        <td style="padding:6px 8px; border:1px solid #ccc;">{{ $detalle->descripcion }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="padding:10px; text-align:center; color:#777;">Sin tareas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Firmas --}}
        <div class="print-sign-row" style="display:flex; justify-content:space-between; margin-top:64px;">
            <div style="text-align:center;">
                <div class="print-sign-line">Administrador de Empresa</div>
            </div>
            <div style="text-align:center;">
                <div class="print-sign-line">Responsable del Taller</div>
            </div>
            <div style="text-align:center;">
                <div class="print-sign-line">Conductor / Propietario</div>
            </div>
        </div>

        {{-- Pie de página --}}
        <p style="text-align:center; margin-top:40px; font-size:8pt; color:#888; border-top:1px solid #ddd; padding-top:8px;">
            Generado por SIGU · {{ now()->format('d/m/Y H:i') }} · Documento de uso interno
        </p>
    </div>
</div>
@endsection
