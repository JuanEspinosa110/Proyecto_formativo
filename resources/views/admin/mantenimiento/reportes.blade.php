@extends('admin.layouts.app')

@section('title', 'Reportes de Fallas — SIGU')

@section('content')
<div class="sigu-fade">
    <div class="sigu-page-hd d-flex justify-content-between align-items-center">
        <div>
            <h1 class="sigu-page-title">Bandeja de Reportes de Fallas</h1>
            <p class="sigu-page-sub">Alertas enviadas por los conductores sobre novedades en los buses.</p>
        </div>
        <div>
            <a href="{{ route('admin.mantenimiento.index') }}" class="btn btn-outline-secondary" style="border-radius:0.5rem;">
                <span class="material-symbols-rounded" style="font-size:1rem;vertical-align:middle;">build</span>
                Ver Mantenimientos
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 mt-4">
        <div class="table-responsive">
            <table class="table sigu-table w-100 table-hover">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Bus (Placa)</th>
                        <th>Conductor</th>
                        <th>Descripción</th>
                        <th>Urgencia</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportes as $reporte)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($reporte->created_at)->format('d/m/Y') }}</td>
                            <td>
                                <strong>{{ $reporte->placa }}</strong>
                                @if($reporte->bus)
                                    <br><small class="text-muted">{{ $reporte->bus->modelo }}</small>
                                @endif
                            </td>
                            <td>
                                @if($reporte->conductor)
                                    {{ $reporte->conductor->primer_nombre }} {{ $reporte->conductor->primer_apellido }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="max-width:250px;">{{ Str::limit($reporte->descripcion, 80) }}</td>
                            <td>
                                @php $urgencia = strtoupper($reporte->urgencia ?? ''); @endphp
                                @if($urgencia === 'ALTA' || $urgencia === 'CRITICA')
                                    <span class="badge bg-danger">{{ $urgencia }}</span>
                                @elseif($urgencia === 'MEDIA')
                                    <span class="badge bg-warning text-dark">{{ $urgencia }}</span>
                                @else
                                    <span class="badge bg-success">{{ $urgencia ?: 'BAJA' }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ $reporte->estado->nombre_estado ?? 'Sin estado' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.mantenimiento.reportes.attend', $reporte->id_reporte) }}"
                                   class="btn btn-sm" style="background:var(--p); color:white; border-radius:0.5rem; padding:0.25rem 0.6rem; text-decoration:none;">
                                    Enviar al Taller
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No hay reportes de fallas registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $reportes->links() }}</div>
    </div>
</div>
@endsection
