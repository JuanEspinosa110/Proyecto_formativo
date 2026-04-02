@extends('empresa-recargas.layouts.app')

@section('title', 'Titularidad de Tarjeta — SIGU')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Titularidad de Tarjeta</h1>
    <div class="card p-4 shadow-sm">
        <form id="buscar-usuario-form" method="POST" action="#">
            @csrf
            <div class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="busqueda" class="form-label">Buscar usuario (Documento o Correo)</label>
                    <input type="text" class="form-control" id="busqueda" name="busqueda" placeholder="Ingrese documento o correo" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </div>
        </form>
        <div id="resultado-busqueda" class="mt-4">
            <!-- Aquí se mostrará la información del usuario y tarjeta -->
        </div>
        <div id="tarjetas-disponibles-cambio" class="mt-3"></div>
    </div>

        <!-- Modal para cambio de titularidad -->
        <div class="modal fade" id="modalCambioTitularidad" tabindex="-1" data-bs-backdrop="true" data-bs-keyboard="true" aria-labelledby="modalCambioTitularidadLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCambioTitularidadLabel">Confirmar cambio de titularidad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-cambio-titularidad">
                                <input type="hidden" id="modal-doc-usuario" name="doc_usuario">
                                <input type="hidden" id="modal-id-tarjeta" name="id_tarjeta">
                                <input type="hidden" id="modal-correo-usuario" name="correo_usuario">
                                <div class="mb-3">
                                    <label for="codigo_verificacion" class="form-label fw-bold">Código de verificación <span class="fw-normal text-muted">(Haz clic en "Enviar código" para recibirlo)</span></label>
                                    <input type="text" 
                                           class="form-control form-control-lg text-center letter-spacing-2" 
                                           id="codigo_verificacion" 
                                           name="codigo_verificacion" 
                                           placeholder="Ej: 123456" 
                                           maxlength="6" 
                                           minlength="6" 
                                           pattern="\d{6}" 
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6); document.getElementById('btn-confirmar-cambio').disabled = this.value.length !== 6;" 
                                           required>
                                    <div class="mt-2 text-end">
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="btn-enviar-codigo">
                                            <i class="bi bi-envelope"></i> Enviar código
                                        </button>
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success" id="btn-confirmar-cambio" disabled>Confirmar cambio</button>
                                </div>
                        </form>
                        <div id="modal-cambio-mensaje" class="mt-2"></div>
                        <div id="modal-reenviar-codigo" class="mt-2"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal informativo de nueva titularidad -->
        <div class="modal fade" id="modalInfoTitularidad" tabindex="-1" aria-labelledby="modalInfoTitularidadLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalInfoTitularidadLabel">Nueva titularidad asignada</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body" id="info-titularidad-body">
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('buscar-usuario-form');
    const resultado = document.getElementById('resultado-busqueda');
    const tarjetasCambioDiv = document.getElementById('tarjetas-disponibles-cambio');
    let usuarioActual = null;
    let tarjetasDisponibles = [];
    let cooldown = 0;
    let cooldownInterval = null;
    let correoUsuario = null;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        resultado.innerHTML = '<div class="text-center text-muted">Buscando...</div>';
        tarjetasCambioDiv.innerHTML = '';
        fetch("{{ route('gestor-recargas.titularidad.buscar') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
            },
            body: JSON.stringify({ busqueda: document.getElementById('busqueda').value })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                resultado.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
            } else {
                usuarioActual = data.usuario;
                correoUsuario = data.usuario.correo;
                tarjetasDisponibles = data.tarjetas_disponibles || [];
                // Guardar id de tarjeta activa para validaciones frontend
                usuarioActual.tarjeta_activa_id = data.titularidad && data.tarjeta ? data.tarjeta.id_tarjeta : null;
                let html = `<div class='alert alert-success'>Usuario encontrado: <b>${data.usuario.nombre}</b> (${data.usuario.correo})</div>`;
                if (data.titularidad && data.tarjeta) {
                    html += `<div class='card p-3 mb-2'><b>Tarjeta actual:</b> ${data.tarjeta.id_tarjeta}<br><b>Saldo:</b> $${data.saldo ?? 0}`;
                    html += `<br><button class='btn btn-sigu mt-2' id='btn-cambiar-tarjeta'>Cambiar tarjeta (pérdida/robo)</button>`;
                    html += `</div>`;
                } else if (tarjetasDisponibles.length > 0) {
                    html += `<div class='alert alert-warning'>El usuario no tiene tarjeta activa.</div>`;
                    html += `<div class='card p-3'><b>Tarjetas disponibles para asignar:</b><div class='table-responsive'><table class='table table-sm align-middle'><thead><tr><th>ID</th><th>Código</th><th></th></tr></thead><tbody>`;
                    tarjetasDisponibles.forEach(function(t) {
                        // Deshabilitar si es la tarjeta actual
                        let disabled = (usuarioActual.tarjeta_activa_id && usuarioActual.tarjeta_activa_id == t.id_tarjeta) ? 'disabled' : '';
                        let btnText = disabled ? 'No disponible (actual)' : 'Seleccionar';
                        html += `<tr><td>${t.id_tarjeta}</td><td>${t.codigo_tarjeta}</td><td class='text-end'><button class='btn btn-outline-primary btn-sm btn-cambiar-tarjeta-final' data-id='${t.id_tarjeta}' ${disabled}>${btnText}</button></td></tr>`;
                    });
                    html += `</tbody></table></div></div>`;
                } else {
                    html += `<div class='alert alert-warning'>El usuario no tiene tarjeta activa y no hay tarjetas disponibles para asignar.</div>`;
                }
                resultado.innerHTML = html;
                tarjetasCambioDiv.innerHTML = '';
                // Guardar correo en input oculto
                document.getElementById('modal-correo-usuario').value = correoUsuario;

                // Botón para cambiar tarjeta si tiene activa
                const btnCambio = document.getElementById('btn-cambiar-tarjeta');
                if (btnCambio) {
                    btnCambio.addEventListener('click', function() {
                        if (tarjetasDisponibles.length === 0) {
                            tarjetasCambioDiv.innerHTML = `<div class='alert alert-warning'>No hay tarjetas disponibles para asignar. Solicite nuevas tarjetas al administrador.</div>`;
                            return;
                        }
                        // Mostrar tarjetas disponibles para seleccionar
                        let html = `<div class='card p-3'><b>Selecciona una nueva tarjeta para asignar:</b><div class='table-responsive'><table class='table table-sm align-middle'><thead><tr><th>ID</th><th>Código</th><th></th></tr></thead><tbody>`;
                        // PAGINACIÓN TARJETAS DISPONIBLES
                        let currentPage = 1;
                        const pageSize = 5;
                        function renderTarjetasPage(page) {
                            let html = `<div class='card p-3'><b>Selecciona una nueva tarjeta para asignar:</b><div class='table-responsive'><table class='table table-sm align-middle'><thead><tr><th>ID</th><th>Código</th><th></th></tr></thead><tbody>`;
                            const start = (page - 1) * pageSize;
                            const end = start + pageSize;
                            tarjetasDisponibles.slice(start, end).forEach(function(t) {
                                let disabled = (usuarioActual.tarjeta_activa_id && usuarioActual.tarjeta_activa_id == t.id_tarjeta) ? 'disabled' : '';
                                let btnText = disabled ? 'No disponible (actual)' : 'Seleccionar';
                                html += `<tr><td>${t.id_tarjeta}</td><td>${t.codigo_tarjeta}</td><td class='text-end'><button class='btn btn-outline-primary btn-sm btn-cambiar-tarjeta-final' data-id='${t.id_tarjeta}' ${disabled}>${btnText}</button></td></tr>`;
                            });
                            html += `</tbody></table></div>`;
                            // Controles de paginación
                            const totalPages = Math.ceil(tarjetasDisponibles.length / pageSize);
                            html += `<div class='d-flex justify-content-between align-items-center mt-2'>`;
                            html += `<button class='btn btn-secondary btn-sm' id='btn-prev-page' ${page === 1 ? 'disabled' : ''}>Anterior</button>`;
                            html += `<span>Página ${page} de ${totalPages}</span>`;
                            html += `<button class='btn btn-secondary btn-sm' id='btn-next-page' ${page === totalPages ? 'disabled' : ''}>Siguiente</button>`;
                            html += `</div></div>`;
                            tarjetasCambioDiv.innerHTML = html;
                            // Eventos de paginación
                            document.getElementById('btn-prev-page').onclick = function() {
                                if (currentPage > 1) {
                                    currentPage--;
                                    renderTarjetasPage(currentPage);
                                }
                            };
                            document.getElementById('btn-next-page').onclick = function() {
                                if (currentPage < totalPages) {
                                    currentPage++;
                                    renderTarjetasPage(currentPage);
                                }
                            };
                            // Reasignar eventos a los botones de selección
                            document.querySelectorAll('.btn-cambiar-tarjeta-final').forEach(function(btn) {
                                btn.addEventListener('click', function() {
                                    btn.disabled = true;
                                    tarjetasCambioDiv.innerHTML += `<div id='cargando-modal' class='alert alert-info mt-2'>Cargando...</div>`;
                                    const idTarjeta = this.getAttribute('data-id');
                                    if (usuarioActual.tarjeta_activa_id && usuarioActual.tarjeta_activa_id == idTarjeta) {
                                        tarjetasCambioDiv.innerHTML += `<div class='alert alert-danger mt-2'>Debes seleccionar una tarjeta diferente a la actual.</div>`;
                                        return;
                                    }
                                    document.getElementById('modal-doc-usuario').value = usuarioActual.doc_usuario;
                                    document.getElementById('modal-id-tarjeta').value = idTarjeta;
                                    document.getElementById('modal-correo-usuario').value = correoUsuario;
                                    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCambioTitularidad'));
                                    modal.show();
                                    // Habilitar botón enviar código y limpiar campo
                                    document.getElementById('btn-enviar-codigo').disabled = false;
                                    document.getElementById('codigo_verificacion').value = '';
                                    document.getElementById('codigo_verificacion').disabled = false;
                                    document.getElementById('btn-confirmar-cambio').disabled = true;
                                    if (cooldownInterval) clearInterval(cooldownInterval);
                                    document.getElementById('modal-cambio-mensaje').innerHTML = '';
                                    document.getElementById('modal-reenviar-codigo').innerHTML = '';
                                    btn.disabled = false;
                                    const cargandoDiv = document.getElementById('cargando-modal');
                                    if (cargandoDiv) cargandoDiv.remove();
                                });
                            });
                        }
                        renderTarjetasPage(currentPage);
                    });
                }

                // Asignar eventos a los botones de asignar tarjeta (cuando no tiene activa)
                document.querySelectorAll('.btn-asignar-tarjeta').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const idTarjeta = this.getAttribute('data-id');
                        if (cooldown > 0) return;
                        fetch("{{ route('gestor-recargas.titularidad.enviar-codigo') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
                            },
                            body: JSON.stringify({ doc_usuario: usuarioActual.doc_usuario, id_tarjeta: idTarjeta })
                        })
                        .then(res => res.json())
                        .then(data => {
                            let msg = '';
                            if(data.success) {
                                msg = `<div class='alert alert-success'>${data.message}</div>`;
                                cooldown = 60;
                                startCooldown(msg);
                            } else {
                                msg = `<div class='alert alert-danger'>${data.message}</div>`;
                                document.getElementById('modal-cambio-mensaje').innerHTML = msg;
                            }
                            document.getElementById('modal-doc-usuario').value = usuarioActual.doc_usuario;
                            document.getElementById('modal-id-tarjeta').value = idTarjeta;
                            var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCambioTitularidad'));
                            modal.show();
                        });
                    });
                });
            }
        })
        .catch(() => {
            const cargandoDiv = document.getElementById('cargando-modal');
            if (cargandoDiv) cargandoDiv.remove();
            btn.disabled = false;
            btn.innerHTML = originalText;
            document.getElementById('tarjetas-cambio-contenedor').innerHTML += '<div class="alert alert-danger mt-2">Error de red en la búsqueda del usuario.</div>';
        });
    });

    // Manejar el envío del formulario de cambio de titularidad
    document.getElementById('form-cambio-titularidad').addEventListener('submit', function(e) {
        e.preventDefault();
        const doc_usuario = document.getElementById('modal-doc-usuario').value;
        const id_tarjeta = document.getElementById('modal-id-tarjeta').value;
        const codigo = document.getElementById('codigo_verificacion').value;
        if (!codigo || !id_tarjeta || !doc_usuario) {
            document.getElementById('modal-cambio-mensaje').innerHTML = `<div class='alert alert-danger'>Todos los campos son obligatorios.</div>`;
            return;
        }
        // Validación frontend: exactamente 6 dígitos numéricos
        if (!/^\d{6}$/.test(codigo)) {
            document.getElementById('modal-cambio-mensaje').innerHTML = `<div class='alert alert-danger'>El código debe tener exactamente 6 dígitos numéricos.</div>`;
            return;
        }
        const btn = this.querySelector('button[type=submit]');
        btn.disabled = true;
        btn.textContent = 'Procesando...';
        fetch("{{ route('gestor-recargas.titularidad.cambiar') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
            },
            body: JSON.stringify({ doc_usuario, id_tarjeta, codigo_verificacion: codigo })
        })
        .then(res => {
            if (!res.ok && res.status === 422) {
                return res.json().then(errData => { throw errData; });
            }
            return res.json();
        })
        .then(data => {
            if(data.success) {
                // Mostrar modal informativo con nueva titularidad y saldo transferido
                var modalCambioEl = document.getElementById('modalCambioTitularidad');
                var modalCambio = bootstrap.Modal.getInstance(modalCambioEl);
                if (modalCambio) modalCambio.hide();
                let infoHtml = `<div class='alert alert-success'>${data.message}</div>`;
                if(data.nueva_titularidad) {
                    infoHtml += `<div class='card p-3 mt-2'><b>Nueva tarjeta asignada:</b> ${data.nueva_titularidad.id_tarjeta}<br><b>Fecha inicio:</b> ${data.nueva_titularidad.fecha_inicio || ''}</div>`;
                }
                if(data.saldo_transferido !== undefined) {
                    infoHtml += `<div class='card p-3 mt-2'><b>Saldo transferido:</b> $${data.saldo_transferido}</div>`;
                }
                document.getElementById('info-titularidad-body').innerHTML = infoHtml;
                var modalInfo = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalInfoTitularidad'));
                modalInfo.show();
            } else {
                document.getElementById('modal-cambio-mensaje').innerHTML = `<div class='alert alert-danger'>${data.message}</div>`;
                btn.disabled = false;
                btn.textContent = 'Confirmar cambio';
            }
        })
        .catch(err => {
            let errorMsg = 'Ha ocurrido un error inesperado. Intenta de nuevo.';
            if (err && err.message) errorMsg = err.message;
            if (err && err.errors) errorMsg = Object.values(err.errors).flat().join(', ');
            document.getElementById('modal-cambio-mensaje').innerHTML = `<div class='alert alert-danger'>${errorMsg}</div>`;
            btn.disabled = false;
            btn.textContent = 'Confirmar cambio';
        });
    });

    function startCooldown(msgHtml) {
        const mensaje = document.getElementById('modal-cambio-mensaje');
        const btnEnviarCodigo = document.getElementById('btn-enviar-codigo');
        if (cooldownInterval) clearInterval(cooldownInterval);
        cooldown = Math.max(0, Math.min(60, cooldown));
        function renderCooldown() {
            if (cooldown > 0) {
                mensaje.innerHTML = `<div class='alert alert-warning mt-2'>Puedes reenviar el código en <span id='cooldown-timer'></span></div>`;
                btnEnviarCodigo.disabled = true;
                document.getElementById('cooldown-timer').textContent = `${cooldown}s`;
            } else {
                mensaje.innerHTML = '';
                btnEnviarCodigo.disabled = false;
            }
        }
        renderCooldown();
        if (cooldown > 0) {
            cooldownInterval = setInterval(() => {
                cooldown--;
                renderCooldown();
                if (cooldown <= 0) {
                    clearInterval(cooldownInterval);
                    renderCooldown();
                }
            }, 1000);
        }
    }

    // Evento para enviar código manualmente
    document.getElementById('btn-enviar-codigo').addEventListener('click', function() {
        const doc_usuario = document.getElementById('modal-doc-usuario').value;
        const id_tarjeta = document.getElementById('modal-id-tarjeta').value;
        const correo_usuario = document.getElementById('modal-correo-usuario').value;
        this.disabled = true;
        document.getElementById('modal-cambio-mensaje').innerHTML = `<span class='text-info'>Enviando código...</span>`;
        fetch("{{ route('gestor-recargas.titularidad.enviar-codigo') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
            },
            body: JSON.stringify({ doc_usuario, id_tarjeta, correo: correo_usuario })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                cooldown = 60;
                startCooldown(`<div class='alert alert-success'>${data.message}</div>`);
            } else if(data.wait) {
                cooldown = data.wait;
                startCooldown(`<div class='alert alert-danger'>${data.message}</div>`);
            } else {
                document.getElementById('modal-cambio-mensaje').innerHTML = `<div class='alert alert-danger'>${data.message}</div>`;
                document.getElementById('btn-enviar-codigo').disabled = false;
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            document.getElementById('modal-cambio-mensaje').innerHTML = `<div class='alert alert-danger'>Error de comunicación con el servidor. Por favor, revisa la consola o recarga la página.</div>`;
            document.getElementById('btn-enviar-codigo').disabled = false;
        });
    });

    // Limpiar backdrop y estado cuando se cierra cualquier modal
    document.getElementById('modalCambioTitularidad').addEventListener('hidden.bs.modal', function () {
        // Eliminar backdrops huérfanos
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
        // Limpiar mensajes y resetear estado del formulario
        document.getElementById('modal-cambio-mensaje').innerHTML = '';
        document.getElementById('modal-reenviar-codigo').innerHTML = '';
    });

    document.getElementById('modalInfoTitularidad').addEventListener('hidden.bs.modal', function () {
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    });
});
</script>
@endpush
