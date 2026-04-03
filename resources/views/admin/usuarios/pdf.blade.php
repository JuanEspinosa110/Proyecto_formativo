<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Personal - SIGU</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; color: #333; margin: 0; padding: 0; }
        .header { background-color: #4a69bd; color: white; padding: 20px; text-align: center; }
        .logo { font-size: 24pt; font-weight: bold; margin-bottom: 5px; }
        .subtitle { font-size: 10pt; opacity: 0.8; }
        .content { padding: 30px; }
        h2 { border-bottom: 2px solid #4a69bd; color: #4a69bd; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f1f2f6; color: #2f3542; font-weight: bold; text-align: left; padding: 10px; border-bottom: 2px solid #dfe4ea; }
        td { padding: 10px; border-bottom: 1px solid #dfe4ea; vertical-align: top; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 10px; font-size: 8pt; font-weight: bold; }
        .bg-success { background-color: #d1f7d1; color: #155724; }
        .bg-danger { background-color: #f8d7da; color: #721c24; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; color: #747d8c; font-size: 8pt; padding: 20px 0; }
    </style>
</head>
<body>

<div class="header">
    <div class="logo">SIGU</div>
    <div class="subtitle">SISTEMA INTEGRADO DE GESTIÓN URBANA</div>
    <div style="margin-top: 10px; font-weight: bold;">INFORME DE PERSONAL - {{ $empresa->nombre_empresa }}</div>
    <div class="subtitle">Generado el: {{ date('d/m/Y H:i:s') }}</div>
</div>

<div class="content">
    <h2>Listado de Conductores y Propietarios</h2>
    <table>
        <thead>
            <tr>
                <th>Documento</th>
                <th>Nombre Completo</th>
                <th>Rol</th>
                <th>Correo / Teléfono</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $u)
                <tr>
                    <td>{{ $u->doc_usuario }}</td>
                    <td><strong>{{ $u->primer_nombre }} {{ $u->segundo_nombre }} {{ $u->primer_apellido }} {{ $u->segundo_apellido }}</strong></td>
                    <td style="text-transform: uppercase;">{{ $u->tipoUsuario->nombre_tipo }}</td>
                    <td>{{ $u->correo }}<br><small>{{ $u->telefono }}</small></td>
                    <td>
                        @if($u->id_estado == 1)
                            <span class="badge bg-success">ACTIVO</span>
                        @else
                            <span class="badge bg-danger">INACTIVO</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="footer">
    Gestión de Transporte v3.0 - Página <script type="text/php">echo $PAGE_NUM . " de " . $PAGE_COUNT;</script>
</div>

</body>
</html>
