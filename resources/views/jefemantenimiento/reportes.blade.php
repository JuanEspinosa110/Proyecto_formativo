@extends('jefemantenimiento.layouts.app')

@section('title', 'Bandeja de Reportes de Fallas — SIGU')

@push('css')
<style>
    .report-card { border-left: 4px solid var(--p); }
    .report-card.urgencia-Alto { border-left-color: #e53e3e; } /* Red */
    .report-card.urgencia-Medio { border-left-color: #d69e2e; } /* Yellow */
    .report-card.urgencia-Bajo { border-left-color: #38a169; } /* Green */
</style>
@endpush

@section('content')
<div class="sigu-fade">
    <div class="sigu-page-hd">
        <div>
            <h1 class="sigu-page-title">Reportes de Fallas</h1>
            <p class="sigu-page-sub">Bandeja de entrada de fallas reportadas por los conductores.</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4">
        @if(session('success'))
            <div class="alert alert-success mb-4" style="background:#e6fffa; color:#234e52; padding:1rem; border-radius:0.5rem;">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table sigu-table w-100 table-hover">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Bus (Placa)</th>
                        <th>Conductor</th>
                        <th>Falla Reportada</th>
                        <th>Urgencia</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportes as $reporte)
                        <tr class="report-card urgencia-{{ $reporte->nivel_urgencia }}">
                            <td>{{ $reporte->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <strong>{{ $reporte->placa }}</strong>
                                @if($reporte->bus)
                                <br><small class="text-muted">Mod: {{ $reporte->bus->modelo }}</small>
                                @endif
                            </td>
                            <td>
                                @if($reporte->conductor)
                                    {{ $reporte->conductor->primer_nombre }} {{ $reporte->conductor->primer_apellido }}
                                @else
                                    <span class="text-muted">Desconocido</span>
                                @endif
                            </td>
                            <td>
                                {{ Str::limit($reporte->descripcion, 50) }}
                                <br>
                                <small class="text-muted" style="cursor:help;" title="{{ $reporte->descripcion }}">Ver detale completo...</small>
                            </td>
                            <td>
                                <span class="badge @if($reporte->nivel_urgencia == 'Alto') bg-danger @elseif($reporte->nivel_urgencia == 'Medio') bg-warning text-dark @else bg-success @endif">
                                    {{ $reporte->nivel_urgencia }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('jefemantenimiento.reportes.attend', $reporte->id_reporte) }}" class="btn btn-sm" style="background:var(--p); color:white; border-radius:0.5rem; padding: 0.25rem 0.5rem; text-decoration:none;">
                                    Atender
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No hay reportes de fallas pendientes en este momento.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $reportes->links() }}
        </div>
    </div>
</div>
@endsection
