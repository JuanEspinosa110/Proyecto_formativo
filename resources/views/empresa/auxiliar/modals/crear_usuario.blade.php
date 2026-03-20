<!-- Modal Crear Usuario (Auxiliar) -->
<div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-light border-0 py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded text-primary">person_add</span>
                    Registrar Nuevo Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('empresa.usuarios.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Primer Nombre</label>
                            <input type="text" name="primer_nombre" class="form-control rounded-3" required placeholder="Ej: Juan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Primer Apellido</label>
                            <input type="text" name="primer_apellido" class="form-control rounded-3" required placeholder="Ej: Pérez">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Documento (ID)</label>
                            <input type="text" name="doc_usuario" class="form-control rounded-3" required placeholder="Cédula o ID">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Rol / Perfil</label>
                            <select name="id_tipo_usuario" class="form-select rounded-3" required>
                                <option value="" disabled selected>Seleccione...</option>
                                <option value="3">Conductor</option>
                                <option value="6">Propietario</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Estado</label>
                            <select name="id_estado" class="form-select rounded-3" required>
                                <option value="1">Activo</option>
                                <option value="2">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Correo Electrónico</label>
                            <input type="email" name="correo" class="form-control rounded-3" required placeholder="correo@ejemplo.com">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Contraseña</label>
                            <input type="password" name="password" class="form-control rounded-3" required placeholder="Mínimo 6 caracteres">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-3 gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>
