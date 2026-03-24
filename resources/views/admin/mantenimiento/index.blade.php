@extends('admin.layouts.app')

@section('title', 'Mantenimientos — SIGU')

@section('content')
<div class="sigu-fade">
    <div class="sigu-page-hd d-flex justify-content-between align-items-center">
        <div>
            <h1 class="sigu-page-title">Gestión de Mantenimientos</h1>
            <p class="sigu-page-sub">Registro de envíos al taller y costos de la flota.</p>
        </div>
        <div>
            <a href="{{ route('admin.mantenimiento.reportes') }}" class="btn btn-outline-secondary me-2" style="border-radius:0.5rem;">
                <span class="material-symbols-rounded" style="font-size:1rem;vertical-align:middle;">notification_important</span>
                Ver Reportes
            </a>
            <a href="{{ route('admin.mantenimiento.create', ['origen'=>'admin']) }}" class="btn" style="background:var(--p); color:white; border-radius:0.5rem; padding:0.5rem 1rem; text-decoration:none; display:inline-flex; align-items:center; gap:0.5rem;">
                <span class="material-symbols-rounded">add</span> Enviar Bus al Taller
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 mt-4">
        <div class="table-responsive">
            <table class="table sigu-table w-100 table-hover align-middle">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Bus (Placa)</th>
                        <th>Kilometraje</th>
                        <th>Costo Total</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mantenimientos as $mant)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($mant->fecha_mantenimiento)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.buses.historial', $mant->placa) }}" style="color:var(--p); font-weight:600; text-decoration:none;">{{ $mant->placa }}</a>
                                @if($mant->bus)
                                    <br><small class="text-muted">{{ $mant->bus->modelo }}</small>
                                @endif
                            </td>
                            <td>{{ number_format($mant->kilometraje) }} KM</td>
                            <td><strong>${{ number_format($mant->costo_total, 0, ',', '.') }}</strong></td>
                            <td>
                                @if((int)$mant->id_estado === 7)
                                    <span class="badge bg-warning text-dark">En Taller</span>
                                @else
                                    <span class="badge bg-success">Finalizado</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                <a href="{{ route('admin.mantenimiento.show', [$mant->id_mantenimiento, 'origen'=>'admin']) }}" class="btn btn-sm" style="border:1px solid var(--p); color:var(--p); border-radius:0.5rem; padding:0.25rem 0.5rem; text-decoration:none;">
                                    Ver detalle
                                </a>
                                @if((int)$mant->id_estado === 7)
                                <form id="formFin{{ $mant->id_mantenimiento }}" action="{{ route('admin.mantenimiento.finalizar', $mant->id_mantenimiento) }}" method="POST">
                                    @csrf
                                    <button type="button" class="btn btn-sm" style="background:#38a169; color:white; border-radius:0.5rem; padding:0.25rem 0.5rem;"
                                            data-confirm-form="formFin{{ $mant->id_mantenimiento }}"
                                            data-confirm-title="Finalizar y liberar bus"
                                            data-confirm-msg="El bus será marcado como disponible nuevamente.">
                                        Finalizar
                                    </button>
                                </form>
                                @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No hay registros de mantenimiento aún.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $mantenimientos->links() }}</div>
    </div>
</div>
@endsection
