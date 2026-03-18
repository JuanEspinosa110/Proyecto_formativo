@extends('auxiliar.layouts.app')

@section('title', 'Auditoría de Documentos — Auxiliar')

@section('content')
<div class="container-fluid pt-0 pb-4">
    
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold">Historial de Auditoría</h1>
            <p class="text-muted small mb-0">Registro de todas las operaciones y cambios sobre documentos.</p>
        </div>
        <a href="{{ route('auxiliar.documentos.index') }}" class="btn btn-light btn-sm d-flex align-items-center gap-2 px-3 rounded-pill border shadow-sm">
            <span class="material-symbols-rounded fs-5">arrow_back</span> Volver
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger shadow-sm py-2 mb-4">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small fw-bold text-uppercase">
                    <tr>
                        <th class="ps-4">Documento</th>
                        <th>Acción</th>
                        <th>Detalles Cambio</th>
                        <th>Realizado por</th>
                        <th>Fecha y Hora</th>
                    </tr>
                </thead>
                <tbody class="text-secondary">
                    @forelse($auditorias as $a)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $a->nombre_documento }}</td>
                            <td>
                                <span class="badge rounded-pill px-2 py-1 {{ $a->tipo_accion == 'CREACION' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">
                                    {{ $a->tipo_accion }}
                                </span>
                            </td>
                            <td class="text-wrap" style="max-width: 300px;">{{ $a->detalles }}</td>
                            <td><span class="small text-muted">{{ $a->doc_usuario }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($a->created_at)->format('d/m/Y H:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <span class="material-symbols-rounded fs-1 d-block mb-2">history</span> No hay registros de auditoría aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($auditorias->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                {{ $auditorias->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
@endsection
