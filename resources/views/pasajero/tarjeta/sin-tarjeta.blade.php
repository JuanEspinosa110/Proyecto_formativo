@extends('pasajero.layouts.app')

@section('title', 'Activa tu tarjeta SIGU')

@section('content')
<div class="sin-tarjeta-wrap py-5">
    <div class="container">
        <div class="row align-items-center justify-content-center">

            {{-- ── Lado Izquierdo: Formularios y Wizard ── --}}
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="sin-tarjeta-box shadow-sm border border-light bg-white rounded-4 p-4 p-md-5 text-start w-100 mx-0">

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="sin-tarjeta-icon m-0 shadow-sm" style="width: 65px; height: 65px; min-width: 65px;">
                            <span class="material-symbols-rounded" style="font-size: 2rem;">credit_card</span>
                        </div>
                        <div>
                            <h2 class="h3 fw-bold mb-1 text-dark" style="font-family: var(--ff-d);">Bienvenido a SIGU</h2>
                            <p class="text-muted mb-0" style="font-size: .9rem;">Inicia tu viaje con nosotros.</p>
                        </div>
                    </div>

                    @if(session('info'))
                    <div class="pas-alert info mb-4">
                        <span class="material-symbols-rounded">info</span>
                        {{ session('info') }}
                    </div>
                    @endif

                    @if(session('success'))
                    <div class="pas-alert ok mb-4">
                        <span class="material-symbols-rounded">check_circle</span>
                        {{ session('success') }}
                    </div>
                    @endif


                    {{-- ── WIZARD CONTENEDOR ── --}}
                    <div id="wizard-container">

                        {{-- Paso 1: Elegir Opción --}}
                        <div id="step-1" class="wizard-step active-step">
                            <h4 class="fw-bold mb-2">Selecciona una opción</h4>
                            <p class="text-muted small mb-4">Para usar el sistema integrado de transporte necesitas una tarjeta. ¿Cómo prefieres continuar?</p>

                            <div class="d-flex flex-column gap-3">
                                {{-- Opción 1 --}}
                                <button type="button" class="btn-option text-start d-flex align-items-center gap-3 p-3 rounded-3 border bg-white shadow-sm w-100" onclick="goToStep(2, 'registro')">
                                    <div class="icon-box rounded bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                                        <span class="material-symbols-rounded fs-4">qr_code_scanner</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1 fs-6 fw-bold">Ya tengo una tarjeta física</h5>
                                        <p class="mb-0 text-muted" style="font-size: .8rem;">Vincular tarjeta existente a mi cuenta</p>
                                    </div>
                                    <span class="material-symbols-rounded text-muted">chevron_right</span>
                                </button>

                                {{-- Opción 2 --}}
                                <button type="button" class="btn-option text-start d-flex align-items-center gap-3 p-3 rounded-3 border bg-white shadow-sm w-100" onclick="goToStep(2, 'compra')">
                                    <div class="icon-box rounded bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                                        <span class="material-symbols-rounded fs-4">add_card</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1 fs-6 fw-bold">Aún no tengo tarjeta</h5>
                                        <p class="mb-0 text-muted" style="font-size: .8rem;">Solicitar una tarjeta nueva para retirar</p>
                                    </div>
                                    <span class="material-symbols-rounded text-muted">chevron_right</span>
                                </button>
                            </div>
                        </div>

                        {{-- Paso 2 (Variante A): Registrar Tarjeta --}}
                        <div id="step-2-registro" class="wizard-step d-none">
                            <button type="button" class="btn btn-sm btn-link text-decoration-none text-muted p-0 mb-3 d-inline-flex align-items-center" onclick="goToStep(1)">
                                <span class="material-symbols-rounded" style="font-size: 1.1rem;">arrow_back</span> Volver a opciones
                            </button>

                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-success bg-opacity-25 text-success rounded-pill px-3 py-2">Paso 2 de 2</span>
                            </div>
                            <h4 class="fw-bold mb-2">Vincular mi tarjeta física</h4>
                            <p class="text-muted small mb-4">Ingresa el código alfanumérico que se encuentra en el reverso de tu tarjeta SIGU para asociarla a tu perfil.</p>

                            @error('codigo_tarjeta')
                            <div class="pas-alert warn mb-3">
                                <span class="material-symbols-rounded">warning</span>
                                {{ $message }}
                            </div>
                            @enderror

                            <form method="POST" action="{{ route('pasajero.tarjeta.registrar') }}" novalidate>
                                @csrf
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-dark mb-1">Código visible de 10-15 dígitos</label>
                                    <div class="input-group input-group-lg shadow-sm">
                                        <span class="input-group-text bg-white border-end-0 text-muted px-3">
                                            <span class="material-symbols-rounded">pin</span>
                                        </span>
                                        <input type="text"
                                               name="codigo_tarjeta"
                                               class="form-control border-start-0 ps-0 @error('codigo_tarjeta') is-invalid @enderror"
                                               placeholder="Ej: SIGU-2025-00123"
                                               value="{{ old('codigo_tarjeta') }}"
                                               style="font-family:monospace;letter-spacing:.05em"
                                               required autofocus>
                                    </div>
                                    <div class="form-text mt-2"><span class="material-symbols-rounded text-info align-middle" style="font-size: 1rem;">info</span> Asegúrate de incluir los guiones si los tiene.</div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm d-flex justify-content-center align-items-center gap-2">
                                    <span class="material-symbols-rounded">link</span> Confirmar Vinculación
                                </button>
                            </form>
                        </div>


                        {{-- Paso 2 (Variante B): Solicitar Nueva --}}
                        <div id="step-2-compra" class="wizard-step d-none">
                            <button type="button" class="btn btn-sm btn-link text-decoration-none text-muted p-0 mb-3 d-inline-flex align-items-center" onclick="goToStep(1)">
                                <span class="material-symbols-rounded" style="font-size: 1.1rem;">arrow_back</span> Volver a opciones
                            </button>

                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-primary bg-opacity-25 text-primary rounded-pill px-3 py-2">Paso 2 de 2</span>
                            </div>
                            <h4 class="fw-bold mb-2">Solicitar nueva tarjeta</h4>
                            <p class="text-muted small mb-4">Indica en qué punto autorizado deseas retirar tu tarjeta. Una vez tramitado, debes presentar tu documento de identidad original.</p>

                            <form method="POST" action="{{ route('pasajero.tarjeta.comprar') }}" novalidate>
                                @csrf
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-dark mb-1">Punto de retiro preferido</label>
                                    <div class="input-group input-group-lg shadow-sm">
                                        <span class="input-group-text bg-white border-end-0 text-muted px-3">
                                            <span class="material-symbols-rounded">location_on</span>
                                        </span>
                                        <select name="punto_compra" class="form-select border-start-0 ps-0 @error('punto_compra') is-invalid @enderror" required>
                                            <option value="" disabled selected>Selecciona un punto de entrega...</option>
                                            <option value="Terminal de Transportes Principal">Terminal de Transportes Principal</option>
                                            <option value="Estación Central Metro">Estación Central</option>
                                            <option value="Centro Comercial La Casona - Local 102">C.C. La Casona (Local 102)</option>
                                            <option value="Punto SAC Alcaldía">Punto SAC (Atención al Ciudadano Alcaldía)</option>
                                        </select>
                                    </div>
                                    @error('punto_compra')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text mt-2">La tarjeta tendrá un costo de emisión ($5.000) que se pagará al momento de retirarla.</div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm d-flex justify-content-center align-items-center gap-2">
                                    <span class="material-symbols-rounded">send</span> Agendar Retiro
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-center">
                        <p style="font-size:.8rem;color:var(--text-3);margin:0">
                            ¿Necesitas ayuda extra? <a href="#" class="text-decoration-none fw-semibold">Contacta a soporte</a>.
                        </p>
                    </div>

                </div>
            </div>

            {{-- ── Lado Derecho: Info Extra e Ilustrativa ── --}}
            <div class="col-lg-5 offset-lg-1 d-none d-md-block">
                <div class="pe-xl-5">
                    <div class="mb-4">
                        <span class="badge bg-warning text-dark mb-2 px-3 py-2 rounded-pill fw-bold">Beneficios SIGU</span>
                        <h3 class="fw-bold mb-3" style="font-family: var(--ff-d);">Mucho más que un pasaje</h3>
                        <p class="text-muted" style="font-size: 1.05rem;">
                            Vincular tu tarjeta te permite acceder a una gran cantidad de servicios digitales y físicos.
                        </p>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 48px; height: 48px;">
                            <span class="material-symbols-rounded">account_balance_wallet</span>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1 fs-6">Protección de Saldo</h5>
                            <p class="text-muted small mb-0">Si tu tarjeta registrada se pierde, puedes reportarla y el saldo remanente no se perderá.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 48px; height: 48px;">
                            <span class="material-symbols-rounded">alt_route</span>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1 fs-6">Integración de Rutas</h5>
                            <p class="text-muted small mb-0">Disfruta de transbordo sin costo o con tarifa preferencial (según el esquema local).</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 48px; height: 48px;">
                            <span class="material-symbols-rounded">storefront</span>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1 fs-6">Más de 500 Puntos de Red</h5>
                            <p class="text-muted small mb-0">Recarga en puntos físicos autorizados, droguerías o mediante aplicaciones bancarias aliadas.</p>
                        </div>
                    </div>

                   <!-- <div class="mt-5 bg-light p-4 rounded-4 border border-info border-opacity-25 relative overflow-hidden">
                        <div style="position: relative; z-index: 2;">
                            <h6 class="fw-bold text-dark mb-2">¡Explora antes de viajar!</h6>
                            <p class="text-muted small mb-3">Conoce todas las rutas y paraderos de la ciudad incluso antes de registrar tu tarjeta.</p>
                            <a href="{{ route('pasajero.rutas.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-bold shadow-sm">Ver Mapa de Rutas</a>
                        </div>
                    </div>-->

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .sin-tarjeta-wrap { min-height: calc(100vh - var(--nav-h)); background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); }
    .btn-option { transition: all 0.2s ease-in-out; border-color: #dee2e6 !important; }
    .btn-option:hover { border-color: var(--pas) !important; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; background-color: #f8faff !important; }
    .btn-option:focus { outline: none; border-color: var(--pas) !important; box-shadow: 0 0 0 4px rgba(37,99,235,0.15) !important; }
    .wizard-step { animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@push('scripts')
<script>
    function goToStep(stepNumber, variant = null) {
        // Ocultar todos
        document.getElementById('step-1').classList.add('d-none');
        document.getElementById('step-2-registro').classList.add('d-none');
        document.getElementById('step-2-compra').classList.add('d-none');

        if (stepNumber === 1) {
            document.getElementById('step-1').classList.remove('d-none');
        } else if (stepNumber === 2) {
            if (variant === 'registro') {
                document.getElementById('step-2-registro').classList.remove('d-none');
                // Auto focus al input
                setTimeout(() => document.querySelector('input[name="codigo_tarjeta"]').focus(), 100);
            } else if (variant === 'compra') {
                document.getElementById('step-2-compra').classList.remove('d-none');
            }
        }
    }

    // Auto-recuperar estado en caso de error de validación de Laravel
    document.addEventListener("DOMContentLoaded", function() {
        @if($errors->has('codigo_tarjeta'))
            goToStep(2, 'registro');
        @elseif($errors->has('punto_compra'))
            goToStep(2, 'compra');
        @endif
    });
</script>
@endpush
