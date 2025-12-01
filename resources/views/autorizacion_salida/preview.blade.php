<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Previsualizar y autorizar salida</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; color: #000; background:#f3f4f6; padding:20px; }
        .page { width: 21cm; min-height: 29.7cm; margin: 0 auto; background-image: url('{{ public_path('images/doc-fondo.png') }}'); background-size: 100% auto; background-repeat:no-repeat; background-position: top center; padding: 140px 60px 60px 60px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); background-color: #fff; }
        h1 { text-align:center; font-size:14pt; margin-bottom:18px }
        p { margin-bottom:12px; text-align:justify }
        .underline { display:inline-block; border-bottom:1px solid #000; min-width:120px; padding:0 6px; }
        .firma-section { margin-top:60px; display:flex; gap:40px; justify-content:space-between }
        .firma-item { width:48%; text-align:center }
        .firma-line { border-top:1px solid #000; width:200px; margin:14px auto 6px auto }
        .firma-img { max-width:220px; max-height:70px; display:block; margin:0 auto }
        #canvasRecoge { width: 220px; height:70px; border:1px solid #ddd; display:block; margin:0 auto; background:#fff }
        .actions { margin-top:18px; text-align:center }
        .btn { display:inline-block; padding:10px 14px; border-radius:6px; cursor:pointer }
        .btn-primary { background:#0b74f6; color:#fff }
        .btn-muted { background:#e5e7eb; color:#111; margin-right:8px }
    </style>
</head>
<body>
    <div class="page">
    <p style="text-align:right; margin-bottom: 12px">Pachuca, Hidalgo a <span class="underline">{{ $dia }}</span> de <span class="underline">{{ $mes }}</span> del <span class="underline">{{ $anio }}</span></p>
        <h1>Autorización para entrega de alumnos en horario escolar</h1>

        <p>
            Por medio de la presente, yo <span class="underline">{{ $tutor_nombre }}</span>, en mi calidad de 
            <span class="underline">{{ $tutor_parentesco }}</span> del alumno <span class="underline">{{ $alumno_nombre }}</span>,
            extiendo mi AUTORIZACIÓN EXPRESA para que el alumno(a) sea entregado y retirado del plantel el día <span class="underline">{{ $dia }}</span> de <span class="underline">{{ $mes }}</span> del <span class="underline">{{ $anio }}</span> a la hora aproximada de <span class="underline">{{ $salida->hora_salida }}</span>.
        </p>

        <p>
            El alumno(a) será recogido por <span class="underline">{{ $recoge_nombre ?? '' }}</span>, con parentesco <span class="underline">{{ $recoge_parentesco ?? '' }}</span>, por el motivo de:
        </p>

        <p style="margin-left:20px">{{ $salida->motivo }}</p>

        <div class="firma-section">
            <div class="firma-item">
                @if($firma_tutor)
                    <img src="{{ $firma_tutor }}" class="firma-img" alt="Firma tutor">
                @endif
                <div class="firma-line"></div>
                <div class="firma-label">{{ $tutor_nombre }}</div>
            </div>

            <div class="firma-item">
                {{-- canvas para firma de quien recoge --}}
                    <div id="previewRecoge" style="width:220px; height:70px; border:1px solid #ddd; background:#fff; display:block; margin:0 auto;">
                        @if($documento && $documento->firma)
                            @php $decodedDoc = json_decode($documento->firma, true) ?? []; @endphp
                            @if(!empty($decodedDoc['recoge']))
                                <img src="{{ $decodedDoc['recoge'] }}" style="max-width:220px; max-height:70px; display:block; margin:0 auto;" />
                            @endif
                        @endif
                    </div>
                    <div style="text-align:center; margin-top:8px">
                        <button id="abrirFirmaBtn" class="btn btn-muted">Firmar (pantalla completa)</button>
                    </div>
                <div class="firma-line"></div>
                    <div class="firma-label">{{ $recoge_nombre ?? $salida->entregadoA?->nombre_completo ?? 'Quien recoge' }}</div>
            </div>
        </div>

            <div class="actions">
                <button id="abrirFirmaBtnFooter" class="btn btn-muted">Limpiar / Firmar</button>
                <button id="abrirFirmaBtn2" class="btn btn-primary">Generar documento</button>
            </div>

            <!-- Modal de firma a pantalla completa -->
            <div id="modalFirma" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
                <div style="background:#fff; border-radius:8px; padding:16px; max-width:950px; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <h3 style="margin:0; font-size:18px; font-weight:600;">Firmar como: <span id="modalRecogeNombre">{{ $recoge_nombre ?? $salida->entregadoA?->nombre_completo ?? 'Quien recoge' }}</span></h3>
                        <button id="modalCerrarBtn" style="background:none; border:none; color:#666; font-size:24px; cursor:pointer; padding:0; width:32px; height:32px; line-height:1;">✕</button>
                    </div>
                    <canvas id="canvasRecogeLarge" width="900" height="400" style="width:900px; height:400px; border:1px solid #ddd; background:#fff; display:block;"></canvas>
                    <div style="margin-top:12px; display:flex; justify-content:flex-end; gap:8px;">
                        <button id="modalLimpiarBtn" style="padding:8px 12px; border-radius:4px; background:#dc2626; color:#fff; border:none; cursor:pointer; font-size:14px;">Limpiar</button>
                        <button id="modalGuardarBtn" style="padding:8px 12px; border-radius:4px; background:#16a34a; color:#fff; border:none; cursor:pointer; font-size:14px;">Aceptar</button>
                        <button id="modalCancelarBtn" style="padding:8px 12px; border-radius:4px; background:#e5e7eb; color:#111; border:none; cursor:pointer; font-size:14px;">Cancelar</button>
                    </div>
                </div>
            </div>
    </div>

<script>
(function(){
    const modal = document.getElementById('modalFirma');
    const abrirBtn = document.getElementById('abrirFirmaBtn');
    const abrirBtnFooter = document.getElementById('abrirFirmaBtnFooter');
    const abrirBtn2 = document.getElementById('abrirFirmaBtn2');
    const modalCancelar = document.getElementById('modalCancelarBtn');
    const modalCerrar = document.getElementById('modalCerrarBtn');
    const modalLimpiar = document.getElementById('modalLimpiarBtn');
    const modalGuardar = document.getElementById('modalGuardarBtn');
    const previewRecoge = document.getElementById('previewRecoge');
    const modalRecogeNombre = document.getElementById('modalRecogeNombre');

    const canvas = document.getElementById('canvasRecogeLarge');
    const ctx = canvas.getContext('2d');
    
    // Canvas exacto sin escalar para dibujo preciso
    function resizeCanvas() {
        const targetWidth = 900;
        const targetHeight = 400;
        canvas.width = targetWidth;
        canvas.height = targetHeight;
        canvas.style.width = targetWidth + 'px';
        canvas.style.height = targetHeight + 'px';
        ctx.fillStyle = '#fff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.strokeStyle = '#111';
        ctx.lineWidth = 2;
    }
    resizeCanvas();

    let drawing = false;
    const getPos = e => {
        const r = canvas.getBoundingClientRect();
        return { x: (e.clientX - r.left), y: (e.clientY - r.top) };
    };
    
    const down = e => {
        e.preventDefault();
        e.stopPropagation();
        if (e.pressure === 0 && e.pointerType !== 'mouse') return;
        if (e.pointerType === 'mouse' && e.button !== 0) return;
        drawing = true;
        const p = getPos(e);
        ctx.beginPath();
        ctx.moveTo(p.x, p.y);
        canvas.setPointerCapture(e.pointerId);
    };
    
    const move = e => {
        if (!drawing) return;
        if (e.pressure === 0 && e.pointerType === 'pen') return;
        e.preventDefault();
        e.stopPropagation();
        const p = getPos(e);
        ctx.lineTo(p.x, p.y);
        const pressure = e.pressure > 0 ? e.pressure : 0.5;
        ctx.lineWidth = Math.max(1, 2 * pressure * 2);
        ctx.stroke();
    };
    
    const up = e => {
        e.preventDefault();
        e.stopPropagation();
        drawing = false;
        try { canvas.releasePointerCapture(e.pointerId); } catch {}
    };
    
    const cancel = e => {
        drawing = false;
        try { canvas.releasePointerCapture(e.pointerId); } catch {}
    };
    
    canvas.addEventListener('pointerdown', down, { passive: false });
    canvas.addEventListener('pointermove', move, { passive: false });
    canvas.addEventListener('pointerup', up, { passive: false });
    canvas.addEventListener('pointercancel', cancel, { passive: false });
    canvas.addEventListener('pointerleave', cancel, { passive: false });

    function clearCanvas() { 
        ctx.clearRect(0,0,canvas.width,canvas.height); 
        ctx.fillStyle='#fff'; 
        ctx.fillRect(0,0,canvas.width,canvas.height); 
    }

    // Abrir modal y precargar firma si existe
    async function openModal() {
        // precargar si hay imagen previa
        const decoded = {!! json_encode($documento && $documento->firma ? json_decode($documento->firma, true) : []) !!};
        const existing = decoded['recoge'] ?? null;
        clearCanvas();
        if (existing) {
            // cargar imagen en canvas
            const img = new Image();
            img.onload = function(){
                // dibujar centrado y escalado
                const scale = Math.min(canvas.width / img.width, canvas.height / img.height);
                const w = img.width * scale;
                const h = img.height * scale;
                const x = (canvas.width - w) / 2;
                const y = (canvas.height - h) / 2;
                ctx.drawImage(img, x, y, w, h);
            };
            img.src = existing;
        }
        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden','false');
    }

    function closeModal() {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden','true');
    }

    abrirBtn && abrirBtn.addEventListener('click', openModal);
    abrirBtnFooter && abrirBtnFooter.addEventListener('click', openModal);
    abrirBtn2 && abrirBtn2.addEventListener('click', openModal);

    modalCancelar && modalCancelar.addEventListener('click', ()=>{ closeModal(); });
    modalCerrar && modalCerrar.addEventListener('click', ()=>{ closeModal(); });
    modalLimpiar.addEventListener('click', ()=>{ clearCanvas(); });

    modalGuardar.addEventListener('click', async ()=>{
        // obtener dataURL de la firma y enviarla
        const data = canvas.toDataURL('image/png');
        // actualizar vista previa
        previewRecoge.innerHTML = '';
        const img = document.createElement('img');
        img.src = data; img.style.maxWidth = '220px'; img.style.maxHeight = '70px'; img.style.display='block'; img.style.margin='0 auto';
        previewRecoge.appendChild(img);
        closeModal();

        const token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
        try {
            const resp = await fetch("{{ route('admin.docs.salidas_finalize', ['salida' => $salida->id]) }}", {
                method: 'POST',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': token, 'Accept':'application/json' },
                body: JSON.stringify({ firma_recoge: data })
            });
            if (!resp.ok) {
                const txt = await resp.text();
                alert('Error finalizando autorización: ' + resp.status + '\n' + txt);
                return;
            }
            const json = await resp.json();
            if (json?.ok && json.pdf_view_url) {
                window.location.href = json.pdf_view_url;
            } else if (json?.ok && json.pdf_url) {
                window.location.href = json.pdf_url;
            } else {
                alert('Autorización finalizada, pero no se devolvió URL del PDF.');
            }
        } catch (e) {
            console.error(e);
            alert('Error al enviar la firma.');
        }
    });
})();
</script>
</body>
</html>
