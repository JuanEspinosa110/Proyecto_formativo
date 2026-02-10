@extends('superadmin.layouts.admin')

@section('content')
<div class="container sa-licencia-container">
    <div class="mb-4">
        <h2 class="sa-licencia-title">Crear Nueva Licencia</h2>
        <p class="text-muted">Siga los pasos para dar de alta una nueva empresa en el sistema central.</p>
    </div>

    <form action="Post" method="POST">
        @csrf
        <div class="card sa-licencia-card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-building me-2 text-primary"></i> 1. Datos de la Empresa</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="sa-licencia-label">NIT / Identificación Tributaria</label>
                        <input type="text" name="nit" class="form-control sa-licencia-input" placeholder="900.123.456-1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="sa-licencia-label">Nombre de la Empresa</label>
                        <input type="text" name="nombre_empresa" class="form-control sa-licencia-input" placeholder="Ej: Logística Nacional S.A.S" required>
                    </div>
                    <div class="col-md-4">
                        <label class="sa-licencia-label">Departamento</label>
                        <select name="id_departamento" class="form-select sa-licencia-input">
                            <option value="">Seleccionar departamento</option>
                            </select>
                    </div>
                    <div class="col-md-4">
                        <label class="sa-licencia-label">Ciudad</label>
                        <select name="id_ciudad" class="form-select sa-licencia-input">
                            <option value="">Seleccionar ciudad</option>
                            </select>
                    </div>
                    <div class="col-md-4">
                        <label class="sa-licencia-label">Teléfono Corporativo</label>
                        <input type="text" name="telefono_empresa" class="form-control sa-licencia-input" placeholder="+57 300 000 0000">
                    </div>
                    <div class="col-md-4">
                        <label class="sa-licencia-label">Correo Electrónico de Facturación</label>
                        <input type="email" name="correo_corporativo" class="form-control sa-licencia-input" placeholder="contabilidad@empresa.com">
                    </div>
                </div>
            </div>
        </div>

        <div class="card sa-licencia-card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-user-shield me-2 text-primary"></i> 2. Datos del Administrador</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="sa-licencia-label">Nombre Completo</label>
                        <input type="text" name="nombre_admin" class="form-control sa-licencia-input" placeholder="Juan Pérez" required>
                    </div>
                    <div class="col-md-4">
                        <label class="sa-licencia-label">Documento de Identidad</label>
                        <input type="text" name="doc_admin" class="form-control sa-licencia-input" placeholder="1.098.765.432" required>
                    </div>
                    <div class="col-md-4">
                        <label class="sa-licencia-label">Correo de Acceso</label>
                        <input type="email" name="email_admin" class="form-control sa-licencia-input" placeholder="admin@empresa.com" required>
                    </div>
                    <div class="col-md-4">
                        <label class="sa-licencia-label">Celular</label>
                        <input type="text" name="telefono_admin" class="form-control sa-licencia-input" placeholder="320 123 4567">
                    </div>
                    <div class="col-md-4">
                        <label class="sa-licencia-label">Contraseña</label>
                        <input type="password" name="password_admin" class="form-control sa-licencia-input" placeholder="********" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('superadmin.licencias.index') }}" class="btn btn-light sa-licencia-btn-cancel">Cancelar</a>
            <a href="{{ route('superadmin.licencias.configurar-plan') }}" class="btn btn-primary sa-licencia-btn-next">Continuar <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
    </form>
</div>
@endsection