<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido a nuestra plataforma</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background-color: #007bff; padding: 20px; color: #ffffff; text-align: center;">
                            <h1 style="margin: 0;">¡Bienvenido, {{ $nombreCliente }}!</h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 30px;">
                            <p style="font-size: 16px;">Gracias por registrarte en nuestra plataforma. Te compartimos tu contraseña temporal para que puedas acceder al sistema:</p>

                            <div style="text-align: center; margin: 30px 0;">
                                <span style="display: inline-block; background-color: #f0f0f0; padding: 12px 24px; border-radius: 8px; font-size: 18px; font-weight: bold; letter-spacing: 1px;">
                                    {{ $contraseña }}
                                </span>
                            </div>

                            <p style="font-size: 15px; color: #666;">Te recomendamos iniciar sesión y cambiar tu contraseña lo antes posible desde tu perfil.</p>
                            <p style="font-size: 15px; color: #666;">Si no solicitaste esta cuenta, ignorá este correo.</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f8f8f8; text-align: center; padding: 20px; font-size: 14px; color: #999;">
                            © {{ date('Y') }} Tu Empresa. Todos los derechos reservados.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>