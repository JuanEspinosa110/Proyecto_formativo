@extends('superadmin.layouts.admin')

@section('title', 'Gestión de Empresas')

@section('content')

<div class="sa-emp-wrapper">

    <!-- HEADER -->
    <header class="sa-emp-header">

        <div class="sa-emp-header-left">
            <h1 class="sa-emp-title">
                <i class="bi bi-building"></i>
                Gestión de Empresas
            </h1>

            <p class="sa-emp-subtitle">
                Administración global de empresas registradas en la plataforma.
            </p>
        </div>

        <div class="sa-emp-header-actions">

            
            <!-- Crear -->
            <a href="{{ route('superadmin.empresas.create') }}"
               class="sa-emp-btn-create">
                <i class="bi bi-plus-circle"></i>
                Nueva Empresa
            </a>

        </div>

    </header>

    <!-- FILTROS -->
    <section class="sa-emp-filters">

        <div class="sa-emp-search-box">
            <i class="bi bi-search"></i>
            <input type="text"
                   class="sa-emp-search-input"
                   placeholder="Buscar empresa por nombre o NIT...">
        </div>

        <select class="sa-emp-filter-select">
            <option value="">Estado</option>
            <option value="activa">Activa</option>
            <option value="inactiva">Inactiva</option>
            <option value="bloqueada">Bloqueada</option>
        </select>

        <select class="sa-emp-filter-select">
            <option value="">Licencia</option>
            <option value="vigente">Vigente</option>
            <option value="vencida">Vencida</option>
        </select>

    </section>

    <!-- TABLA -->
    <section class="sa-emp-table-wrapper">

        <table class="sa-emp-table">

            <thead class="sa-emp-thead">
                <tr>
                    <th>Empresa</th>
                    <th>NIT</th>
                    <th>Estado</th>
                    <th>Licencia</th>
                    <th>Flota</th>
                    <th>Gestión</th>
                </tr>
            </thead>

            <tbody class="sa-emp-tbody">

                @foreach($empresas as $empresa)

                <tr>

                    <td>{{ $empresa->nombre }}</td>

                    <td>{{ $empresa->nit }}</td>

                    <td>
                        <span class="sa-emp-status 
                        {{ $empresa->estado === 'activa'
                            ? 'sa-emp-status-active'
                            : 'sa-emp-status-inactive' }}">
                            {{ ucfirst($empresa->estado) }}
                        </span>
                    </td>

                    <td>{{ ucfirst($empresa->licencia_estado) }}</td>

                    <td>{{ $empresa->flota_total }} buses</td>

                    <!-- ====== ACCIONES ====== -->
                    <td class="sa-emp-actions">

                        <!-- Ver -->
                        <a href="{{ route('superadmin.empresas.show', $empresa->id) }}"
                           class="sa-emp-action-btn view"
                           title="Ver empresa">
                            <i class="bi bi-eye"></i>
                        </a>

                        <!-- Editar -->
                        <a href="{{ route('superadmin.empresas.edit', $empresa->id) }}"
                           class="sa-emp-action-btn edit"
                           title="Editar empresa">
                            <i class="bi bi-pencil"></i>
                        </a>

                        <!-- Auxiliares -->
                        <a href="{{ route('superadmin.empresas.auxiliares', $empresa->id) }}"
                           class="sa-emp-action-btn aux"
                           title="Auxiliares">
                            <i class="bi bi-people"></i>
                        </a>

                        <!-- Buses -->
                        <a href="{{ route('superadmin.empresas.buses', $empresa->id) }}"
                           class="sa-emp-action-btn bus"
                           title="Buses">
                            <i class="bi bi-truck-front"></i>
                        </a>

                        <!-- Documentos -->
                        <a href="{{ route('superadmin.empresas.documentos', $empresa->id) }}"
                           class="sa-emp-action-btn docs"
                           title="Ver documentación">
                            <i class="bi bi-folder2-open"></i>
                        </a>

                        <!-- Subir docs -->
                        <a href="{{ route('superadmin.empresas.documentos.upload', $empresa->id) }}"
                           class="sa-emp-action-btn upload"
                           title="Subir documentación">
                            <i class="bi bi-cloud-upload"></i>
                        </a>

                        <!-- Activar / Inactivar -->
                        <a href="{{ route('superadmin.empresas.toggle', $empresa->id) }}"
                           class="sa-emp-action-btn toggle"
                           title="Activar / Inactivar">
                            <i class="bi bi-power"></i>
                        </a>

                        <!-- Eliminar -->
                        <form action="{{ route('superadmin.empresas.destroy', $empresa->id) }}"
                              method="POST"
                              class="sa-emp-delete-form">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="sa-emp-action-btn delete"
                                    title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>

                        </form>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </section>

</div>

@endsection
