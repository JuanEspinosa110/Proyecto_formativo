@extends('superadmin.layouts.admin')

@section('content')

<div class="reporte-wrapper">

    <div class="reporte-container">

        <h1 class="reporte-title">Reporte General</h1>

        {{-- KPIs --}}
        <div class="kpi-grid">
            <div class="kpi-card">
                <h6>Total ventas</h6>
                <h3>{{ $totalVentas }}</h3>
            </div>

            <div class="kpi-card">
                <h6>Total ingresos</h6>
                <h3>${{ number_format($totalIngresos, 0) }}</h3>
            </div>

            <div class="kpi-card">
                <h6>Ventas hoy</h6>
                <h3>{{ $ventasHoy }}</h3>
            </div>

            <div class="kpi-card">
                <h6>Ventas del mes</h6>
                <h3>{{ $ventasMes }}</h3>
            </div>
        </div>

        {{-- Ventas recientes --}}
        <div class="reporte-card">
            <h5>Ventas recientes</h5>

            <table class="reporte-table">
                <thead>
                    <tr>
                        <th>ID Venta</th>
                        <th>ID Viaje</th>
                        <th>Valor</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ventasRecientes as $venta)
                        <tr>
                            <td>{{ $venta->id_venta }}</td>
                            <td>{{ $venta->id_viaje }}</td>
                            <td>${{ number_format($venta->valor, 0) }}</td>
                            <td>{{ $venta->fecha }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">
                                No hay ventas registradas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Ingresos por empresa --}}
        <div class="reporte-card">
            <h5>Ingresos por empresa</h5>

            <table class="reporte-table">
                <thead>
                    <tr>
                        <th>Empresa</th>
                        <th>Total ingresos</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ingresosPorEmpresa as $empresa)
                        <tr>
                            <td>{{ $empresa->empresa }}</td>
                            <td>${{ number_format($empresa->total, 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">
                                No hay datos disponibles
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Ingresos por día --}}
        <div class="reporte-card">
            <h5>Ingresos por día</h5>

            <table class="reporte-table">
                <thead>
                    <tr>
                        <th>Día</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ingresosPorDia as $dia)
                        <tr>
                            <td>{{ $dia->dia }}</td>
                            <td>${{ number_format($dia->total, 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">
                                No hay registros
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

<a href="{{ route('superadmin.reportes.pdf') }}" class="btn btn-primary">
    Descargar PDF
</a>


@endsection
