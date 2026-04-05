@extends('admin.layouts.app')

@section('title', 'Registrar Mantenimiento — SIGU')

@section('content')
<div class="sigu-fade">
    <div class="sigu-page-hd">
        <div>
            <h1 class="sigu-page-title">Registrar Mantenimiento</h1>
            <p class="sigu-page-sub">Envíe un vehículo al taller y registre las tareas a realizar.</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 mt-4" style="max-width:800px;">
        
        @if ($errors->any())
            <div class="alert alert-danger" style="border-radius:0.5rem; border-left:4px solid #c53030; background-color:#fff5f5; color:#c53030;">
                <div class="d-flex align-items-center mb-2">
                    <span class="material-symbols-rounded me-2" style="font-size:1.2rem;">error</span>
                    <strong>Por favor, corrija los siguientes errores:</strong>
                </div>
                <ul class="mb-0 small ps-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.mantenimiento.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="origen" value="admin">

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label for="placa" class="form-label fw-semibold">Bus (Placa) <span class="text-danger">*</span></label>
                    <select name="placa" id="placa" class="form-select" required @if(isset($placa_predefinida) && $placa_predefinida) disabled @endif>
                        <option value="">Seleccione un bus...</option>
                        @foreach($buses as $bus)
                            <option value="{{ $bus->placa }}"
                                @if(isset($placa_predefinida) && $placa_predefinida == $bus->placa) selected @endif
                                @if($bus->id_estado == 4) disabled @endif>
                                {{ $bus->placa }} — {{ $bus->modelo }}
                                @if($bus->id_estado == 4) (Ya en taller) @endif
                            </option>
                        @endforeach
                    </select>
                    @if(isset($placa_predefinida) && $placa_predefinida)
                        <input type="hidden" name="placa" value="{{ $placa_predefinida }}">
                    @endif
                </div>
                <div class="col-md-6 mb-3">
                    <label for="fecha_mantenimiento" class="form-label fw-semibold">Fecha de ingreso <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_mantenimiento" id="fecha_mantenimiento" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <hr class="mb-4">
            <h5 class="mb-3 fw-bold">Tareas a realizar</h5>
            <div id="detalles-container">
                <div class="detalle-row mb-3 p-3 rounded" style="background:#f8fafc; border:1px solid #e2e8f0;">
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-2">
                            <label class="form-label small">Tipo</label>
                            <select name="detalles[0][id_tipo_mantenimiento]" class="form-select tipo-mantenimiento-select" required>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->id_tipo_mantenimiento }}">{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label small">Descripción</label>
                            <input type="text" name="detalles[0][descripcion]" class="form-control" placeholder="Ej. Cambio de aceite" required>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label small">¿Vincular Falla?</label>
                            <select name="detalles[0][id_reporte]" class="form-select form-select-sm reporte-select">
                                <option value="">-- Ninguna --</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label small">Foto (Opcional)</label>
                            <input type="file" name="detalles[0][evidencia_foto]" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-md-1 mb-2 pb-1 d-flex justify-content-center">
                            <button type="button" class="btn btn-sm text-muted" disabled style="opacity:0.3;">
                                <span class="material-symbols-rounded">delete</span>
                            </button>
                        </div>
                    </div>

                    {{-- Sub-panel preventivo dinámico por fila --}}
                    <div class="preventivo-panel mt-2 p-2 rounded" style="background:#fff; border:1px dashed #cbd5e1; display:none;">
                        <span class="d-block small fw-bold text-primary mb-2">Mantenimiento Preventivo</span>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small mb-1">Próximo (Fecha)</label>
                                <input type="date" name="detalles[0][fecha_proximo]" class="form-control form-control-sm fecha-input">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small mb-1">Próximo (Km)</label>
                                <input type="number" name="detalles[0][km_proximo]" class="form-control form-control-sm km-input" placeholder="Ej. 150000" min="0">
                            </div>
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
    let pendingReports = [];
    const preselectedReportId = @json($reporte_id ?? null);
    const tipos = @json($tipos->map(fn($t) => ['id' => $t->id_tipo_mantenimiento, 'nombre' => $t->nombre]));

    function updateAllReportSelects() {
        const selects = document.querySelectorAll('.reporte-select');
        selects.forEach(select => {
            const currentValue = select.value;
            select.innerHTML = '<option value="">-- Ninguna --</option>';
            pendingReports.forEach(r => {
                const opt = document.createElement('option');
                opt.value = r.id_reporte;
                const desc = r.descripcion.length > 40 ? r.descripcion.substring(0, 40) + '...' : r.descripcion;
                opt.textContent = `[${r.nivel}] ${r.fecha} — ${desc}`;
                select.appendChild(opt);
            });
            if (currentValue) select.value = currentValue;
            else if (preselectedReportId && select.name === 'detalles[0][id_reporte]') {
                select.value = preselectedReportId;
            }
        });
    }

    document.getElementById('add-detalle').addEventListener('click', () => {
        const opts = tipos.map(t => `<option value="${t.id}">${t.nombre}</option>`).join('');
        const row = document.createElement('div');
        row.className = 'detalle-row mb-3 p-3 rounded';
        row.style.cssText = 'background:#f8fafc; border:1px solid #e2e8f0;';
        row.innerHTML = `
            <div class="row align-items-end">
                <div class="col-md-3 mb-2">
                    <label class="form-label small">Tipo</label>
                    <select name="detalles[${idx}][id_tipo_mantenimiento]" class="form-select tipo-mantenimiento-select" required>${opts}</select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label small">Descripción</label>
                    <input type="text" name="detalles[${idx}][descripcion]" class="form-control" placeholder="Ej. Revisión de frenos" required>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label small">¿Vincular Falla?</label>
                    <select name="detalles[${idx}][id_reporte]" class="form-select form-select-sm reporte-select">
                        <option value="">-- Ninguna --</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label small">Foto (Opcional)</label>
                    <input type="file" name="detalles[${idx}][evidencia_foto]" class="form-control form-control-sm" accept="image/*">
                </div>
                <div class="col-md-1 mb-2 pb-1 d-flex justify-content-center">
                    <button type="button" class="btn text-danger remove-detalle p-2" title="Eliminar tarea">
                        <span class="material-symbols-rounded">delete</span>
                    </button>
                </div>
            </div>
            
            <div class="preventivo-panel mt-2 p-2 rounded" style="background:#fff; border:1px dashed #cbd5e1; display:none;">
                <span class="d-block small fw-bold text-primary mb-2">Mantenimiento Preventivo</span>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label small mb-1">Próximo (Fecha)</label>
                        <input type="date" name="detalles[${idx}][fecha_proximo]" class="form-control form-control-sm fecha-input">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label small mb-1">Próximo (Km)</label>
                        <input type="number" name="detalles[${idx}][km_proximo]" class="form-control form-control-sm km-input" placeholder="Ej. 150000" min="0">
                    </div>
                </div>
            </div>`;
        document.getElementById('detalles-container').appendChild(row);
        
        togglePreventivoRow(row.querySelector('.tipo-mantenimiento-select'));

        // Poblar el nuevo select
        const newSelect = row.querySelector('.reporte-select');
        newSelect.innerHTML = '<option value="">-- Ninguna --</option>';
        pendingReports.forEach(r => {
            const opt = document.createElement('option');
            opt.value = r.id_reporte;
            const desc = r.descripcion.length > 40 ? r.descripcion.substring(0, 40) + '...' : r.descripcion;
            opt.textContent = `[${r.nivel}] ${r.fecha} — ${desc}`;
            newSelect.appendChild(opt);
        });
        
        idx++;
    });

    document.getElementById('detalles-container').addEventListener('click', e => {
        const btn = e.target.closest('.remove-detalle');
        if (btn) btn.closest('.detalle-row').remove();
    });

    function togglePreventivoRow(selectElement) {
        const row = selectElement.closest('.detalle-row');
        const panel = row.querySelector('.preventivo-panel');
        if (selectElement.value == "1") {
            panel.style.display = 'block';
        } else {
            panel.style.display = 'none';
            row.querySelector('.fecha-input').value = '';
            row.querySelector('.km-input').value = '';
        }
    }

    document.getElementById('detalles-container').addEventListener('change', e => {
        if (e.target.classList.contains('tipo-mantenimiento-select')) {
            togglePreventivoRow(e.target);
        }
    });

    togglePreventivoRow(document.querySelector('.tipo-mantenimiento-select'));

    // 🔗 Lógica de Vinculación de Reportes (AJAX)
    const placaSelect = document.getElementById('placa');

    placaSelect.addEventListener('change', function() {
        const placa = this.value;
        if (!placa) {
            pendingReports = [];
            updateAllReportSelects();
            return;
        }

        fetch(`{{ url('') }}/admin/mantenimiento/api/reportes-pendientes/${placa}`)
            .then(res => res.json())
            .then(data => {
                pendingReports = data;
                updateAllReportSelects();
            })
            .catch(err => console.error('Error fetching reports:', err));
    });

    if (placaSelect.value) {
        placaSelect.dispatchEvent(new Event('change'));
    }
</script>
@endpush
@endsection
