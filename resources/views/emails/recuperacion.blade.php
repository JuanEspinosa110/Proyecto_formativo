<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Código de Recuperación</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; border-radius: 10px; padding: 30px; border-top: 5px solid #5e548e;">
        <h2 style="color: #5e548e; margin-top: 0;">Recuperación de Contraseña</h2>
        <p>Has solicitado restablecer tu contraseña en el sistema SIGU.</p>
        <p>Tu código de seguridad es:</p>
        <div style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #5e548e; background: #fff; padding: 15px; border-radius: 5px; border: 1px dashed #5e548e; display: inline-block; margin: 10px 0;">
            {{ $codigo }}
        </div>
        <p>Este código es válido por 10 minutos. No compartas este código con nadie.</p>
        <hr style="border: 0; border-top: 1px solid #dee2e6; margin: 20px 0;">
        <p style="font-size: 12px; color: #6c757d;">Si no has solicitado este cambio, puedes ignorar este correo de forma segura.</p>
        <p style="font-size: 14px; font-weight: bold; color: #5e548e;">Atentamente,<br>Equipo SIGU</p>
    </div>
</body>
</html>
