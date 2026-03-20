@extends('admin.layouts.app')

@section('title', 'Documentos - SIGU')

@section('content')
<div class="sa-content-header">
    <div class="sa-content-title">
        <h1><span class="material-symbols-rounded">description</span> Docs. Vehículos</h1>
        <p>Revisión y control de legalidad de la flota de {{ $empresa->nombre_empresa }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.documentos.export') }}" class="btn btn-outline-success d-flex align-items-center gap-2 px-3 fw-bold shadow-sm">
            <span class="material-symbols-rounded">file_download</span> Exportar Excel
        </a>
    </div>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <span class="material-symbols-rounded">check_circle</span>
    <span class="fw-medium">{{ $message }}</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Filtros -->
<div class="sa-filters-section">
    <form method="GET" action="{{ route('admin.documentos.index') }}" class="row g-3 align-items-end">
        <div class="col-md-2">
            <label for="tipo" class="form-label small fw-bold text-muted text-uppercase">Tipo</label>
            <select name="tipo" id="tipo" class="form-select bg-light border-0">
                <option value="">Todos</option>
                @foreach ($tiposDocumento as $tipo)
                <option value="{{ $tipo->id_tipo_documento }}" {{ request('tipo') == $tipo->id_tipo_documento ? 'selected' : '' }}>
                    {{ $tipo->nombre }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label for="estado" class="form-label small fw-bold text-muted text-uppercase">Estado</label>
            <select name="estado" id="estado" class="form-select bg-light border-0">
                <option value="">Todos</option>
                @foreach ($estados as $est)
                <option value="{{ $est->id_estado }}" {{ request('estado') == $est->id_estado ? 'selected' : '' }}>
                    {{ $est->nombre_estado }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label for="placa" class="form-label small fw-bold text-muted text-uppercase">Placa</label>
            <input type="text" name="placa" id="placa" class="form-control bg-light border-0" placeholder="Ej: ABC123" value="{{ request('placa') }}">
        </div>

        <div class="col-md-4">
            <label for="propietario" class="form-label small fw-bold text-muted text-uppercase">Propietario / ID</label>
            <input type="text" name="propietario" id="propietario" class="form-control bg-light border-0" placeholder="Nombre o Cédula..." value="{{ request('propietario') }}">
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-dark w-100 fw-bold">
                <span class="material-symbols-rounded fs-5">search</span> Filtrar
            </button>
        </div>
    </form>
</div>

    @include('admin.documentos.partials.table')


<style>
    .sa-content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .sa-content-title h1 {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
    }

    .sa-content-title p {
        margin: 0.25rem 0 0 0;
        opacity: 0.8;
    }

    .sa-filters-section {
        background: var(--card);
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        box-shadow: 0 6px 18px rgba(31, 36, 48, 0.04);
    }

    .sa-kpi-section {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .sa-kpi-card {
        flex: 1;
        min-width: 150px;
        padding: 1rem;
        background: linear-gradient(135deg, var(--p-light), var(--p-mid));
        border-radius: 8px;
        color: white;
    }

    .kpi-title {
        font-size: 0.85rem;
        opacity: 0.85;
    }

    .kpi-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 0.5rem;
    }

    .btn-group-sm .sigu-btn {
        padding: 0.35rem 0.75rem;
        font-size: 0.85rem;
    }

    @media (max-width: 768px) {
        .sa-content-header {
            flex-direction: column;
            gap: 1rem;
        }

        .sa-filters-section .row {
            grid-auto-flow: dense;
        }

        .sa-kpi-section {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

<!-- Modal Visor de Documentos -->
<div class="modal fade" id="modalVisorDocumento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pt-4 px-4 bg-dark text-white">
                <h5 class="modal-title fw-bold" id="visor_titulo">Visualización de Documento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-secondary bg-opacity-10" style="height: 70vh;">
                <iframe id="visor_iframe" class="w-100 h-100 d-none border-0" src=""></iframe>
                <div id="visor_image_container" class="w-100 h-100 d-none d-flex align-items-center justify-content-center p-3">
                    <img id="visor_img" src="" class="img-fluid rounded-3 shadow-sm" style="max-height: 100%;">
                </div>
                <div id="visor_error" class="w-100 h-100 d-none d-flex flex-column align-items-center justify-content-center text-muted">
                    <span class="material-symbols-rounded display-1 mb-3">error</span>
                    <p class="fw-bold">No se puede previsualizar este archivo.</p>
                    <a id="visor_download" href="#" class="btn btn-primary rounded-pill px-4" download>Descargar Archivo</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalVisor = new bootstrap.Modal(document.getElementById('modalVisorDocumento'));
        const iframe = document.getElementById('visor_iframe');
        const imgContainer = document.getElementById('visor_image_container');
        const img = document.getElementById('visor_img');
        const error = document.getElementById('visor_error');
        const download = document.getElementById('visor_download');
        const titulo = document.getElementById('visor_titulo');

        document.querySelectorAll('.btn-visor').forEach(btn => {
            btn.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                const nombre = this.getAttribute('data-nombre');
                
                titulo.innerText = 'Documento: ' + nombre;
                
                // Reset states
                iframe.classList.add('d-none');
                imgContainer.classList.add('d-none');
                error.classList.add('d-none');
                iframe.src = '';
                img.src = '';
                download.href = url;

                if (!url || url.includes('null')) {
                    error.classList.remove('d-none');
                } else {
                    const ext = url.split('.').pop().toLowerCase();
                    if (ext === 'pdf') {
                        iframe.src = url;
                        iframe.classList.remove('d-none');
                    } else if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                        img.src = url;
                        imgContainer.classList.remove('d-none');
                    } else {
                        error.classList.remove('d-none');
                    }
                }
                modalVisor.show();
            });
        });
    });
</script>
@endpush