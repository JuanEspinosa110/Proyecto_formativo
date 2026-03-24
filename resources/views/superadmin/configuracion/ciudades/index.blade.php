@extends('superadmin.layouts.admin')

@section('content')


<div class="container-fluid px-4">
    <div class="row mt-4">
        <div class="col-12">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                @foreach ($errors->all() as $error)
                {{ $error }}
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            {{-- HEADER Y BOTONES --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Ciudades</h1>
                <div>
                    <button type="button" class="btn btn-outline-secondary shadow-sm" data-bs-toggle="modal" data-bs-target="#deptoModal">
                        <i class="fas fa-map-marked-alt me-1"></i> Nuevo Depto
                    </button>
                    <button type="button" class="btn btn-primary shadow-sm ms-2" data-bs-toggle="modal" data-bs-target="#crearModal">
                        <i class="fas fa-plus me-1"></i> Crear Ciudad
                    </button>
                    <a href="{{ route('superadmin.configuracion.ciudades.export', ['filtro_ciudad' => request('filtro_ciudad'), 'filtro_depto' => request('filtro_depto')]) }}"
                        class="btn btn-success shadow-sm ms-2">
                        <i class="fas fa-file-excel me-1"></i> Exportar Excel
                    </a>
                </div>
            </div>

            {{-- BUSCADOR --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('superadmin.configuracion.ciudades.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="small text-muted text-uppercase fw-bold">Ciudad / ID</label>
                            <input type="text" name="filtro_ciudad" value="{{ request('filtro_ciudad') }}" class="form-control" placeholder="Ej: Medellín o 05001">
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted text-uppercase fw-bold">Departamento / ID</label>
                            <input type="text" name="filtro_depto" value="{{ request('filtro_depto') }}" class="form-control" placeholder="Ej: Antioquia o 05">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-secondary w-100">Filtrar</button>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <a href="{{ route('superadmin.configuracion.ciudades.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- TABLA --}}
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted text-uppercase small">
                                <tr>
                                    <th class="px-4 py-2" style="width: 100px;">ID</th>
                                    <th class="py-2">Ciudad</th>
                                    <th class="py-2">Departamento</th>
                                    <th class="py-2 text-end px-4" style="width: 150px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse($ciudades as $ciudad)
                                <tr>
                                    <td class="px-4 py-2 fw-bold text-dark">{{ $ciudad->id_ciudad }}</td>
                                    <td class="py-2">{{ $ciudad->nombre_city }}</td>
                                    <td class="py-2">{{ $ciudad->departamento->nombre_departamento ?? 'Sin Departamento' }}</td>
                                    <td class="text-end px-4 py-2">
                                        <div class="d-flex justify-content-end gap-3">
                                            <a href="#"
                                               class="text-primary text-decoration-none d-flex align-items-center"
                                               data-bs-toggle="modal"
                                               data-bs-target="#editarModal"
                                               data-id="{{ $ciudad->id_ciudad }}"
                                               data-nombre="{{ $ciudad->nombre_city }}"
                                               data-depto="{{ $ciudad->id_departamento }}"
                                               title="Editar ciudad">
                                                <span class="material-symbols-rounded fs-5">edit</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fas fa-folder-open d-block mb-2 fa-2x"></i>
                                        No se encontraron registros.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($ciudades->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    <div class="d-flex justify-content-center">
                        {{ $ciudades->appends(request()->query())->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- MODAL CREAR DEPARTAMENTO --}}
<div class="modal fade" id="deptoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small">
                    <span class="material-symbols-rounded text-secondary me-2 fs-5">map_marked</span>
                    NUEVO DEPARTAMENTO
                </h6>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ route('superadmin.configuracion.ciudades.storeDepartamento') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Código ID (2 dígitos) <span class="text-danger">*</span></label>
<<<<<<< HEAD
                        <input type="text" name="id_departamento" class="form-control @error('id_departamento') is-invalid @enderror"
                            placeholder="Ej: 05" maxlength="2" required value="{{ old('id_departamento') }}">
                        @error('id_departamento')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Nombre del Departamento <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_departamento" class="form-control @error('nombre_departamento') is-invalid @enderror" placeholder="Ej: Antioquia" required value="{{ old('nombre_departamento') }}">
                        @error('nombre_departamento')
=======
                        <input type="text" name="id_departamento" id="id_departamento" inputmode="numeric" class="form-control @error('id_departamento') is-invalid @enderror"
                            placeholder="Ej: 05" maxlength="2" required value="{{ old('id_departamento') }}">
                        @error('id_departamento')
>>>>>>> origin/feature/modulo-pasajeros
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-0 text-input-validate" data-type="text">
                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">Nombre del Departamento <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_departamento" class="form-control form-control-sm @error('nombre_departamento') is-invalid @enderror" placeholder="Ej: Antioquia" required value="{{ old('nombre_departamento') }}">
                        @error('nombre_departamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-sm btn-light px-3 fw-bold" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-sm btn-secondary px-4 fw-bold shadow-sm">GUARDAR DEPTO</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL CREAR CIUDAD --}}
<div class="modal fade" id="crearModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small">
                    <span class="material-symbols-rounded text-primary me-2 fs-5">add_location_alt</span>
                    REGISTRAR NUEVA CIUDAD
                </h6>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ route('superadmin.configuracion.ciudades.store') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Código Postal <span class="text-danger">*</span></label>
                        <input type="text" name="id_ciudad" id="id_ciudad" inputmode="numeric" class="form-control @error('id_ciudad') is-invalid @enderror" placeholder="Ej: 05001" required value="{{ old('id_ciudad') }}">
                        @error('id_ciudad')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre de la Ciudad <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_city" id="nombre_city" class="form-control @error('nombre_city') is-invalid @enderror" placeholder="Ej: Medellín" required value="{{ old('nombre_city') }}">
                        @error('nombre_city')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Departamento <span class="text-danger">*</span></label>
                        <select name="id_departamento" id="id_departamento" class="form-select @error('id_departamento') is-invalid @enderror" required>
                            <option value="">Seleccione...</option>
                            @foreach($departamentos as $depto)
                            <option value="{{ $depto->id_departamento }}" {{ old('id_departamento') == $depto->id_departamento ? 'selected' : '' }}>
                                {{ $depto->nombre_departamento }}
                            </option>
                            @endforeach
                        </select>
                        @error('id_departamento')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-sm btn-light px-3 fw-bold" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm">GUARDAR CIUDAD</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDITAR --}}
<div class="modal fade" id="editarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center small">
                    <span class="material-symbols-rounded text-warning me-2 fs-5">edit_location</span>
                    MODIFICAR CIUDAD
                </h6>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formEditar" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3 text-input-validate" data-type="text">
                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">Nombre de la Ciudad <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_city" id="editNombre" class="form-control form-control-sm @error('nombre_city') is-invalid @enderror" required>
                        @error('nombre_city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">Departamento <span class="text-danger">*</span></label>
                        <select name="id_departamento" id="editDepto" class="form-select form-select-sm @error('id_departamento') is-invalid @enderror" required>
                            @foreach($departamentos as $depto)
                            <option value="{{ $depto->id_departamento }}">{{ $depto->nombre_departamento }}</option>
                            @endforeach
                        </select>
                        @error('id_departamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editarModal = document.getElementById('editarModal');
        if (editarModal) {
            editarModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                if (!button) return;

                var id = button.getAttribute('data-id');
                var nombre = button.getAttribute('data-nombre');
                var depto = button.getAttribute('data-depto');

                var form = document.getElementById('formEditar');
                form.action = "{{ url('superadmin/configuracion/ciudades') }}/" + id;

                document.getElementById('editNombre').value = nombre;
                document.getElementById('editDepto').value = depto;

                // Save ID for reopening on validation error
                sessionStorage.setItem('last_edit_id_ciudad', id);
            });
        }

        // Lista de IDs de los inputs que quieres restringir a solo números
        const camposNumericos = ['id_departamento', 'id_ciudad'];

        camposNumericos.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('input', function(e) {
                    // Reemplaza cualquier cosa que NO sea un número (0-9) con un string vacío
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }
        });

        // Redirect error to correct modal
        @if($errors->any())
        @if(old('_method') == 'PUT')
        var lastEditId = sessionStorage.getItem('last_edit_id_ciudad');
        if (lastEditId) {
            var form = document.getElementById('formEditar');
            form.action = "{{ url('superadmin/configuracion/ciudades') }}/" + lastEditId;
            var myModal = new bootstrap.Modal(document.getElementById('editarModal'));
            myModal.show();
        }
        @elseif(old('nombre_departamento') || old('id_departamento'))
        var myModal = new bootstrap.Modal(document.getElementById('deptoModal'));
        myModal.show();
        @else
        var myModal = new bootstrap.Modal(document.getElementById('crearModal'));
        myModal.show();
        @endif
        @endif

        // Validaciones de Entrada (Solo números/texto)
        document.querySelectorAll('.text-input-validate').forEach(container => {
            const input = container.querySelector('input');
            const type = container.getAttribute('data-type');

            if (input) {
                input.addEventListener('input', function(e) {
                    if (type === 'number') {
                        this.value = this.value.replace(/[^0-9]/g, '');
                    } else if (type === 'text') {
                        this.value = this.value.replace(/[0-9]/g, '');
                    }
                });
            }
        });
    });
</script>

<style>
    .ls-1 {
        letter-spacing: 0.5px;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(94, 84, 142, 0.03) !important;
        cursor: default;
    }
</style>
@endsection
