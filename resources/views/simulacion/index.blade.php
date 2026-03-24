<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulador de Cobro - Transporte</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts & Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #581c87, #ba99f3ff);
            --bg-light: #dbdfedff;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-light);
            min-height: 100vh;
        }
        .navbar-custom {
            background: var(--primary-gradient);
            padding: 1.25rem;
            color: white;
            box-shadow: 0 4px 15px rgba(187, 157, 239, 1);
        }
        .trip-card {
            border: none;
            border-radius: 1.25rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            overflow: hidden;
        }
        .trip-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(158, 142, 217, 0.95) !important;
        }
        .badge-status {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.5rem 0.75rem;
        }
        .btn-simulate {
            background: var(--primary-gradient);
            border: none;
            color: white;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-simulate:hover:not(:disabled) {
            transform: scale(1.02);
            filter: brightness(1.1);
            color: white;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
        }
        .btn-simulate:disabled {
            background: #d5e3f5ff;
            color: #94a3b8;
            opacity: 0.8;
            cursor: not-allowed;
        }
        .data-box {
            background-color: #afa5dcff;
            border: 1px solid #fdfdfdff;
            border-radius: 0.75rem;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-custom mb-5">
        <div class="container d-flex align-items-center justify-content-between">
            <span class="navbar-brand mb-0 h1 fw-bold text-white d-flex align-items-center gap-2">
                <span class="material-symbols-rounded fs-3">sensors</span> 
                Módulo de Simulación Autónoma
            </span>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mb-5">
        <div class="row mb-4">
            <div class="col-12 text-center text-md-start">
                <h2 class="fw-extrabold text-dark">Rutas Programadas para Hoy</h2>
                <p class="text-muted">Seleccione un viaje activo para interactuar con la simulación del visor.</p>
            </div>
        </div>

        <div class="row g-4">
            @forelse($viajes as $viaje)
                <div class="col-md-6 col-lg-4">
                    <div class="card trip-card shadow-sm p-4 d-flex flex-column h-100">
                        
                        <!-- Cabecera de Tarjeta -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="text-muted small fw-bold text-uppercase letter-spacing-1">Ruta</span>
                                <h4 class="fw-bold text-dark mt-1 mb-0">{{ $viaje->ruta->nombre_ruta ?? 'N/A' }}</h4>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill badge-status">
                                {{ optional($viaje->estado)->nombre_estado ?? 'Activo' }}
                            </span>
                        </div>

                        <!-- Información Detallada -->
                        <p class="text-muted small mb-3">
                            <span class="material-symbols-rounded fs-6 align-middle">person</span> 
                            Conductor: 
                            <strong class="text-dark">
                                @if($viaje->conductor)
                                    {{ $viaje->conductor->primer_nombre }} {{ $viaje->conductor->primer_apellido }}
                                @else
                                    <span class="text-danger">Por asignar</span>
                                @endif
                            </strong>
                        </p>

                        <div class="data-box p-3 mb-3 mt-auto">
                            <div class="row g-2">
                                <div class="col-6 border-end">
                                    <span class="text-muted d-block small fw-medium">Autobús / Placa</span>
                                    <span class="fw-bold text-primary">
                                        {{ $viaje->placa ?? 'N/A' }}
                                    </span>
                                </div>
                                <div class="col-6 ps-3">
                                    <span class="text-muted d-block small fw-medium">Hora Salida</span>
                                    <span class="fw-bold text-dark">
                                        {{ \Carbon\Carbon::parse($viaje->fecha)->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Estado de Validaciones -->
                        <div class="mb-3">
                            @if(!$viaje->esta_completo)
                                <div class="badge bg-danger bg-opacity-10 text-danger w-100 p-2 rounded-3 mb-1 d-flex align-items-center justify-content-center gap-1">
                                    <span class="material-symbols-rounded fs-6">warning</span> Datos Incompletos
                                </div>
                            @endif

                            @if(!$viaje->esta_en_horario)
                                <div class="badge bg-warning bg-opacity-10 text-warning w-100 p-2 rounded-3 d-flex align-items-center justify-content-center gap-1">
                                    <span class="material-symbols-rounded fs-6 text-warning">lock</span> Fuera de Horario
                                </div>
                            @endif
                        </div>

                        <!-- Acción -->
                        <button class="btn btn-simulate w-100 py-3 rounded-pill d-flex justify-content-center align-items-center gap-2 btn-simular" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalPago" 
                                data-id="{{ $viaje->id_viaje }}"
                                data-ruta="{{ $viaje->ruta->nombre_ruta ?? 'N/A' }}"
                                data-placa="{{ $viaje->placa ?? 'N/A' }}"
                                {{ $viaje->puede_simular ? '' : 'disabled' }}>
                            <span class="material-symbols-rounded fs-5">play_circle</span> 
                            {{ $viaje->puede_simular ? 'Escanear tarjeta' : 'No disponible' }}
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <span class="material-symbols-rounded fs-1 text-muted opacity-50" style="font-size: 4rem;">directions_bus</span>
                    <p class="text-muted mt-3 fw-bold">No hay rutas activas en este momento.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Cargar Modal de Pago -->
    @include('simulacion.modals.pago')

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btnsSimular = document.querySelectorAll('.btn-simular');
            const modalIdViaje = document.getElementById('modal_id_viaje');
            const modalRutaText = document.getElementById('modal_ruta_text');
            const modalPlacaText = document.getElementById('modal_placa_text');

            btnsSimular.forEach(btn => {
                btn.addEventListener('click', function () {
                    const idViaje = this.getAttribute('data-id');
                    const ruta = this.getAttribute('data-ruta');
                    const placa = this.getAttribute('data-placa');

                    modalIdViaje.value = idViaje;
                    if(modalRutaText) modalRutaText.innerText = ruta;
                    if(modalPlacaText) modalPlacaText.innerText = placa;
                });
            });
        });
    </script>

</body>
</html>
