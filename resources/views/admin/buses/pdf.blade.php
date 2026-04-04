<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario de Flota - SIGU</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; color: #333; margin: 0; padding: 0; }
        .header { background-color: #1e3799; color: white; padding: 20px; text-align: center; }
        .logo { font-size: 24pt; font-weight: bold; margin-bottom: 5px; }
        .subtitle { font-size: 10pt; opacity: 0.8; }
        .content { padding: 30px; }
        h2 { border-bottom: 2px solid #1e3799; color: #1e3799; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f1f2f6; color: #2f3542; font-weight: bold; text-align: left; padding: 10px; border-bottom: 2px solid #dfe4ea; }
        td { padding: 10px; border-bottom: 1px solid #dfe4ea; vertical-align: middle; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 10px; font-size: 8pt; font-weight: bold; }
        .bg-success { background-color: #d1f7d1; color: #155724; }
        .bg-danger { background-color: #f8d7da; color: #721c24; }
        .bg-warning { background-color: #fff3cd; color: #856404; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; color: #747d8c; font-size: 8pt; padding: 20px 0; }
    </style>
</head>
<body>

<div class="header">
    <div class="logo">SIGU</div>
    <div class="subtitle">SISTEMA INTEGRADO DE GESTIÓN URBANA</div>
    <div style="margin-top: 10px; font-weight: bold;">INVENTARIO DE FLOTA - {{ $empresa->nombre_empresa ?? 'Empresa' }}</div>
    <div class="subtitle">Generado el: {{ date('d/m/Y H:i:s') }}</div>
</div>

<div class="content">
    <h2>Reporte Detallado de Vehículos</h2>
    <table>
        <thead>
            <tr>
                <th>Placa</th>
                <th>Modelo</th>
                <th>Capacidad</th>
                <th>Kilometraje</th>
                <th>Propietario / Contacto</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($buses as $bus)
                <tr>
                    <td style="font-weight: bold; color: #1e3799;">{{ $bus->placa }}</td>
                    <td>{{ $bus->modelo }}</td>
                    <td style="text-align: center;">{{ $bus->capacidad_pasajeros }}</td>
                    <td>{{ number_format($bus->kilometraje) }} KM</td>
                    <td>
                        {{ $bus->nombre_propietario ?? ($bus->propietario ? ($bus->propietario->primer_nombre . ' ' . $bus->propietario->primer_apellido) : 'PARTICULAR') }}
                        <br>
                        <small>{{ $bus->telefono ?? ($bus->propietario ? $bus->propietario->telefono : '---') }}</small>
                    </td>
                    <td>
                        @php
                            $estado = optional($bus->estado)->nombre_estado ?? 'N/A';
                            $class = match($estado) {
                                'ACTIVO' => 'bg-success',
                                'INACTIVO' => 'bg-danger',
                                'EN_MANTENIMIENTO', 'FUERA_DE_SERVICIO' => 'bg-warning',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $class }}">{{ $estado }}</span>
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
