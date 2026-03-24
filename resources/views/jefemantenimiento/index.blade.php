@extends('jefemantenimiento.layouts.app')

@section('title', 'Historial de Mantenimientos — SIGU')

@section('content')
<div class="sigu-fade">
    <div class="sigu-page-hd d-flex justify-content-between align-items-center">
        <div>
            <h1 class="sigu-page-title">Historial de Mantenimientos</h1>
            <p class="sigu-page-sub">Registro histórico de reparaciones y preventivos de la flota.</p>
        </div>
        <div>
            <a href="{{ route('jefemantenimiento.create') }}" class="btn" style="background:var(--p); color:white; border-radius:0.5rem; padding: 0.5rem 1rem; text-decoration:none; display:inline-flex; align-items:center; gap:0.5rem;">
                <span class="material-symbols-rounded">add</span> Nuevo Mantenimiento
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 mt-4">
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
                                <strong>{{ $mant->placa }}</strong>
                            </td>
                            <td>{{ number_format($mant->kilometraje) }} KM</td>
                            <td>${{ number_format($mant->costo_total, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge @if($mant->id_estado == 1) bg-success @else bg-secondary @endif">
                                    {{ $mant->estado ? $mant->estado->nombre_estado : 'Finalizado' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('jefemantenimiento.show', $mant->id_mantenimiento) }}" class="btn btn-sm" style="border:1px solid var(--p); color:var(--p); border-radius:0.5rem; padding: 0.25rem 0.5rem; text-decoration:none;">
                                    Detalles
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No se han registrado mantenimientos aún.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $mantenimientos->links() }}
        </div>
    </div>
</div>
@endsection
