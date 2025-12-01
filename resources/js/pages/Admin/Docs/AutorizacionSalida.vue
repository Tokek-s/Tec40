<template>
	<AdminLayout>
		<Head title="Autorización de salida" />
		<div class="max-w-5xl mx-auto space-y-4">
			<div class="flex items-center justify-between">
				<h1 class="text-2xl font-bold text-slate-800">Registro de salida del alumno</h1>
				<div class="flex items-center gap-2">
					<Link :href="route('admin.docs.salidas_index')" class="px-4 py-2 rounded-md bg-slate-800 text-white hover:bg-slate-700">Ver documentos</Link>
					<Link :href="route('admin.docs.salidas_index')" class="px-4 py-2 rounded-md bg-amber-600 text-white hover:bg-amber-700">Autorizar salidas</Link>
				</div>
			</div>

			<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
				<form @submit.prevent="submit">
					<div class="grid md:grid-cols-2 gap-6">
						<!-- Alumno -->
						<div>
							<label class="block text-sm text-black mb-1">Nombre del alumno</label>
							<div class="relative">
								<input v-model="alumnoQuery" @input="buscarAlumnos" type="text" placeholder="Buscar por nombre, matrícula o CURP"
									class="w-full px-3 py-2 rounded-md border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-black placeholder-black" />
												<ul v-if="sugerencias.length && alumnoQuery"
														class="absolute z-10 bg-white border border-slate-200 rounded-md mt-1 w-full max-h-48 overflow-auto text-black">
													<li v-for="a in sugerencias" :key="a.id" @click="seleccionarAlumno(a)"
															class="px-3 py-2 hover:bg-slate-100 cursor-pointer text-black">
										{{ a.nombre }} — {{ a.matricula }}
									</li>
								</ul>
							</div>
						</div>

						<!-- Fecha y hora -->
						<div>
							<label class="block text-sm text-black mb-1">Fecha</label>
							<input type="date" v-model="fecha" class="w-full px-3 py-2 rounded-md border border-slate-300 text-black" />
						</div>

						<div>
							<label class="block text-sm text-black mb-1">Hora aproximada</label>
							<input type="time" v-model="hora" class="w-full px-3 py-2 rounded-md border border-slate-300 text-black" />
						</div>
					</div>

					<!-- Contactos autorizados (hasta 3) -->
					<div class="mt-6">
						<label class="block text-sm text-black mb-1">Nombre de quien autoriza</label>
								<select v-model="contactoId" @change="onContactoChange" class="w-full px-3 py-2 rounded-md border border-slate-300 text-black">
							<option :value="null" disabled>Selecciona un contacto autorizado</option>
							<option v-for="c in contactos" :key="c.id" :value="c.id">{{ c.nombre }} ({{ c.parentesco }})</option>
						</select>
							<p v-if="alumnoId && !contactos.length" class="text-xs text-slate-500 mt-1">No hay contactos autorizados para este alumno.</p>

							<div class="mt-3 grid grid-cols-2 gap-3">
								<input v-model="nombreRecoge" type="text" placeholder="Nombre de quien recogerá" class="px-3 py-2 rounded-md border border-slate-300 text-black" />
								<input v-model="parentescoRecoge" type="text" placeholder="Parentesco" class="px-3 py-2 rounded-md border border-slate-300 text-black" />
							</div>
					</div>

					<!-- Motivo -->
					<div class="mt-6">
						<label class="block text-sm text-black mb-1">Motivo</label>
						<input v-model="motivo" type="text" class="w-full px-3 py-2 rounded-md border border-slate-300 text-black placeholder-black" placeholder="Describe el motivo" />

						<!-- Nota: el nombre del tutor no se pide aquí; se usa el usuario autenticado como quien autoriza -->
					</div>

					<!-- Firma (canvas) - Firma del tutor -->
					<div class="mt-6">
						<label class="block text-sm text-black mb-2">Firma del tutor (opcional)</label>
						<div ref="canvasWrap" @click.stop.prevent="openModal('tutor')" class="border-2 border-dashed border-slate-300 rounded-lg p-3 select-none cursor-pointer max-w-md" :class="canvasFocused ? 'ring-2 ring-indigo-500' : ''">
							<canvas ref="canvasRef" class="bg-white rounded w-full outline-none touch-none" style="height: 80px; pointer-events: none;" tabindex="0"
								@focus="onCanvasFocus" @blur="onCanvasBlur"></canvas>
							<div class="mt-2 flex items-center gap-2">
								<button type="button" @click.prevent.stop="limpiarFirma" class="px-3 py-1.5 rounded bg-rose-600 text-white hover:bg-rose-700">Limpiar</button>
								<span class="text-xs text-slate-500">Haz clic en el recuadro para abrir firma en tamaño completo.</span>
							</div>
						</div>
					</div>

					<!-- Nota: La firma de quien recoge se realiza en la vista de autorización (preview) -->

					<div class="mt-6 flex justify-end">
						<button type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Generar petición</button>
					</div>
				</form>

					<!-- Modal para firma en grande -->
					<div v-if="modalVisible" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
						<div class="bg-white rounded-lg shadow-lg p-4 max-w-[950px]">
							<div class="flex justify-between items-center mb-2">
								<h3 class="text-lg font-semibold">Firma en tamaño completo</h3>
								<button @click="closeModal" class="text-gray-600 hover:text-gray-800">✕</button>
							</div>
							<canvas ref="modalCanvasRef" class="bg-white border" style="width:900px; height:400px; display:block;"></canvas>
							<div class="mt-3 flex justify-end gap-2">
								<button @click.prevent="limpiarModal" type="button" class="px-3 py-1 rounded bg-rose-600 text-white">Limpiar</button>
								<button @click.prevent="aceptarModal" type="button" class="px-3 py-1 rounded bg-green-600 text-white">Aceptar</button>
								<button @click.prevent="closeModal" type="button" class="px-3 py-1 rounded bg-gray-200">Cancelar</button>
							</div>
						</div>
					</div>

			</div>
		</div>
	</AdminLayout>
</template>

<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { route } from 'ziggy-js';
import AdminLayout from '../../../layouts/AdminLayout.vue';

const props = defineProps<{ hoy: string }>();

const alumnoQuery = ref('');
const sugerencias = ref<any[]>([]);
const alumnoId = ref<number|null>(null);
const contactos = ref<any[]>([]);
const contactoId = ref<number|null>(null);
const nombreRecoge = ref('');
const parentescoRecoge = ref('');
const motivo = ref('');
const fecha = ref(props.hoy ?? '');
const hora = ref('');

let searchDebounce: any;
const buscarAlumnos = () => {
	clearTimeout(searchDebounce);
	searchDebounce = setTimeout(async () => {
		if (!alumnoQuery.value) { sugerencias.value = []; return; }
		const res = await fetch(route('admin.docs.buscar_alumno', { q: alumnoQuery.value }));
		sugerencias.value = await res.json();
	}, 250);
};

const seleccionarAlumno = async (a: any) => {
	alumnoId.value = a.id;
	alumnoQuery.value = a.nombre;
	sugerencias.value = [];
	const res = await fetch(route('admin.docs.contactos_alumno', { alumno: a.id }));
	contactos.value = await res.json();
	if (!contactos.value.length) contactoId.value = null;
};

const onContactoChange = () => {
	const c = contactos.value.find((x: any) => x.id === contactoId.value);
	if (c) {
		nombreRecoge.value = c.nombre || '';
		parentescoRecoge.value = c.parentesco || '';
	}
};

// Firma en canvas con pointer events (tutor)
const canvasRef = ref<HTMLCanvasElement|null>(null);
const canvasWrap = ref<HTMLDivElement|null>(null);
const canvasFocused = ref(false);
let ctx: CanvasRenderingContext2D|null = null;
let dpr = 1;
let drawing = false;
const start = (x: number, y: number) => {
	if (!ctx) return;
	ctx.beginPath();
	ctx.moveTo(x, y);
	drawing = true;
};
const draw = (x: number, y: number) => {
	if (!ctx || !drawing) return;
	ctx.lineTo(x, y);
	ctx.strokeStyle = '#111';
	ctx.lineWidth = Math.max(2, 2 * dpr);
	ctx.stroke();
};
const end = () => { drawing = false; };


const modalVisible = ref(false);
const modalTarget = ref<'tutor'|null>(null);
const modalCanvasRef = ref<HTMLCanvasElement|null>(null);
let modalCtx: CanvasRenderingContext2D|null = null;
let modalDpr = 1;
let modalDrawing = false;

const openModal = (target: 'tutor') => {
	modalTarget.value = target;
	modalVisible.value = true;

	setTimeout(() => setupModalCanvas(), 50);
};

const setupModalCanvas = () => {
	const c = modalCanvasRef.value!;
	if (!c) return;
	// Canvas exacto sin escalar: área de dibujo = área visible (sin DPR para evitar transformación)
	const rect = c.getBoundingClientRect();
	const targetWidth = Math.min(rect.width, 900); // máx 900px de ancho
	const targetHeight = Math.min(rect.height, 400); // máx 400px de alto
	
	// Configurar canvas SIN multiplicar por DPR para que 1px canvas = 1px pantalla
	c.width = targetWidth;
	c.height = targetHeight;
	c.style.width = targetWidth + 'px';
	c.style.height = targetHeight + 'px';
	
	modalCtx = c.getContext('2d');
	// NO usar setTransform - dejar identidad para dibujo 1:1
	modalCtx!.fillStyle = '#fff';
	modalCtx!.fillRect(0, 0, c.width, c.height);

	const src = canvasRef.value;
	if (src) {
		try {
			const img = new Image();
			img.onload = () => {
				// dibujar centrado y escalado
				const scale = Math.min(targetWidth / img.width, targetHeight / img.height, 1);
				const w = img.width * scale;
				const h = img.height * scale;
				const x = (targetWidth - w) / 2;
				const y = (targetHeight - h) / 2;
				modalCtx!.drawImage(img, x, y, w, h);
			};
			img.src = src.toDataURL('image/png');
		} catch (e) {}
	}

	const pos = (e: PointerEvent) => {
		const r = c.getBoundingClientRect();
		// Coordenadas exactas sin multiplicar por DPR
		return { x: (e.clientX - r.left), y: (e.clientY - r.top) };
	};
	
	const down = (e: PointerEvent) => { 
		e.preventDefault(); 
		e.stopPropagation();
		// Solo iniciar dibujo si hay presión real (tablet/stylus) o es mouse/touch con botón primario
		if (e.pressure === 0 && e.pointerType !== 'mouse') return;
		if (e.pointerType === 'mouse' && e.button !== 0) return;
		
		c.focus(); 
		modalDrawing = true; 
		const p = pos(e); 
		modalCtx!.beginPath(); 
		modalCtx!.moveTo(p.x, p.y); 
		c.setPointerCapture(e.pointerId);
	};
	
	const move = (e: PointerEvent) => { 
		if (!modalDrawing) return; 
		// Ignorar movimientos sin presión (hover de tablet)
		if (e.pressure === 0 && e.pointerType === 'pen') return;
		
		e.preventDefault(); 
		e.stopPropagation();
		const p = pos(e); 
		modalCtx!.lineTo(p.x, p.y); 
		modalCtx!.strokeStyle = '#111'; 
		// Usar presión de tablet si está disponible (0.0 a 1.0)
		const pressure = e.pressure > 0 ? e.pressure : 0.5;
		modalCtx!.lineWidth = Math.max(1, 2 * pressure * 2);
		modalCtx!.stroke(); 
	};
	
	const up = (e: PointerEvent) => { 
		e.preventDefault(); 
		e.stopPropagation();
		modalDrawing = false; 
		try { c.releasePointerCapture(e.pointerId); } catch {}
	};
	
	const cancel = (e: PointerEvent) => {
		modalDrawing = false;
		try { c.releasePointerCapture(e.pointerId); } catch {}
	};
	
	c.addEventListener('pointerdown', down, { passive: false } as any);
	c.addEventListener('pointermove', move, { passive: false } as any);
	c.addEventListener('pointerup', up, { passive: false } as any);
	c.addEventListener('pointercancel', cancel, { passive: false } as any);
	c.addEventListener('pointerleave', cancel, { passive: false } as any);
	(c as any)._modalHandlers = { down, move, up, cancel };
};

const closeModal = () => {
	const c = modalCanvasRef.value!;
	if (c) {
		const h = (c as any)._modalHandlers || {};
		c.removeEventListener('pointerdown', h.down);
		c.removeEventListener('pointermove', h.move);
		c.removeEventListener('pointerup', h.up);
		c.removeEventListener('pointercancel', h.cancel);
		c.removeEventListener('pointerleave', h.cancel);
	}
	modalVisible.value = false;
	modalTarget.value = null;
	modalCtx = null;
	modalDrawing = false;
};

const aceptarModal = () => {
	const c = modalCanvasRef.value!;
	if (!c) { closeModal(); return; }
	const data = c.toDataURL('image/png');
	// Draw into small canvas manteniendo proporciones
	const small = canvasRef.value;
	const smallCtx = ctx;
	if (small && smallCtx) {
		const img = new Image();
		img.onload = () => {
			smallCtx.clearRect(0,0, small.width, small.height);
			smallCtx.fillStyle = '#fff';
			smallCtx.fillRect(0,0, small.width, small.height);
			
			// Calcular escala para mantener proporciones (fit contenido sin aplastar)
			const scale = Math.min(small.width / img.width, small.height / img.height);
			const w = img.width * scale;
			const h = img.height * scale;
			// Centrar en el canvas pequeño
			const x = (small.width - w) / 2;
			const y = (small.height - h) / 2;
			
			smallCtx.drawImage(img, x, y, w, h);
		};
		img.src = data;
	}
	closeModal();
};

const limpiarModal = () => {
	const c = modalCanvasRef.value;
	if (!c || !modalCtx) return;
	modalCtx.clearRect(0,0,c.width,c.height);
	modalCtx.fillStyle = '#fff';
	modalCtx.fillRect(0,0,c.width,c.height);
};


onMounted(() => {
	const c = canvasRef.value!;
	const setup = () => {
		const rect = c.getBoundingClientRect();
		dpr = window.devicePixelRatio || 1;
		c.width = Math.max(1, Math.round(rect.width * dpr));
		c.height = Math.max(1, Math.round(rect.height * dpr));
		if (!ctx) ctx = c.getContext('2d');
		// fondo blanco
		ctx!.setTransform(1, 0, 0, 1, 0, 0);
		ctx!.fillStyle = '#fff';
		ctx!.fillRect(0, 0, c.width, c.height);
	};
	setup();
	const pos = (e: PointerEvent) => {
		const rect = c.getBoundingClientRect();
		return { x: (e.clientX - rect.left) * dpr, y: (e.clientY - rect.top) * dpr };
	};
	const down = (e: PointerEvent) => { e.preventDefault(); c.focus(); canvasFocused.value = true; c.setPointerCapture(e.pointerId); const p = pos(e); start(p.x, p.y); };
	const move = (e: PointerEvent) => { e.preventDefault(); const p = pos(e); draw(p.x, p.y); };
	const up = (e: PointerEvent) => { e.preventDefault(); c.releasePointerCapture(e.pointerId); end(); };
	c.addEventListener('pointerdown', down, { passive: false } as any);
	c.addEventListener('pointermove', move, { passive: false } as any);
	c.addEventListener('pointerup', up, { passive: false } as any);
	c.addEventListener('pointerleave', end as any, { passive: false } as any);
	(c as any)._handlers = { down, move, up };


	const handleDocPointerDown = (e: Event) => {
		const wrap = canvasWrap.value;
		if (wrap && !wrap.contains(e.target as Node)) {
			canvasFocused.value = false;
			c.blur();
		}
	};
	document.addEventListener('pointerdown', handleDocPointerDown);
	(c as any)._outside = handleDocPointerDown;

	const onResize = () => { setup(); };
	window.addEventListener('resize', onResize);
	(c as any)._resize = onResize;
});

onBeforeUnmount(() => {
	const c = canvasRef.value!;
	const h = (c as any)._handlers || {};
	c.removeEventListener('pointerdown', h.down);
	c.removeEventListener('pointermove', h.move);
	c.removeEventListener('pointerup', h.up);
	const outside = (c as any)._outside;
	if (outside) document.removeEventListener('pointerdown', outside);
	const resize = (c as any)._resize;
	if (resize) window.removeEventListener('resize', resize);

	const cm = modalCanvasRef.value;
	if (cm) {
		const mh = (cm as any)._modalHandlers || {};
		cm.removeEventListener('pointerdown', mh.down);
		cm.removeEventListener('pointermove', mh.move);
		cm.removeEventListener('pointerup', mh.up);
		cm.removeEventListener('pointercancel', mh.cancel);
		cm.removeEventListener('pointerleave', mh.cancel);
	}
});

const limpiarFirma = () => {
	const c = canvasRef.value!;
	ctx!.clearRect(0, 0, c.width, c.height);
	ctx!.fillStyle = '#fff';
	ctx!.fillRect(0, 0, c.width, c.height);
	// also clear modal if open and target is tutor
	if (modalVisible.value && modalTarget.value === 'tutor') limpiarModal();
};

const onCanvasFocus = () => { canvasFocused.value = true; };
const onCanvasBlur = () => { canvasFocused.value = false; };
const focusCanvas = () => { canvasRef.value?.focus(); };

const submit = async () => {
	try {
		const pendingWin = window.open('', '_blank');
		if (!alumnoId.value) { alert('Selecciona un alumno'); return; }

		if (!nombreRecoge.value.trim()) { alert('Ingresa el nombre de quien recoge'); return; }
		if (!parentescoRecoge.value.trim()) { alert('Ingresa el parentesco de quien recoge'); return; }
		if (!motivo.value.trim()) { alert('Escribe el motivo'); return; }
		if (!fecha.value) { alert('Selecciona la fecha'); return; }
		if (!hora.value) { alert('Selecciona la hora aproximada'); return; }
	const c = canvasRef.value!;
	const dataUrl = c.toDataURL('image/png');
		const res = await fetch(route('admin.docs.salidas_store'), {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'Accept': 'application/json',
				'X-Requested-With': 'XMLHttpRequest',
				'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') as HTMLMetaElement)?.content ?? ''
			},
			credentials: 'same-origin',
			body: JSON.stringify({ alumno_id: alumnoId.value, contacto_id: contactoId.value, motivo: motivo.value, firma: dataUrl, fecha: fecha.value, hora: hora.value, recoge_nombre: nombreRecoge.value, recoge_parentesco: parentescoRecoge.value })
		});

		if (!res.ok) {
			if (res.status === 419) {
				alert('La sesión expiró. Por favor recarga la página e intenta de nuevo.');
				try { pendingWin?.close(); } catch {}
				return;
			}
			const text = await res.text();
			console.error('Error al generar documento:', res.status, text);
			alert('No se pudo generar el documento. Revisa los datos o inténtalo de nuevo.');
			try { pendingWin?.close(); } catch {}
			return;
		}

		const json = await res.json().catch(() => null);
		if (json?.ok) {
			// El endpoint ahora devuelve preview_url para firmar quien recoge
			if (json.preview_url) {
				if (pendingWin) pendingWin.location.href = json.preview_url; else window.open(json.preview_url, '_blank');
			} else if (json.pdf_view_url) {
				if (pendingWin) pendingWin.location.href = json.pdf_view_url; else window.open(json.pdf_view_url, '_blank');
			} else if (json.pdf_url) {
				if (pendingWin) pendingWin.location.href = json.pdf_url; else window.open(json.pdf_url, '_blank');
			} else {
				alert('Salida creada, pero no se recibió la URL de previsualización o PDF.');
				try { pendingWin?.close(); } catch {}
			}
		} else {
			alert('No se pudo crear la salida.');
			try { pendingWin?.close(); } catch {}
		}
	} catch (e) {
		console.error(e);
		alert('Ocurrió un error inesperado al generar el documento.');
	}
};
</script>


