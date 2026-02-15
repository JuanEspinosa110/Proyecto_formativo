<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Transporte</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS propio -->
    <link rel="stylesheet" href="{{ asset('../../css/login.css') }}">
</head>
<body class="login-body">

<header class="login-header">
    <h1>Transporte Ibague</h1>
</header>

<div class="top-navigation">
    <a href="{{ route('home') }}" class="btn-home">
        Volver al inicio
    </a>
</div>

<main class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="login-card">
        <h2 class="text-center mb-3">Acceso al sistema</h2>
        <p class="text-center text-muted mb-4">Plataforma institucional</p>

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
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button class="btn btn-primary w-100">Ingresar</button>
        </form>

        <div class="login-links mt-3">
            <a href=" {{route('recuperar')}}  ">¿Olvidaste tu contraseña?</a>
            <a href="#">Soporte técnico</a>
        </div>
    </div>
</main>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
