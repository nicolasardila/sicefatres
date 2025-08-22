<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="xAI">
    <meta name="description" content="Reporte de Actividades Generado">
    <title>Reporte de Actividades</title>
    <style>
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #333;
            margin: 40px;
            background-color: #fff;
        }

        /* Encabezado */
        .header {
    text-align: center;
    margin-bottom: 40px;
    border-bottom: 2px solid #004aad;
    padding-bottom: 20px;
}

.header img.logo {
    display: block;
    margin: 0 auto 10px auto; /* centra horizontalmente */
    max-width: 150px;
}

        .header h1 {
            font-size: 24pt;
            color: #004aad;
            margin: 0;
        }
        .header p {
            font-size: 10pt;
            color: #666;
            margin: 5px 0;
        }

        /* Estilo de registros */
        .record {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8fafc;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            page-break-inside: avoid;
        }
        .field {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #e8ecef;
        }
        .field:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            width: 200px;
            color: #004aad;
            min-width: 200px;
        }
        .value {
            flex: 1;
            color: #333;
        }

        /* Estilo de tabla para pantallas grandes */
        @media screen and (min-width: 768px) {
            .record {
                padding: 20px;
            }
            .field {
                padding: 10px 0;
            }
        }

        /* Estilo para impresión */
        @media print {
            body {
                margin: 0;
                font-size: 10pt;
            }
            .header {
                border-bottom: 1px solid #004aad;
            }
            .record {
                box-shadow: none;
                border: 1px solid #ccc;
            }
            .footer {
                position: fixed;
                bottom: 0;
                width: 100%;
                text-align: center;
                font-size: 8pt;
                color: #666;
            }
        }

        /* Pie de página */
        .footer {
            text-align: center;
            font-size: 10pt;
            color: #666;
            margin-top: 40px;
            border-top: 1px solid #e0e0e0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <img src="{{ public_path('imgLombri/logoreporte.png') }}" alt="Logo" class="logo">
        <h1>Reporte de Actividades</h1>
        <div>Unidad Productiva de Lombricultivo</div>
        <div>SEGUIMIENTO Y REPORTE DE ACTIVIDADES POR CAMAS DEL LOMBRICULTIVO</div>
        
        <p>Generado el {{ date('d/m/Y') }} | xAI Solutions</p>
    </div>

    <!-- Contenido -->
    @foreach($actividades as $actividad)
    <div class="record">
        <div class="field">
            <div class="label">Cama</div>
            <div class="value">{{ $actividad->wormBed->number ?? 'Sin número' }}</div>
        </div>
        <div class="field">
            <div class="label">Tipo</div>
            <div class="value">{{ ucfirst($actividad->tipo) }}</div>
        </div>
        <div class="field">
            <div class="label">Descripción</div>
            <div class="value">{{ $actividad->descripcion ?? '-' }}</div>
        </div>
        <div class="field">
            <div class="label">Fecha</div>
            <div class="value">{{ $actividad->fecha_actividad }}</div>
        </div>
        <div class="field">
            <div class="label">Hora</div>
            <div class="value">{{ $actividad->hora_actividad }}</div>
        </div>

        @if($columnas['feeding'])
        <div class="field">
            <div class="label">Alimento</div>
            <div class="value">{{ $actividad->feeding->tipo_alimento ?? '-' }}</div>
        </div>
        <div class="field">
            <div class="label">Cantidad (kg)</div>
            <div class="value">{{ $actividad->feeding->cantidad_alimento ?? '-' }}</div>
        </div>
        @endif

        @if($columnas['moisture'])
        <div class="field">
            <div class="label">Nivel Humedad (%)</div>
            <div class="value">{{ $actividad->moisture->nivel_humedad ?? '-' }}</div>
        </div>
        @endif

        @if($columnas['harvest'])
        <div class="field">
            <div class="label">Peso Cosecha (kg)</div>
            <div class="value">{{ $actividad->harvest->peso_cosechado ?? '-' }}</div>
        </div>
        <div class="field">
            <div class="label">Producto</div>
            <div class="value">{{ $actividad->harvest->producto ?? '-' }}</div>
        </div>
        @endif

        @if($columnas['ph'])
        <div class="field">
            <div class="label">pH</div>
            <div class="value">{{ $actividad->ph->ph ?? '-' }}</div>
        </div>
        @endif

        @if($columnas['temperature'])
        <div class="field">
            <div class="label">Temperatura (°C)</div>
            <div class="value">{{ $actividad->temperature->valor ?? '-' }}</div>
        </div>
        @endif
    </div>
    @endforeach

    <!-- Pie de página -->
    <div class="footer">
        <p>Reporte generado por xAI Solutions | Página {{ '{PAGE}' }} de {{ '{TOTALPAGES}' }}</p>
    </div>
</body>
</html>