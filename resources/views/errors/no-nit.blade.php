<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta pendiente de asignación — SIGU</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter+Tight:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sigu-core.css') }}">
    <link rel="stylesheet" href="{{ asset('css/errors.css') }}">
</head>

<body class="error-page-body">
    <div class="error-card">
        <div class="error-icon">
            <span class="material-symbols-rounded" style="font-size: 40px;">domain_disabled</span>
        </div>
        <h1 class="error-title">Cuenta sin empresa</h1>
        <p class="error-msg">
            Tu cuenta administrativa ha sido registrada correctamente, pero actualmente no tienes una empresa asociada
            (NIT).
            Es necesario que un <strong>Super Administrador</strong> te vincule a una empresa para poder acceder a tu
            panel.
        </p>
        <div class="error-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-sigu">
                    <span class="material-symbols-rounded">logout</span>
                    Volver al Inicio (Cerrar sesión)
                </button>
            </form>
        </div>
    </div>
</body>

</html>