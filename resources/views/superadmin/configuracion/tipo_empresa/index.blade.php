@extends('superadmin.layouts.admin')

@section('content')

<<<<<<< HEAD
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5>Crear Tipo de Empresa</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('superadmin.tipo-empresa.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <input type="text"
                           name="nombre_tipo"
                           class="form-control @error('nombre_tipo') is-invalid @enderror"
                           placeholder="Ingrese nombre"
                           required>

                    @error('nombre_tipo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <button class="btn btn-success">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<form method="GET" class="mb-3">
    <div class="row">
        <div class="col-md-4">
            <input type="text" name="buscar"
                   value="{{ request('buscar') }}"
                   class="form-control"
                   placeholder="Buscar tipo de empresa...">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary">Buscar</button>
        </div>
    </div>
</form>

<div class="card shadow-sm">
    <div class="card-header">
        <h5>Listado</h5>
    </div>

    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th width="120">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tipos as $tipo)
                <tr>
                    <td>{{ $tipo->id_tipo_empresa }}</td>
                    <td>{{ $tipo->nombre_tipo }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#editarModal"
                            data-id="{{ $tipo->id_tipo_empresa }}"
                            data-nombre="{{ $tipo->nombre_tipo }}">
                            Editar
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $tipos->links() }}
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formEditar" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Editar Tipo de Empresa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="text" name="nombre_tipo" id="editNombre"
                           class="form-control" required>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Guardar Cambios</button>
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancelar</button>
                </div>

=======
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
                <h1 class="h3 mb-0 text-gray-800">Tipos de Empresa</h1>
                <div>
                    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#crearModal">
                        <i class="fas fa-plus me-1"></i> Crear Nuevo
                    </button>
                    <a href="{{ route('superadmin.configuracion.tipo-empresa.export', ['buscar' => request('buscar')]) }}" class="btn btn-success shadow-sm ms-2">
                        <i class="fas fa-file-excel me-1"></i> Exportar Excel
                    </a>
                </div>
            </div>

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            {{-- BUSCADOR --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('superadmin.configuracion.tipo-empresa.index') }}" class="row g-3">
                        <div class="col-md-6 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control border-start-0" placeholder="Buscar por nombre...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100">Filtrar</button>
                        </div>
                        @if(request('buscar'))
                        <div class="col-md-2">
                            <a href="{{ route('superadmin.configuracion.tipo-empresa.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
                        </div>
                        @endif
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
                                    

                var form = document.getElementById('formEditar');
                form.action = "{{ url('superadmin/configuracion/tipo-empresa') }}/" + id;

                document.getElementById('editNombre').value = nombre;

                // Save ID for reopening on validation error
                sessionStorage.setItem('last_edit_id_tipo_empresa', id);
            });
        }

        // Redirect error to correct modal
        @if($errors->any())
        @if(old('_method') == 'PUT')
        var lastEditId = sessionStorage.getItem('last_edit_id_tipo_empresa');
        if (lastEditId) {
            var form = document.getElementById('formEditar');
            form.action = "{{ url('superadmin/configuracion/tipo-empresa') }}/" + lastEditId;
            var myModal = new bootstrap.Modal(document.getElementById('editarModal'));
            myModal.show();
        }
        @else
        var myModal = new bootstrap.Modal(document.getElementById('crearModal'));
        myModal.show();
        @endif
        @endif
    });
</script>
>>>>>>> origin/develop

@endsection