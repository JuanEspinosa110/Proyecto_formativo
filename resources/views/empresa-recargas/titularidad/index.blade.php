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
    <div class="modal fade" id="modalCambioTitularidad" tabindex="-1" aria-labelledby="modalCambioTitularidadLabel" aria-hidden="true">
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
                <div class="mb-3">
                    <label for="codigo_verificacion" class="form-label">Código de verificación (enviado al correo del usuario)</label>
                    <input type="text" class="form-control" id="codigo_verificacion" name="codigo_verificacion" required>
                </div>
                <button type="submit" class="btn btn-success">Confirmar cambio</button>
            </form>
            <div id="modal-cambio-mensaje" class="mt-2"></div>
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
                                    const idTarjeta = this.getAttribute('data-id');
                                    if (usuarioActual.tarjeta_activa_id && usuarioActual.tarjeta_activa_id == idTarjeta) {
                                        tarjetasCambioDiv.innerHTML += `<div class='alert alert-danger mt-2'>Debes seleccionar una tarjeta diferente a la actual.</div>`;
                                        return;
                                    }
                                    // Consultar cooldown antes de intentar enviar código
                                    fetch("{{ route('gestor-recargas.titularidad.consultar-cooldown') }}", {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
                                        },
                                        body: JSON.stringify({ doc_usuario: usuarioActual.doc_usuario })
                                    })
                                    .then(res => res.json())
                                    .then(cooldownData => {
                                        if (cooldownData.cooldown_active) {
                                            cooldown = cooldownData.wait;
                                            document.getElementById('modal-doc-usuario').value = usuarioActual.doc_usuario;
                                            document.getElementById('modal-id-tarjeta').value = idTarjeta;
                                            var modal = new bootstrap.Modal(document.getElementById('modalCambioTitularidad'));
                                            modal.show();
                                            startCooldown(`<div class='alert alert-warning mt-2'>Ya se envió un código recientemente. Espera para reenviar.</div>`);
                                        } else {
                                            // No hay cooldown, ahora sí enviar código
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
                                                } else if(data.wait) {
                                                    cooldown = data.wait;
                                                    startCooldown(`<div class='alert alert-danger'>${data.message}</div>`);
                                                } else {
                                                    msg = `<div class='alert alert-danger'>${data.message}</div>`;
                                                    document.getElementById('modal-cambio-mensaje').innerHTML = msg;
                                                }
                                                document.getElementById('modal-doc-usuario').value = usuarioActual.doc_usuario;
                                                document.getElementById('modal-id-tarjeta').value = idTarjeta;
                                                var modal = new bootstrap.Modal(document.getElementById('modalCambioTitularidad'));
                                                modal.show();
                                            });
                                        }
                                    });
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
                            var modal = new bootstrap.Modal(document.getElementById('modalCambioTitularidad'));
                            modal.show();
                        });
                    });
                });
            }
        })
        .catch(() => {
            resultado.innerHTML = '<div class="alert alert-danger">Error en la búsqueda.</div>';
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
        const btn = this.querySelector('button[type=submit]');
        btn.disabled = true;
        fetch("{{ route('gestor-recargas.titularidad.cambiar') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
            },
            body: JSON.stringify({ doc_usuario, id_tarjeta, codigo_verificacion: codigo })
        })
        .then(res => res.json())
        .then(data => {
            let msg = '';
            if(data.success) {
                msg = `<div class='alert alert-success'>${data.message}</div>`;
                setTimeout(() => window.location.reload(), 1500);
            } else {
                msg = `<div class='alert alert-danger'>${data.message}</div>`;
                btn.disabled = false;
            }
            document.getElementById('modal-cambio-mensaje').innerHTML = msg;
        });
    });

    function startCooldown(msgHtml) {
        const mensaje = document.getElementById('modal-cambio-mensaje');
        if (cooldownInterval) clearInterval(cooldownInterval);
        // Forzar cooldown a máximo 60 y mínimo 0
        cooldown = Math.max(0, Math.min(60, cooldown));
        if (cooldown === 0) {
            mensaje.innerHTML = '';
            return;
        }
        mensaje.innerHTML = `<div class='alert alert-warning mt-2'>Puedes reenviar el código en <span id='cooldown-timer'></span></div>`;
        function renderCooldown() {
            // Siempre mostrar solo segundos
            let display = `${cooldown}s`;
            document.getElementById('cooldown-timer').textContent = display;
        }
        renderCooldown();
        cooldownInterval = setInterval(() => {
            cooldown--;
            renderCooldown();
            if (cooldown <= 0) {
                clearInterval(cooldownInterval);
                mensaje.innerHTML = '';
            }
        }, 1000);
    }
});
</script>
@endpush
