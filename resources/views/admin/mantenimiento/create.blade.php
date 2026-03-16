@extends('admin.layouts.app')

@section('title', 'Registrar Mantenimiento — SIGU')

@section('content')
<div class="sigu-fade">
    <div class="sigu-page-hd">
        <div>
            <a href="{{ route('admin.mantenimiento.index') }}" class="text-muted small" style="text-decoration:none;">← Volver al historial</a>
            <h1 class="sigu-page-title mt-1">Enviar Bus al Taller</h1>
            <p class="sigu-page-sub">El bus quedará en estado "En Mantenimiento" hasta que lo finalice.</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 mt-4" style="max-width:800px;">
        <form action="{{ route('admin.mantenimiento.store') }}" method="POST">
            @csrf
            <input type="hidden" name="origen" value="admin">

            @if(isset($reporte_id) && $reporte_id)
                <input type="hidden" name="reporte_id" value="{{ $reporte_id }}">
                <div class="alert alert-info mb-4" style="background:#ebf8ff; color:#2b6cb0; padding:0.75rem 1rem; border-radius:0.5rem; font-size:0.9rem;">
                    <span class="material-symbols-rounded" style="font-size:1rem; vertical-align:middle;">info</span>
                    Esta orden de trabajo está vinculada a un reporte de falla. Se marcará como "Atendido" al guardar.
                </div>
            @endif

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label for="placa" class="form-label fw-semibold">Bus (Placa) <span class="text-danger">*</span></label>
                    <select name="placa" id="placa" class="form-select" required @if(isset($placa_predefinida) && $placa_predefinida) disabled @endif>
                        <option value="">Seleccione un bus...</option>
                        @foreach($buses as $bus)
                            <option value="{{ $bus->placa }}"
                                @if(isset($placa_predefinida) && $placa_predefinida == $bus->placa) selected @endif
                                @if($bus->id_estado == 7) disabled @endif>
                                {{ $bus->placa }} — {{ $bus->modelo }}
                                @if($bus->id_estado == 7) (Ya en taller) @endif
                            </option>
                        @endforeach
                    </select>
                    @if(isset($placa_predefinida) && $placa_predefinida)
                        <input type="hidden" name="placa" value="{{ $placa_predefinida }}">
                        <small class="text-muted">Placa vinculada al reporte.</small>
                    @endif
                </div>
                <div class="col-md-6 mb-3">
                    <label for="fecha_mantenimiento" class="form-label fw-semibold">Fecha de ingreso <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_mantenimiento" id="fecha_mantenimiento" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <hr class="mb-4">
            <h5 class="mb-3">Tareas a realizar</h5>
            <div id="detalles-container">
                <div class="detalle-row mb-3 p-3 rounded" style="background:#f8fafc; border:1px solid #e2e8f0;">
                    <div class="row align-items-end">
                        <div class="col-md-4 mb-2">
                            <label class="form-label small">Tipo</label>
                            <select name="detalles[0][id_tipo_mantenimiento]" class="form-select" required>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->id_tipo_mantenimiento }}">{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-7 mb-2">
                            <label class="form-label small">Descripción</label>
                            <input type="text" name="detalles[0][descripcion]" class="form-control" placeholder="Ej. Cambio de aceite completo" required>
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
                + Agregar tarea
            </button>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="costo_total" class="form-label fw-semibold">Costo total estimado ($) <span class="text-danger">*</span></label>
                    <input type="number" name="costo_total" id="costo_total" class="form-control" placeholder="0" min="0" required>
                </div>
            </div>

            <div class="d-flex gap-3 mt-2">
                <button type="submit" class="btn" style="background:var(--p); color:white; padding:0.5rem 2rem;">
                    Enviar al Taller
                </button>
                <a href="{{ route('admin.mantenimiento.index') }}" class="btn btn-light" style="padding:0.5rem 2rem;">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let idx = 1;
const tipos = @json($tipos->map(fn($t) => ['id' => $t->id_tipo_mantenimiento, 'nombre' => $t->nombre]));
document.getElementById('add-detalle').addEventListener('click', () => {
    const opts = tipos.map(t => `<option value="${t.id}">${t.nombre}</option>`).join('');
    const row = document.createElement('div');
    row.className = 'detalle-row mb-3 p-3 rounded';
    row.style.cssText = 'background:#f8fafc; border:1px solid #e2e8f0;';
    row.innerHTML = `
        <div class="row align-items-end">
            <div class="col-md-4 mb-2">
                <label class="form-label small">Tipo</label>
                <select name="detalles[${idx}][id_tipo_mantenimiento]" class="form-select" required>${opts}</select>
            </div>
            <div class="col-md-7 mb-2">
                <label class="form-label small">Descripción</label>
                <input type="text" name="detalles[${idx}][descripcion]" class="form-control" placeholder="Ej. Revisión de frenos" required>
            </div>
            <div class="col-md-1 mb-2 d-flex align-items-end">
                <button type="button" class="btn btn-sm text-danger remove-detalle" title="Eliminar tarea">
                    <span class="material-symbols-rounded">delete</span>
                </button>
            </div>
        </div>`;
    document.getElementById('detalles-container').appendChild(row);
    idx++;
});

// Delegación de eventos para el botón eliminar
document.getElementById('detalles-container').addEventListener('click', e => {
    const btn = e.target.closest('.remove-detalle');
    if (btn) btn.closest('.detalle-row').remove();
});
</script>
@endpush
@endsection
