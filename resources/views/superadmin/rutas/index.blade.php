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
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <span class="material-symbols-rounded">map</span>
                        </span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Buscar origen/destino..." value="{{ request('search') }}">
                    </div>
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
                @if(request()->hasAny(['search', 'id_estado']))
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
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Ciudad</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Barrio Origen</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Barrio Destino</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Estado</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rutas as $ruta)
                        <tr class="border-top">
                            <td class="ps-4 text-muted small fw-bold">#{{ $ruta->id_ruta }}</td>
                            <td class="text-uppercase small fw-medium text-muted">
                                {{ optional($ruta->ciudad)->nombre_city ?? '—' }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary bg-opacity-10 text-primary fs-xs">ORI</span>
                                    <span class="fw-semibold text-dark">{{ optional($ruta->barrioOrigen)->nombre ?? '—' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success bg-opacity-10 text-success fs-xs">DES</span>
                                    <span class="fw-semibold text-dark">{{ optional($ruta->barrioDestino)->nombre ?? '—' }}</span>
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
                            <label class="form-label small fw-bold text-muted text-uppercase">Ciudad <span class="text-danger">*</span></label>
                            <select name="id_ciudad" class="form-select bg-light border-0 py-2" required>
                                @foreach($ciudades as $ciu)
                                    @if($ciu->id_ciudad == '730001')
                                        <option value="{{ $ciu->id_ciudad }}" selected>{{ $ciu->nombre_city }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="invalid-feedback feedback-id_ciudad"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Barrio Origen <span class="text-danger">*</span></label>
                            <select name="id_barrio_origen" id="create_id_barrio_origen" class="form-select bg-light border-0 py-2" required disabled>
                                <option value="" selected >Seleccionar Ciudad primero...</option>
                            </select>
                            <div class="invalid-feedback feedback-id_barrio_origen"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Barrio Destino <span class="text-danger">*</span></label>
                            <select name="id_barrio_destino" id="create_id_barrio_destino" class="form-select bg-light border-0 py-2" required disabled>
                                <option value="" selected >Seleccionar Ciudad primero...</option>
                            </select>
                            <div class="invalid-feedback feedback-id_barrio_destino"></div>
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
                            <label class="form-label small fw-bold text-muted text-uppercase">Ciudad <span class="text-danger">*</span></label>
                            <select name="id_ciudad" id="edit_id_ciudad" class="form-select bg-light border-0 py-2" required>
                                @foreach($ciudades as $ciu)
                                    @if($ciu->id_ciudad == '730001')
                                        <option value="{{ $ciu->id_ciudad }}" selected>{{ $ciu->nombre_city }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="invalid-feedback feedback-id_ciudad"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Barrio Origen <span class="text-danger">*</span></label>
                            <select name="id_barrio_origen" id="edit_id_barrio_origen" class="form-select bg-light border-0 py-2" required>
                                <option value="" selected disabled>Seleccionar...</option>
                            </select>
                            <div class="invalid-feedback feedback-id_barrio_origen"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Barrio Destino <span class="text-danger">*</span></label>
                            <select name="id_barrio_destino" id="edit_id_barrio_destino" class="form-select bg-light border-0 py-2" required>
                                <option value="" selected disabled>Seleccionar...</option>
                            </select>
                            <div class="invalid-feedback feedback-id_barrio_destino"></div>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Modal de Edición
        document.querySelectorAll('.edit-ruta').forEach(btn => {
            btn.addEventListener('click', async function() {
                const data = JSON.parse(this.dataset.json);
                const form = document.getElementById('formEditRuta');
                
                form.querySelector('[name="id_ciudad"]').value = data.id_ciudad || '';
                form.querySelector('[name="id_estado"]').value = data.id_estado;
                
                // Cargar barrios de la ciudad seleccionada y luego setear los valores
                await loadBarrios(data.id_ciudad, 'edit_id_barrio_origen', data.id_barrio_origen);
                await loadBarrios(data.id_ciudad, 'edit_id_barrio_destino', data.id_barrio_destino);
                
                form.action = `/superadmin/rutas/${data.id_ruta}`;
                clearValidation('formEditRuta', 'edit-errors-alert');
            });
        });

        // Escuchar cambios en ciudad para cargar barrios (Crear)
        document.querySelector('#formCreateRuta [name="id_ciudad"]').addEventListener('change', function() {
            loadBarrios(this.value, 'create_id_barrio_origen');
            loadBarrios(this.value, 'create_id_barrio_destino');
        });

        // Escuchar cambios en ciudad para cargar barrios (Editar)
        document.querySelector('#formEditRuta [name="id_ciudad"]').addEventListener('change', function() {
            loadBarrios(this.value, 'edit_id_barrio_origen');
            loadBarrios(this.value, 'edit_id_barrio_destino');
        });

        async function loadBarrios(ciudadId, selectId, currentValue = null) {
            const select = document.getElementById(selectId);
            if (!ciudadId) {
                select.innerHTML = '<option value="" selected disabled>Seleccionar...</option>';
                return;
            }

            select.disabled = true;
            select.innerHTML = '<option value="">Cargando...</option>';

            try {
                const response = await fetch(`/superadmin/rutas/barrios/${ciudadId}`);
                const barrios = await response.json();

                let options = '<option value="" selected disabled>Seleccionar...</option>';
                barrios.forEach(b => {
                    options += `<option value="${b.id_barrio}" ${currentValue == b.id_barrio ? 'selected' : ''}>${b.nombre}</option>`;
                });
                select.innerHTML = options;
            } catch (error) {
                console.error('Error cargando barrios:', error);
                select.innerHTML = '<option value="" disabled>Error al cargar</option>';
            } finally {
                select.disabled = false;
            }
        }

        // Gestión AJAX Dinámica
        function handleRutaAjax(formId, alertId) {
            const form = document.getElementById(formId);
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = e.submitter;
                const originalText = btn.innerHTML;
                
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Procesando...';
                
                clearValidation(formId, alertId);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (response.ok) {
                        const resData = await response.json();
                        // Mostrar éxito antes de recargar
                        btn.classList.replace('btn-primary', 'btn-success');
                        btn.innerHTML = '<span class="material-symbols-rounded align-middle">check_circle</span> Guardado';
                        
                        setTimeout(() => location.reload(), 800);
                    } else if (response.status === 422) {
                        const data = await response.json();
                        showValidationErrors(formId, alertId, data.errors);
                    } else {
                        const errorData = await response.json();
                        alert('Error: ' + (errorData.message || 'Error técnico en el servidor.'));
                    }
                } catch (err) {
                    console.error(err);
                    alert('Error de conexión.');
                } finally {
                    if (!document.getElementById(formId).querySelector('.is-invalid')) {
                        // Solo restaurar si no hay errores (si hay errores se queda el botón para reintentar)
                    }
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
            if(aId) {
                const al = document.getElementById(aId);
                al.classList.add('d-none');
                al.innerHTML = '';
            }
        }

        function showValidationErrors(fId, aId, errors) {
            const fm = document.getElementById(fId);
            const al = document.getElementById(aId);
            let firstError = null;

            Object.keys(errors).forEach(f => {
                // Mapear campos a sus respectivos inputs en CREATE y EDIT
                let inp = fm.querySelector(`[name="${f}"]`);
                
                if (inp) {
                    inp.classList.add('is-invalid');
                    const feed = fm.querySelector(`.feedback-${f}`);
                    if (feed) {
                        feed.innerText = errors[f][0];
                        feed.style.display = 'block';
                    }
                    if (!firstError) firstError = errors[f][0];
                }
            });

            if(al && firstError) {
                al.innerHTML = `<div class="d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded">error</span>
                    <span>${firstError}</span>
                </div>`;
                al.classList.remove('d-none');
            }
        }

        handleRutaAjax('formCreateRuta', 'create-errors-alert');
        handleRutaAjax('formEditRuta', 'edit-errors-alert');

        // Cargar barrios de Ibagué (73001) automáticamente al abrir el modal de creación
        const modalCreate = document.getElementById('modalCreateRuta');
        if (modalCreate) {
            modalCreate.addEventListener('show.bs.modal', function() {
                loadBarrios('730001', 'create_id_barrio_origen');
                loadBarrios('730001', 'create_id_barrio_destino');
            });
        }
    });
</script>

<style>
    .fs-xs { font-size: 0.75rem; }
    .btn-outline-primary:hover { border-color: transparent !important; }
    .modal-content { border-radius: 1.25rem !important; }
    .table-hover tbody tr:hover { background-color: rgba(94, 84, 142, 0.04) !important; cursor: default; }
    .is-invalid { border-color: #dc3545 !important; background-image: none !important; }
    .invalid-feedback { font-weight: 500; font-size: 0.8rem; }
</style>
@endpush
@endsection
