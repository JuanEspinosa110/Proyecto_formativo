<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Código de Seguridad - Sistema de Transporte</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body { background-color: #f6f9ff; }
        .login-body { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .two-factor-card { max-width: 450px; width: 100%; }
        .logo-icon { max-height: 60px; }
    </style>
</head>
<body class="login-body">

<div class="two-factor-card mx-auto px-3">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-5">
            
            <div class="text-center mb-4">
                <img src="{{ asset('imagenes/logo-sigu.png') }}" alt="SIGU Logo" class="logo-icon mb-3" style="max-height: 50px;">
                <h4 class="fw-bold text-dark">Verificación de Seguridad</h4>
                <p class="text-muted small">Hemos enviado un código de 6 dígitos a su correo. Ingréselo para continuar.</p>
            </div>
            
            @if (session('error'))
                <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-octagon me-1"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ session('status') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('2fa.verify') }}" class="needs-validation" novalidate>
                @csrf

                <div class="mb-4 text-center">
                    <label for="codigo" class="form-label fw-bold">Código de 6 dígitos</label>
                    <input type="text" id="codigo" name="codigo" class="form-control form-control-lg text-center fw-bold text-primary @error('codigo') is-invalid @enderror" style="letter-spacing: 12px; font-size: 1.5rem;" maxlength="6" autofocus required autocomplete="off" placeholder="------">
                    
                    @error('codigo')
                        <div class="invalid-feedback text-start">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-4 form-check text-start">
                    <input type="checkbox" class="form-check-input" id="remember_device" name="remember_device" value="1" checked>
                    <label class="form-check-label text-muted small" for="remember_device" style="user-select: none; cursor: pointer;">
                        No volver a pedir código en este equipo por 1 día
                    </label>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold">
                        <i class="bi bi-shield-check me-2"></i> Verificar
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center">
                <span class="text-muted small">¿No recibiste el código?</span>
                <form method="POST" action="{{ route('2fa.resend') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline fw-bold text-decoration-none">
                        Reenviar código
                    </button>
                </form>
            </div>

            <div class="mt-3 text-center">
                <a href="{{ route('login') }}" class="text-muted small text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> Volver al inicio de sesión
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('codigo');
        input.addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
</script>
</body>
</html>
