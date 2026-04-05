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
        <!-- Logo + Título (Específico para Index) -->
        <div class="navbar-index-brand">
            <img src="{{ asset('imagenes/logo-sigu.png') }}" alt="SIGU Logo" class="navbar-index-logo-img">
            <div class="navbar-index-logo-text">
                <h1 class="navbar-index-logo-title">SIGU</h1>
                <p class="navbar-index-logo-subtitle">Sistema Integral de Gestión Urbana</p>
            </div>
        </div>

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
<section class="hero-section" style="background-image: linear-gradient(to right, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.70) 40%, rgba(0,0,0,0.40) 70%, rgba(0,0,0,0.20) 100%), url('{{ asset('imagenes/hero.jpg') }}');">
    <div class="container">
        <div class="row align-items-center">
            <!-- LADO IZQUIERDO: Propuesta de valor -->
            <div class="col-lg-6">
                <span class="badge bg-primary mb-3 px-3 py-2">
                    <i class="fas fa-star me-2"></i>Nueva forma de viajar
                </span>
                <h1 class="hero-title">
                    Viaja más <span class="text-primary">fácil</span>, rápido y seguro
                </h1>
                <p class="hero-text">
                    Olvídate del efectivo. Paga con tu tarjeta virtual o física desde tu teléfono. Sin filas, sin trámites.
                </p>

                <!-- Stats/Datos relevantes -->
                <div class="hero-stats mt-5">
                    <div class="hero-stat">
                        <div class="stat-number">+50K</div>
                        <div class="stat-label">Usuarios activos</div>
                    </div>
                    <div class="hero-stat">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Disponible siempre</div>
                    </div>
                    <div class="hero-stat">
                        <div class="stat-number">0%</div>
                        <div class="stat-label">Comisión al registrar</div>
                    </div>
                </div>

                <!-- CTA Buttons - Discretos -->
                <div class="hero-cta mt-5">
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        Crear cuenta gratis
                    </a>
    
                </div>
            </div>

            <!-- LADO DERECHO: Información visual -->
            <div class="col-lg-6">
                <div class="hero-card-visual">
                    <div class="hero-card-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="hero-card-content">
                        <h3>Tarjeta Digital</h3>
                        <p>Acceso inmediato sin esperas</p>
                        <ul class="hero-features">
                            <li><i class="fas fa-check"></i> Recarga en segundos</li>
                            <li><i class="fas fa-check"></i> Saldo en tiempo real</li>
                            <li><i class="fas fa-check"></i> Seguridad garantizada</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- BENEFICIOS -->
<section class="benefits-section" id="beneficios">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2">
                <i class="fas fa-star me-2"></i>Lo que nos hace especial
            </span>
            <h2 class="display-5 fw-bold mb-2">Beneficios pensados para ti</h2>
            <p class="text-muted fs-5">Disfruta de una experiencia sin complicaciones</p>
        </div>

        <div class="row g-4 align-items-stretch">
            <div class="col-md-6 col-lg-3">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h5>Pago sin efectivo</h5>
                    <p>Viaja usando tarjeta virtual o física.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="benefit-card">
                    <div class="benefit-icon benefit-icon-success">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5>Control de saldo</h5>
                    <p>Consulta y recarga en segundos.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="benefit-card">
                    <div class="benefit-icon benefit-icon-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h5>Menos filas</h5>
                    <p>Accede rápido al transporte.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="benefit-card">
                    <div class="benefit-icon benefit-icon-info">
                        <i class="fas fa-shield-alt"></i>
                    </div>
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
                        <button type="button" class="btn btn-{{ $esMasVendido ? 'dark' : 'outline-dark' }} w-100 mb-4" data-bs-toggle="modal" data-bs-target="#contactModal">
                            Contáctanos
                        </button>

                        <div class="plan-offer-note text-center small text-muted mb-4">
                            Oferta por tiempo limitado
                        </div>

                        <!-- Características -->
                        <div class="plan-features">
                            <!--<div class="plan-feature-item">
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
                            </div>-->
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
                <a href="#" class="text-primary text-decoration-none fw-bold" data-bs-toggle="modal" data-bs-target="#contactModal">
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
                    <h5 class="fw-bold">Elige tu plan</h5>
                    <p class="text-muted">Selecciona el que mejor se adapte a ti</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div class="step-number mb-3">2</div>
                    <h5 class="fw-bold">Contacta con nosotros</h5>
                    <p class="text-muted">Nos comunicaremos para obtener más información y activar tu plan</p>
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
        <!-- Footer Main Content -->
        <div class="row py-5">
            <!-- Sección 1: Branding -->
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="footer-brand">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-bus me-2"></i>SIGU
                    </h5>
                    <p class="footer-description">
                        Sistema integral de gestión para licencias de transporte. Solución completa y segura para conductores modernos.
                    </p>
                    <div class="social-links mt-4">
                        <a href="#" class="social-link" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-link" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sección 2: Navegación -->
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="footer-section">
                    <h6 class="footer-title">Navegación</h6>
                    <ul class="footer-links">
                        <li><a href="#beneficios"><i class="fas fa-arrow-right me-2"></i>Beneficios</a></li>
                        <li><a href="#planes"><i class="fas fa-arrow-right me-2"></i>Planes</a></li>
                        <li><a href="#como-empezar"><i class="fas fa-arrow-right me-2"></i>Cómo empezar</a></li>
                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#contactModal"><i class="fas fa-arrow-right me-2"></i>Contacto</a></li>
                    </ul>
                </div>
            </div>

            <!-- Sección 3: Contacto -->
            <div class="col-md-4">
                <div class="footer-section">
                    <h6 class="footer-title">Contacto</h6>
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <small>Email</small>
                                <a href="mailto:contacto@sigu.com">contacto@sigu.com</a>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <small>Teléfono</small>
                                <a href="tel:+573001234567">+57 300 123 4567</a>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <small>Ubicación</small>
                                <span>Colombia, Latinoamérica</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="footer-divider"></div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="copyright-text">© 2026 SIGU - Sistema de Gestión de Licencias. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <ul class="footer-legal">
                        <li><a href="#">Privacidad</a></li>
                        <li><a href="#">Términos</a></li>
                        <li><a href="#">Cookies</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>


<!-- MODAL DE CONTACTO -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header">
                <h5 class="modal-title" id="contactModalLabel">
                    <i class="fas fa-headset me-2"></i>Equipo de Soporte
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="contact-card-container">
                    <!-- Tarjeta de presentación del Super Admin -->
                    <div class="contact-card">
                        <div class="contact-card-header">
                            <div class="contact-card-avatar">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="contact-card-info">
                                <h5>{{ $superAdmin->nombre }}</h5>
                                <p>Super Administrador</p>
                            </div>
                        </div>

                        <div class="contact-card-body">
                            <!-- Email -->
                            <div class="contact-item">
                                <div class="contact-item-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-item-content">
                                    <div class="contact-item-label">Correo Electrónico</div>
                                    <div class="contact-item-value">
                                        <a href="mailto:{{ $superAdmin->correo }}">{{ $superAdmin->correo }}</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Teléfono -->
                            <div class="contact-item">
                                <div class="contact-item-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="contact-item-content">
                                    <div class="contact-item-label">Teléfono</div>
                                    <div class="contact-item-value">
                                        <a href="tel:{{ str_replace([' ', '-'], '', $superAdmin->telefono) }}">{{ $superAdmin->telefono }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <a href="mailto:{{ $superAdmin->correo }}" class="btn-message">
                                <i class="fas fa-envelope me-1"></i>Enviar Email
                            </a>
                        </div>
                    </div>

                    <!-- Divisor -->
                    <div class="divider-text">O contacta al equipo general</div>

                    <!-- Información de Contacto General -->
                    <div class="company-contact-card">
                        <div class="company-contact-card-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h4>SIGU - Sistema de Gestión de Licencias</h4>
                        <p style="margin-bottom: 0; opacity: 0.9;">Estamos disponibles para ayudarte</p>

                        <div class="company-contact-info">
                            <div class="company-contact-info-item">
                                <i class="fas fa-envelope"></i>
                                <label>Email General</label>
                                <a href="mailto:contacto@sigu.com">contacto@sigu.com</a>
                            </div>
                            <div class="company-contact-info-item">
                                <i class="fas fa-phone"></i>
                                <label>Línea Directa</label>
                                <a href="tel:+573001234567">+57 (300) 123-4567</a>
                            </div>
                            <div class="company-contact-info-item">
                                <i class="fas fa-clock"></i>
                                <label>Horario</label>
                                <div style="font-weight: 600;">Lun - Vie: 8am - 6pm</div>
                            </div>
                            <div class="company-contact-info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <label>Ubicación</label>
                                <div style="font-weight: 600;">Bogotá, Colombia</div>
                            </div>
                        </div>
                    </div>

                    <!-- Info adicional -->
                    <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; text-align: center;">
                        <p style="margin: 0; font-size: 0.9rem; color: #666;">
                            <i class="fas fa-info-circle me-2"></i>
                            Tiempo de respuesta típico: <strong>2-4 horas</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
