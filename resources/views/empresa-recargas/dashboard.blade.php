@extends('empresa-recargas.layouts.app')

@section('title', 'Dashboard Recargas — SIGU')

@section('content')
<div class="admin-dashboard sigu-fade">
    <div class="sigu-page-hd">
        <div>
            <h1 class="sigu-page-title">Bienvenido, {{ Auth::user()->primer_nombre }}</h1>
            <p class="sigu-page-sub">{{ $empresa->nombre_empresa ?? 'Empresa de Recargas' }}</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Recargas Hoy -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100">
                <div class="mb-3">
                    <span class="material-symbols-rounded text-primary fs-1">receipt_long</span>
                </div>
                <h3 class="fw-bold mb-1">{{ $totalRecargasHoy }}</h3>
                <p class="text-muted mb-0 small">Recargas realizadas hoy</p>
            </div>
        </div>

        <!-- Monto Hoy -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100">
                <div class="mb-3">
                    <span class="material-symbols-rounded text-success fs-1">attach_money</span>
                </div>
                <h3 class="fw-bold mb-1">${{ number_format($montoRecargasHoy, 0, ',', '.') }}</h3>
                <p class="text-muted mb-0 small">Monto recargado hoy</p>
            </div>
        </div>

        <!-- Usuarios -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100">
                <div class="mb-3">
                    <span class="material-symbols-rounded text-info fs-1">group</span>
                </div>
                <h3 class="fw-bold mb-1">{{ $usuariosEmpresa }}</h3>
                <p class="text-muted mb-0 small">Usuarios de la empresa</p>
            </div>
        </div>
    </div>

    <!-- Gráficas -->
    <div class="row g-4 mb-4">
        <!-- Gráfica de Montos -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-4">Montos Recargados (Últimos 7 días)</h5>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="montosChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfica de Transacciones -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-4">Transacciones de Recargas</h5>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="transaccionesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const fechas = @json($fechas7Dias);
    const montos = @json($montos7Dias);
    const cantidades = @json($cantidad7Dias);

    // Gráfica de Montos (Bar Chart)
    const ctxMontos = document.getElementById('montosChart').getContext('2d');
    new Chart(ctxMontos, {
        type: 'bar',
        data: {
            labels: fechas,
            datasets: [{
                label: 'Monto Recargado ($)',
                data: montos,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return '$' + value; }
                    }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Gráfica de Cantidades (Line Chart)
    const ctxTransacciones = document.getElementById('transaccionesChart').getContext('2d');
    new Chart(ctxTransacciones, {
        type: 'line',
        data: {
            labels: fechas,
            datasets: [{
                label: 'N° de Recargas',
                data: cantidades,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: 'rgba(75, 192, 192, 1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>
@endpush
@endsection
