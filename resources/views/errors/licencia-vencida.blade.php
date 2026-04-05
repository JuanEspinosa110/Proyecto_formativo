<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Licencia de Empresa Vencida — SIGU</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter+Tight:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sigu-core.css') }}">
    <link rel="stylesheet" href="{{ asset('css/errors.css') }}">
    <style>
        .contact-box {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #ffc107;
        }
        .btn-pasajero {
            background: #6f42c1;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-pasajero:hover {
            background: #5a32a3;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="error-page-body">
    <div class="error-card">
        <div class="error-icon" style="background: rgba(220, 53, 69, 0.1); color: #dc3545;">
            <span class="material-symbols-rounded" style="font-size: 40px;">timer_off</span>
        </div>
        <h1 class="error-title">Licencia Expirada</h1>
        <p class="error-msg text-muted mb-4">
            La licencia de servicios asociada a su empresa ha vencido o se encuentra inactiva. 
            Las funciones administrativas y operativas han sido restringidas temporalmente.
        </p>
        
        <div class="contact-box text-start">
            <div class="small fw-bold text-uppercase text-muted mb-1" style="font-size: 0.7rem;">Soporte Técnico</div>
            <div class="d-flex align-items-center">
                <span class="material-symbols-rounded text-warning me-2">mail</span>
                <span class="fw-semibold">{{ $contacto }}</span>
            </div>
        </div>

        <div class="d-flex flex-column gap-2">
            <a href="{{ route('pasajero.dashboard') }}" class="btn-sigu btn-pasajero w-100 d-flex align-items-center justify-content-center text-decoration-none">
                <span class="material-symbols-rounded me-2">person</span>
                Continuar como Pasajero
            </a>
            
            <form action="{{ route('logout') }}" method="POST" class="w-100">
                @csrf
                <button type="submit" class="btn btn-link text-muted w-100 mt-2" style="font-size: 0.9rem; text-decoration: none;">
                    Finalizar sesión
                </button>
            </form>
        </div>
    </div>
</body>

</html>
