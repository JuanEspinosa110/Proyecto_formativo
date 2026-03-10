@extends('superadmin.layouts.admin')

@section('title', 'Gestión de Barrios — SIGU')

@section('content')
<div class="sigu-fade">

    <!-- CABECERA DE PÁGINA -->
    <div class="sigu-page-hd d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="sigu-page-title d-flex align-items-center gap-2">
                <span class="material-symbols-rounded">location_city</span>
                Gestión de Barrios
            </h1>
            <p class="sigu-page-sub">Panel de configuración de barrios por ciudad — Módulo Configuración</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('superadmin.configuracion.barrios.export') }}" class="btn btn-outline-success d-flex align-items-center gap-2 px-3 shadow-sm border-2">
                <span class="material-symbols-rounded fs-5">file_download</span>
                <span class="fw-semibold">Exportar Excel</span>
            </a>
            <button type="button" class="btn btn-primary d-flex align-items-center gap-2 px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCrear">
                <span class="material-symbols-rounded fs-5">add</span>
                <span class="fw-semibold">Nuevo Barrio</span>
            </button>
        </div>
    </div>

    <!-- ALERTAS Y MENSAJES -->
    <div class="mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center" role="alert">
                <span class="material-symbols-rounded me-2">check_circle</span>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center" role="alert">
                <span class="material-symbols-rounded me-2">error</span>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- TARJETA PRINCIPAL -->
    <div class="card border-0 shadow-sm rounded-4 mt-4 overflow-hidden">
        <div class="card-body p-0">
            
            <!-- FILTROS -->
            <div class="p-4 border-bottom bg-light bg-opacity-50">
                <form action="{{ route('superadmin.configuracion.barrios.index') }}" method="GET" class="row g-3">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0">
                                <span class="material-symbols-rounded text-muted">search</span>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Buscar barrio por nombre..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-secondary px-4 fw-semibold shadow-sm">Filtrar</button>
                        @if(request('search'))
                            <a href="{{ route('superadmin.configuracion.barrios.index') }}" class="btn btn-link text-decoration-none text-muted fw-medium">Limpiar</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- TABLA -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-muted text-uppercase fs-xs fw-bold" style="width: 100px;">ID</th>
                            <th class="text-muted text-uppercase fs-xs fw-bold">Nombre del Barrio</th>
                            <th class="text-muted text-uppercase fs-xs fw-bold">Ciudad</th>
                            <th class="text-end pe-4 text-muted text-uppercase fs-xs fw-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($barrios as $barrio)
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-light text-dark border px-2 py-1 font-monospace">#{{ $barrio->id_barrio }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $barrio->nombre }}</span>
                                </td>
                                <td>
                                    <div class="d-inline-flex align-items-center gap-2 py-1 px-2 bg-light rounded-pill border">
                                        <span class="material-symbols-rounded text-primary fs-6">location_on</span>
                                        <span class="text-muted small fw-medium">{{ optional($barrio->ciudad)->nombre_city ?? 'Sin ciudad' }}</span>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-3">
                                        <a href="#" 
                                           class="text-primary text-decoration-none d-flex align-items-center"
                                           onclick="editarBarrio({{ $barrio->id_barrio }}, '{{ addslashes($barrio->nombre) }}', '{{ $barrio->id_ciudad }}')"
                                           title="Editar barrio">
                                            <span class="material-symbols-rounded fs-5">edit</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="material-symbols-rounded fs-1 text-muted mb-2 opacity-25">location_off</span>
                                        <h5 class="text-muted fw-normal">No se encontraron resultados</h5>
                                        <p class="text-muted small">Intente con otros términos de búsqueda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINACIÓN -->
            @if($barrios->hasPages())
                <div class="p-4 border-top bg-light bg-opacity-25">
                    {{ $barrios->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL CREAR --}}
<div class="modal fade" id="modalCrear" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small">
                    <span class="material-symbols-rounded text-primary me-2 fs-5">add_location_alt</span>
                    REGISTRAR NUEVO BARRIO
                </h6>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ route('superadmin.configuracion.barrios.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3 text-input-validate" data-type="text">
                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">Nombre del Barrio</label>
                        <input type="text" name="nombre" class="form-control form-control-sm @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" placeholder="Ej: Chapinero" required maxlength="100">
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">Ciudad Asociada</label>
                        <select name="id_ciudad" class="form-select form-select-sm @error('id_ciudad') is-invalid @enderror" required>
                            <option value="" disabled selected>Seleccione...</option>
                            @foreach($ciudades as $ciudad)
                                <option value="{{ $ciudad->id_ciudad }}" {{ old('id_ciudad') == $ciudad->id_ciudad ? 'selected' : '' }}>
                                    {{ $ciudad->nombre_city }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_ciudad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-sm btn-light px-3 fw-bold" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm">CREAR BARRIO</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDITAR --}}
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small">
                    <span class="material-symbols-rounded text-warning me-2 fs-5">edit_location</span>
                    MODIFICAR BARRIO
                </h6>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formEditar" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3 text-input-validate" data-type="text">
                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">Nombre del Barrio</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control form-control-sm shadow-none" required maxlength="100">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">Ciudad Asociada</label>
                        <select name="id_ciudad" id="edit_id_ciudad" class="form-select form-select-sm shadow-none" required>
                            @foreach($ciudades as $ciudad)
                                <option value="{{ $ciudad->id_ciudad }}">{{ $ciudad->nombre_city }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-sm btn-light px-3 fw-bold" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm">GUARDAR CAMBIOS</button>
                </div>
            </form>
        </div>
    </div>
</div>


<style>
    .fs-xs { font-size: 0.75rem; }
    .ls-1 { letter-spacing: 0.5px; }
    .table-hover tbody tr:hover {
        background-color: rgba(94, 84, 142, 0.03) !important;
        cursor: default;
    }
</style>

@push('scripts')
<script>
    /**
     * Prepara el modal de edición con los datos del registro
     */
    function editarBarrio(id, nombre, idCiudad) {
        const form = document.getElementById('formEditar');
        form.action = "{{ url('superadmin/configuracion/barrios') }}/" + id;
        document.getElementById('edit_nombre').value = nombre;
        document.getElementById('edit_id_ciudad').value = idCiudad;
        
        const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
        modal.show();
    }

    // Validaciones de Entrada (Solo texto)
    document.querySelectorAll('.text-input-validate').forEach(container => {
        const input = container.querySelector('input');
        const type = container.getAttribute('data-type');
        
        if (input) {
            input.addEventListener('input', function(e) {
                if (type === 'text') {
                    this.value = this.value.replace(/[0-9]/g, '');
                }
            });
        }
    });

    // Auto-open modal si hay errores de validación
    @if($errors->any() && !old('_method'))
        window.addEventListener('load', () => {
            const modal = new bootstrap.Modal(document.getElementById('modalCrear'));
            modal.show();
        });
    @endif
</script>
@endpush
@endsection
