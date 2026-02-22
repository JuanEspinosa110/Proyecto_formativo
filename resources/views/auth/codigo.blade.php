<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificar Código - Transporte Ibagué</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

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

    <!-- CARD -->
    <main class="recov-wrapper">

        <div class="recov-card">

            <div class="recov-card-header"></div>

             <div class="recov-card-body">

                <div class="text-center mb-8">
                <h1 class="recov-title">Verificar código</h1>
                <p class="recov-description">
                    Ingresa el código de 6 dígitos enviado a tu correo.
                </p>
            </div>

                @if(session('success'))
                    <div class="verify-success" style="margin:15px 0;">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Mensaje de error --}}
                @if($errors->any())
                    <div class="verify-error">
                        {{ $errors->first() }}
                    </div>
                @endif

            <form method="POST" action="{{ route('password.verify.code') }}">
                @csrf

                <input type="hidden" name="correo" value="{{ session('correo') }}">

                <div class="recov-group">
                    <input 
                        type="text"
                        name="codigo"
                        maxlength="6"
                        placeholder="000000"
                        class="verify-input"
                        required
                        autofocus
                    >
                </div>

                <button type="submit" class="recov-btn">
                    Verificar código
                </button>
            </form>

            <div class="recov-back">
                <form method="POST" action="{{ route('password.resend.code') }}">
                    @csrf
                    <input type="hidden" name="correo" value="{{ session('correo') }}">
                    <button type="submit" class="verify-resend">
                        Reenviar código
                    </button>
                </form>
            </div>

        </div>
    </main>

</div>

</body>
</html>
