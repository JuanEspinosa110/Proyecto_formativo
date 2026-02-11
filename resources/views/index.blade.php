<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ViajaFácil - Transporte Público</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS propio -->
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
</head>
<body>

<!-- HEADER -->
<nav class="navbar navbar-expand-lg fixed-top bg-white shadow-sm">
    <div class="container">

        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            ViajaFácil
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="#beneficios">Beneficios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#tarjetas">Tarjeta</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#como-empezar">Cómo empezar</a>
                </li>
            </ul>

            <div class="ms-lg-3">

                @auth
                    {{-- Redirección futura según rol --}}
                    <a href="/dashboard" class="btn btn-primary">
                        Ir al panel
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="btn btn-outline-primary me-2">
                        Iniciar sesión
                    </a>

                    <a href="{{ route('register') }}"
                       class="btn btn-primary">
                        Registrarse
                    </a>
                @endauth

            </div>

        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero-section pt-5 mt-5">

    <div class="container">

        <div class="row align-items-center">

            <div class="col-lg-6">

                <span class="badge bg-primary mb-3">
                    Nuevo sistema digital
                </span>

                <h1 class="hero-title">
                    Viaja más <span class="text-primary">fácil</span>,
                    rápido y seguro
                </h1>

                <p class="hero-text">
                    Olvídate del efectivo.
                    Usa tu tarjeta virtual o física para viajar sin filas.
                </p>

                <div class="d-flex gap-3 mt-4">

                    <a href="{{ route('register') }}"
                       class="btn btn-primary btn-lg">
                        Crear cuenta
                    </a>

                    <a href="#beneficios"
                       class="btn btn-outline-secondary btn-lg">
                        Saber más
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- BENEFICIOS -->
<section class="py-5 bg-light" id="beneficios">

    <div class="container text-center">

        <h2 class="fw-bold mb-4">
            Beneficios pensados para ti
        </h2>

        <div class="row g-4">

            <div class="col-md-6 col-lg-3">
                <div class="benefit-card">
                    <h5>Pago sin efectivo</h5>
                    <p>Viaja usando tarjeta virtual o física.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="benefit-card">
                    <h5>Control de saldo</h5>
                    <p>Consulta y recarga en segundos.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="benefit-card">
                    <h5>Menos filas</h5>
                    <p>Accede rápido al transporte.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="benefit-card">
                    <h5>Seguridad</h5>
                    <p>Datos protegidos y confiables.</p>
                </div>
            </div>

        </div>

    </div>

</section>

<!-- FOOTER -->
<footer class="footer">

    <div class="container text-center">

        <p class="mb-0">
            © 2026 ViajaFácil - Sistema de Transporte Digital
        </p>

    </div>

</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
