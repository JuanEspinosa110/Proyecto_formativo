<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Contraseña - Transporte Ibagué</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body class="recov-body">

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

    <main class="newpass-wrapper">
        <div class="newpass-card">

                <div class="recov-card-header">
                    <div class="recov-header-accent"></div>
                </div>
            <div class="recov-card-body">
                <div class="text-center mb-8">
                    <h1 class="recov-title">Nueva contraseña</h1>
                    <p class="recov-description">Ingresa tu nueva clave para asegurar tu cuenta.</p>
                </div>
                @if ($errors->any())
                    <div class="recov-error-box">
                        <strong>Corrige los siguientes errores:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif



                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="correo" value="{{ $correo }}">

                    <div class="recov-group">
                        <label>Nueva contraseña</label>
                        <input type="password" name="password" required>
                    </div>

                    <div class="recov-group">
                        <label>Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" required>
                    </div>

                    <button type="submit" class="recov-btn">
                        Restablecer contraseña
                    </button>
                </form>

                <div class="recov-back">
                    <a href="{{ route('login') }}">Volver al login</a>
                </div>

            </div>
        </div>
    </main>

    <footer class="recov-footer">
        © 2026 Sistema de Gestión de Transporte - Ibagué
    </footer>

</div>

</body>
</html>
