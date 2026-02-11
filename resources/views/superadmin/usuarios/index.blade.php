@extends('superadmin.layouts.admin')

@section('title', 'Usuarios del Sistema')

@section('content')

<div class="sa-users-wrapper">

    <!-- ================= HEADER ================= -->
    <header class="sa-users-header">

        <div>
            <h1 class="sa-users-title">
                <i class="bi bi-people-fill"></i>
                Usuarios del Sistema
            </h1>

            <p class="sa-users-subtitle">
                Administración global de usuarios, roles, afiliaciones y permisos.
            </p>
        </div>

        <a href="#"
           class="sa-users-btn-create">
            <i class="bi bi-person-plus"></i>
            Nuevo Usuario
        </a>

    </header>

    <!-- ================= FILTROS ================= -->
    <section class="sa-users-filters">

        <input type="text"
               class="sa-users-search-input"
               placeholder="Buscar por nombre, correo o documento...">

        <select class="sa-users-filter-select">
            <option value="">Rol</option>
            <option>SuperAdmin</option>
            <option>Empresa</option>
            <option>Conductor</option>
            <option>Pasajero</option>
        </select>

        <select class="sa-users-filter-select">
            <option value="">Empresa</option>
            <option>Transportes Ibagué</option>
            <option>Movilidad Plus</option>
        </select>

        <select class="sa-users-filter-select">
            <option value="">Estado</option>
            <option>Activo</option>
            <option>Inactivo</option>
            <option>Bloqueado</option>
        </select>

    </section>

    <!-- ================= TABLA ================= -->
    <section class="sa-users-table-wrapper">

        <table class="sa-users-table">

            <thead class="sa-users-thead">
                <tr>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Empresa</th>
                    <th>Estado</th>
                    <th>Afiliaciones</th>
                    <th>Gestión</th>
                </tr>
            </thead>

            <tbody class="sa-users-tbody">

                @forelse($usuarios as $usuario)

                <tr>

                <td>
                    <div class="sa-users-userbox">
                        <span class="sa-users-avatar">
                            <i class="bi bi-person"></i>
                        </span>
                        <div>
                            <strong>
                                {{ $usuario->primer_nombre }} {{ $usuario->primer_apellido }}
                            </strong>
                            <small>{{ $usuario->correo }}</small>
                        </div>
                    </div>
                </td>

                <td>{{ $usuario->id_tipo_usuario }}</td>

                <td>{{ $usuario->NIT ?? '—' }}</td>

                <td>
                    <span class="sa-users-status
                        {{ $usuario->id_estado == 1 ? 'active' : 'blocked' }}">
                        {{ $usuario->id_estado == 1 ? 'Activo' : 'Bloqueado' }}
                    </span>
                </td>

                <td>
                    {{ $usuario->id_tipo_usuario == 3 ? 'Conductor' : '—' }}
                </td>

                <td class="sa-users-actions">

                    <a href="{{ route('superadmin.usuarios.show', $usuario->doc_usuario) }}"
                    class="sa-users-action-btn view">
                        <i class="bi bi-eye"></i>
                    </a>

                    <a href="{{ route('superadmin.usuarios.password', $usuario->doc_usuario) }}"
                    class="sa-users-action-btn edit">
                        <i class="bi bi-key"></i>
                    </a>

                </td>

                </tr>

                @empty

                <tr>
                <td colspan="6" class="sa-users-empty">
                    <i class="bi bi-people"></i>
                    <p>No hay usuarios registrados en el sistema.</p>
                </td>
                </tr>

                @endforelse

            </tbody>



        </table>

    </section>

</div>

@endsection
