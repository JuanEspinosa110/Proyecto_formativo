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

<body>

<div class="verify-page">

    <!-- HEADER -->
    <header class="verify-header">
        <div class="verify-logo">
            <span class="material-symbols-outlined">mark_email_read</span>
            <h2>Transporte Ibagué</h2>
        </div>
    </header>

    <!-- CARD -->
    <main class="verify-container">
        <div class="verify-card">

            <div class="verify-title-box">
                <h1>Verificar código</h1>
                <p>Ingresa el código de 6 dígitos enviado a tu correo.</p>
            </div>

            {{-- Mensaje de error --}}
            @if($errors->any())
                <div class="verify-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.verify.code') }}">
                @csrf

                <input type="hidden" name="correo" value="{{ session('correo') }}">

                <div class="verify-group">
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

                <button type="submit" class="verify-btn">
                    Verificar código
                </button>
            </form>

            <div class="verify-footer">
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
