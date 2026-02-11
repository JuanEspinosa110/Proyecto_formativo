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
<body class="login-body">

<header class="login-header">
    <h1>Sistema de Gestión de Transporte</h1>
</header>

<div class="regis-wrapper">
    <div class="regis-card">

        <div class="regis-header">
            <h1>Crear cuenta de pasajero</h1>
            <p>Complete sus datos para registrarse en el sistema</p>
        </div>

        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            <div class="regis-group">
                <label>Número de documento</label>
                <input type="text" name="doc_usuario" class="regis-input" required>
            </div>

            <div class="regis-row">
                <div class="regis-group">
                    <label>Primer nombre</label>
                    <input type="text" name="primer_nombre" class="regis-input" required>
                </div>

                <div class="regis-group">
                    <label>Segundo nombre</label>
                    <input type="text" name="segundo_nombre" class="regis-input">
                </div>
            </div>

            <div class="regis-row">
                <div class="regis-group">
                    <label>Primer apellido</label>
                    <input type="text" name="primer_apellido" class="regis-input" required>
                </div>

                <div class="regis-group">
                    <label>Segundo apellido</label>
                    <input type="text" name="segundo_apellido" class="regis-input">
                </div>
            </div>

            <div class="regis-row">
                <div class="regis-group">
                    <label>Correo electrónico</label>
                    <input type="email" name="correo" class="regis-input" required>
                </div>

                <div class="regis-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" class="regis-input" required>
                </div>
            </div>

            <div class="regis-row">
                <div class="regis-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" class="regis-input" required>
                </div>

                <div class="regis-group">
                    <label>Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" class="regis-input" required>
                </div>
            </div>

            <button type="submit" class="regis-btn">
                Registrarse
            </button>

        </form>

        <div class="regis-footer">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}">Iniciar sesión</a>
        </div>

    </div>
</div>

</body>
</html>

