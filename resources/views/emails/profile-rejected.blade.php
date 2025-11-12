<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización sobre la Verificación de tu Perfil</title>
    <style>
        /* Estilos básicos para compatibilidad en correo electrónico */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            font-size: 16px;
            color: #ffffff !important;
            background-color: #FF7043; /* Color Naranja/Rojo */
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="padding: 40px; background-color: #FF7043; color: #ffffff; border-radius: 8px 8px 0 0;">
                            <h1 style="margin: 0; font-size: 28px;">Actualización de Perfil</h1>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 20px 40px; color: #333333; line-height: 1.6;">
                            <p style="margin: 0 0 20px 0; font-size: 18px;">
                                Hola **{{ $user->name ?? 'Usuario' }}**,
                            </p>
                            <p style="margin: 0 0 20px 0;">
                                Queremos informarte que el estado de verificación de tu perfil ha sido **rechazado** o **revocado**.
                            </p>
                            <p style="margin: 0 0 20px 0; font-weight: bold; color: #FF7043;">
                                Esto puede deberse a que la documentación estaba incompleta, ilegible o no cumplía con nuestros requisitos.
                            </p>
                            
                            <h3 style="margin: 25px 0 15px 0;">Próximos Pasos:</h3>
                            <p style="margin: 0 0 20px 0;">
                                Te invitamos a iniciar sesión y revisar la sección de tu perfil. Por favor, asegúrate de que toda la documentación cargada sea clara y esté actualizada. Podrás volver a enviar tu documentación para una nueva revisión.
                            </p>
                            
                            <!-- Button -->
                            <div style="text-align: center; margin-top: 25px;">
                                <a href="{{ url('/profile') }}" class="button" target="_blank" style="color: #ffffff !important;">
                                    Revisar mi Perfil
                                </a>
                            </div>

                            <p style="margin: 30px 0 0 0;">
                                Lamentamos cualquier inconveniente. Si tienes dudas, por favor, contacta a nuestro soporte.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 40px; background-color: #f8f8f8; color: #999999; border-radius: 0 0 8px 8px; font-size: 12px; text-align: center;">
                            <p style="margin: 0;">Saludos cordiales,</p>
                            <p style="margin: 5px 0 0 0;">El equipo de **{{ config('app.name') }}**</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>