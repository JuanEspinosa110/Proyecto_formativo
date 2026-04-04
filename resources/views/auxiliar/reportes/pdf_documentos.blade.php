<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado Documental - SIGU</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9pt; color: #333; margin: 0; padding: 0; }
        .header { background-color: #5E17EB; color: white; padding: 15px; text-align: center; }
        .logo { font-size: 20pt; font-weight: bold; }
        .subtitle { font-size: 9pt; opacity: 0.9; }
        .content { padding: 20px; }
        h2 { border-bottom: 2px solid #5E17EB; color: #5E17EB; padding-bottom: 5px; font-size: 14pt; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 25px; }
        th { background-color: #f1f2f6; color: #2f3542; font-weight: bold; text-align: left; padding: 8px; border-bottom: 2px solid #dfe4ea; }
        td { padding: 8px; border-bottom: 1px solid #dfe4ea; vertical-align: middle; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 8px; font-size: 7pt; font-weight: bold; }
        .bg-success { background-color: #d1f7d1; color: #155724; }
        .bg-danger { background-color: #f8d7da; color: #721c24; }
        .bg-warning { background-color: #fff3cd; color: #856404; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; color: #747d8c; font-size: 8pt; padding: 10px 0; }
        .section-title { background: #f8f9fa; padding: 5px 10px; font-weight: bold; color: #5E17EB; border-left: 4px solid #5E17EB; margin-top: 20px; }
    </style>
</head>
<body>

<div class="header">
    <div class="logo">SIGU</div>
    <div class="subtitle">SISTEMA INTEGRADO DE GESTIÓN URBANA</div>
    <div style="margin-top: 5px; font-weight: bold;">REPORTE DE ESTADO DOCUMENTAL - {{ $empresa->nombre_empresa ?? 'Empresa' }}</div>
    <div class="subtitle">Generado el: {{ date('d/m/Y H:i:s') }}</div>
</div>

<div class="content">
    
    <div class="section-title">1. DOCUMENTACIÓN DE VEHÍCULOS (FLOTA)</div>
    <table>
        <thead>
            <tr>
                <th width="12%">Placa</th>
                <th width="20%">Tipo Documento</th>
                <th width="35%">Nombre Documento</th>
                <th width="15%">Vencimiento</th>
                <th width="18%">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($documentosBus as $d)
                <tr>
                    <td style="font-weight: bold; color: #5E17EB;">{{ $d->placa }}</td>
                    <td>{{ $d->tipoDocumento->nombre ?? 'N/A' }}</td>
                    <td>{{ $d->nombre }}</td>
                    <td>{{ $d->fecha_vencimiento ? \Carbon\Carbon::parse($d->fecha_vencimiento)->format('Y-m-d') : 'N/A' }}</td>
                    <td>
                        @php
                            $estadoNom = $d->estado->nombre_estado ?? 'N/A';
                            $class = str_contains(strtoupper($estadoNom), 'ACTIVO') || str_contains(strtoupper($estadoNom), 'APROBADO') ? 'bg-success' : 
                                     (str_contains(strtoupper($estadoNom), 'VENCIDO') || str_contains(strtoupper($estadoNom), 'RECHAZADO') ? 'bg-danger' : 'bg-warning');
                        @endphp
                        <span class="badge {{ $class }}">{{ $estadoNom }}</span>
                    </td>
                </tr>
            @endforeach
            @if($documentosBus->isEmpty())
                <tr><td colspan="5" style="text-align: center;">No hay documentos registrados para vehículos.</td></tr>
            @endif
        </tbody>
    </table>

    <div class="section-title">2. DOCUMENTACIÓN DE PERSONAL (CONDUCTORES/PROPIETARIOS)</div>
    <table>
        <thead>
            <tr>
                <th width="15%">Documento</th>
                <th width="25%">Nombre Completo</th>
                <th width="15%">Rol</th>
                <th width="15%">Tipo Doc</th>
                <th width="15%">Vencimiento</th>
                <th width="15%">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($documentosUser as $d)
                <tr>
                    <td>{{ $d->doc_usuario }}</td>
                    <td><strong>{{ $d->usuario ? ($d->usuario->primer_nombre . ' ' . $d->usuario->primer_apellido) : $d->nombre }}</strong></td>
                    <td style="text-transform: uppercase; font-size: 8pt;">{{ $d->usuario->tipoUsuario->nombre_tipo ?? 'N/A' }}</td>
                    <td>{{ $d->tipoDocumento->nombre ?? 'N/A' }}</td>
                    <td>{{ $d->fecha_vencimiento ? \Carbon\Carbon::parse($d->fecha_vencimiento)->format('Y-m-d') : 'N/A' }}</td>
                    <td>
                        @php
                            $estadoNom = $d->estado->nombre_estado ?? 'N/A';
                            $class = str_contains(strtoupper($estadoNom), 'ACTIVO') || str_contains(strtoupper($estadoNom), 'APROBADO') ? 'bg-success' : 
                                     (str_contains(strtoupper($estadoNom), 'VENCIDO') || str_contains(strtoupper($estadoNom), 'RECHAZADO') ? 'bg-danger' : 'bg-warning');
                        @endphp
                        <span class="badge {{ $class }}">{{ $estadoNom }}</span>
                    </td>
                </tr>
            @endforeach
            @if($documentosUser->isEmpty())
                <tr><td colspan="6" style="text-align: center;">No hay documentos registrados para personal.</td></tr>
            @endif
        </tbody>
    </table>

</div>

<div class="footer">
    Gestión de Transporte v3.0 - Página <script type="text/php">echo $PAGE_NUM . " de " . $PAGE_COUNT;</script>
</div>

</body>
</html>
