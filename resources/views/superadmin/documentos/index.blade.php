@extends('superadmin.layouts.admin')

@section('title', 'Documentación del Sistema')

@section('content')

<div class="sa-doc-wrapper">

    <!-- ================= HEADER ================= -->
    <header class="sa-doc-header">

        <div class="sa-doc-header-left">
            <h1 class="sa-doc-title">
                <i class="bi bi-folder2-open"></i>
                Documentación del Sistema
            </h1>

            <p class="sa-doc-subtitle">
                Administración global de documentos legales, técnicos y operativos.
            </p>
        </div>

        <div class="sa-doc-header-actions">

            <a href="#" class="sa-doc-btn-secondary">
                <i class="bi bi-tags"></i>
                Tipos de Documentos
            </a>

            <a href="#" class="sa-doc-btn-primary">
                <i class="bi bi-cloud-upload"></i>
                Subir Documento
            </a>

        </div>

    </header>

    <!-- ================= FILTROS ================= -->
    <form method="GET" class="sa-doc-filters">

        <input type="text"
            name="buscar"
            value="{{ request('buscar') }}"
            class="sa-doc-filter-input"
            placeholder="Buscar por empresa o bus...">

        <select name="estado" class="sa-doc-filter-select">
            <option value="">Estado</option>
            <option value="vigente">Vigente</option>
            <option value="vencido">Vencido</option>
        </select>

        <select name="tipo" class="sa-doc-filter-select">
            <option value="">Tipo</option>
            <option value="Licencia">Licencia</option>
            <option value="Seguro">Seguro</option>
            <option value="Revision">Revisión técnica</option>
        </select>

        <button class="sa-doc-btn-secondary">
            Filtrar
        </button>

    </form>

    <!-- ================= TABLA ================= -->
    <section class="sa-doc-table-wrapper">

        <table class="sa-doc-table">

            <thead class="sa-doc-thead">
                <tr>
                    <th>Documento</th>
                    <th>Tipo</th>
                    <th>Empresa</th>
                    <th>Bus</th>
                    <th>Estado</th>
                    <th>Vencimiento</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody class="sa-doc-tbody">

                @forelse($documentos as $doc)

                <tr>
                    <td>{{ $doc->nombre_documento }}</td>
                    <td>{{ $doc->tipo_documento }}</td>
                    <td>{{ $doc->empresa_nombre ?? '—' }}</td>
                    <td>{{ $doc->placa_bus ?? '—' }}</td>

                    <td>
                        @php
                            $vencido = \Carbon\Carbon::parse($doc->fecha_vencimiento)->isPast();
                        @endphp

                        <span class="sa-doc-status {{ $vencido ? 'expired' : 'active' }}">
                            {{ $vencido ? 'Vencido' : 'Vigente' }}
                        </span>
                    </td>

                    <td>{{ $doc->fecha_vencimiento }}</td>

                    <td class="sa-doc-actions">

                        <a href="{{ route('superadmin.documentos.download', $doc->id_documento) }}"
                        class="sa-doc-action-btn download">
                            <i class="bi bi-download"></i>
                        </a>

                        <a href="{{ route('superadmin.documentos.edit', $doc->id_documento) }}"
                        class="sa-doc-action-btn edit">
                            <i class="bi bi-pencil"></i>
                        </a>

                        <form action="{{ route('superadmin.documentos.destroy', $doc->id_documento) }}"
                            method="POST">
                            @csrf
                            @method('DELETE')

                            <button class="sa-doc-action-btn delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>

                    </td>
                </tr>

                @empty

                <tr>
                    <td colspan="7" class="text-center">
                        No hay documentos registrados.
                    </td>
                </tr>

                @endforelse

            </tbody>


        </table>

    </section>

</div>

@endsection
