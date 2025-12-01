<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Incidencia Disciplinaria</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            position: relative;
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 0;
        }
        .fondo {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        .contenido {
            position: relative;
            padding: 40px 50px;
            z-index: 1;
        }
        .encabezado {
            text-align: center;
            margin-bottom: 15px;
        }
        .encabezado .logos {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .encabezado img {
            height: 60px;
        }
        .encabezado .titulo-institucion {
            font-size: 11px;
            line-height: 1.3;
            color: #333;
            font-weight: bold;
        }
        .titulo-documento {
            background-color: #8B0000;
            color: white;
            padding: 8px;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0;
            border-radius: 3px;
        }
        .cuerpo {
            font-size: 11px;
            line-height: 1.6;
            color: #000;
            text-align: justify;
        }
        .cuerpo p {
            margin-bottom: 10px;
        }
        .fecha-lugar {
            text-align: right;
            margin-bottom: 15px;
            font-style: italic;
        }
        .descripcion-box {
            border: 1px solid #333;
            padding: 10px;
            min-height: 100px;
            margin: 10px 0;
            background-color: #f9f9f9;
        }
        .medidas-box {
            border: 1px solid #333;
            padding: 10px;
            min-height: 80px;
            margin: 10px 0;
            background-color: #f9f9f9;
        }
        .firmas {
            margin-top: 40px;
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
        }
        .firma-item {
            text-align: center;
            width: 40%;
        }
        .firma-item img {
            max-height: 80px;
            margin-bottom: 5px;
        }
        .firma-item .linea {
            border-top: 1px solid #333;
            margin-bottom: 5px;
        }
        .firma-item .texto {
            font-size: 10px;
            font-weight: bold;
        }
        .footer-nota {
            margin-top: 20px;
            font-size: 9px;
            text-align: justify;
            color: #555;
            font-style: italic;
        }
    </style>
</head>
<body>
    @if(!empty($fondo_base64))
    <img src="{{ $fondo_base64 }}" class="fondo" alt="Fondo">
    @endif

    <div class="contenido">
        <!-- Espaciado superior -->
        <div style="height: 90px;"></div>

        <div style="text-align: right; font-size: 11px; margin-bottom: 20px;">
            Pachuca de Soto, {{ $fecha_formato }}
        </div>

        <h2 style="text-align: center; font-size: 14px; margin: 20px 0; font-weight: bold;">
            Reporte de Incidencias Disciplinarias
        </h2>

        <div class="cuerpo">
            <p style="margin-bottom: 15px;">
                El presente reporte involucra a <strong>{{ $alumno }}</strong>, alumno del <strong>{{ $grado }}°</strong> grupo <strong>{{ $grupo }}</strong>, el día <strong>{{ $fecha }}</strong>
            </p>

            <p style="margin-bottom: 10px;">
                El suceso de registro en el área de <strong><u>{{ $area }}</u></strong> y consistió en lo siguiente:
            </p>
            
            <div style="margin: 10px 0 15px 0; padding: 10px; border: 1px solid #333; min-height: 60px; background-color: #f9f9f9;">
                {{ $descripcion }}
            </div>

            <p style="margin-bottom: 10px;">
                Al detectarse la incidencia, el personal de la institución tomó las siguientes medidas
            </p>
            
            <div style="margin: 10px 0 20px 0; padding: 10px; border: 1px solid #333; min-height: 60px; background-color: #f9f9f9;">
                {{ $medidas }}
            </div>

            <div style="font-size: 10px; text-align: justify; line-height: 1.4; margin-bottom: 30px;">
                <p>
                    Asumo plena responsabilidad de que el contenido de este reporte es un reflejo objetivo de los hechos ocurridos y las medidas tomadas de acuerdo con el reglamento. Se notifica al Padre/Madre/Tutor para que esté al tanto de la conducta y las medidas tomadas.
                </p>
            </div>

            <table style="width: 100%; margin-top: 50px;">
                <tr>
                    <td style="width: 50%; text-align: center; vertical-align: bottom; padding-right: 15px;">
                        @if(!empty($firma_tutor))
                        <img src="{{ $firma_tutor }}" alt="Firma Tutor" style="width: 280px; height: 140px; object-fit: contain; margin-bottom: 10px;">
                        @else
                        <div style="height: 140px;"></div>
                        @endif
                        <div style="border-top: 1px solid #000; padding-top: 5px; margin-top: 10px;">
                            <div style="font-size: 10px; font-weight: bold;">{{ $nombre_tutor_firma ?? '' }}</div>
                            <div style="font-size: 9px; font-weight: normal;">Firma Padre/Madre/Tutor</div>
                        </div>
                    </td>
                    <td style="width: 50%; text-align: center; vertical-align: bottom; padding-left: 15px;">
                        @if(!empty($firma_docente))
                        <img src="{{ $firma_docente }}" alt="Firma Docente" style="width: 280px; height: 140px; object-fit: contain; margin-bottom: 10px;">
                        @else
                        <div style="height: 140px;"></div>
                        @endif
                        <div style="border-top: 1px solid #000; padding-top: 5px; margin-top: 10px;">
                            <div style="font-size: 10px; font-weight: bold;">{{ $nombre_docente_reporta ?? '' }}</div>
                            <div style="font-size: 9px; font-weight: normal;">Firma del docente que reporta</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
