@extends('conductor.layouts.app')

@section('title', 'Historial de Fallas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <h4 class="fw-bold text-dark mb-0">Historial de Fallas Reportadas</h4>
    
    <div class="d-flex gap-2">
        <button class="btn btn-warning rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1 text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#fallaModal">
            <span class="material-symbols-rounded fs-6">add</span> Reportar Falla
        </button>
        <a href="{{ route('conductor.dashboard') }}" class="btn btn-light rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1">
            <span class="material-symbols-rounded fs-6">arrow_back</span> Volver
        </a>
    </div>
</div>

<div class="card p-4 rounded-4 shadow-sm border-0 bg-white mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <span class="material-symbols-rounded text-danger">car_repair</span> Listado de Reportes
        </h5>
        
        <div class="btn-group bg-light p-1 rounded-pill" role="group">
            <a href="{{ route('conductor.fallas', ['filtro' => 'todos']) }}" class="btn {{ $filtro == 'todos' ? 'btn-primary text-white' : 'btn-light text-muted' }} rounded-pill px-3 fw-bold">Todos</a>
            <a href="{{ route('conductor.fallas', ['filtro' => 'hoy']) }}" class="btn {{ $filtro == 'hoy' ? 'btn-primary text-white' : 'btn-light text-muted' }} rounded-pill px-3 fw-bold">Hoy</a>
            <a href="{{ route('conductor.fallas', ['filtro' => 'semana']) }}" class="btn {{ $filtro == 'semana' ? 'btn-primary text-white' : 'btn-light text-muted' }} rounded-pill px-3 fw-bold">Semana</a>
            <a href="{{ route('conductor.fallas', ['filtro' => 'mes']) }}" class="btn {{ $filtro == 'mes' ? 'btn-primary text-white' : 'btn-light text-muted' }} rounded-pill px-3 fw-bold">Mes</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle border-top border-bottom">
            <thead class="bg-light text-muted small">
                <tr>
                    <th class="ps-3 border-0">FECHA</th>
                    <th class="border-0">VEHÍCULO</th>
                    <th class="border-0">DESCRIPCIÓN</th>
                    <th class="border-0">NIVEL URGENGIA</th>
                    <th class="border-0 text-end pe-3">ESTADO</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fallas as $falla)
                    <tr>
                        <td class="ps-3 small text-muted">
                            {{ \Carbon\Carbon::parse($falla->created_at)->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $falla->placa }}</span>
                        </td>
                        <td>
                            <div class="small fw-bold text-dark">{{ $falla->descripcion }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $falla->nivel_urgencia }}</span>
                        </td>
                        <td class="text-end pe-3">
                            @if($falla->id_estado == 6) 
                                <span class="badge bg-danger rounded-pill">PENDIENTE</span>
                            @elseif($falla->id_estado == 1) 
                                <span class="badge bg-warning rounded-pill text-dark">EN PROCESO</span>
                            @elseif($falla->id_estado == 5)
                                <span class="badge bg-success rounded-pill">SOLUCIONADO</span>
                            @else
                                <span class="badge bg-secondary rounded-pill">OTROS</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <span class="material-symbols-rounded fs-1 opacity-25">car_repair</span>
                            <p class="mt-2 mb-0">No hay fallas reportadas históricas.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $fallas->appends(['filtro' => $filtro])->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- MODAL REPORTE FALLA (Reutilizado del dashboard) -->
<div class="modal fade" id="fallaModal" tabindex="-1" aria-labelledby="fallaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow rounded-4" action="{{ route('conductor.reportarFalla') }}" method="POST">
            @csrf
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="fallaModalLabel">
                    <span class="material-symbols-rounded text-warning">warning</span> Reportar Falla Mecánica
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <p class="text-muted small mb-4">Evidencie todo problema mecánico u operativo a los líderes.</p>
                
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small text-uppercase">Vehículo Implicado</label>
                    @if($asignacionActiva)
                        <input type="text" name="placa" class="form-control bg-light rounded-3 font-monospace fw-bold" value="{{ $asignacionActiva->placa }}" readonly required>
                    @else
                        <select name="placa" class="form-select rounded-3" required>
                            <option value="" disabled selected>Seleccione placa del vehículo...</option>
                            @foreach($asignaciones->unique('placa') as $asig)
                                <option value="{{ $asig->placa }}">{{ $asig->placa }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small text-uppercase">Nivel de Urgencia</label>
                    <select name="nivel_urgencia" class="form-select rounded-3" required>
                        @foreach($nivelesUrgencia as $nivel)
                            <option value="{{ $nivel }}" {{ $nivel == 'Bajo' ? 'selected' : '' }}>{{ $nivel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small text-uppercase">Descripción Detallada</label>
                    <textarea name="descripcion" class="form-control rounded-3 bg-light border-0 py-3 px-3" rows="4" placeholder="Explique la situación..." required></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 pt-2">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark shadow-sm">Registrar Envío</button>
            </div>
        </form>
    </div>
</div>
@endsection
