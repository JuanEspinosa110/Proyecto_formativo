<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Recorridos</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 11px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        h2 { text-align: center; color: #333; margin-bottom: 30px; }
    </style>
</head>
<body>
    <h2>Reporte de Recorridos (Asignaciones)</h2>
    <table>
        <thead>
            <tr>
                <th>FECHA</th>
                <th>PLACA</th>
                <th>RUTA</th>
                <th>CONDUCTOR</th>
                <th>SALIDA</th>
                <th>LLEGADA</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recorridos as $r)
            <tr>
                <td>{{ $r->fecha }}</td>
                <td>{{ $r->placa }}</td>
                <td>{{ $r->ruta->nombre_ruta ?? $r->id_ruta }}</td>
                <td>{{ $r->conductor ? ($r->conductor->primer_nombre . ' ' . $r->conductor->primer_apellido) : 'N/A' }}</td>
                <td>{{ $r->hora_salida }}</td>
                <td>{{ $r->hora_llegada }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
