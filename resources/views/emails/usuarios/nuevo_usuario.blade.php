<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a SIGU - Tus Credenciales</title>
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f7fa; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .header { background: #5e548e; padding: 40px 20px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px; }
        .header p { margin: 10px 0 0; font-size: 14px; opacity: 0.9; }
        .content { padding: 40px; color: #2d3748; line-height: 1.6; }
        .welcome-msg { font-size: 18px; font-weight: 700; margin-bottom: 20px; color: #1a202c; }
        .credentials-card { background: #f8fafc; border: 1px solid #edf2f7; border-radius: 10px; padding: 25px; margin: 25px 0; }
        .credential-item { margin-bottom: 15px; }
        .credential-item:last-child { margin-bottom: 0; }
        .label { font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #718096; margin-bottom: 4px; display: block; }
        .value { font-size: 16px; font-weight: 600; color: #2d3748; }
        .password-box { background: #ffffff; border: 1px dashed #cbd5e0; padding: 10px 15px; border-radius: 6px; display: inline-block; font-family: monospace; font-size: 18px; color: #5e548e; margin-top: 5px; }
        .btn { display: inline-block; background: #5e548e; color: #ffffff !important; padding: 16px 32px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 15px; margin-top: 10px; transition: background 0.3s ease; }
        .btn:hover { background: #4a4171; }
        .footer { background: #f8fafc; padding: 25px; text-align: center; color: #718096; font-size: 12px; border-top: 1px solid #edf2f7; }
        .footer p { margin: 5px 0; }
        .security-note { font-size: 13px; color: #718096; margin-top: 25px; font-style: italic; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SIGU</h1>
            <p>Sistema Integrado de Gestión Unificada</p>
        </div>
        <div class="content">
            <p class="welcome-msg">¡Hola, {{ $nombre }}!</p>
            <p>Se ha creado una cuenta administrativa para ti en nuestra plataforma. Ahora puedes acceder con las siguientes credenciales:</p>
            
            <div class="credentials-card">
                <div class="credential-item">
                    <span class="label">Documento de Usuario</span>
                    <span class="value">{{ $documento }}</span>
                </div>
                @if($nit)
                <div class="credential-item">
                    <span class="label">NIT Empresa</span>
                    <span class="value">{{ $nit }}</span>
                </div>
                @endif
                <div class="credential-item">
                    <span class="label">Contraseña de Acceso</span>
                    <div class="password-box">{{ $password }}</div>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ $url_login }}" class="btn">INICIAR SESIÓN</a>
            </div>

            <p class="security-note text-center">
                <strong>Importante:</strong> Por motivos de seguridad, te recomendamos cambiar tu contraseña una vez que hayas ingresado al sistema por primera vez.
            </p>
        </div>
        <div class="footer">
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            <p>&copy; {{ date('Y') }} SIGU - Departamento de Tecnología.</p>
        </div>
    </div>
</body>
</html>
