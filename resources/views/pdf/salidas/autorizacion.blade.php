<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Autorización de salida</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #111; margin: 30px; }
        h1 { font-size: 18px; margin: 0 0 16px; }
        .row { margin-bottom: 10px; }
        .label { display: block; font-weight: bold; color: #444; margin-bottom: 4px; }
        .box { border: 1px solid #bbb; padding: 8px; border-radius: 4px; }
        .firma-box { border: 1px dashed #999; height: 140px; display: flex; align-items: center; justify-content: center; }
        .muted { color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <h1>Autorización de salida</h1>

    <div class="row">
        <span class="label">Nombre de quien retira</span>
        <div class="box">{{ $salida->entregadoA->nombre_completo }} @if(!empty($salida->entregadoA->parentesco)) ({{ $salida->entregadoA->parentesco }}) @endif</div>
    </div>

    <div class="row">
        <span class="label">Motivo</span>
        <div class="box">{!! nl2br(e($salida->motivo)) !!}</div>
    </div>

    <div class="row">
        <span class="label">Fecha</span>
        <div class="box">{{ optional($salida->fecha)->format('d/m/Y') }}</div>
        <div class="muted">(Alumno: {{ $salida->alumno->nombre_s }} {{ $salida->alumno->apellido_paterno }} {{ $salida->alumno->apellido_materno }})</div>
    </div>

    <div class="row">
        <span class="label">Firma</span>
        @if(!empty($firma_data))
            <div class="firma-box">
                <img src="{{ $firma_data }}" style="max-height:120px;" />
            </div>
        @else
            <div class="firma-box muted">(Firma capturada digitalmente)</div>
        @endif
    </div>
</body>
</html>