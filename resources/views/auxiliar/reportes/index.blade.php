@extends('admin.layouts.app')

@section('title', 'Generar Reportes — SIGU')

@section('content')
<div class="container-fluid pt-0 pb-4">
    
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold">Reportes y Estadísticas</h1>
            <p class="text-muted small mb-0">Exporte información operativa en formato Excel.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger shadow-sm py-2 mb-4">{{ session('error') }}</div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded text-primary">analytics</span> Configurar Reporte
                </h5>
                <form id="formGenerarReporte" method="GET" action="{{ route('empresa.reportes.export') }}">
                    
                    <div class="row g-3">
                        <!-- Tipo de Reporte -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Tipo de Reporte <span class="text-danger">*</span></label>
                            <select name="tipo_reporte" id="tipo_reporte" class="form-select @error('tipo_reporte') is-invalid @enderror" required>
                                <option value="" selected disabled>Seleccione una opción...</option>
                                <option value="conductores">Listado de Conductores</option>
                                <option value="recorridos">Historial de Recorridos (Asignaciones)</option>
                                <option value="fallas">Reportes de Fallas Mecánicas</option>
                                <option value="documentos">Inventario de Documentación</option>
                            </select>
                            @error('tipo_reporte') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Formato de Reporte -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Formato de Descarga <span class="text-danger">*</span></label>
                            <select name="formato" id="formato" class="form-select" required>
                                <option value="excel" selected>Excel (.xlsx)</option>
                                <option value="pdf">PDF (.pdf)</option>
                            </select>
                        </div>

                        <!-- Rango de Fechas (Condicional) -->
                        <div class="col-md-6 d-none" id="wrapper_fecha_inicio">
                            <label class="form-label small fw-bold text-muted text-uppercase">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror">
                            @error('fecha_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 d-none" id="wrapper_fecha_fin">
                            <label class="form-label small fw-bold text-muted text-uppercase">Fecha Fin</label>
                            <input type="date" name="fecha_fin" class="form-control @error('fecha_fin') is-invalid @enderror">
                            @error('fecha_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 mt-4 d-grid">
                            <button type="submit" class="btn btn-primary fw-bold rounded-pill shadow-sm py-2">
                                <span class="material-symbols-rounded fs-5 align-middle me-1">download</span> Descargar Reporte
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Cards Informativos -->
            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary-subtle text-primary p-3 rounded-4">
                                <span class="material-symbols-rounded fs-2">badge</span>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Conductores</h6>
                                <p class="text-muted small mb-0">Datos de contacto y estado.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success-subtle text-success p-3 rounded-4">
                                <span class="material-symbols-rounded fs-2">route</span>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Recorridos</h6>
                                <p class="text-muted small mb-0">Rutas y horarios servidos.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectTipo = document.getElementById('tipo_reporte');
    const wrapIni = document.getElementById('wrapper_fecha_inicio');
    const wrapFin = document.getElementById('wrapper_fecha_fin');

    selectTipo.addEventListener('change', function() {
        // Mostrar fechas solo para recorridos y fallas (que tienen fecha de creación/viaje)
        if (this.value === 'recorridos' || this.value === 'fallas') {
            wrapIni.classList.remove('d-none');
            wrapFin.classList.remove('d-none');
        } else {
            wrapIni.classList.add('d-none');
            wrapFin.classList.add('d-none');
            wrapIni.querySelector('input').value = '';
            wrapFin.querySelector('input').value = '';
        }
    });

    const form = document.getElementById('formGenerarReporte');
    if (form) {
        form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Descargando...';

        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();
        const url = form.action + '?' + params;

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Error en descarga');
                const contentType = response.headers.get('content-type');
                const isExcel = contentType && (contentType.includes('spreadsheet') || contentType.includes('excel') || contentType.includes('xlsx'));
                const extension = isExcel ? 'xlsx' : 'pdf';
                return response.blob().then(blob => ({ blob, extension }));
            })
            .then(({ blob, extension }) => {
                const downloadUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = downloadUrl;
                const tipo = selectTipo.value || 'reporte';
                a.download = `reporte_${tipo}_${new Date().getTime()}.${extension}`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(downloadUrl);
            })
            .catch(err => {
                alert('No se pudo descargar el reporte. Intente nuevamente.');
                console.error(err);
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
    }
});
</script>
@endpush

@endsection
