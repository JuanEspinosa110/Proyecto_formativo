<!-- MODAL SIMULAR PAGO -->
<div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-dark text-white rounded-top-4 pb-3">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <span class="material-symbols-rounded">sensors</span> Simular Lectura de Tarjeta
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 py-3">
                
                <!-- Info del Viaje -->
                <div class="bg-light p-3 rounded-4 mb-3 border">
                    <div class="row g-2">
                        <div class="col-12">
                            <span class="text-muted d-block small fw-bold text-uppercase">Ruta</span>
                            <span id="modal_ruta_text" class="fw-bold text-dark">...</span>
                        </div>
                        <div class="col-12 border-top mt-2 pt-2">
                            <span class="text-muted d-block small fw-bold text-uppercase">Vehículo</span>
                            <span id="modal_placa_text" class="badge bg-primary bg-opacity-10 text-primary rounded-pill fw-bold">...</span>
                        </div>
                    </div>
                </div>

                <!-- Sección de Cobro -->
                <form id="formSimularPago" action="{{ route('simulacion.validar') }}" method="POST">
                    @csrf
                    <!-- ID VIAJE DINAMICO -->
                    <input type="hidden" name="id_viaje" id="modal_id_viaje">

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small text-uppercase">Ingresar Código de Tarjeta</label>
                        <input type="text" name="codigo_tarjeta" id="sim_codigo_tarjeta" class="form-control form-control-lg rounded-3 text-center font-monospace fw-bold" placeholder="Escriba o escanee..." required autocomplete="off" maxlength="20" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>

                    <button type="submit" id="btnValidarPago" class="btn btn-primary w-100 py-3 rounded-pill fw-bold fs-5 shadow-sm d-flex justify-content-center align-items-center gap-2">
                        <span class="material-symbols-rounded fs-4">payments</span> Validar Pago
                    </button>
                </form>

                <!-- Panel de Resultado -->
                <div id="resultPanel" class="mt-4 p-3 rounded-4 text-center d-none" style="transition: all 0.3s ease;">
                    <div id="resultIcon" class="mb-2"></div>
                    <h5 id="resultTitle" class="fw-bold mb-1"></h5>
                    <p id="resultMessage" class="small mb-0"></p>
                    <div id="resultBalance" class="mt-2 d-none">
                        <span class="badge bg-white bg-opacity-10 text-dark rounded-pill border">
                            Saldo: <span id="resultBalanceAmount" class="fw-bold"></span>
                        </span>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- SCRIPTS PARA EL MODAL DE PAGO -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formSimularPago');
    const inputTarjeta = document.getElementById('sim_codigo_tarjeta');
    const resultPanel = document.getElementById('resultPanel');
    const resultIcon = document.getElementById('resultIcon');
    const resultTitle = document.getElementById('resultTitle');
    const resultMessage = document.getElementById('resultMessage');
    const btnValidar = document.getElementById('btnValidarPago');

    // Al abrir el modal, enfocar input
    const modalElement = document.getElementById('modalPago');
    if(modalElement) {
        modalElement.addEventListener('shown.bs.modal', () => {
             inputTarjeta.focus();
             resultPanel.classList.add('d-none'); // Limpiar anterior
        });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const codigo = inputTarjeta.value.trim();
        if (!codigo) return;

        // Visual Procesando
        btnValidar.disabled = true;
        btnValidar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Validando...';
        resultPanel.classList.add('d-none');

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
             resultPanel.classList.remove('d-none');
             
             if (data.success) {
                 // Éxito
                 resultPanel.className = 'mt-4 p-3 rounded-4 text-center bg-success bg-opacity-10 border border-success border-opacity-25 text-success';
                 resultIcon.innerHTML = '<span class="material-symbols-rounded fs-1 text-success">check_circle</span>';
                 resultTitle.innerText = 'PAGO EXITOSO';
                 resultMessage.innerText = data.message;
                 
                 playAudioSuccess();
             } else {
                 // Error
                 resultPanel.className = 'mt-4 p-3 rounded-4 text-center bg-danger bg-opacity-10 border border-danger border-opacity-25 text-danger';
                 resultIcon.innerHTML = '<span class="material-symbols-rounded fs-1 text-danger">cancel</span>';
                 resultTitle.innerText = 'PAGO RECHAZADO';
                 resultMessage.innerText = data.message;

                 playAudioError();
             }
        })
        .catch(error => {
             console.error('Error:', error);
             resultPanel.className = 'mt-4 p-3 rounded-4 text-center bg-danger bg-opacity-10 border border-danger border-opacity-25 text-danger';
             resultIcon.innerHTML = '<span class="material-symbols-rounded fs-1 text-danger">error</span>';
             resultTitle.innerText = 'ERROR';
             resultMessage.innerText = 'Problemas al conectar con el servidor';
        })
        .finally(() => {
             btnValidar.disabled = false;
             btnValidar.innerHTML = '<span class="material-symbols-rounded fs-4">payments</span> Validar Pago';
             inputTarjeta.value = ''; // Limpiar
             inputTarjeta.focus();
        });
    });

    function playAudioSuccess() {
        try {
            const context = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = context.createOscillator();
            const gainNode = context.createGain();
            oscillator.connect(gainNode);
            gainNode.connect(context.destination);
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(800, context.currentTime);
            gainNode.gain.setValueAtTime(0.1, context.currentTime);
            oscillator.start();
            oscillator.stop(context.currentTime + 0.1);
        } catch(e) {}
    }

    function playAudioError() {
         try {
            const context = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = context.createOscillator();
            const gainNode = context.createGain();
            oscillator.connect(gainNode);
            gainNode.connect(context.destination);
            oscillator.type = 'sawtooth';
            oscillator.frequency.setValueAtTime(220, context.currentTime);
            gainNode.gain.setValueAtTime(0.1, context.currentTime);
            oscillator.start();
            oscillator.stop(context.currentTime + 0.3);
        } catch(e) {}
    }
});
</script>
