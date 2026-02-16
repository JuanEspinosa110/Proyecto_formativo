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
    <h1>Sistema integral de gestion urbana</h1>
</header>

<div class="top-navigation">
    <a href="{{ route('home') }}" class="btn-home">
        Volver al inicio
    </a>
</div>

<div class="regis-wrapper">
    <div class="regis-card">

        <div class="regis-header">
            <h1>Crear cuenta de pasajero</h1>
            <p>Complete sus datos para registrarse en el sistema</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Ups </strong> Corrige los siguientes errores:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

       <form method="POST" action="{{ route('register.store') }}">
            @csrf

            <div class="regis-group">
                <label>Número de documento</label>
                <input type="text" 
                    name="doc_usuario" 
                    value="{{ old('doc_usuario') }}"
                    class="regis-input @error('doc_usuario') is-invalid @enderror"
                    required>
                @error('doc_usuario')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="regis-row">
                <div class="regis-group">
                    <label>Primer nombre</label>
                    <input type="text" 
                        name="primer_nombre"
                        value="{{ old('primer_nombre') }}"
                        class="regis-input @error('primer_nombre') is-invalid @enderror"
                        required>
                    @error('primer_nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="regis-group">
                    <label>Segundo nombre</label>
                    <input type="text" 
                        name="segundo_nombre"
                        value="{{ old('segundo_nombre') }}"
                        class="regis-input @error('segundo_nombre') is-invalid @enderror">
                    @error('segundo_nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="regis-row">
                <div class="regis-group">
                    <label>Primer apellido</label>
                    <input type="text" 
                        name="primer_apellido"
                        value="{{ old('primer_apellido') }}"
                        class="regis-input @error('primer_apellido') is-invalid @enderror"
                        required>
                    @error('primer_apellido')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="regis-group">
                    <label>Segundo apellido</label>
                    <input type="text" 
                        name="segundo_apellido"
                        value="{{ old('segundo_apellido') }}"
                        class="regis-input @error('segundo_apellido') is-invalid @enderror">
                    @error('segundo_apellido')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="regis-row">
                <div class="regis-group">
                    <label>Correo electrónico</label>
                    <input type="email" 
                        name="correo"
                        value="{{ old('correo') }}"
                        class="regis-input @error('correo') is-invalid @enderror"
                        required>
                    @error('correo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="regis-group">
                    <label>Teléfono</label>
                    <input type="text" 
                        name="telefono"
                        value="{{ old('telefono') }}"
                        class="regis-input @error('telefono') is-invalid @enderror"
                        required>
                    @error('telefono')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="regis-row">
                <div class="regis-group">
                    <label>Contraseña</label>
                    <input type="password" 
                        name="password"
                        class="regis-input @error('password') is-invalid @enderror"
                        required>
                    <small class="text-muted">
                        La contraseña debe tener mínimo 8 caracteres, una mayúscula,
                        un número y un símbolo.
                    </small>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="regis-group">
                    <label>Confirmar contraseña</label>
                    <input type="password" 
                        name="password_confirmation"
                        class="regis-input @error('password_confirmation') is-invalid @enderror"
                        required>
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

