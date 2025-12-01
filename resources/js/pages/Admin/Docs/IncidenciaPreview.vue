<template>
  <AdminLayout>
    <Head title="Autorizar Incidencia" />
    <div class="max-w-4xl mx-auto space-y-4">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-800">Autorizar Incidencia Disciplinaria</h1>
        <Link :href="route('admin.docs.incidencias_index')" class="px-4 py-2 rounded-md bg-slate-600 text-white hover:bg-slate-700">Regresar</Link>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-6">
        <!-- Información del alumno -->
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <p class="text-sm text-slate-500">Alumno</p>
            <p class="text-lg font-semibold text-slate-900">{{ incidencia.alumno.nombre }}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500">Matrícula</p>
            <p class="text-lg font-semibold text-slate-900">{{ incidencia.alumno.matricula }}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500">Grado</p>
            <p class="text-lg font-semibold text-slate-900">{{ incidencia.grado }}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500">Grupo</p>
            <p class="text-lg font-semibold text-slate-900">{{ incidencia.grupo }}</p>
          </div>
        </div>

        <hr class="border-slate-200">

        <!-- Detalles de la incidencia -->
        <div>
          <p class="text-sm text-slate-500">Fecha de la incidencia</p>
          <p class="text-lg font-semibold text-slate-900">{{ incidencia.fecha }}</p>
        </div>

        <div>
          <p class="text-sm text-slate-500">Área donde ocurrió</p>
          <p class="text-lg font-semibold text-slate-900">{{ incidencia.area }}</p>
        </div>

        <div>
          <p class="text-sm text-slate-500">Descripción de la incidencia</p>
          <div class="mt-2 p-4 bg-slate-50 rounded-md border border-slate-200">
            <p class="text-slate-900 whitespace-pre-wrap">{{ incidencia.descripcion }}</p>
          </div>
        </div>

        <div>
          <p class="text-sm text-slate-500">Medidas que se tomarán</p>
          <div class="mt-2 p-4 bg-slate-50 rounded-md border border-slate-200">
            <p class="text-slate-900 whitespace-pre-wrap">{{ incidencia.medidas }}</p>
          </div>
        </div>

        <div>
          <p class="text-sm text-slate-500">Registrado por</p>
          <p class="text-lg font-semibold text-slate-900">{{ incidencia.registrado_por }}</p>
        </div>

        <!-- Firma del docente -->
        <div>
          <p class="text-sm text-slate-500 mb-2">Firma del docente</p>
          <div class="border border-slate-300 rounded-md p-2 bg-white inline-block">
            <img :src="incidencia.firma_docente" alt="Firma del docente" class="max-h-32">
          </div>
        </div>

        <hr class="border-slate-200">

        <!-- Selector de contacto -->
        <div>
          <label class="block text-sm font-semibold text-slate-800 mb-2">Selecciona quién firma *</label>
          <select v-model="contactoSeleccionado" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:outline-none text-slate-800">
            <option value="">-- Selecciona un contacto --</option>
            <option v-for="contacto in contactos" :key="contacto.id" :value="contacto.nombre">
              {{ contacto.nombre }} ({{ contacto.parentesco }})
            </option>
          </select>
        </div>

        <!-- Firma del tutor -->
        <div>
          <p class="text-sm text-slate-500 mb-2">Firma del tutor/padre/madre *</p>
          <div @click.stop.prevent="abrirModal" class="border-2 border-dashed border-slate-300 rounded-lg p-3 select-none cursor-pointer">
            <canvas ref="canvasRef" class="bg-white rounded w-full outline-none touch-none" style="height: 80px; pointer-events: none;"></canvas>
            <div class="mt-2 flex items-center gap-2">
              <button type="button" @click.prevent.stop="limpiarFirma" class="px-3 py-1.5 rounded bg-slate-500 text-white hover:bg-slate-600">Limpiar</button>
              <span class="text-xs text-slate-500">Haz clic en el recuadro para abrir firma en tamaño completo.</span>
            </div>
          </div>
        </div>

        <!-- Botón de finalizar -->
        <div class="flex justify-end">
          <button @click="finalizar" :disabled="enviando" class="px-6 py-3 rounded-md bg-emerald-600 text-white hover:bg-emerald-700 disabled:bg-slate-300 disabled:cursor-not-allowed font-semibold">
            {{ enviando ? 'Generando PDF...' : 'Finalizar y generar PDF' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Modal firma pantalla completa -->
    <div v-if="modalAbierto" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
      <div class="bg-white rounded-lg shadow-lg p-4 max-w-[950px]">
        <div class="flex justify-between items-center mb-2">
          <h3 class="text-lg font-semibold">Firma en tamaño completo</h3>
          <button @click="cerrarModal" class="text-gray-600 hover:text-gray-800">✕</button>
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
import { ref, onMounted } from 'vue';
import { route } from 'ziggy-js';
import AdminLayout from '../../../layouts/AdminLayout.vue';

const props = defineProps<{
  incidencia: {
    id: number;
    fecha: string;
    descripcion: string;
    medidas: string;
    area: string;
    nombre_docente_reporta: string;
    registrado_por: string;
    firma_docente: string;
    alumno: { nombre: string; matricula: string };
    grado: string;
    grupo: string;
  };
  contactos: Array<{
    id: number;
    nombre: string;
    parentesco: string;
  }>;
}>();

const canvasRef = ref<HTMLCanvasElement>();
const modalCanvasRef = ref<HTMLCanvasElement>();
const modalAbierto = ref(false);
const enviando = ref(false);
const contactoSeleccionado = ref('');

let dibujando = false;
let ctx: CanvasRenderingContext2D | null = null;
let modalCtx: CanvasRenderingContext2D | null = null;

onMounted(() => {
  setupCanvas();
});

const setupCanvas = () => {
  const c = canvasRef.value;
  if (!c) return;
  const rect = c.getBoundingClientRect();
  const dpr = window.devicePixelRatio || 1;
  c.width = Math.max(1, Math.round(rect.width * dpr));
  c.height = Math.max(1, Math.round(rect.height * dpr));
  ctx = c.getContext('2d');
  if (ctx) {
    ctx.setTransform(1, 0, 0, 1, 0, 0);
    ctx.fillStyle = '#fff';
    ctx.fillRect(0, 0, c.width, c.height);
  }
};

const limpiarFirma = () => {
  const c = canvasRef.value;
  if (!c || !ctx) return;
  ctx.clearRect(0, 0, c.width, c.height);
  ctx.fillStyle = '#fff';
  ctx.fillRect(0, 0, c.width, c.height);
  if (modalAbierto.value) limpiarModal();
};

const abrirModal = () => {
  modalAbierto.value = true;
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
  if (!modalCtx) return;
  modalCtx.fillStyle = '#fff';
  modalCtx.fillRect(0, 0, c.width, c.height);

  // Copiar firma del canvas pequeño si existe
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
    dibujandoModal = true;
    const p = pos(e); 
    modalCtx!.beginPath(); 
    modalCtx!.moveTo(p.x, p.y); 
    c.setPointerCapture(e.pointerId);
  };
  
  const move = (e: PointerEvent) => { 
    if (!dibujandoModal) return; 
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
    dibujandoModal = false;
    try { c.releasePointerCapture(e.pointerId); } catch {}
  };
  
  const cancel = (e: PointerEvent) => {
    dibujandoModal = false;
    try { c.releasePointerCapture(e.pointerId); } catch {}
  };
  
  c.addEventListener('pointerdown', down, { passive: false } as any);
  c.addEventListener('pointermove', move, { passive: false } as any);
  c.addEventListener('pointerup', up, { passive: false } as any);
  c.addEventListener('pointercancel', cancel, { passive: false } as any);
  c.addEventListener('pointerleave', cancel, { passive: false } as any);
  (c as any)._modalHandlers = { down, move, up, cancel };
};

let dibujandoModal = false;

const cerrarModal = () => {
  const c = modalCanvasRef.value;
  if (c) {
    const h = (c as any)._modalHandlers || {};
    c.removeEventListener('pointerdown', h.down);
    c.removeEventListener('pointermove', h.move);
    c.removeEventListener('pointerup', h.up);
    c.removeEventListener('pointercancel', h.cancel);
    c.removeEventListener('pointerleave', h.cancel);
  }
  modalAbierto.value = false;
  modalCtx = null;
  dibujandoModal = false;
};

const limpiarModal = () => {
  const c = modalCanvasRef.value;
  if (!c || !modalCtx) return;
  modalCtx.clearRect(0, 0, c.width, c.height);
  modalCtx.fillStyle = '#fff';
  modalCtx.fillRect(0, 0, c.width, c.height);
};

const aceptarModal = () => {
  const c = modalCanvasRef.value;
  if (!c) { cerrarModal(); return; }
  const data = c.toDataURL('image/png');
  const small = canvasRef.value;
  const smallCtx = ctx;
  if (small && smallCtx) {
    const img = new Image();
    img.onload = () => {
      smallCtx.clearRect(0, 0, small.width, small.height);
      smallCtx.fillStyle = '#fff';
      smallCtx.fillRect(0, 0, small.width, small.height);
      const scale = Math.min(small.width / img.width, small.height / img.height);
      const w = img.width * scale;
      const h = img.height * scale;
      const x = (small.width - w) / 2;
      const y = (small.height - h) / 2;
      smallCtx.drawImage(img, x, y, w, h);
    };
    img.src = data;
  }
  cerrarModal();
};

const finalizar = async () => {
  if (!contactoSeleccionado.value) {
    alert('Debes seleccionar quién firma');
    return;
  }

  if (!canvasRef.value) {
    alert('Canvas no disponible');
    return;
  }

  const firmaTutor = canvasRef.value.toDataURL('image/png');
  if (!firmaTutor || firmaTutor === 'data:,') {
    alert('Debes firmar antes de finalizar');
    return;
  }

  enviando.value = true;

  try {
    router.post(route('admin.docs.incidencias_finalize', { incidencia: props.incidencia.id }), {
      firma_tutor: firmaTutor,
      nombre_tutor: contactoSeleccionado.value
    }, {
      onSuccess: (page: any) => {
        const pdfUrl = page.props?.flash?.pdf_url;
        if (pdfUrl) {
          window.open(pdfUrl, '_blank');
        }
        router.visit(route('admin.docs.incidencias_index'));
      },
      onError: (errors: any) => {
        console.error(errors);
        alert('Error al generar el PDF');
        enviando.value = false;
      }
    });
  } catch (error) {
    console.error(error);
    alert('Error al finalizar');
    enviando.value = false;
  }
};
</script>
