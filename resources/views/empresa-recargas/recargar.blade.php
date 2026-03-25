@extends('empresa-recargas.layouts.app')

@section('title', 'Realizar Recarga')

@section('content')
<div class="admin-dashboard sigu-fade">
    <div class="sigu-page-hd">
        <div>
            <h1 class="sigu-page-title">Recargar Tarjeta</h1>
            <p class="sigu-page-sub">Añadir saldo a la tarjeta de un pasajero</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <form action="{{ route('gestor-recargas.recargar.store') }}" method="POST" id="formRecarga">
                    @csrf
                    
                    <!-- Paso 1: Consultar Tarjeta -->
                    <div class="mb-4">
                        <label for="id_tarjeta" class="form-label fw-bold">ID / Código de la Tarjeta</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-lg @error('id_tarjeta') is-invalid @enderror" 
                                   id="id_tarjeta" name="id_tarjeta" value="{{ old('id_tarjeta') }}" required autofocus placeholder="Ej: TARJ-INT-10001">
                            <button class="btn px-4 fw-medium text-white" style="background-color: #a855f7; border-color: #a855f7;" type="button" id="btnConsultar" onmouseover="this.style.backgroundColor='#9333ea'" onmouseout="this.style.backgroundColor='#a855f7'">
                                Buscar <span class="material-symbols-rounded align-middle ms-1 fs-5">search</span>
                            </button>
                        </div>
                        <div class="form-text mt-2 text-primary" id="mensajeInfoTarjeta">Ingrese el ID para buscar al pasajero.</div>
                        @error('id_tarjeta')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Datos del pasajero (Oculto hasta consultar) -->
                    <div id="resultadoConsulta" class="d-none alert alert-info border-0 rounded-3 mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white text-info rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px">
                                <span class="material-symbols-rounded fs-5">person</span>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold" id="lblPropietario">Nombre del Pasajero</h6>
                                <p class="mb-0 small">Saldo actual: <strong id="lblSaldo">$0</strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 2: Monto a recargar (Oculto hasta consultar) -->
                    <div id="panelMonto" class="d-none">
                        <hr class="my-4 text-muted">
                        <div class="mb-4">
                            <label for="monto" class="form-label fw-bold">Monto a Recargar ($)</label>
                            <input type="number" step="1" class="form-control form-control-lg @error('monto') is-invalid @enderror" 
                                   id="monto" name="monto" value="{{ old('monto') }}" min="1000">
                            @error('monto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light btn-lg fw-medium w-50" id="btnLimpiar">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg fw-bold w-50" id="btnConfirmar">
                                Confirmar Recarga
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const btnConsultar = document.getElementById('btnConsultar');
        const inTarjeta = document.getElementById('id_tarjeta');
        const panelMonto = document.getElementById('panelMonto');
        const resultadoConsulta = document.getElementById('resultadoConsulta');
        const lblPropietario = document.getElementById('lblPropietario');
        const lblSaldo = document.getElementById('lblSaldo');
        const mensajeInfoTarjeta = document.getElementById('mensajeInfoTarjeta');
        const btnLimpiar = document.getElementById('btnLimpiar');
        const inMonto = document.getElementById('monto');

        // Función para consultar la tarjeta
        const consultar = async () => {
            const id = inTarjeta.value.trim();
            if (!id) return;

            // Feedback visual
            btnConsultar.innerHTML = 'Buscando...';
            btnConsultar.disabled = true;
            mensajeInfoTarjeta.innerHTML = '';
            resultadoConsulta.classList.add('d-none');
            panelMonto.classList.add('d-none');
            resultadoConsulta.classList.remove('alert-danger', 'alert-success', 'alert-info');

            try {
                const response = await fetch(`{{ route('gestor-recargas.recargar.consultar') }}?id_tarjeta=${id}`);
                const data = await response.json();

                if (data.success) {
                    // Tarjeta válida
                    lblPropietario.textContent = data.propietario;
                    lblSaldo.textContent = '$' + new Intl.NumberFormat('es-CO').format(data.saldo_actual);
                    resultadoConsulta.classList.add('alert-success', 'text-success');
                    resultadoConsulta.classList.remove('d-none');
                    
                    // Bloquear input ID y habilitar monto
                    inTarjeta.readOnly = true;
                    btnConsultar.classList.add('d-none');
                    panelMonto.classList.remove('d-none');
                    inMonto.required = true;
                    inMonto.focus();
                } else {
                    // Tarjeta inválida o error
                    resultadoConsulta.classList.add('alert-danger');
                    resultadoConsulta.classList.remove('d-none');
                    lblPropietario.textContent = 'Error';
                    lblSaldo.textContent = data.message;
                }
            } catch (error) {
                console.error(error);
                resultadoConsulta.classList.add('alert-danger');
                resultadoConsulta.classList.remove('d-none');
                lblPropietario.textContent = 'Error de conexión';
                lblSaldo.textContent = 'No se pudo comunicar con el servidor.';
            } finally {
                btnConsultar.innerHTML = 'Buscar <span class="material-symbols-rounded align-middle ms-1 fs-5">search</span>';
                btnConsultar.disabled = false;
            }
        };

        btnConsultar.addEventListener('click', consultar);

        // Permitir Enter en el input de tarjeta para buscar
        inTarjeta.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if(!inTarjeta.readOnly) {
                    consultar();
                }
            }
        });

        // Botón Cancelar/Limpiar
        btnLimpiar.addEventListener('click', function() {
            inTarjeta.readOnly = false;
            inTarjeta.value = '';
            btnConsultar.classList.remove('d-none');
            panelMonto.classList.add('d-none');
            resultadoConsulta.classList.add('d-none');
            inMonto.required = false;
            inMonto.value = '';
            mensajeInfoTarjeta.innerHTML = 'Ingrese el ID para buscar al pasajero.';
            inTarjeta.focus();
        });

        // Si hay error de validación anterior y ya hay tarjeta, dejar abierto (para UX si falla server-side submit)
        @if(old('id_tarjeta') && $errors->has('monto'))
            // Ya se había consultado y falló la recarga
            consultar();
        @endif
    });
</script>
@endpush
</div>
@endsection
