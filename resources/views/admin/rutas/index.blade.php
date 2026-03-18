@extends('admin.layouts.app')

@section('title', 'Rutas — SIGU')

@section('content')
<div class="container-fluid pt-0 pb-4">
    <!-- Header de Página con Botones de Acción -->
    <div class="d-flex align-items-center justify-content-between mb-4 px-1">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold">Mapa de Rutas</h1>
            <p class="text-muted small mb-0">Gestión global de destinos y recorridos autorizados por empresa</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.rutas.export', request()->all()) }}" class="btn btn-outline-success d-flex align-items-center gap-2 px-3 fw-semibold">
                <span class="material-symbols-rounded" style="font-size: 1.2rem;">download</span>
                Exportar Excel
            </a>
        </div>
    </div>

    <!-- Barra de Filtros Estilizada -->
    <div class="card border-0 shadow-sm mb-4 rounded-3 pt-1">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.rutas.index') }}" class="row g-4 align-items-end">
                <!-- Primera Fila: Filtros Rápidos -->
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-1">Código</label>
                    <input type="text" name="codigo_ruta" class="form-control bg-light border-0" placeholder="Ej: 101" value="{{ request('codigo_ruta') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-1">Buscar Trayecto (Origen → Destino)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 text-muted">
                            <span class="material-symbols-rounded fs-5">search</span>
                        </span>
                        <input type="text" name="trayecto" class="form-control bg-light border-0" placeholder="Ej: Jordán Centro" value="{{ request('trayecto') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-1">Ciudad</label>
                    <select name="id_ciudad" class="form-select bg-light border-0">
                        <option value="">Todas...</option>
                        @foreach($ciudades as $ciu)
                        <option value="{{ $ciu->id_ciudad }}" {{ request('id_ciudad') == $ciu->id_ciudad ? 'selected' : '' }}>
                            {{ $ciu->nombre_city }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Segunda Fila: Puntos Específicos -->
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-1">Barrio Origen</label>
                    <select name="id_barrio_origen" class="form-select bg-light border-0">
                        <option value="">Origen...</option>
                        @foreach($barrios as $bar)
                        <option value="{{ $bar->id_barrio }}" {{ request('id_barrio_origen') == $bar->id_barrio ? 'selected' : '' }}>
                            {{ $bar->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-1">Barrio Destino</label>
                    <select name="id_barrio_destino" class="form-select bg-light border-0">
                        <option value="">Destino...</option>
                        @foreach($barrios as $bar)
                        <option value="{{ $bar->id_barrio }}" {{ request('id_barrio_destino') == $bar->id_barrio ? 'selected' : '' }}>
                            {{ $bar->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Botones -->
                <div class="col-md-1 d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-3" title="Aplicar Filtros">
                        <span class="material-symbols-rounded">filter_alt</span>
                    </button>
                    @if(request()->hasAny(['codigo_ruta', 'id_ciudad', 'id_barrio_origen', 'id_barrio_destino', 'trayecto']))
                        <a href="{{ route('admin.rutas.index') }}" class="btn btn-outline-secondary border-0" title="Limpiar todo">
                            <span class="material-symbols-rounded">close</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla con Datos Dinámicos -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted border-0">CODIGO</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Ciudad</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Barrio Origen</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Barrio Destino</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0 pe-4">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rutas as $ruta)
                    <tr class="border-top">
                        <td class="ps-4 text-muted small fw-bold">#{{ $ruta->codigo_ruta }}</td>
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
                        <td class="pe-4">
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
<div class="modal fade" id="modalCreateRuta" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small">
                    <span class="material-symbols-rounded text-primary me-2 fs-5">add_location_alt</span>
                    REGISTRAR NUEVA RUTA
                </h6>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formCreateRuta" action="{{ route('admin.rutas.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div id="create-errors-alert" class="alert alert-danger d-none shadow-sm py-2 small mb-4"></div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Ciudad Destino <span class="text-danger">*</span></label>
                            <select name="id_ciudad" class="form-select form-select-sm" required>
                                @foreach($ciudades as $ciu)
                                @if($ciu->id_ciudad == '730001')
                                <option value="{{ $ciu->id_ciudad }}" selected>{{ $ciu->nombre_city }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 text-input-validate" data-type="number">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Código Celda <span class="text-danger">*</span></label>
                            <input type="text" name="codigo_ruta" class="form-control form-control-sm" required placeholder="00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Estado <span class="text-danger">*</span></label>
                            <select name="id_estado" class="form-select form-select-sm" required>
                                <option value="" selected disabled>Seleccionar...</option>
                                @foreach($estados as $est)
                                <option value="{{ $est->id_estado }}">{{ $est->nombre_estado }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Barrio Origen <span class="text-danger">*</span></label>
                            <select name="id_barrio_origen" id="create_id_barrio_origen" class="form-select form-select-sm" required>
                                <option value="" selected>Seleccionar...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Barrio Destino <span class="text-danger">*</span></label>
                            <select name="id_barrio_destino" id="create_id_barrio_destino" class="form-select form-select-sm" required disabled>
                                <option value="" selected>Seleccionar...</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-sm btn-light px-3 fw-bold" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm">GUARDAR RUTA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EDITAR -->
<div class="modal fade" id="modalEditRuta" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small">
                    <span class="material-symbols-rounded text-warning me-2 fs-5">edit_location_alt</span>
                    MODIFICAR RUTA
                </h6>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formEditRuta" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div id="edit-errors-alert" class="alert alert-danger d-none shadow-sm py-2 small mb-4"></div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Ciudad Destino <span class="text-danger">*</span></label>
                            <select name="id_ciudad" id="edit_id_ciudad" class="form-select form-select-sm" required>
                                @foreach($ciudades as $ciu)
                                    <option value="{{ $ciu->id_ciudad }}">{{ $ciu->nombre_city }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 text-input-validate" data-type="number">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Código Celda <span class="text-danger">*</span></label>
                            <input type="text" name="codigo_ruta" id="edit_codigo_ruta" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Estado <span class="text-danger">*</span></label>
                            <select name="id_estado" id="edit_id_estado" class="form-select form-select-sm" required>
                                @foreach($estados as $est)
                                    <option value="{{ $est->id_estado }}">{{ $est->nombre_estado }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Barrio Origen <span class="text-danger">*</span></label>
                            <select name="id_barrio_origen" id="edit_id_barrio_origen" class="form-select form-select-sm" required></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Barrio Destino <span class="text-danger">*</span></label>
                            <select name="id_barrio_destino" id="edit_id_barrio_destino" class="form-select form-select-sm" required></select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-sm btn-light px-3 fw-bold" data-bs-dismiss="modal">DESCARTAR</button>
                    <button type="submit" class="btn btn-sm btn-warning px-4 fw-bold shadow-sm">GUARDAR CAMBIOS</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal VER DETALLES -->
<div class="modal fade" id="modalViewRuta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small">
                    <span class="material-symbols-rounded text-info me-2 fs-5">visibility</span>
                    DETALLES DEL RECORRIDO
                </h6>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                            <span class="material-symbols-rounded fs-1">tag</span>
                        </div>
                        <div>
                            <h4 id="view_codigo_ruta" class="fw-bold mb-0 text-dark"></h4>
                            <p class="text-muted small mb-0" id="view_ciudad"></p>
                        </div>
                    </div>
                    <span id="view_estado" class="badge rounded-pill"></span>
                </div>

                <div class="p-3 bg-light rounded-3 border border-light-subtle">
                    <div class="position-relative ps-4 py-2 mt-2">
                        <div class="position-absolute h-100 border-start border-2 border-dashed border-primary border-opacity-20" style="left: 7px; top: 0;"></div>
                        
                        <div class="mb-4">
                            <div class="position-absolute bg-primary rounded-circle" style="left: 2px; width: 12px; height: 12px; border: 2px solid #fff;"></div>
                            <label class="d-block text-muted small fw-bold text-uppercase ls-1">Punto de Partida</label>
                            <span id="view_origen" class="fw-bold text-dark fs-6 d-block"></span>
                        </div>

                        <div>
                            <div class="position-absolute bg-success rounded-circle" style="left: 2px; width: 12px; height: 12px; border: 2px solid #fff;"></div>
                            <label class="d-block text-muted small fw-bold text-uppercase ls-1">Destino Final</label>
                            <span id="view_destino" class="fw-bold text-dark fs-6 d-block"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer border-0 p-3 bg-light">
                <button type="button" class="btn btn-sm btn-dark w-100 fw-bold" data-bs-dismiss="modal">CERRAR DETALLES</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Rutas script initialized');

        // Delegación de eventos (Top-level Sync)
        document.addEventListener('click', function(e) {
            const btnVer = e.target.closest('.view-ruta');
            if (btnVer) {
                e.preventDefault();
                console.log('View button clicked', btnVer.dataset.json);
                handleVerRuta(btnVer);
                return;
            }

            const btnEdit = e.target.closest('.edit-ruta');
            if (btnEdit) {
                e.preventDefault();
                console.log('Edit button clicked', btnEdit.dataset.json);
                handleEditRuta(btnEdit);
                return;
            }
        });

       function handleVerRuta(btn) {
            try {
                const data = JSON.parse(btn.dataset.json);
                
                // Asignar textos
                document.getElementById('view_codigo_ruta').textContent = `#${data.codigo_ruta}`;
                document.getElementById('view_ciudad').textContent = btn.dataset.ciudad || '—';
                document.getElementById('view_origen').textContent = btn.dataset.origen || '—';
                document.getElementById('view_destino').textContent = btn.dataset.destino || '—';
                
                // Manejo de insignias de estado
                const viewEst = document.getElementById('view_estado');
                viewEst.textContent = btn.dataset.estado || '—';
                
                // Reset de clases para evitar acumulación
                viewEst.className = 'badge rounded-pill px-3 py-2 fw-bold';
                
                const idEst = parseInt(data.id_estado);
                if (idEst === 1 || idEst === 9) {
                    viewEst.classList.add('bg-success-subtle', 'text-success', 'border', 'border-success-subtle');
                } else if (idEst === 2) {
                    viewEst.classList.add('bg-danger-subtle', 'text-danger', 'border', 'border-danger-subtle');
                } else {
                    viewEst.classList.add('bg-warning-subtle', 'text-warning', 'border', 'border-warning-subtle');
                }

                const modalEl = document.getElementById('modalViewRuta');
                const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                modalInstance.show();
            } catch (err) { 
                console.error('Error al mostrar vista:', err); 
            }
        }

        async function handleEditRuta(btn) {
            try {
                const data = JSON.parse(btn.dataset.json);
                const form = document.getElementById('formEditRuta');
                if (!form) return;

                clearValidation('formEditRuta', 'edit-errors-alert');
                form.querySelector('#edit_codigo_ruta').value = data.codigo_ruta;
                form.querySelector('#edit_id_ciudad').value = data.id_ciudad;
                form.querySelector('[name="id_estado"]').value = data.id_estado;

                // Cargar barrios antes de mostrar el modal
                await loadBarrios(data.id_ciudad, 'edit_id_barrio_origen', data.id_barrio_origen);
                await loadBarrios(data.id_ciudad, 'edit_id_barrio_destino', data.id_barrio_destino);

                form.action = `rutas/${data.id_ruta}`; // Ruta relativa
                
                const modalEl = document.getElementById('modalEditRuta');
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            } catch (err) { console.error('Edit Ruta Error:', err); }
        }

        // Carga de barrios por Ciudad
        async function loadBarrios(ciudadId, selectId, currentValue = null) {
            const select = document.getElementById(selectId);
            if (!select) return;
            if (!ciudadId) {
                select.innerHTML = '<option value="" selected disabled>Seleccionar...</option>';
                return;
            }

            select.disabled = true;
            select.innerHTML = '<option value="">Cargando...</option>';

            try {
                // USAR RUTA RELATIVA para evitar problemas con subcarpetas
                const response = await fetch(`rutas/barrios/${ciudadId}`);
                if (!response.ok) throw new Error('Error en red');
                const barrios = await response.json();

                let options = '<option value="" selected disabled>Seleccionar...</option>';
                barrios.forEach(b => {
                    options += `<option value="${b.id_barrio}" ${currentValue == b.id_barrio ? 'selected' : ''}>${b.nombre}</option>`;
                });
                select.innerHTML = options;
            } catch (error) {
                console.error('Fetch barrios failed:', error);
                select.innerHTML = '<option value="" disabled>Error al cargar</option>';
            } finally {
                select.disabled = false;
            }
        }

        // Listeners manuales para el select de ciudad
        const cCreate = document.querySelector('#formCreateRuta [name="id_ciudad"]');
        if (cCreate) cCreate.addEventListener('change', function() {
            loadBarrios(this.value, 'create_id_barrio_origen');
            loadBarrios(this.value, 'create_id_barrio_destino');
        });

        const cEdit = document.querySelector('#formEditRuta [name="id_ciudad"]');
        if (cEdit) cEdit.addEventListener('change', function() {
            loadBarrios(this.value, 'edit_id_barrio_origen');
            loadBarrios(this.value, 'edit_id_barrio_destino');
        });

        // Configurar AJAX para forms
        handleRutaAjax('formCreateRuta', 'create-errors-alert');
        handleRutaAjax('formEditRuta', 'edit-errors-alert');

        function handleRutaAjax(fId, aId) {
            const form = document.getElementById(fId);
            if (!form) return;
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = e.submitter;
                if (!btn) return;
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (response.ok) {
                        btn.innerHTML = 'Listo';
                        setTimeout(() => location.reload(), 500);
                    } else if (response.status === 422) {
                        const res = await response.json();
                        showValidationErrors(fId, aId, res.errors);
                    }
                } catch (err) { console.error('Ajax error:', err); }
                finally { btn.disabled = false; btn.innerHTML = originalText; }
            });
        }

        function clearValidation(fId, aId) {
            const fm = document.getElementById(fId);
            if (fm) fm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            const al = document.getElementById(aId);
            if (al) al.classList.add('d-none');
        }

        function showValidationErrors(fId, aId, errors) {
            const fm = document.getElementById(fId);
            const al = document.getElementById(aId);
            Object.keys(errors).forEach(f => {
                let inp = fm.querySelector(`[name="${f}"]`);
                if (inp) {
                    inp.classList.add('is-invalid');
                    const feed = fm.querySelector(`.feedback-${f}`);
                    if (feed) feed.innerText = errors[f][0];
                }
            });
            if (al) {
                al.innerText = Object.values(errors)[0][0];
                al.classList.remove('d-none');
            }
        }

        const modalCreate = document.getElementById('modalCreateRuta');
        if (modalCreate) {
            modalCreate.addEventListener('show.bs.modal', function() {
                loadBarrios('730001', 'create_id_barrio_origen');
                loadBarrios('730001', 'create_id_barrio_destino');
            });
        }

        // Validaciones de Entrada (Solo números)
        document.querySelectorAll('.text-input-validate').forEach(container => {
            const input = container.querySelector('input');
            const type = container.getAttribute('data-type');
            
            if (input) {
                input.addEventListener('input', function(e) {
                    if (type === 'number') {
                        this.value = this.value.replace(/[^0-9]/g, '');
                    }
                });
            }
        });
    });
</script>

<style>
    .fs-xs {
        font-size: 0.75rem;
    }

    .ls-1 {
        letter-spacing: 0.5px;
    }

    .fw-extrabold {
        font-weight: 800;
    }

    .btn-outline-primary:hover {
        border-color: transparent !important;
    }

    .rounded-4 {
        border-radius: 1rem !important;
    }

    .modal-content {
        border-radius: 1.5rem !important;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(94, 84, 142, 0.04) !important;
        cursor: default;
    }

    .is-invalid {
        border-color: #dc3545 !important;
        background-image: none !important;
    }

    .invalid-feedback {
        font-weight: 500;
        font-size: 0.8rem;
    }

    .bg-info-subtle {
        background-color: #e1f5fe !important;
    }
    
    .text-info {
        color: #0288d1 !important;
    }
</style>
@endpush
@endsection