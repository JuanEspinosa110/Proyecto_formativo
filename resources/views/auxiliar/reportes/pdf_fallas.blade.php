<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Fallas Mecánicas</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 11px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        h2 { text-align: center; color: #333; margin-bottom: 30px; }
    </style>
</head>
<body>
    <h2>Reporte de Fallas Mecánicas</h2>
    <table>
        <thead>
            <tr>
                <th>FECHA</th>
                <th>PLACA</th>
                <th>DESCRIPCIÓN</th>
                <th>URGENCIA</th>
                <th>ESTADO</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fallas as $f)
            <tr>
                <td>{{ $f->created_at }}</td>
                <td>{{ $f->placa }}</td>
                <td>{{ $f->descripcion }}</td>
                <td>{{ $f->nivel_urgencia }}</td>
                <td>{{ optional($f->estado)->nombre_estado ?? 'Desconocido' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
