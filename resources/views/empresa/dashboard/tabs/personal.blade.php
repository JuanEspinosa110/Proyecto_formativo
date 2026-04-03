<!-- Tab PERSONAL (Gestion de Usuarios) -->
<div class="tab-pane fade {{ $tab == 'personal' ? 'show active' : '' }}" id="tab-personal" role="tabpanel">
    <!-- Barra de Filtros -->
    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-body p-3 bg-white">
            <form method="GET" action="{{ route('empresa.dashboard') }}" class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="personal">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0 ps-3">
                            <span class="material-symbols-rounded text-muted fs-5">search</span>
                        </span>
                        <input type="text" name="search" class="form-control bg-light border-0 py-2"
                            placeholder="Buscar por nombre o documento..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-muted x-small fw-bold text-uppercase mb-0 min-w-max">Filtrar:</label>
                        <select name="role" class="form-select form-select-sm bg-light border-0">
                            <option value="">Todos los roles</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id_tipo_usuario }}" {{ (string) ($selectedRole ?? '') === (string) $r->id_tipo_usuario ? 'selected' : '' }}>
                                    {{ $r->nombre_tipo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 ms-auto text-end d-flex gap-1 justify-content-end">
                    <button type="submit"
                        class="btn btn-dark btn-sm rounded-pill px-3 fw-semibold shadow-sm">Filtrar</button>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold shadow-sm"
                        data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
                        <span class="material-symbols-rounded align-middle fs-6">person_add</span> Usuario
                    </button>
                    <a href="{{ route('empresa.dashboard', ['tab' => 'personal']) }}"
                        class="btn btn-light btn-sm rounded-pill fw-semibold border shadow-sm">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla Principal (Reusando el partial de Admin) -->
    <div class="sigu-fade">
        @include('admin.usuarios.partials.table', ['usuarios' => $usuarios])
    </div>
</div>