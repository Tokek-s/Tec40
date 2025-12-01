<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
            size: 8.9cm 14cm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
        }
        
        /* FRONTAL - Usa imagen de fondo con borde azul */
        .frontal {
            width: 8.9cm;
            height: 14cm;
            position: relative;
            page-break-after: always;
        }
        
        .frontal .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 8.9cm;
            height: 14cm;
            z-index: 1;
        }
        
        /* Contenido Frontal */
        .contenido {
            position: absolute;
            top: 2.8cm;
            left: 0;
            right: 0;
            text-align: center;
            z-index: 5;
        }
        
        .foto {
            width: 4.2cm;
            height: 4.2cm;
            border-radius: 50%;
            border: 6px solid #C62828;
            margin: 0 auto 0.4cm;
            overflow: hidden;
            background: #f5f5f5;
        }
        
        .foto img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .nombre {
            font-size: 15px;
            font-weight: bold;
            color: #000;
            margin: 0 0 0.2cm 0;
            padding: 0 1cm;
        }
        
        .estudiante {
            font-size: 14px;
            font-weight: bold;
            color: #C62828;
            margin: 0 0 0.6cm 0;
        }
        
        .info-basica {
            padding: 0 1.2cm;
        }
        
        .info-basica p {
            font-size: 11px;
            font-weight: bold;
            color: #000;
            margin: 0.3cm 0;
        }
        
        /* REVERSO - Usa imagen de fondo sin borde */
        .reverso {
            width: 8.9cm;
            height: 14cm;
            position: relative;
        }
        
        .reverso .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 8.9cm;
            height: 14cm;
            z-index: 1;
        }
        
        /* Contenido Reverso */
        .contenido-reverso {
            position: absolute;
            top: 3.8cm;
            left: 0;
            right: 0;
            padding: 0 0.6cm;
            z-index: 5;
        }
        
        .info-medica {
            text-align: left;
        }
        
        .info-medica p {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            line-height: 1.2;
        }
        
        .info-medica p:nth-child(1) {
            margin: 0 0 1.2cm 0;
        }
        
        .info-medica p:nth-child(2) {
            margin: 0 0 1.2cm 1.2cm;
        }
        
        .info-medica p:nth-child(3) {
            margin: 0 0 0 2.4cm;
        }
    </style>
</head>
<body>
    <!-- FRONTAL -->
    <div class="frontal">
        <img src="{{ public_path('images/front-cred.png') }}" class="bg-image" alt="Fondo frontal">
        <div class="contenido">
            <div class="foto">
                <img src="{{ $alumno['foto_path'] }}">
            </div>
            <p class="nombre">{{ $alumno['nombre_completo'] }}</p>
            <p class="estudiante">Estudiante</p>
            <div class="info-basica">
                <p>Grado: {{ $alumno['grado'] }}  Grupo: {{ $alumno['grupo'] }}</p>
                <p>Matrícula: {{ $alumno['matricula'] }}</p>
            </div>
        </div>
    </div>
    
    <!-- REVERSO -->
    <div class="reverso">
        <img src="{{ public_path('images/back-cred.png') }}" class="bg-image" alt="Fondo reverso">
        <div class="contenido-reverso">
            <div class="info-medica">
                <p>Alergias: {{ $alumno['alergias'] ? $alumno['alergias'] : '_____________' }}</p>
                <p>Tipo de sangre: {{ $alumno['tipo_sangre'] ? $alumno['tipo_sangre'] : '______' }}</p>
                <p>Contacto: {{ $alumno['telefono_tutor'] ? $alumno['telefono_tutor'] : '_____________' }}</p>
            </div>
        </div>
    </div>
</body>
</html>
