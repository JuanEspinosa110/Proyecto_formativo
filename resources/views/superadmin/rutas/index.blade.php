@extends('superadmin.layouts.admin')

@section('title', 'Rutas — SIGU')

@section('content')
<div class="container-fluid py-4">
    <!-- Header de Página con Botones de Acción -->
    <div class="d-flex align-items-center justify-content-between mb-4 mt-2 px-1">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold">Mapa de Rutas</h1>
            <p class="text-muted small mb-0">Gestión global de destinos y recorridos autorizados por empresa</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('superadmin.rutas.export', request()->all()) }}" class="btn btn-outline-success d-flex align-items-center gap-2 px-3 fw-semibold">
                <span class="material-symbols-rounded" style="font-size: 1.2rem;">download</span>
                Exportar Excel
            </a>
            <button class="btn btn-primary d-flex align-items-center gap-2 px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateRuta">
                <span class="material-symbols-rounded">add_location_alt</span>
                Registrar Ruta
            </button>
        </div>
    </div>

    <!-- Barra de Filtros Estilizada -->
    <div class="card border-0 shadow-sm mb-4 rounded-3 pt-1">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('superadmin.rutas.index') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <span class="material-symbols-rounded">map</span>
                        </span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Buscar origen/destino..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="NIT" class="form-select bg-light">
                        <option value="">Empresa (Todas)</option>
                        @foreach($empresas as $emp)
                            <option value="{{ $emp->NIT }}" {{ request('NIT') == $emp->NIT ? 'selected' : '' }}>
                                {{ $emp->nombre_empresa }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="id_estado" class="form-select bg-light">
                        <option value="">Estados (Todos)</option>
                        @foreach($estados as $est)
                            <option value="{{ $est->id_estado }}" {{ request('id_estado') == $est->id_estado ? 'selected' : '' }}>
                                {{ $est->nombre_estado }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 fw-semibold">Consultar</button>
                </div>
                @if(request()->hasAny(['search', 'id_estado', 'NIT']))
                <div class="col-md-1">
                    <a href="{{ route('superadmin.rutas.index') }}" class="btn btn-light w-100 text-muted" title="Restablecer">
                        <span class="material-symbols-rounded" style="font-size: 1.2rem;">filter_list_off</span>
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Tabla con Datos Dinámicos -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted border-0">ID</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Empresa</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Ciudad</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Recorrido (Origen - Destino)</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Estado</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rutas as $ruta)
                        <tr class="border-top">
                            <td class="ps-4 text-muted small fw-bold">#{{ $ruta->id_ruta }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">{{ optional($ruta->empresa)->nombre_empresa ?? '—' }}</span>
                                    <small class="text-muted">NIT: {{ $ruta->NIT }}</small>
                                </div>
                            </td>
                            <td class="text-uppercase small fw-medium text-muted">
                                {{ optional($ruta->ciudad)->nombre_city ?? '—' }}
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="badge bg-primary bg-opacity-10 text-primary fs-xs">ORI</span>
                                        <span class="text-dark fw-semibold">{{ $ruta->origen }}</span>
                                    </div>
                                    <div class="ms-4 small text-muted">
                                        <span class="material-symbols-rounded align-bottom" style="font-size: 0.9rem;">location_on</span>
                                        {{ optional($ruta->barrioOrigen)->nombre ?? '—' }}
                                    </div>
                                    <div class="d-flex align-items-center gap-1 mt-1">
                                        <span class="badge bg-success bg-opacity-10 text-success fs-xs">DES</span>
                                        <span class="text-dark fw-semibold">{{ $ruta->destino }}</span>
                                    </div>
                                    <div class="ms-4 small text-muted">
                                        <span class="material-symbols-rounded align-bottom" style="font-size: 0.9rem;">location_on</span>
                                        {{ optional($ruta->barrioDestino)->nombre ?? '—' }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $bad = match($ruta->id_estado) {
                                        1, 9 => 'success',
                                        2 => 'danger',
                                        default => 'warning'
                                    };
                                @endphp
                                <span class="badge bg-{{ $bad }}-subtle text-{{ $bad }} border border-{{ $bad }} rounded-pill px-3 py-1 fw-bold fs-xs">
                                    {{ optional($ruta->estado)->nombre_estado ?? '—' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-outline-primary btn-sm rounded-3 edit-ruta shadow-sm px-3 fw-semibold" 
                                        data-bs-toggle="modal" data-bs-target="#modalEditRuta"
                                        data-json="{{ json_encode($ruta) }}">
                                    <span class="material-symbols-rounded" style="font-size: 1.1rem; vertical-align: middle;">edit_square</span>
                                    Modificar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted bg-light bg-opacity-50">
                                <span class="material-symbols-rounded display-4 opacity-25">route</span>
                                <p class="mt-2 fw-medium mb-0">No se encontraron rutas con los filtros aplicados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top bg-white">
            <div class="d-flex justify-content-between align-items-center px-2">
                <small class="text-muted">Mostrando {{ $rutas->count() }} resultados de {{ $rutas->total() }}</small>
                <div>{{ $rutas->links() }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal CREAR -->
<div class="modal fade" id="modalCreateRuta" tabindex="-1" aria-labelledby="modalCreateLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalCreateLabel">
                    <span class="material-symbols-rounded align-middle me-2 text-primary">add_location_alt</span>
                    Nueva Ruta de Viaje
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formCreateRuta" action="{{ route('superadmin.rutas.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4 pb-4">
                    <div id="create-errors-alert" class="alert alert-danger d-none shadow-sm py-2 small mb-4"></div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Empresa Responsable <span class="text-danger">*</span></label>
                            <select name="NIT" class="form-select bg-light border-0 py-2" required>
                                <option value="" selected disabled>Seleccionar Empresa...</option>
                                @foreach($empresas as $emp)
                                    <option value="{{ $emp->NIT }}">{{ $emp->nombre_empresa }} ({{ $emp->NIT }})</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback feedback-NIT"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Ciudad <span class="text-danger">*</span></label>
                            <select name="id_ciudad" class="form-select bg-light border-0 py-2" required>
                                <option value="" selected disabled>Seleccionar Ciudad...</option>
                                @foreach($ciudades as $ciu)
                                    <option value="{{ $ciu->id_ciudad }}">{{ $ciu->nombre_city }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback feedback-id_ciudad"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Barrio Origen <span class="text-danger">*</span></label>
                            <select name="id_barrio_origen" class="form-select bg-light border-0 py-2" required>
                                <option value="" selected disabled>Seleccionar...</option>
                                @foreach($barrios as $bar)
                                    <option value="{{ $bar->id_barrio }}">{{ $bar->nombre }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback feedback-id_barrio_origen"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Punto de Origen <span class="text-danger">*</span></label>
                            <input type="text" name="origen" class="form-control bg-light border-0 py-2" required placeholder="Ej: TERMINAL DE TRANSPORTES" style="text-transform:uppercase">
                            <div class="invalid-feedback feedback-origen"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Barrio Destino <span class="text-danger">*</span></label>
                            <select name="id_barrio_destino" class="form-select bg-light border-0 py-2" required>
                                <option value="" selected disabled>Seleccionar...</option>
                                @foreach($barrios as $bar)
                                    <option value="{{ $bar->id_barrio }}">{{ $bar->nombre }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback feedback-id_barrio_destino"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Punto de Destino <span class="text-danger">*</span></label>
                            <input type="text" name="destino" class="form-control bg-light border-0 py-2" required placeholder="Ej: CENTRO COMERCIAL" style="text-transform:uppercase">
                            <div class="invalid-feedback feedback-destino"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Estado de Habilitación <span class="text-danger">*</span></label>
                            <select name="id_estado" class="form-select bg-light border-0 py-2" required>
                                <option value="" selected disabled>Seleccionar...</option>
                                @foreach($estados as $est)
                                    <option value="{{ $est->id_estado }}">{{ $est->nombre_estado }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback feedback-id_estado"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 bg-white d-flex gap-2">
                    <button type="button" class="btn btn-light px-4 fw-semibold border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm flex-fill">Guardar Ruta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EDITAR -->
<div class="modal fade" id="modalEditRuta" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalEditLabel">
                    <span class="material-symbols-rounded align-middle me-2 text-primary">edit_location_alt</span>
                    Modificar Ruta de Viaje
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formEditRuta" method="POST">
                @csrf @method('PUT')
                <div class="modal-body px-4 pb-4">
                    <div id="edit-errors-alert" class="alert alert-danger d-none shadow-sm py-2 small mb-4"></div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Empresa <span class="text-danger">*</span></label>
                            <select name="NIT" id="edit_NIT" class="form-select bg-light border-0 py-2" required>
                                @foreach($empresas as $emp)
                                    <option value="{{ $emp->NIT }}">{{ $emp->nombre_empresa }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback feedback-NIT"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Ciudad <span class="text-danger">*</span></label>
                            <select name="id_ciudad" id="edit_id_ciudad" class="form-select bg-light border-0 py-2" required>
                                @foreach($ciudades as $ciu)
                                    <option value="{{ $ciu->id_ciudad }}">{{ $ciu->nombre_city }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback feedback-id_ciudad"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Barrio Origen <span class="text-danger">*</span></label>
                            <select name="id_barrio_origen" id="edit_id_barrio_origen" class="form-select bg-light border-0 py-2" required>
                                @foreach($barrios as $bar)
                                    <option value="{{ $bar->id_barrio }}">{{ $bar->nombre_barrio }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback feedback-id_barrio_origen"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Punto de Origen <span class="text-danger">*</span></label>
                            <input type="text" name="origen" id="edit_origen" class="form-control bg-light border-0 py-2" required style="text-transform:uppercase">
                            <div class="invalid-feedback feedback-origen"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Barrio Destino <span class="text-danger">*</span></label>
                            <select name="id_barrio_destino" id="edit_id_barrio_destino" class="form-select bg-light border-0 py-2" required>
                                @foreach($barrios as $bar)
                                    <option value="{{ $bar->id_barrio }}">{{ $bar->nombre_barrio }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback feedback-id_barrio_destino"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Punto de Destino <span class="text-danger">*</span></label>
                            <input type="text" name="destino" id="edit_destino" class="form-control bg-light border-0 py-2" required style="text-transform:uppercase">
                            <div class="invalid-feedback feedback-destino"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Estado <span class="text-danger">*</span></label>
                            <select name="id_estado" id="edit_id_estado" class="form-select bg-light border-0 py-2" required>
                                @foreach($estados as $est)
                                    <option value="{{ $est->id_estado }}">{{ $est->nombre_estado }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback feedback-id_estado"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 bg-white d-flex gap-2">
                    <button type="button" class="btn btn-light px-4 fw-semibold border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm flex-fill">Actualizar Datos</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Inicializar Modal de Edición
    document.querySelectorAll('.edit-ruta').forEach(btn => {
        btn.addEventListener('click', function() {
            const data = JSON.parse(this.dataset.json);
            document.getElementById('edit_NIT').value = data.NIT || '';
            document.getElementById('edit_id_ciudad').value = data.id_ciudad || '';
            document.getElementById('edit_id_barrio_origen').value = data.id_barrio_origen || '';
            document.getElementById('edit_origen').value = data.origen || '';
            document.getElementById('edit_id_barrio_destino').value = data.id_barrio_destino || '';
            document.getElementById('edit_destino').value = data.destino || '';
            document.getElementById('edit_id_estado').value = data.id_estado;
            document.getElementById('formEditRuta').action = `/superadmin/rutas/${data.id_ruta}`;
            
            clearValidation('formEditRuta', 'edit-errors-alert');
        });
    });

    // Gestión AJAX Dinámica
    function handleRutaAjax(formId, alertId) {
        const form = document.getElementById(formId);
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = e.submitter;
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Enviando...';
            
            clearValidation(formId, alertId);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (response.ok) {
                    location.reload(); 
                } else if (response.status === 422) {
                    const data = await response.json();
                    showValidationErrors(formId, alertId, data.errors);
                } else {
                    alert('Error técnico en el servidor.');
                }
            } catch (err) {
                console.error(err);
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    }

    function clearValidation(fId, aId) {
        const fm = document.getElementById(fId);
        if(!fm) return;
        fm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        fm.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');
        if(aId) document.getElementById(aId).classList.add('d-none');
    }

    function showValidationErrors(fId, aId, errors) {
        const fm = document.getElementById(fId);
        const al = document.getElementById(aId);
        let errStr = [];

        Object.keys(errors).forEach(f => {
            const inp = fm.querySelector(`[name="${f}"]`) || fm.querySelector(`#edit_${f}`);
            if (inp) {
                inp.classList.add('is-invalid');
                const feed = fm.querySelector(`.feedback-${f}`);
                if (feed) feed.innerText = errors[f][0];
            }
            errStr.push(errors[f][0]);
        });

        if(al) {
            al.innerHTML = `<strong>Atención:</strong> ${errStr[0]}`;
            al.classList.remove('d-none');
        }
    }

    handleRutaAjax('formCreateRuta', 'create-errors-alert');
    handleRutaAjax('formEditRuta', 'edit-errors-alert');
</script>

<style>
    .fs-xs { font-size: 0.75rem; }
    .btn-outline-primary:hover { border-color: transparent !important; }
    .modal-content { border-radius: 1.25rem !important; }
    .table-hover tbody tr:hover { background-color: rgba(94, 84, 142, 0.04) !important; cursor: default; }
</style>
@endpush
@endsection
