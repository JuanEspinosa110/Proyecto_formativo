<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Documentos</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 11px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        h2 { text-align: center; color: #333; margin-bottom: 30px; }
    </style>
</head>
<body>
    <h2>Reporte de Documentos</h2>
    <table>
        <thead>
            <tr>
                <th>PLACA</th>
                <th>TIPO</th>
                <th>NOMBRE</th>
                <th>VENCIMIENTO</th>
                <th>ESTADO</th>
            </tr>
        </thead>
        <tbody>
            @foreach($documentos as $d)
            <tr>
                <td>{{ $d->placa }}</td>
                <td>{{ $d->tipoDocumento->nombre ?? 'N/A' }}</td>
                <td>{{ $d->nombre }}</td>
                <td>{{ $d->fecha_vencimiento }}</td>
                <td>{{ $d->estado->nombre_estado ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
