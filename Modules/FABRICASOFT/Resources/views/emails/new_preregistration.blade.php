<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Solicitud FABRICASOFT</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 3px solid #39A900;
            margin-bottom: 30px;
        }
        .logo {
            color: #39A900;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            font-size: 16px;
        }
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #495057;
        }
        .info-value {
            flex: 1;
            color: #333;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #39A900;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .btn:hover {
            background-color: #2d7a00;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            background-color: #ffc107;
            color: #856404;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">FABRICASOFT</div>
            <div class="subtitle">Sistema de Gestión de Proyectos de Software</div>
        </div>

        <h2 style="color: #39A900; margin-bottom: 20px;">
            🚀 Nueva Solicitud de Desarrollo de Software
        </h2>

        <div class="alert">
            <strong>¡Atención Administrador!</strong><br>
            Se ha recibido una nueva solicitud de desarrollo de software que requiere tu revisión.
        </div>

        <div class="info-section">
            <h3 style="color: #495057; margin-bottom: 15px;">📋 Información de la Solicitud</h3>
            
            <div class="info-row">
                <div class="info-label">ID de Solicitud:</div>
                <div class="info-value"><strong>#{{ $preregistration->id }}</strong></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Estado:</div>
                <div class="info-value">
                    <span class="status-badge">PENDIENTE DE REVISIÓN</span>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Fecha de Recepción:</div>
                <div class="info-value">{{ $preregistration->created_at->format('d/m/Y H:i:s') }}</div>
            </div>
        </div>

        <div class="info-section">
            <h3 style="color: #495057; margin-bottom: 15px;">👤 Información del Cliente</h3>
            
            <div class="info-row">
                <div class="info-label">Nombre:</div>
                <div class="info-value"><strong>{{ $preregistration->full_name }}</strong></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $preregistration->email }}</div>
            </div>
            
            @if($preregistration->phone)
            <div class="info-row">
                <div class="info-label">Teléfono:</div>
                <div class="info-value">{{ $preregistration->phone }}</div>
            </div>
            @endif
            
            <div class="info-row">
                <div class="info-label">Organización:</div>
                <div class="info-value">{{ $preregistration->organization }}</div>
            </div>
        </div>

        <div class="info-section">
            <h3 style="color: #495057; margin-bottom: 15px;">💻 Detalles del Proyecto</h3>
            
            <div class="info-row">
                <div class="info-label">Tipo de Software:</div>
                <div class="info-value"><strong>{{ $preregistration->software_type }}</strong></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Descripción:</div>
                <div class="info-value">
                    {{ Str::limit($preregistration->project_description, 200) }}
                    @if(strlen($preregistration->project_description) > 200)
                        <em>... (ver más en el sistema)</em>
                    @endif
                </div>
            </div>
            
            @if($preregistration->additional_requirements)
            <div class="info-row">
                <div class="info-label">Requisitos Adicionales:</div>
                <div class="info-value">
                    {{ Str::limit($preregistration->additional_requirements, 150) }}
                    @if(strlen($preregistration->additional_requirements) > 150)
                        <em>... (ver más en el sistema)</em>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/fabricasoft/admin/solicitud/' . $preregistration->id) }}" class="btn">
                🔍 Ver Detalles Completos
            </a>
        </div>

        <div style="background-color: #e9ecef; padding: 20px; border-radius: 5px; margin: 20px 0;">
            <h4 style="color: #495057; margin-bottom: 15px;">📝 Próximos Pasos Recomendados</h4>
            <ol style="margin: 0; padding-left: 20px; color: #495057;">
                <li>Revisar la solicitud completa en el sistema</li>
                <li>Evaluar la viabilidad técnica del proyecto</li>
                <li>Contactar al cliente si es necesario</li>
                <li>Aprobar o rechazar la solicitud</li>
                <li>Agregar notas administrativas según corresponda</li>
            </ol>
        </div>

        <div class="footer">
            <p><strong>FABRICASOFT</strong> - Sistema de Gestión de Proyectos</p>
            <p>Este es un mensaje automático del sistema. No respondas a este email.</p>
            <p>Si tienes alguna pregunta, contacta al equipo técnico.</p>
        </div>
    </div>
</body>
</html>
