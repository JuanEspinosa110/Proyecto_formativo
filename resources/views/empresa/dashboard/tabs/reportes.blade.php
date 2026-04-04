<!-- Tab REPORTES (Generación de Informes) -->
<div class="tab-pane fade {{ $tab == 'reportes' ? 'show active' : '' }}" id="tab-reportes" role="tabpanel">
    <div class="row g-4">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4 text-center">
                    <div class="bg-primary text-white p-3 rounded-circle d-inline-flex mb-3 shadow">
                        <span class="material-symbols-rounded fs-1">person</span>
                    </div>
                    <h5 class="fw-black mb-1">Informe de Personal</h5>
                    <p class="text-muted small mb-4">Listado completo de conductores y propietarios con sus datos de contacto.</p>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('empresa.usuarios.export', ['format' => 'excel']) }}" class="btn btn-outline-success btn-sm rounded-pill fw-bold shadow-sm py-2">
                            <span class="material-symbols-rounded fs-6 align-middle me-1">file_download</span> DESCARGAR EXCEL
                        </a>
                        <a href="{{ route('empresa.usuarios.export', ['format' => 'pdf']) }}" class="btn btn-outline-danger btn-sm rounded-pill fw-bold shadow-sm py-2">
                            <span class="material-symbols-rounded fs-6 align-middle me-1">picture_as_pdf</span> DESCARGAR PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4 text-center">
                    <div class="bg-success text-white p-3 rounded-circle d-inline-flex mb-3 shadow">
                        <span class="material-symbols-rounded fs-1">directions_bus</span>
                    </div>
                    <h5 class="fw-black mb-1">Inventario de Flota</h5>
                    <p class="text-muted small mb-4">Reporte detallado de vehículos, estados operativos y kilometraje.</p>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('empresa.buses.export', ['format' => 'excel']) }}" class="btn btn-outline-success btn-sm rounded-pill fw-bold shadow-sm py-2">
                            <span class="material-symbols-rounded fs-6 align-middle me-1">file_download</span> EXCEL DE FLOTA
                        </a>
                        <a href="{{ route('empresa.buses.export', ['format' => 'pdf']) }}" class="btn btn-outline-danger btn-sm rounded-pill fw-bold shadow-sm py-2">
                            <span class="material-symbols-rounded fs-6 align-middle me-1">picture_as_pdf</span> DESCARGAR PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4 text-center">
                    <div class="bg-warning text-dark p-3 rounded-circle d-inline-flex mb-3 shadow">
                        <span class="material-symbols-rounded fs-1">folder_open</span>
                    </div>
                    <h5 class="fw-black mb-1">Estado Documental</h5>
                    <p class="text-muted small mb-4">Listado de vencimientos de SOAT, Tecnomecánica y Licencias.</p>
                        <div class="d-flex flex-column gap-2">
                            <a href="{{ route('empresa.reportes.export', ['tipo_reporte' => 'documentos', 'formato' => 'excel']) }}" class="btn btn-outline-primary btn-sm rounded-pill fw-bold shadow-sm py-2">
                                <span class="material-symbols-rounded fs-6 align-middle me-1">description</span> REPORTE EXCEL (VENCIMIENTOS)
                            </a>
                            <a href="{{ route('empresa.reportes.export', ['tipo_reporte' => 'documentos', 'formato' => 'pdf']) }}" class="btn btn-outline-danger btn-sm rounded-pill fw-bold shadow-sm py-2">
                                <span class="material-symbols-rounded fs-6 align-middle me-1">picture_as_pdf</span> DESCARGAR PDF (ESTADO)
                            </a>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
