@extends('superadmin.layouts.admin')

@section('title', 'Crear Licencia - Paso 2')

@section('content')
<div class="container sa-licencia-container">
    <!-- Header con progreso -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="sa-licencia-title">Seleccionar Plan de Licencia</h2>
                <p class="text-muted">Configure el plan y vigencia para {{ $datos['nombre_empresa'] }}</p>
            </div>
            <div>
                <span class="badge bg-success" style="font-size: 1rem; padding: 0.5rem 1rem;">
                    Paso 2 de 2
                </span>
            </div>
        </div>

        <!-- Barra de progreso -->
        <div class="progress" style="height: 4px;">
            <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Información de la empresa -->
    <div class="alert alert-info d-flex justify-content-between align-items-center mb-4">
        <div>
            <strong><i class="fas fa-building me-2"></i>Empresa:</strong> {{ $datos['nombre_empresa'] }}
        </div>
        <div>
            <strong><i class="fas fa-id-card me-2"></i>NIT:</strong> {{ number_format($datos['NIT'], 0, ',', '.') }}
        </div>
    </div>

    <form action="{{ route('superadmin.licencias.store') }}" method="POST">
        @csrf

        <!-- Selección de Planes -->
        <div class="card sa-licencia-card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-layer-group me-2 text-primary"></i>
                    Planes Disponibles
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @foreach($planes as $plan)
                    <div class="col-md-3">
                        <label class="w-100 h-100" style="cursor: pointer;">
                            <input type="radio"
                                name="id_plan"
                                value="{{ $plan->id_plan }}"
                                class="d-none plan-radio"
                                {{ old('id_plan') == $plan->id_plan ? 'checked' : '' }}
                                required>
                            <div class="card h-100 sa-licencia-plan-card {{ old('id_plan') == $plan->id_plan ? 'plan-seleccionado' : '' }}">
                                <div class="card-body text-center p-4">
                                    @if($plan->nombre_plan == 'PREMIUM')
                                    <span class="badge bg-primary mb-3">RECOMENDADO</span>
                                    @endif

                                    <h4 class="fw-bold mb-3">{{ $plan->nombre_plan }}</h4>

                                    <div class="sa-licencia-plan-price mb-4">
                                        ${{ number_format($plan->precio, 0, ',', '.') }}
                                    </div>

                                    <ul class="list-unstyled sa-licencia-feature-list text-start mb-4">
                                        <li><i class="fas fa-check text-primary me-2"></i> {{ $plan->duracion_meses }} meses de servicio</li>
                                        <li><i class="fas fa-check text-primary me-2"></i> Soporte técnico</li>
                                        <li><i class="fas fa-check text-primary me-2"></i> {{ $plan->descripcion }}</li>
                                    </ul>

                                    <button type="button" class="btn w-100 sa-licencia-btn-select">
                                        {{ old('id_plan') == $plan->id_plan ? 'Plan Seleccionado' : 'Seleccionar Plan' }}
                                    </button>
                                </div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
                @error('id_plan')<div class="text-danger mt-3">{{ $message }}</div>@enderror
            </div>
        </div>

        <!-- Vigencia de la Licencia -->
        <div class="card sa-licencia-card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt me-2 text-primary"></i>
                    Vigencia de la Licencia
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="sa-licencia-label">Fecha de Inicio *</label>
                        <input type="date"
                            name="fecha_inicio"
                            id="fecha_inicio"
                            class="form-control sa-licencia-input @error('fecha_inicio') is-invalid @enderror"
                            value="{{ old('fecha_inicio', date('Y-m-d')) }}"
                            required>
                        <small class="form-text text-muted d-block mt-2">No puede ser menor a hoy</small>
                        @error('fecha_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="sa-licencia-label">Fecha de Vencimiento *</label>
                        <input type="date"
                            name="fecha_vencimiento"
                            id="fecha_vencimiento"
                            class="form-control sa-licencia-input @error('fecha_vencimiento') is-invalid @enderror"
                            value="{{ old('fecha_vencimiento') }}"
                            readonly>
                        <small class="form-text text-muted d-block mt-2">Se calcula automáticamente</small>
                        @error('fecha_vencimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <label class="sa-licencia-label">Duración del Plan *</label>
                        <div class="input-group">
                            <input type="number"
                                id="duracion_meses"
                                class="form-control sa-licencia-input"
                                value="0"
                                readonly>
                            <span class="input-group-text">meses</span>
                        </div>
                    </div>
                </div>
                <div class="mt-3 small text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    La licencia se activará automáticamente en la fecha de inicio. La fecha de vencimiento se calcula automáticamente según la duración del plan seleccionado.
                </div>
            </div>
        </div>

        <!-- Botones de navegación -->
        <div class="d-flex justify-content-between">
            <a href="{{ route('superadmin.licencias.create') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver al Paso 1
            </a>
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-check me-2"></i>Crear Licencia
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const planRadios = document.querySelectorAll('.plan-radio');
        const fechaInicio = document.querySelector('#fecha_inicio');
        const fechaVencimiento = document.querySelector('#fecha_vencimiento');
        const duracionMeses = document.querySelector('#duracion_meses');
        const hoy = new Date().toISOString().split('T')[0];

        // Establecer el mínimo a hoy
        fechaInicio.setAttribute('min', hoy);

        /**
         * Valida que la fecha de inicio no sea menor a hoy
         */
        function validarFechaInicio() {
            const fechaSeleccionada = fechaInicio.value;
            if (fechaSeleccionada < hoy) {
                fechaInicio.value = hoy;
            }
        }

        /**
         * Obtiene los datos del plan mediante AJAX
         */
        async function obtenerDatosPlan(idPlan) {
            try {
                const response = await fetch(`/superadmin/licencias/plan/${idPlan}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                console.log('Datos del plan obtenidos:', data);
                return data;
            } catch (error) {
                console.error('Error al obtener datos del plan:', error);
                duracionMeses.value = 0;
                fechaVencimiento.value = '';
                return null;
            }
        }

        /**
         * Calcula la fecha de vencimiento basada en fecha de inicio + duración en meses
         */
        function calcularFechaVencimiento() {
            // Validar que tengamos fecha inicio y duración
            if (!fechaInicio.value || !duracionMeses.value || duracionMeses.value == 0) {
                fechaVencimiento.value = '';
                return false;
            }

            try {
                const fecha = new Date(fechaInicio.value + 'T00:00:00');
                const meses = parseInt(duracionMeses.value, 10);

                // Validar que sea un número válido
                if (isNaN(meses) || meses <= 0) {
                    fechaVencimiento.value = '';
                    return false;
                }

                // Sumar los meses
                fecha.setMonth(fecha.getMonth() + meses);

                const year = fecha.getFullYear();
                const month = String(fecha.getMonth() + 1).padStart(2, '0');
                const day = String(fecha.getDate()).padStart(2, '0');

                const fechaCalculada = `${year}-${month}-${day}`;
                fechaVencimiento.value = fechaCalculada;

                console.log(`Fecha de vencimiento calculada: ${fechaCalculada} (desde ${fechaInicio.value} + ${meses} meses)`);
                return true;
            } catch (error) {
                console.error('Error al calcular fecha de vencimiento:', error);
                fechaVencimiento.value = '';
                return false;
            }
        }

        /**
         * Manejo del cambio de plan
         */
        async function manejarCambioPlan(radio) {
            if (!radio || !radio.value) return;

            // 1. Limpiar todos los planes (volver al estado base)
            document.querySelectorAll('.sa-licencia-plan-card').forEach(card => {
                card.classList.remove('plan-seleccionado');
                // Opcional: Cambiar el texto del botón de nuevo a original
                const btn = card.querySelector('.sa-licencia-btn-select');
                if (btn) btn.textContent = 'Seleccionar Plan';
            });

            // 2. Encontrar la tarjeta correspondiente al radio actual
            // Como el radio está dentro del LABEL, buscamos el div de la tarjeta dentro de ese mismo LABEL
            const labelPadre = radio.closest('label');
            const card = labelPadre.querySelector('.sa-licencia-plan-card');

            if (card) {
                card.classList.add('plan-seleccionado');
                const btn = card.querySelector('.sa-licencia-btn-select');
                if (btn) btn.textContent = '✓ Seleccionado';
            }

            // 3. obtener datos del plan...
            const datosPlan = await obtenerDatosPlan(radio.value);
            if (datosPlan && datosPlan.duracion_meses) {
                duracionMeses.value = datosPlan.duracion_meses;
                if (typeof calcularFechaVencimiento === "function") {
                    calcularFechaVencimiento();
                }
            }
        }

        // Event listeners para los radio buttons
        planRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                console.log('Radio button changed:', this.value);
                manejarCambioPlan(this);
            });
        });

        // Event listeners para los botones "Seleccionar Plan"
        document.querySelectorAll('.sa-licencia-btn-select').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                // Encontrar el radio input más cercano
                const label = this.closest('label');
                if (!label) {
                    console.error('No se encontró el label padre del botón');
                    return;
                }

                const radio = label.querySelector('.plan-radio');
                if (radio) {
                    console.log('Seleccionando plan:', radio.value);
                    radio.checked = true;
                    // Disparar el evento change para que se ejecute la lógica
                    radio.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                } else {
                    console.error('No se encontró el radio button dentro del label');
                }
            });
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            const planChecked = document.querySelector('input[name="id_plan"]:checked');

            if (!planChecked) {
                e.preventDefault(); // Detiene el envío

                // Si usas SweetAlert2 (que es lo más común en Laravel):
                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: 'Debes seleccionar un plan de licencia para continuar.',
                    confirmButtonColor: '#3085d6'
                });

                // Si usas alertas simples:
                // alert("Debes seleccionar un plan de licencia para continuar.");
            }
        });

        // Event listener para cambio de fecha de inicio
        fechaInicio.addEventListener('change', function() {
            console.log('Fecha de inicio cambiada a:', this.value);
            validarFechaInicio();
            calcularFechaVencimiento();
        });

        // Event listener para cambio de duración (aunque es readonly, por si acaso)
        duracionMeses.addEventListener('change', function() {
            console.log('Duración de meses cambiada a:', this.value);
            calcularFechaVencimiento();
        });

        // Validar y calcular al cargar si hay un plan pre-seleccionado
        const planSeleccionado = document.querySelector('.plan-radio:checked');
        if (planSeleccionado) {
            console.log('Plan pre-seleccionado encontrado:', planSeleccionado.value);
            manejarCambioPlan(planSeleccionado);
        }

        // Validar fecha inicial al cargar
        validarFechaInicio();
    });
</script>
@endsection