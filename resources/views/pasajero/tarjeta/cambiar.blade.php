@extends('pasajero.layouts.app')

@section('title', 'Cambiar Tarjeta SIGU')

@section('content')
<div class="perfil-wrap py-4 px-3 px-md-4">
    <div class="pas-header mb-4">
        <a href="{{ route('pasajero.saldo') }}" class="btn btn-sm btn-outline-secondary mb-3 d-inline-flex align-items-center">
            <span class="material-symbols-rounded align-middle me-1" style="font-size: 1.2rem;">arrow_back</span> Volver a Mi Tarjeta
        </a>
        <div>
            <h1 class="d-flex align-items-center gap-2">
                <span class="material-symbols-rounded text-primary" style="font-size: 2rem;">find_replace</span>
                Cambiar Tarjeta
            </h1>
            <p class="text-muted">Si tu tarjeta fue robada, extraviada o se deterioró, aquí puedes vincular una nueva y dar de baja la anterior.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="pas-alert warn mb-4">
            <span class="material-symbols-rounded">warning</span>
            {{ session('error') }}
        </div>
    @endif

    <div class="perfil-card">
        <div class="perfil-card-head bg-white border-bottom">
            <h3 class="fw-bold mb-0 text-dark">
                <span class="material-symbols-rounded text-primary me-2">credit_score</span>
                Vincular nueva tarjeta
            </h3>
        </div>
        <div class="perfil-card-body p-4">
            <form method="POST" action="{{ route('pasajero.tarjeta.iniciar-cambio') }}" novalidate>
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark">Código de la Nueva Tarjeta <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><span class="material-symbols-rounded text-muted">pin</span></span>
                        <input type="text"
                               name="codigo_tarjeta_nueva"
                               class="form-control @error('codigo_tarjeta_nueva') is-invalid @enderror"
                               placeholder="Ej: SIGU-2025-00123"
                               value="{{ old('codigo_tarjeta_nueva') }}"
                               style="font-family: monospace; letter-spacing: 1px;"
                               required>
                    </div>
                    @error('codigo_tarjeta_nueva')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                    <div class="form-text mt-1 text-muted" style="font-size:.8rem;">
                        <span class="material-symbols-rounded align-middle text-info" style="font-size: 1rem;">info</span>
                        Ingresa el código impreso al reverso de tu nueva tarjeta física.
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark">Motivo del Cambio <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><span class="material-symbols-rounded text-muted">format_list_bulleted</span></span>
                        <select name="motivo" class="form-select @error('motivo') is-invalid @enderror" required>
                            <option value="" disabled selected>Seleccione el motivo...</option>
                            <option value="Robo" {{ old('motivo') == 'Robo' ? 'selected' : '' }}>Robo</option>
                            <option value="Pérdida" {{ old('motivo') == 'Pérdida' ? 'selected' : '' }}>Pérdida / Extravío</option>
                            <option value="Deterioro Físico" {{ old('motivo') == 'Deterioro Físico' ? 'selected' : '' }}>Deterioro físico / Daño</option>
                            <option value="Otro" {{ old('motivo') == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>
                    @error('motivo')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="card bg-light border-warning border-opacity-50">
                        <div class="card-body p-3">
                            <div class="form-check form-switch d-flex align-items-center gap-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="traspasoSwitch" name="traspaso_saldo" {{ old('traspaso_saldo') ? 'checked' : '' }} style="width: 2.5em; height: 1.25em; cursor: pointer;">
                                <div>
                                    <label class="form-check-label fw-bold text-dark mb-1" for="traspasoSwitch" style="cursor: pointer;">
                                        Solicitar traspaso de saldo
                                    </label>
                                    <p class="text-muted small mb-0">Al activar esta opción, el saldo actual de tu tarjeta inactiva se moverá a tu nueva tarjeta. Te enviaremos un código a tu correo para autorizarlo.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-danger d-flex align-items-center mb-4 py-2" role="alert">
                    <span class="material-symbols-rounded text-danger fs-4 me-3">report</span>
                    <div style="font-size: .85rem;">
                        <strong>Atención:</strong> Tan pronto inicies el proceso, tu tarjeta anterior será <b>Inactivada de forma permanente</b> y no servirá para más viajes.
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold py-2 d-flex justify-content-center align-items-center gap-2">
                    Continuar <span class="material-symbols-rounded">arrow_forward</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
