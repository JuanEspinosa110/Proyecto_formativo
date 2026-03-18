@extends('conductor.layouts.app')

@section('title', 'Historial de Recorridos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <h4 class="fw-bold text-dark mb-0">Historial de Recorridos</h4>
    
    <a href="{{ route('conductor.dashboard') }}" class="btn btn-light rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1">
        <span class="material-symbols-rounded fs-6">arrow_back</span> Volver
    </a>
</div>

<div class="card p-4 rounded-4 shadow-sm border-0 bg-white mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <span class="material-symbols-rounded text-primary">analytics</span> Recorridos Realizados
        </h5>
        
        <div class="btn-group bg-light p-1 rounded-pill" role="group">
            <a href="{{ route('conductor.recorridos', ['filtro' => 'todos']) }}" class="btn {{ $filtro == 'todos' ? 'btn-primary text-white' : 'btn-light text-muted' }} rounded-pill px-3 fw-bold">Todos</a>
            <a href="{{ route('conductor.recorridos', ['filtro' => 'hoy']) }}" class="btn {{ $filtro == 'hoy' ? 'btn-primary text-white' : 'btn-light text-muted' }} rounded-pill px-3 fw-bold">Hoy</a>
            <a href="{{ route('conductor.recorridos', ['filtro' => 'semana']) }}" class="btn {{ $filtro == 'semana' ? 'btn-primary text-white' : 'btn-light text-muted' }} rounded-pill px-3 fw-bold">Semana</a>
            <a href="{{ route('conductor.recorridos', ['filtro' => 'mes']) }}" class="btn {{ $filtro == 'mes' ? 'btn-primary text-white' : 'btn-light text-muted' }} rounded-pill px-3 fw-bold">Mes</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle border-top border-bottom">
            <thead class="bg-light text-muted small">
                <tr>
                    <th class="ps-3 border-0">FECHA</th>
                    <th class="border-0">RUTA</th>
                    <th class="border-0">VEHÍCULO</th>
                    <th class="border-0">SENTIDO</th>
                    <th class="border-0">TIEMPOS</th>
                    <th class="border-0 text-center">PASAJEROS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recorridos as $rec)
                    <tr>
                        <td class="ps-3">
                            <span class="badge bg-light text-dark border px-2 py-1">{{ \Carbon\Carbon::parse($rec->hora_salida)->format('d/m/Y') }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $rec->ruta->nombre_ruta ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $rec->placa }}</span>
                        </td>
                        <td>
                            <div class="small fw-bold text-muted">{{ $rec->sentido ?? 'IDA' }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark small">
                                {{ \Carbon\Carbon::parse($rec->hora_salida)->format('H:i') }} - 
                                @if($rec->hora_llegada) 
                                    <span class="text-success">{{ \Carbon\Carbon::parse($rec->hora_llegada)->format('H:i') }}</span> 
                                @else 
                                    <span class="badge bg-warning text-dark">En progreso</span> 
                                @endif
                            </div>
                        </td>
                        <td class="text-center fw-bold text-primary">{{ $rec->cantidad_pasajeros }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <span class="material-symbols-rounded fs-1 opacity-25">analytics</span>
                            <p class="mt-2 mb-0">No se encontraron recorridos para este filtro.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $recorridos->appends(['filtro' => $filtro])->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
