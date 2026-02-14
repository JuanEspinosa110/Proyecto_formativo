<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte General</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h1, h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h1>Reporte General</h1>

<h2>Resumen</h2>
<table>
    <tr>
        <th>Total ventas</th>
        <th>Total ingresos</th>
        <th>Ventas hoy</th>
        <th>Ventas del mes</th>
    </tr>
    <tr>
        <td>{{ $totalVentas }}</td>
        <td>${{ number_format($totalIngresos, 0) }}</td>
        <td>{{ $ventasHoy }}</td>
        <td>{{ $ventasMes }}</td>
    </tr>
</table>

<h2>Ventas recientes</h2>
<table>
    <thead>
        <tr>
            <th>ID Viaje</th>
            <th>Valor</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ventasRecientes as $venta)
            <tr>
                <td>{{ $venta->id_viaje }}</td>
                <td>${{ number_format($venta->valor, 0) }}</td>
                <td>{{ $venta->fecha }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h2>Ingresos por empresa</h2>
<table>
    <thead>
        <tr>
            <th>Empresa</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ingresosPorEmpresa as $empresa)
            <tr>
                <td>{{ $empresa->empresa }}</td>
                <td>${{ number_format($empresa->total, 0) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
