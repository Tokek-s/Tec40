<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Autorización de Salida</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            position: relative;
        }
        
        .page {
            width: 21cm;
            height: 29.7cm;
            padding: 0;
            margin: 0 auto;
            position: relative;
        }
        
        .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 21cm;
            height: 29.7cm;
            z-index: 1;
        }
        
        .content {
            position: relative;
            padding: 140px 60px 60px 60px;
            z-index: 5;
        }
        
        .fecha-header {
            text-align: right;
            margin-bottom: 8px;
            font-size: 10pt;
            padding-right: 20px;
        }
        
        .fecha-header .underline {
            min-width: 25px;
            padding: 0 2px;
        }
        
        h1 {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 30px;
        }
        
        p {
            margin-bottom: 15px;
            text-align: justify;
        }
        
        .underline {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 150px;
            text-align: center;
            padding: 0 5px;
        }
        
        .firma-section {
            margin-top: 80px;
            display: table;
            width: 100%;
        }
        
        .firma-item {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }
        
        .firma-label {
            font-size: 10pt;
            margin-top: 10px;
        }
        
        .firma-line {
            border-top: 1px solid #000;
            width: 200px;
            margin: 60px auto 5px auto;
        }
        
        .firma-img {
            max-width: 200px;
            max-height: 60px;
            margin: 0 auto;
            display: block;
        }
    </style>
</head>
<body>
    <div class="page">
        @if(!empty($fondo_image))
        <img src="{{ $fondo_image }}" class="bg-image" alt="Fondo">
        @endif
        <div class="content">
            <p class="fecha-header">Pachuca, Hidalgo a <span class="underline">{{ $dia }}</span> de <span class="underline">{{ $mes }}</span> del <span class="underline">{{ $anio }}</span></p>
            <h1>Autorización para entrega de alumnos en horario escolar</h1>
            
            <p>
                Por medio de la presente, yo <span class="underline">{{ $tutor_nombre }}</span>, en mi calidad de 
                <span class="underline">{{ $parentesco }}</span> del alumno <span class="underline">{{ $alumno_nombre }}</span> del {{ $grado }}° grupo {{ $grupo }}, extiendo mi AUTORIZACIÓN EXPRESA para que el alumno(a) sea 
                entregado y retirado del plantel el día <span class="underline">{{ $dia }}</span> de 
                <span class="underline">{{ $mes }}</span> del <span class="underline">{{ $anio }}</span> a la hora 
                aproximada de <span class="underline">{{ $hora }}</span>.
            </p>
            
            <p>
                El alumno(a) será recogido por <span class="underline">{{ $recoge_nombre }}</span>, con parentesco <span class="underline">{{ $recoge_parentesco }}</span>, por el motivo de:
            </p>
            
            <p style="margin-left: 20px;">
                {{ $motivo }}
            </p>
            
            <p style="margin-top: 30px;">
                Asumo total responsabilidad por la seguridad del alumno(a) desde el momento de su 
                entrega a la persona autorizada, y me comprometo a que esta presente su identificación 
                oficial al personal escolar para su cotejo.
            </p>
            
            <div class="firma-section">
                <div class="firma-item">
                    @if($firma_tutor)
                        <img src="{{ $firma_tutor }}" class="firma-img" alt="Firma">
                    @endif
                    <div class="firma-line"></div>
                    <div class="firma-label">{{ $tutor_nombre }}</div>
                </div>
                
                <div class="firma-item">
                    @if($firma_recoge)
                        <img src="{{ $firma_recoge }}" class="firma-img" alt="Firma">
                    @endif
                    <div class="firma-line"></div>
                    <div class="firma-label">{{ $recoge_nombre }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
