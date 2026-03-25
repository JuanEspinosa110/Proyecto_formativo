@extends('pasajero.layouts.app')

@section('title', 'Verificar Traspaso')

@section('content')
<div class="perfil-wrap py-5 px-3 px-md-4">
    
    <div class="text-center mb-5">
        <div class="d-inline-flex bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3">
            <span class="material-symbols-rounded" style="font-size: 3rem;">mark_email_read</span>
        </div>
        <h2 class="fw-bold" style="font-family: var(--ff-d);">Verificación de Seguridad</h2>
        <p class="text-muted max-w-md mx-auto">Para proteger tu saldo, hemos enviado un código de 6 dígitos (OTP) a tu correo electrónico registrado.</p>
    </div>

    @if(session('success'))
        <div class="pas-alert ok mb-4">
            <span class="material-symbols-rounded">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="pas-alert warn mb-4">
            <span class="material-symbols-rounded">warning</span>
            {{ session('error') }}
        </div>
    @endif

    <div class="perfil-card max-w-sm mx-auto shadow-sm" style="max-width: 480px;">
        <div class="perfil-card-body p-4 p-md-5">
            
            <form method="POST" action="{{ route('pasajero.tarjeta.confirmar-cambio') }}">
                @csrf
                <div class="mb-4 text-center">
                    <label class="form-label fw-bold text-dark mb-3">Ingresa tu código de seguridad</label>
                    <input type="text"
                           name="codigo"
                           class="form-control form-control-lg text-center fw-bold text-primary @error('codigo') is-invalid @enderror"
                           placeholder="0 0 0 0 0 0"
                           maxlength="6"
                           value="{{ old('codigo') }}"
                           style="letter-spacing: .5em; font-size: 1.5rem;"
                           required autofocus>
                    
                    @error('codigo')
                        <div class="invalid-feedback fw-semibold mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold d-flex justify-content-center align-items-center gap-2 mb-3">
                    <span class="material-symbols-rounded">gpp_good</span> Confirmar y Traspasar
                </button>
            </form>

            <div class="text-center border-top pt-3 mt-4">
                <p class="text-muted small mb-2">¿Si no completas este paso, tu tarjeta anterior se mantendrá activa.</p>
                <a href="{{ route('pasajero.tarjeta.cambiar') }}" class="btn btn-sm btn-link text-decoration-none text-secondary">
                    <span class="material-symbols-rounded align-middle" style="font-size: 1rem;">cancel</span> Cancelar proceso
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
