@extends('pasajero.layouts.app')
@section('title', 'Historial')

@section('content')
<div class="container-fluid py-4 px-4">

    <div class="pas-header">
        <div>
            <h1><span class="material-symbols-rounded">history</span> Historial</h1>
            <p>Consulta tus viajes y recargas realizados con la tarjeta SIGU.</p>
        </div>
        <div style="display:flex;gap:.5rem;align-items:center">
            <span style="font-size:.82rem;color:var(--text-2)">Gastado total:</span>
            <span style="font-family:var(--ff-d);font-weight:700;color:var(--err)">
                -$ {{ number_format($totalGastado, 0, ',', '.') }}
            </span>
            <span style="color:var(--border)">|</span>
            <span style="font-size:.82rem;color:var(--text-2)">Recargado total:</span>
            <span style="font-family:var(--ff-d);font-weight:700;color:var(--ok)">
                +$ {{ number_format($totalRecargado, 0, ',', '.') }}
            </span>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="hist-tabs">
        <button class="hist-tab {{ $tab === 'viajes' ? 'active' : '' }}"
                onclick="cambiarTab('viajes')">
            <span class="material-symbols-rounded" style="font-size:.95rem;vertical-align:middle">directions_bus</span>
            Viajes
            <span style="background:var(--pas-l);color:var(--pas);border-radius:var(--r-xl);padding:.05rem .45rem;font-size:.72rem;margin-left:.3rem">
                {{ $viajes->total() }}
            </span>
        </button>
        <button class="hist-tab {{ $tab === 'recargas' ? 'active' : '' }}"
                onclick="cambiarTab('recargas')">
            <span class="material-symbols-rounded" style="font-size:.95rem;vertical-align:middle">add_card</span>
            Recargas
            <span style="background:var(--ok-bg);color:var(--ok);border-radius:var(--r-xl);padding:.05rem .45rem;font-size:.72rem;margin-left:.3rem">
                {{ $recargas->total() }}
            </span>
        </button>
    </div>

    {{-- Tab: Viajes --}}
    <div id="tabViajes" style="{{ $tab !== 'viajes' ? 'display:none' : '' }}">
        <div class="pas-card">
            <div class="table-responsive">
                <table class="hist-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Ruta</th>
                            <th>Trayecto</th>
                            <th>Bus</th>
                            <th style="text-align:right">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($viajes as $venta)
                        <tr>
                            <td style="white-space:nowrap;color:var(--text-2);font-size:.82rem">
                                {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                @if($venta->viaje?->ruta)
                                    <span class="hist-ruta-pill">
                                        <span class="material-symbols-rounded" style="font-size:.8rem">alt_route</span>
                                        Ruta #{{ $venta->viaje->ruta->codigo_ruta }}
                                    </span>
                                @else
                                    <span style="color:var(--text-3)">—</span>
                                @endif
                            </td>
                            <td style="font-size:.82rem">
                                @if($venta->viaje?->ruta)
                                    {{ $venta->viaje->ruta->barrioOrigen->nombre ?? '—' }}
                                    <span class="material-symbols-rounded" style="font-size:.85rem;color:var(--pas);vertical-align:middle">arrow_forward</span>
                                    {{ $venta->viaje->ruta->barrioDestino->nombre ?? '—' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td style="font-size:.82rem;font-family:monospace">
                                {{ $venta->viaje?->placa ?? '—' }}
                            </td>
                            <td style="text-align:right">
                                <span class="hist-monto-neg">-$ {{ number_format($venta->valor, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="pas-empty">
                                    <span class="material-symbols-rounded">directions_bus</span>
                                    <p>No tienes viajes registrados.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($viajes->hasPages())
            <div style="padding:1rem 1.25rem;display:flex;justify-content:flex-end;border-top:1px solid var(--border)">
                {{ $viajes->appends(['tab' => 'viajes'])->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Tab: Recargas --}}
    <div id="tabRecargas" style="{{ $tab !== 'recargas' ? 'display:none' : '' }}">
        <div class="pas-card">
            <div class="table-responsive">
                <table class="hist-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tarjeta</th>
                            <th style="text-align:right">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recargas as $rec)
                        <tr>
                            <td style="white-space:nowrap;color:var(--text-2);font-size:.82rem">
                                {{ \Carbon\Carbon::parse($rec->created_at)->format('d/m/Y H:i') }}
                            </td>
                            <td style="font-family:monospace;font-size:.82rem">{{ $rec->id_tarjeta }}</td>
                            <td style="text-align:right">
                                <span class="hist-monto-pos">$ {{ number_format($rec->monto, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3">
                                <div class="pas-empty">
                                    <span class="material-symbols-rounded">add_card</span>
                                    <p>No tienes recargas registradas.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($recargas->hasPages())
            <div style="padding:1rem 1.25rem;display:flex;justify-content:flex-end;border-top:1px solid var(--border)">
                {{ $recargas->appends(['tab' => 'recargas'])->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function cambiarTab(tab) {
    document.getElementById('tabViajes').style.display   = tab === 'viajes'   ? '' : 'none';
    document.getElementById('tabRecargas').style.display = tab === 'recargas' ? '' : 'none';
    document.querySelectorAll('.hist-tab').forEach((el, i) => {
        el.classList.toggle('active', (i === 0 && tab === 'viajes') || (i === 1 && tab === 'recargas'));
    });
    history.replaceState(null, '', '?tab=' + tab);
}
</script>
@endpush
