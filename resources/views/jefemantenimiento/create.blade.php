@extends('jefemantenimiento.layouts.app')

@section('title', 'Registrar Mantenimiento — SIGU')

@section('content')
<div class="sigu-fade">
    <div class="sigu-page-hd">
        <div>
            <h1 class="sigu-page-title">Registrar Mantenimiento</h1>
            <p class="sigu-page-sub">Cree un nuevo registro de mantenimiento para un vehículo.</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 mt-4" style="max-width:800px;">
        <form action="{{ route('jefemantenimiento.store') }}" method="POST">
            @csrf
            <input type="hidden" name="origen" value="jefe">
            @if($reporte_id)
                <input type="hidden" name="reporte_id" value="{{ $reporte_id }}">
            @endif

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label for="placa" class="form-label">Vehículo (Placa)</label>
                    <select name="placa" id="placa" class="form-select" required @if($placa_predefinida) disabled @endif>
                        <option value="">Seleccione un bus...</option>
                        @foreach($buses as $bus)
                            <option value="{{ $bus->placa }}" @if($placa_predefinida == $bus->placa) selected @endif>
                                {{ $bus->placa }} - {{ $bus->modelo }}
                            </option>
                        @endforeach
                    </select>
                    @if($placa_predefinida)
                        <input type="hidden" name="placa" value="{{ $placa_predefinida }}">
                    @endif
                </div>
                <div class="col-md-6 mb-3">
                    <label for="fecha_mantenimiento" class="form-label">Fecha del Trabajo</label>
                    <input type="date" name="fecha_mantenimiento" id="fecha_mantenimiento" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <hr class="mb-4">
            
            <h4 class="mb-3">Detalles de la Reparación</h4>
            <div id="detalles-container">
                <div class="detalle-row mb-3 p-3 rounded" style="background:#f8fafc; border:1px solid #e2e8f0;">
                    <div class="row align-items-end">
                        <div class="col-md-4 mb-2">
                            <label class="form-label small">Tipo de Mantenimiento</label>
                            <select name="detalles[0][id_tipo_mantenimiento]" class="form-select" required>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->id_tipo_mantenimiento }}">{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-7 mb-2">
                            <label class="form-label small">Descripción de la tarea</label>
                            <input type="text" name="detalles[0][descripcion]" class="form-control" placeholder="Ej. Cambio de pastillas delanteras" required>
                        </div>
                        <div class="col-md-1 mb-2 d-flex align-items-end">
                            <button type="button" class="btn btn-sm text-muted" disabled title="La primera tarea no se puede eliminar" style="opacity:0.3;">
                                <span class="material-symbols-rounded">delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" id="add-detalle" class="btn btn-sm mb-4" style="border:1px dashed var(--p); color:var(--p);">
                + Agregar otra tarea
            </button>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="costo_total" class="form-label">Costo Total ($)</label>
                    <input type="number" name="costo_total" id="costo_total" class="form-control" placeholder="0.00" required>
                </div>
            </div>

            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn" style="background:var(--p); color:white; padding:0.5rem 2rem;">
                    Guardar Registro
                </button>
                <a href="{{ route('jefemantenimiento.index') }}" class="btn btn-light" style="padding:0.5rem 2rem;">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let detalleIndex = 1;
    const tiposOpts = @json($tipos->map(fn($t) => ['id' => $t->id_tipo_mantenimiento, 'nombre' => $t->nombre]));

    document.getElementById('add-detalle').addEventListener('click', function() {
        const opts = tiposOpts.map(t => `<option value="${t.id}">${t.nombre}</option>`).join('');
        const row = document.createElement('div');
        row.className = 'detalle-row mb-3 p-3 rounded';
        row.style.cssText = 'background:#f8fafc; border:1px solid #e2e8f0;';
        row.innerHTML = `
            <div class="row align-items-end">
                <div class="col-md-4 mb-2">
                    <label class="form-label small">Tipo de Mantenimiento</label>
                    <select name="detalles[${detalleIndex}][id_tipo_mantenimiento]" class="form-select" required>${opts}</select>
                </div>
                <div class="col-md-7 mb-2">
                    <label class="form-label small">Descripción de la tarea</label>
                    <input type="text" name="detalles[${detalleIndex}][descripcion]" class="form-control" placeholder="Ej. Revisión de niveles" required>
                </div>
                <div class="col-md-1 mb-2 d-flex align-items-end">
                    <button type="button" class="btn btn-sm text-danger remove-detalle" title="Eliminar tarea">
                        <span class="material-symbols-rounded">delete</span>
                    </button>
                </div>
            </div>`;
        document.getElementById('detalles-container').appendChild(row);
        detalleIndex++;
    });

    // Eliminar fila por delegación
    document.getElementById('detalles-container').addEventListener('click', e => {
        const btn = e.target.closest('.remove-detalle');
        if (btn) btn.closest('.detalle-row').remove();
    });
</script>
@endpush
@endsection
