<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña - Transporte Ibagué</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS propio -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body class="recov-body">



    <!-- HEADER -->
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



    <!-- MAIN -->
    <main class="recov-wrapper">

        <div class="recov-card">

            <div class="recov-card-header">
                <div class="recov-header-accent"></div>
            </div>


            <div class="recov-card-body">
                <div class="text-center mb-8">
                    <h1 class="recov-title">Recuperar contraseña</h1>
                    <p class="recov-description">
                        Introduce tu correo electrónico para recibir un código de verificación.
                    </p>
                </div>

                <!-- Mensaje éxito -->
                @if(session('success'))
                    <div class="recov-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Errores -->
                @if ($errors->any())
                    <div class="recov-error">
                        <strong>Ha ocurrido un error:</strong>
                        <div>
                            {{ $errors->first() }}
                        </div>
                    </div>
                @endif


                <form method="POST" action="{{ route('password.send.code') }}">
                    @csrf

                    <div class="recov-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" name="email" value="{{ old('email') }}" required>

                    </div>

                    <button type="submit" class="recov-btn">
                        Enviar código
                    </button>
                </form>

                <div class="recov-back">
                    <a href="{{ route('login') }}">Volver al login</a>
                </div>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="recov-footer">
        © 2026 Sistema de Gestión de Transporte - Ibagué
    </footer>

</div>
</body>
</html>
