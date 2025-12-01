<template>
	<AdminLayout>
		<Head title="Reporte de Incidencia" />
		<div class="max-w-5xl mx-auto space-y-4">
			<div class="flex items-center justify-between">
				<h1 class="text-2xl font-bold text-slate-800">Reporte de Incidencia Disciplinaria</h1>
				<div class="flex items-center gap-2">
					<Link :href="route('admin.docs.incidencias_index')" class="px-4 py-2 rounded-md bg-slate-600 text-white hover:bg-slate-700">
						Ver registros
					</Link>
				</div>
			</div>

			<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
				<form @submit.prevent="submit" class="space-y-6">
					<!-- Búsqueda de alumno -->
					<div>
						<label class="block text-sm font-semibold text-slate-800 mb-2">Alumno *</label>
						<div class="relative">
							<input v-model="alumnoQuery" @input="buscarAlumnos" type="text" placeholder="Buscar por nombre, matrícula o CURP"
								class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-red-500 focus:outline-none text-slate-800" />
							<ul v-if="sugerencias.length && alumnoQuery"
								class="absolute z-10 bg-white border border-slate-200 rounded-md mt-1 w-full max-h-48 overflow-auto shadow-lg">
								<li v-for="a in sugerencias" :key="a.id" @click="seleccionarAlumno(a)"
									class="px-4 py-2 hover:bg-slate-100 cursor-pointer text-slate-800">
									{{ a.nombre }} — {{ a.matricula }}
								</li>
							</ul>
						</div>
						<p v-if="alumnoId" class="mt-2 text-sm text-emerald-600 font-medium">✓ Alumno seleccionado</p>
					</div>

					<!-- Fecha -->
					<div>
						<label class="block text-sm font-semibold text-slate-800 mb-2">Fecha de la incidencia *</label>
						<input v-model="fecha" type="date" required
							class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-red-500 focus:outline-none text-slate-800" />
					</div>

					<!-- Área donde ocurrió -->
					<div>
						<label class="block text-sm font-semibold text-slate-800 mb-2">Área donde ocurrió el suceso *</label>
						<input v-model="area" type="text" required placeholder="Ej: Patio, Salón de clases, Baños, etc."
							class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-red-500 focus:outline-none text-slate-800" />
					</div>

					<!-- Descripción -->
					<div>
						<label class="block text-sm font-semibold text-slate-800 mb-2">Descripción de la incidencia *</label>
						<textarea v-model="descripcion" rows="6" required placeholder="Describe detalladamente el suceso registrado..."
							class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-red-500 focus:outline-none text-slate-800"></textarea>
						<p class="mt-1 text-xs text-slate-500">Asumo plena responsabilidad de que el contenido de este reporte es un reflejo objetivo de los hechos ocurridos.</p>
					</div>

					<!-- Medidas tomadas -->
					<div>
						<label class="block text-sm font-semibold text-slate-800 mb-2">Medidas que se tomarán *</label>
						<textarea v-model="medidas" rows="4" required placeholder="Describe las medidas tomadas de acuerdo con el reglamento..."
							class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-red-500 focus:outline-none text-slate-800"></textarea>
					</div>

					<!-- Nombre del docente que reporta -->
					<div>
						<label class="block text-sm font-semibold text-slate-800 mb-2">Nombre completo del docente que reporta *</label>
						<input v-model="nombreDocente" type="text" required placeholder="Ej: Prof. Juan Pérez López"
							class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-red-500 focus:outline-none text-slate-800" />
					</div>

					<!-- Firma del docente -->
					<div>
						<label class="block text-sm font-semibold text-slate-800 mb-2">Firma del docente que reporta *</label>
						<div @click.stop.prevent="openModal('docente')" class="border-2 border-dashed border-slate-300 rounded-lg p-3 select-none cursor-pointer">
							<canvas ref="canvasRef" class="bg-white rounded w-full outline-none touch-none" style="height: 80px; pointer-events: none;"></canvas>
							<div class="mt-2 flex items-center gap-2">
								<button type="button" @click.prevent.stop="limpiarFirma" class="px-3 py-1.5 rounded bg-rose-600 text-white hover:bg-rose-700">Limpiar</button>
								<span class="text-xs text-slate-500">Haz clic en el recuadro para abrir firma en tamaño completo.</span>
							</div>
						</div>
					</div>

					<!-- Botón submit -->
					<div class="flex justify-end gap-3 pt-4 border-t">
						<Link :href="route('admin.docs.incidencias_index')" class="px-6 py-2.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-medium">
							Cancelar
						</Link>
						<button type="submit" class="px-6 py-2.5 rounded-lg bg-red-600 text-white hover:bg-red-700 font-medium">
							Generar reporte
						</button>
					</div>
				</form>
			</div>
		</div>

		<!-- Modal firma pantalla completa -->
		<div v-if="modalVisible" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
			<div class="bg-white rounded-lg shadow-lg p-4 max-w-[950px]">
				<div class="flex justify-between items-center mb-2">
					<h3 class="text-lg font-semibold">Firma en tamaño completo</h3>
					<button @click="closeModal" class="text-gray-600 hover:text-gray-800">✕</button>
				</div>
				<canvas ref="modalCanvasRef" class="bg-white border" style="width:900px; height:400px; display:block;"></canvas>
				<div class="mt-3 flex justify-end gap-2">
					<button @click.prevent="limpiarModal" type="button" class="px-3 py-1 rounded bg-rose-600 text-white">Limpiar</button>
					<button @click.prevent="aceptarModal" type="button" class="px-3 py-1 rounded bg-emerald-600 text-white">Aceptar</button>
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
const descripcion = ref('');
const medidas = ref('');
const area = ref('');
const nombreDocente = ref('');
const fecha = ref(props.hoy ?? '');

let searchDebounce: any;
const buscarAlumnos = () => {
	clearTimeout(searchDebounce);
	searchDebounce = setTimeout(async () => {
		if (!alumnoQuery.value) { sugerencias.value = []; return; }
		const res = await fetch(route('admin.docs.buscar_alumno', { q: alumnoQuery.value }));
		sugerencias.value = await res.json();
	}, 250);
};

const seleccionarAlumno = (a: any) => {
	alumnoId.value = a.id;
	alumnoQuery.value = a.nombre;
	sugerencias.value = [];
};

// Firma en canvas
const canvasRef = ref<HTMLCanvasElement|null>(null);
let ctx: CanvasRenderingContext2D|null = null;
let dpr = 1;

// Modal firma
const modalVisible = ref(false);
const modalTarget = ref<'docente'|null>(null);
const modalCanvasRef = ref<HTMLCanvasElement|null>(null);
let modalCtx: CanvasRenderingContext2D|null = null;
let modalDrawing = false;

const openModal = (target: 'docente') => {
	modalTarget.value = target;
	modalVisible.value = true;
	setTimeout(() => setupModalCanvas(), 50);
};

const setupModalCanvas = () => {
	const c = modalCanvasRef.value;
	if (!c) return;
	const rect = c.getBoundingClientRect();
	const targetWidth = Math.min(rect.width, 400);
	const targetHeight = Math.min(rect.height, 400);
	
	c.width = targetWidth;
	c.height = targetHeight;
	c.style.width = targetWidth + 'px';
	c.style.height = targetHeight + 'px';
	
	modalCtx = c.getContext('2d');
	modalCtx!.fillStyle = '#fff';
	modalCtx!.fillRect(0, 0, c.width, c.height);

	const src = canvasRef.value;
	if (src) {
		try {
			const img = new Image();
			img.onload = () => {
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
		return { x: (e.clientX - r.left), y: (e.clientY - r.top) };
	};
	
	const down = (e: PointerEvent) => { 
		e.preventDefault(); 
		e.stopPropagation();
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
		if (e.pressure === 0 && e.pointerType === 'pen') return;
		e.preventDefault(); 
		e.stopPropagation();
		const p = pos(e); 
		modalCtx!.lineTo(p.x, p.y); 
		modalCtx!.strokeStyle = '#111'; 
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
	const small = canvasRef.value;
	const smallCtx = ctx;
	if (small && smallCtx) {
		const img = new Image();
		img.onload = () => {
			smallCtx.clearRect(0,0, small.width, small.height);
			smallCtx.fillStyle = '#fff';
			smallCtx.fillRect(0,0, small.width, small.height);
			const scale = Math.min(small.width / img.width, small.height / img.height);
			const w = img.width * scale;
			const h = img.height * scale;
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
		ctx!.setTransform(1, 0, 0, 1, 0, 0);
		ctx!.fillStyle = '#fff';
		ctx!.fillRect(0, 0, c.width, c.height);
	};
	setup();

	const onResize = () => { setup(); };
	window.addEventListener('resize', onResize);
	(c as any)._resize = onResize;
});

onBeforeUnmount(() => {
	const c = canvasRef.value!;
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
	if (modalVisible.value && modalTarget.value === 'docente') limpiarModal();
};

const submit = async () => {
	try {
		const pendingWin = window.open('', '_blank');
		if (!alumnoId.value) { alert('Selecciona un alumno'); return; }
		if (!fecha.value) { alert('Selecciona la fecha'); return; }
		if (!area.value.trim()) { alert('Escribe el área donde ocurrió la incidencia'); return; }
		if (!descripcion.value.trim()) { alert('Escribe la descripción de la incidencia'); return; }
		if (!medidas.value.trim()) { alert('Escribe las medidas que se tomarán'); return; }
		if (!nombreDocente.value.trim()) { alert('Escribe el nombre del docente que reporta'); return; }
		
		const c = canvasRef.value!;
		const dataUrl = c.toDataURL('image/png');
		
		const res = await fetch(route('admin.docs.incidencias_store'), {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'Accept': 'application/json',
				'X-Requested-With': 'XMLHttpRequest',
				'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') as HTMLMetaElement)?.content ?? ''
			},
			credentials: 'same-origin',
			body: JSON.stringify({ 
				alumno_id: alumnoId.value, 
				descripcion: descripcion.value,
				medidas: medidas.value,
				area: area.value,
				nombre_docente: nombreDocente.value,
				fecha: fecha.value, 
				firma: dataUrl 
			})
		});

		if (!res.ok) {
			if (res.status === 419) {
				alert('La sesión expiró. Por favor recarga la página e intenta de nuevo.');
				try { pendingWin?.close(); } catch {}
				return;
			}
			const text = await res.text();
			console.error('Error al generar reporte:', res.status, text);
			alert('No se pudo generar el reporte. Revisa los datos o inténtalo de nuevo.');
			try { pendingWin?.close(); } catch {}
			return;
		}

		const json = await res.json().catch(() => null);
		if (json?.ok) {
			if (json.preview_url) {
				if (pendingWin) pendingWin.location.href = json.preview_url; else window.open(json.preview_url, '_blank');
			} else if (json.pdf_url) {
				if (pendingWin) pendingWin.location.href = json.pdf_url; else window.open(json.pdf_url, '_blank');
			} else {
				alert('Incidencia creada, pero no se recibió la URL de previsualización.');
				try { pendingWin?.close(); } catch {}
			}
		} else {
			alert('No se pudo crear la incidencia.');
			try { pendingWin?.close(); } catch {}
		}
	} catch (e) {
		console.error(e);
		alert('Ocurrió un error inesperado al generar el reporte.');
	}
};
</script>
