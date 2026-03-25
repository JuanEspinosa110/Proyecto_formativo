@extends('jefemantenimiento.layouts.app')

@section('title', 'Detalle de Mantenimiento — SIGU')

@push('styles')
<style>
@media print {
    .sigu-sidebar,
    .sigu-topbar,
    .d-print-none,
    nav, aside, footer { display: none !important; }

    body, .sigu-main, .sigu-content, .sigu-fade {
        margin: 0 !important;
        padding: 0 !important;
        background: white !important;
    }

    .print-order { display: block !important; width: 100%; }
    .screen-only { display: none !important; }

    * { font-family: Arial, sans-serif; font-size: 11pt; color: #000 !important; }
    .text-muted { color: #555 !important; }
    .badge { border: 1px solid #888 !important; background: none !important; padding: 2px 6px; }
    .print-task-row { break-inside: avoid; }
}

@media screen {
    .print-order { display: none; }
}
</style>
@endpush

@section('content')
<div class="sigu-fade">

    {{-- ─── Encabezado (pantalla) ─── --}}
    <div class="sigu-page-hd d-flex justify-content-between">
        <div>
            <a href="{{ route('jefemantenimiento.index') }}" class="text-muted small" style="text-decoration:none;">← Historial</a>
            <h1 class="sigu-page-title mt-1">Detalle de Mantenimiento</h1>
            <p class="sigu-page-sub">Orden #{{ str_pad($mantenimiento->id_mantenimiento, 5, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div class="d-print-none d-flex gap-2 align-items-center pt-2">

            {{-- Imprimir: contorno naranja --}}
            <button onclick="window.print()" class="btn btn-sm d-flex align-items-center gap-1"
                    style="border:1.5px solid var(--jm-accent,#f6820c); color:var(--jm-accent,#f6820c); border-radius:0.5rem; padding:0.35rem 0.9rem; background:transparent;">
                <span class="material-symbols-rounded" style="font-size:1rem;">print</span>
                Imprimir
            </button>

            {{-- Volver: neutro --}}
            <a href="{{ route('jefemantenimiento.index') }}"
               class="btn btn-sm d-flex align-items-center gap-1"
               style="border:1.5px solid #cbd5e0; color:#4a5568; border-radius:0.5rem; padding:0.35rem 0.9rem; background:transparent; text-decoration:none;">
                <span class="material-symbols-rounded" style="font-size:1rem;">arrow_back</span>
                Volver
            </a>

            @if((int)$mantenimiento->id_estado === 7)
            {{-- Aprobar Salida: relleno naranja (acción principal) --}}
            <form id="formAprobar" action="{{ route('jefemantenimiento.aprobar-salida', $mantenimiento->id_mantenimiento) }}" method="POST">
                @csrf
                <button type="button" class="btn btn-sm d-flex align-items-center gap-1"
                        style="background:var(--jm-accent,#f6820c); color:#fff; border:none; border-radius:0.5rem; padding:0.35rem 1rem;"
                        data-confirm-form="formAprobar"
                        data-confirm-title="Aprobar salida del taller"
                        data-confirm-msg="El bus ser&aacute; marcado como disponible y podr&aacute; asignarse a rutas.">
                    <span class="material-symbols-rounded" style="font-size:1rem;">verified</span>
                    Aprobar Salida
                </button>
            </form>
            @endif

        </div>

    </div>

    {{-- ─── Tarjeta pantalla ─── --}}
    <div class="row mt-4 screen-only">
        <div class="col-md-4">
            <div class="bg-white rounded-lg shadow-sm p-4 h-100">
                <h5 class="text-uppercase small fw-bold text-muted mb-3">Información del Bus</h5>
                <h3 class="mb-1">{{ $mantenimiento->placa }}</h3>
                <p class="text-muted mb-4">{{ $mantenimiento->bus->modelo ?? 'Modelo N/A' }}</p>
                <hr>
                <div class="mb-3 mt-4">
                    <label class="small text-muted d-block">Fecha del Servicio</label>
                    <span>{{ \Carbon\Carbon::parse($mantenimiento->fecha_mantenimiento)->format('d/m/Y') }}</span>
                </div>
                <div class="mb-3">
                    <label class="small text-muted d-block">Kilometraje al Ingresar</label>
                    <span>{{ number_format($mantenimiento->kilometraje) }} KM</span>
                </div>
                <div class="mb-3">
                    <label class="small text-muted d-block">Costo Total</label>
                    <span class="fs-4 fw-bold text-success">${{ number_format($mantenimiento->costo_total, 0, ',', '.') }}</span>
                </div>
                <div>
                    <label class="small text-muted d-block">Estado</label>
                    <label class="small text-muted d-block">Estado</label>
                    @if((int)$mantenimiento->id_estado === 4)
                        <span class="badge bg-warning text-dark">En Taller</span>
                    @elseif((int)$mantenimiento->id_estado === 7)
                        <span class="badge bg-success">Finalizado</span>
                    @else
                        <span class="badge bg-secondary">{{ $mantenimiento->estado->nombre_estado ?? 'Desconocido' }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="bg-white rounded-lg shadow-sm p-4 h-100">
                <h5 class="text-uppercase small fw-bold text-muted mb-3">Tareas Realizadas</h5>
                <div class="list-group list-group-flush">
                    @forelse($mantenimiento->detalles as $detalle)
                        <div class="list-group-item px-0 py-3">
                            <h6 class="mb-1 fw-bold">{{ $detalle->tipoMantenimiento->nombre ?? 'General' }}</h6>
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
                        <p class="text-center py-4 text-muted">No hay tareas especificadas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         ORDEN DE TRABAJO — solo visible al imprimir
    ════════════════════════════════════════════════════ --}}
    <div class="print-order">

        {{-- Encabezado --}}
        <table style="width:100%; border-bottom:2px solid #000; padding-bottom:12px; margin-bottom:16px;">
            <tr>
                <td style="width:60%;">
                    <p style="margin:0; font-size:18pt; font-weight:bold;">SIGU</p>
                    <p style="margin:0; font-size:9pt; color:#555;">Sistema de Gestión de Unidades — Taller</p>
                </td>
                <td style="text-align:right; vertical-align:bottom;">
                    <p style="margin:0; font-size:14pt; font-weight:bold;">ORDEN DE TRABAJO</p>
                    <p style="margin:0; font-size:10pt;">#{{ str_pad($mantenimiento->id_mantenimiento, 5, '0', STR_PAD_LEFT) }}</p>
                </td>
            </tr>
        </table>

        {{-- Datos --}}
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
                <td style="padding:6px 8px;">@if((int)$mantenimiento->id_estado === 4) En Taller @else Finalizado @endif</td>
                <td style="padding:6px 0; font-weight:bold; color:#555; font-size:9pt;">COSTO TOTAL</td>
                <td style="padding:6px 8px; font-weight:bold; font-size:12pt;">${{ number_format($mantenimiento->costo_total, 0, ',', '.') }}</td>
            </tr>
        </table>

        {{-- Tareas --}}
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
                    <tr><td colspan="3" style="padding:10px; text-align:center; color:#777;">Sin tareas.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- Firmas --}}
        <div style="display:flex; justify-content:space-between; margin-top:64px;">
            <div style="text-align:center;">
                <div style="border-top:1px solid #000; width:200px; padding-top:4px; font-size:9pt;">Jefe de Mantenimiento</div>
            </div>
            <div style="text-align:center;">
                <div style="border-top:1px solid #000; width:200px; padding-top:4px; font-size:9pt;">Responsable del Taller</div>
            </div>
            <div style="text-align:center;">
                <div style="border-top:1px solid #000; width:200px; padding-top:4px; font-size:9pt;">Conductor / Propietario</div>
            </div>
        </div>

        <p style="text-align:center; margin-top:40px; font-size:8pt; color:#888; border-top:1px solid #ddd; padding-top:8px;">
            Generado por SIGU · {{ now()->format('d/m/Y H:i') }} · Documento de uso interno
        </p>
    </div>

</div>
@endsection
