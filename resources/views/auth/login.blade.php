<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Transporte</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS propio -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body class="login-body">

<header class="login-header">
    
    <div class="header-left">
        <img src="{{ asset('imagenes/logo-sigu.png') }}" alt="SIGU Logo" class="logo-icon">
    </div>

    <div class="header-center">
        <h1 class="logo-title">SIGU</h1>
        <p class="logo-subtitle">Sistema Integral de Gestión Urbana</p>
    </div>

    <div class="header-right">
        <a href="{{ route('home') }}" class="btn-home">
            Volver al inicio
        </a>
    </div>

</header>

<main class="login-wrapper">

    <div class="login-box">

        <!-- LADO IZQUIERDO - IMAGEN -->
        <div class="login-left">
            <!-- La imagen se carga desde CSS -->
        </div>

        <!-- LADO DERECHO - FORMULARIO -->
        <div class="login-right">

            <div class="login-card">
                <h2 class="text-center mb-3">Acceso al sistema</h2>
                <p class="text-center text-muted mb-4">
                    Sistema integral de gestión urbana
                </p>

                @include('components.alertas')

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Documento de identidad</label>
                        <input type="number" name="doc_us" class="form-control"
                            value="{{ old('doc_us') }}"
                            placeholder="Ingrese su número de documento" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password"
                            class="form-control"
                            placeholder="••••••••" required>
                    </div>

                    <button class="btn btn-primary w-100">
                        Ingresar
                    </button>
                </form>

                <div class="login-links mt-3">
                    <a href="{{ route('recuperar') }}">
                        ¿Olvidaste tu contraseña?
                    </a>
                    <a href="#">Soporte técnico</a>
                </div>
            </div>

        </div>
    </div>

</main>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
