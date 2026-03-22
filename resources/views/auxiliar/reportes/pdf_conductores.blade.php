<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Conductores</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        h2 { text-align: center; color: #333; margin-bottom: 30px; }
    </style>
</head>
<body>
    <h2>Reporte de Conductores</h2>
    <table>
        <thead>
            <tr>
                <th>DOCUMENTO</th>
                <th>NOMBRE</th>
                <th>CORREO</th>
                <th>TELÉFONO</th>
                <th>ESTADO</th>
            </tr>
        </thead>
        <tbody>
            @foreach($conductores as $c)
            <tr>
                <td>{{ $c->doc_usuario }}</td>
                <td>{{ $c->primer_nombre }} {{ $c->primer_apellido }}</td>
                <td>{{ $c->correo }}</td>
                <td>{{ $c->telefono }}</td>
                <td>{{ $c->id_estado == 1 ? 'Activo' : 'Inactivo' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
