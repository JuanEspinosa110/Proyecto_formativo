<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGU</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- CSS propio -->
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
</head>
<body>

<!-- HEADER -->
<nav class="navbar navbar-expand-lg fixed-top bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">
            <i class="fas fa-bus text-primary me-2"></i>SIGU
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="#beneficios">Beneficios</a></li>
                <li class="nav-item"><a class="nav-link" href="#planes">Planes</a></li>
                <li class="nav-item"><a class="nav-link" href="#como-empezar">Cómo empezar</a></li>
            </ul>

            <div class="ms-lg-3">
                <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">
                    Iniciar sesión
                </a>
                <a href="{{ route('register') }}" class="btn btn-primary">
                    Registrarse
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <span class="badge bg-primary mb-3 px-3 py-2">
                    <i class="fas fa-star me-2"></i>Nuevo sistema digital
                </span>
                <h1 class="hero-title">
                    Viaja más <span class="text-primary">fácil</span>, rápido y seguro
                </h1>
                <p class="hero-text">
                    Olvídate del efectivo. Usa tu tarjeta virtual o física para viajar sin filas.
                </p>
                <div class="d-flex gap-3 mt-4">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Crear cuenta
                    </a>
                    <a href="#planes" class="btn btn-outline-secondary btn-lg">
                        Saber más
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center mt-5 mt-lg-0">
                <i class="fas fa-mobile-alt text-primary" style="font-size: 15rem; opacity: 0.1;"></i>
            </div>
        </div>
    </div>
</section>

<!-- BENEFICIOS -->
<section class="py-5 bg-light" id="beneficios">
    <div class="container text-center">
        <h2 class="fw-bold mb-4">Beneficios pensados para ti</h2>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="benefit-card">
                    <i class="fas fa-credit-card text-primary mb-3" style="font-size: 2.5rem;"></i>
                    <h5>Pago sin efectivo</h5>
                    <p>Viaja usando tarjeta virtual o física.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="benefit-card">
                    <i class="fas fa-chart-line text-success mb-3" style="font-size: 2.5rem;"></i>
                    <h5>Control de saldo</h5>
                    <p>Consulta y recarga en segundos.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="benefit-card">
                    <i class="fas fa-clock text-warning mb-3" style="font-size: 2.5rem;"></i>
                    <h5>Menos filas</h5>
                    <p>Accede rápido al transporte.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="benefit-card">
                    <i class="fas fa-shield-alt text-info mb-3" style="font-size: 2.5rem;"></i>
                    <h5>Seguridad</h5>
                    <p>Datos protegidos y confiables.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PLANES DE LICENCIA -->
<section class="py-5 planes-section" id="planes">
    <div class="container">
        <!-- Header de la sección -->
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2">
                Planes y precios
            </span>
            <h2 class="display-5 fw-bold mb-3">
                Da el salto con nuestras<br>
                mayores ofertas
            </h2>
            <div class="d-flex flex-wrap justify-content-center gap-4 text-muted small mt-4">
                <div>
                    <i class="fas fa-undo-alt me-2"></i>30 días de garantía de reembolso
                </div>
                <div>
                    <i class="fas fa-times-circle me-2"></i>Cancela en cualquier momento
                </div>
                <div>
                    <i class="fas fa-headset me-2"></i>Ayuda las 24 horas
                </div>
            </div>
        </div>

        <!-- Tarjetas de planes -->
        <div class="row g-4 justify-content-center">
            @php
                $planesPublicos = DB::table('planes_licencia')
                    ->where('id_estado', 1)
                    ->orderBy('precio', 'asc')
                    ->get();
            @endphp

            @forelse($planesPublicos as $index => $plan)
            @php
                // Calcular descuento ficticio
                $precioOriginal = $plan->precio * 1.5;
                $descuento = round((($precioOriginal - $plan->precio) / $precioOriginal) * 100);
                
                // Determinar si es el más vendido (el segundo plan)
                $esMasVendido = $index === 1;
                
                // Colores por plan
                $colores = ['primary', 'success', 'warning', 'info'];
                $color = $colores[$index % 4];
            @endphp
            
            <div class="col-lg-3 col-md-6">
                <div class="plan-card {{ $esMasVendido ? 'plan-destacado' : '' }} h-100">
                    @if($esMasVendido)
                    <div class="plan-badge-top">MÁS VENDIDO</div>
                    @endif
                    
                    <!-- Descuento badge -->
                    @if($descuento > 0)
                    <div class="plan-discount-badge">-{{ $descuento }}%</div>
                    @endif

                    <div class="plan-card-body">
                        <!-- Header del plan -->
                        <div class="plan-header mb-4">
                            <h4 class="plan-title">{{ $plan->nombre_plan }}</h4>
                            <p class="plan-subtitle text-muted">{{ Str::limit($plan->descripcion, 50) }}</p>
                        </div>

                        <!-- Precio -->
                        <div class="plan-pricing mb-4">
                            @if($descuento > 0)
                            <div class="plan-price-original">
                                COP ${{ number_format($precioOriginal, 0, ',', '.') }}
                            </div>
                            @endif
                            <div class="plan-price-current">
                                COP <span class="plan-price-amount">${{ number_format($plan->precio, 0, ',', '.') }}</span>
                                <span class="plan-price-period">/{{ $plan->duracion_meses == 1 ? 'mes' : $plan->duracion_meses . ' meses' }}</span>
                            </div>
                            @if($plan->duracion_meses > 1)
                            <div class="plan-bonus text-{{ $color }} fw-bold mt-2">
                                <i class="fas fa-gift me-1"></i>+{{ floor($plan->duracion_meses / 4) }} mes(es) gratis
                            </div>
                            @endif
                        </div>

                        <!-- Botón de acción -->
                        <a href="{{ route('register') }}" class="btn btn-{{ $esMasVendido ? 'dark' : 'outline-dark' }} w-100 mb-4">
                            Elegir plan
                        </a>

                        <div class="plan-offer-note text-center small text-muted mb-4">
                            Oferta por tiempo limitado
                        </div>

                        <!-- Características -->
                        <div class="plan-features">
                            <div class="plan-feature-item">
                                <i class="fas fa-check-circle text-{{ $color }} me-2"></i>
                                <span>Hasta {{ $plan->duracion_meses * 10 }} usuarios</span>
                            </div>
                            <div class="plan-feature-item">
                                <i class="fas fa-check-circle text-{{ $color }} me-2"></i>
                                <span>{{ $plan->duracion_meses * 5 }} buses registrados</span>
                            </div>
                            <div class="plan-feature-item">
                                <i class="fas fa-check-circle text-{{ $color }} me-2"></i>
                                <span>Soporte técnico incluido</span>
                            </div>
                            <div class="plan-feature-item">
                                <i class="fas fa-check-circle text-{{ $color }} me-2"></i>
                                <span>{{ $plan->descripcion }}</span>
                            </div>
                        </div>

                        <!-- Info adicional -->
                        <div class="plan-footer-info mt-4 pt-4 border-top">
                            <small class="text-muted d-block">
                                Obtén {{ $plan->duracion_meses }} meses por COP ${{ number_format($plan->precio * $plan->duracion_meses, 0, ',', '.') }}
                                (valorado en COP ${{ number_format($precioOriginal * $plan->duracion_meses, 0, ',', '.') }}).
                                Se renueva por COP ${{ number_format($plan->precio, 0, ',', '.') }}/mes.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <p class="text-muted">No hay planes disponibles en este momento</p>
            </div>
            @endforelse
        </div>

        <!-- Información adicional -->
        <div class="text-center mt-5">
            <p class="text-muted">
                ¿Necesitas un plan personalizado? 
                <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-bold">
                    Contáctanos <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </p>
        </div>
    </div>
</section>

<!-- CÓMO EMPEZAR -->
<section class="py-5 bg-light" id="como-empezar">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Comienza en 3 simples pasos</h2>
            <p class="text-muted">Es rápido, fácil y seguro</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div class="step-number mb-3">1</div>
                    <h5 class="fw-bold">Regístrate</h5>
                    <p class="text-muted">Crea tu cuenta en menos de 2 minutos</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div class="step-number mb-3">2</div>
                    <h5 class="fw-bold">Elige tu plan</h5>
                    <p class="text-muted">Selecciona el que mejor se adapte a ti</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div class="step-number mb-3">3</div>
                    <h5 class="fw-bold">¡Listo!</h5>
                    <p class="text-muted">Comienza a usar el sistema</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="container">
        <div class="row py-5">
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-bus text-primary me-2"></i>ViajaFácil
                </h5>
                <p class="text-muted">Sistema de transporte digital para empresas modernas.</p>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <h6 class="fw-bold mb-3">Enlaces</h6>
                <ul class="list-unstyled">
                    <li><a href="#beneficios" class="text-muted text-decoration-none">Beneficios</a></li>
                    <li><a href="#planes" class="text-muted text-decoration-none">Planes</a></li>
                    <li><a href="#como-empezar" class="text-muted text-decoration-none">Cómo empezar</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="fw-bold mb-3">Contacto</h6>
                <p class="text-muted">
                    <i class="fas fa-envelope me-2"></i>info@viajafacil.com<br>
                    <i class="fas fa-phone me-2"></i>+57 300 000 0000
                </p>
            </div>
        </div>
        <div class="border-top pt-4 text-center">
            <p class="mb-0 text-muted">© 2026 ViajaFácil - Sistema de Transporte Digital</p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Smooth scroll -->
<script>
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>
</body>
</html>
