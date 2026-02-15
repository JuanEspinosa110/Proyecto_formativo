<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Contraseña - Transporte Ibagué</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>

<div class="npw-page">

    <header class="npw-header">
        <h2>Transporte Ibagué</h2>
    </header>

    <div class="top-navigation">
    <a href="{{ route('home') }}" class="btn-home">
        Volver al inicio
    </a>
</div>


    <main class="npw-main">
        <div class="npw-card">

            <div class="npw-card-header">
                <h1>Nueva contraseña</h1>
                <p>Ingresa tu nueva clave para asegurar tu cuenta.</p>
            </div>

            @if ($errors->any())
                <div class="npw-error-box">
                    <strong>Corrige los siguientes errores:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif



            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="correo" value="{{ old('correo', $correo) }}">

                <div class="npw-group">
                    <label>Nueva contraseña</label>
                    <input type="password" name="password" required>
                </div>

                <div class="npw-group">
                    <label>Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" required>
                </div>

                <button type="submit" class="npw-btn">
                    Restablecer contraseña
                </button>
            </form>

            <div class="npw-back">
                <a href="{{ route('login') }}">Volver al login</a>
            </div>

        </div>
    </main>

    <footer class="npw-footer">
        © 2026 Sistema de Gestión de Transporte - Ibagué
    </footer>

</div>

</body>
</html>
