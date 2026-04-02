<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Verificación - Cívica</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #f3f4f6; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { max-height: 60px; }
        .content { color: #374151; font-size: 16px; line-height: 1.5; }
        .code-box { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 15px; margin: 20px 0; text-align: center; }
        .code { font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #1d4ed8; }
        .footer { margin-top: 30px; font-size: 12px; color: #6b7280; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Sistema Integrado Cívica</h2>
        </div>
        <div class="content">
            <p>Hola, <strong>{{ $nombre }}</strong>,</p>
            <p>Hemos detectado un intento de inicio de sesión en su cuenta administrativa. Para verificar su identidad, utilice el siguiente código de seguridad:</p>
            
            <div class="code-box">
                <span class="code">{{ $codigo }}</span>
            </div>

            <p>Este código es válido por 10 minutos. Si no solicitó este código o no intentó iniciar sesión, ignore este correo y le recomendamos cambiar su contraseña.</p>
        </div>
        <div class="footer">
            <p>Este es un mensaje automático, por favor no responda a este correo.</p>
            <p>&copy; {{ date('Y') }} Cívica. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
