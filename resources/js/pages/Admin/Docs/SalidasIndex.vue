<template>
  <AdminLayout>
    <Head title="Salidas anticipadas" />
    <div class="max-w-6xl mx-auto space-y-4">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-slate-800">Salidas anticipadas</h1>
        <Link :href="route('admin.docs.autorizacion_salida')" class="w-full sm:w-auto text-center px-4 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700">Nueva salida</Link>
      </div>

      <!-- Filtros -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <div class="grid md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm text-black mb-1">Alumno</label>
            <div class="relative">
              <input v-model="alumnoQuery" @input="buscarAlumnos" type="text" placeholder="Buscar por nombre, matrícula o CURP"
                class="w-full px-3 py-2 rounded-md border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-black" />
              <ul v-if="sugerencias.length && alumnoQuery"
                  class="absolute z-10 bg-white border border-slate-200 rounded-md mt-1 w-full max-h-48 overflow-auto">
                <li v-for="a in sugerencias" :key="a.id" @click="seleccionarAlumno(a)"
                    class="px-3 py-2 hover:bg-slate-100 cursor-pointer text-black">
                  {{ a.nombre }} — {{ a.matricula }}
                </li>
              </ul>
            </div>
          </div>
          <div>
            <label class="block text-sm text-black mb-1">Fecha</label>
            <input v-model="fecha" type="date" class="w-full px-3 py-2 rounded-md border border-slate-300 text-black" />
          </div>
          <div class="flex items-end gap-2">
            <button @click="aplicar" class="flex-1 px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Aplicar</button>
            <button @click="limpiar" class="flex-1 px-4 py-2 rounded-md bg-slate-600 text-white hover:bg-slate-700">Limpiar</button>
          </div>
        </div>
      </div>

      <!-- Tabla -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-slate-50">
              <tr class="text-left text-slate-900 font-semibold">
                <th class="px-4 py-3 whitespace-nowrap">Fecha</th>
                <th class="px-4 py-3 whitespace-nowrap">Hora</th>
                <th class="px-4 py-3 whitespace-nowrap">Alumno</th>
                <th class="px-4 py-3 whitespace-nowrap">Contacto</th>
                <th class="px-4 py-3 whitespace-nowrap">Motivo</th>
                <th class="px-4 py-3 text-right whitespace-nowrap">PDF</th>
                <th class="px-4 py-3 text-right whitespace-nowrap">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="s in salidas.data" :key="s.id" class="border-t border-slate-100">
                <td class="px-4 py-3 text-slate-900 whitespace-nowrap">{{ s.fecha }}</td>
                <td class="px-4 py-3 text-slate-900 whitespace-nowrap">{{ s.hora_salida }}</td>
                <td class="px-4 py-3 text-slate-900 whitespace-nowrap">{{ s.alumno }}</td>
                <td class="px-4 py-3 text-slate-900 whitespace-nowrap">{{ s.contacto }}</td>
                <td class="px-4 py-3 text-slate-900 whitespace-nowrap">{{ s.motivo }}</td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                  <a v-if="s.pdf_url" :href="s.pdf_url" target="_blank" class="px-3 py-1.5 rounded-md bg-slate-800 text-white hover:bg-slate-700">Abrir</a>
                  <span v-else class="text-slate-400">—</span>
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                  <div class="flex justify-end gap-2">
                    <button v-if="s.pdf_url" @click="borrarPDF(s.id)" 
                      class="px-3 py-1.5 rounded-md bg-orange-600 text-white hover:bg-orange-700 transition-colors text-sm">
                      Borrar PDF
                    </button>
                    <button v-else @click="autorizarSalida(s.id)" class="px-3 py-1.5 rounded-md bg-emerald-600 text-white hover:bg-emerald-700 transition-colors text-sm">Autorizar salida</button>
                    <button @click="eliminarSalida(s.id)" 
                      class="px-3 py-1.5 rounded-md bg-red-600 text-white hover:bg-red-700 transition-colors text-sm">
                      Eliminar
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!salidas.data.length">
                <td colspan="7" class="px-4 py-6 text-center text-slate-500">No hay registros.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Paginación -->
      <div class="flex justify-between items-center text-sm text-slate-500">
        <span>Página {{ salidas.current_page }} de {{ salidas.last_page }}</span>
        <div class="space-x-2">
          <button :disabled="!salidas.prev_page_url" @click="ir(salidas.prev_page_url)" class="px-3 py-1 rounded bg-slate-100 disabled:opacity-50">Anterior</button>
          <button :disabled="!salidas.next_page_url" @click="ir(salidas.next_page_url)" class="px-3 py-1 rounded bg-slate-100 disabled:opacity-50">Siguiente</button>
        </div>
      </div>
    </div>
  </AdminLayout>
  
</template>

<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';
import AdminLayout from '../../../layouts/AdminLayout.vue';

interface Paginacion<T> {
  data: T[];
  current_page: number;
  last_page: number;
  next_page_url?: string|null;
  prev_page_url?: string|null;
}

const props = defineProps<{ salidas: Paginacion<any>, filters: { alumno_id?: number|string|null, fecha?: string|null } }>();
const salidas = props.salidas;

const alumnoQuery = ref('');
const alumnoId = ref<number|null>(props.filters?.alumno_id ? Number(props.filters.alumno_id) : null);
const fecha = ref<string|undefined>(props.filters?.fecha ?? undefined);
const sugerencias = ref<any[]>([]);

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

const aplicar = () => {
  router.get(route('admin.docs.salidas_index'), { alumno_id: alumnoId.value ?? undefined, fecha: fecha.value ?? undefined }, { preserveScroll: true });
};

const limpiar = () => {
  alumnoId.value = null;
  alumnoQuery.value = '';
  fecha.value = undefined;
  router.get(route('admin.docs.salidas_index'), {}, { preserveScroll: true });
};

const ir = (url?: string|null) => { if (url) router.visit(url, { preserveScroll: true }); };

const borrarPDF = (salidaId: number) => {
  if (!confirm('¿Estás seguro de que deseas borrar este PDF? Esta acción no se puede deshacer.')) {
    return;
  }
  
  router.delete(route('admin.docs.salida_borrar_pdf', { id: salidaId }), {
    preserveScroll: true,
    onSuccess: () => {
      alert('PDF borrado exitosamente');
    },
    onError: () => {
      alert('Error al borrar el PDF');
    }
  });
};

const eliminarSalida = (salidaId: number) => {
  if (!confirm('¿Estás seguro de que deseas eliminar completamente esta salida? Se borrarán el registro, el PDF y la firma. Esta acción no se puede deshacer.')) {
    return;
  }
  
  router.delete(route('admin.docs.salida_eliminar', { id: salidaId }), {
    preserveScroll: true,
    onSuccess: () => {
      alert('Salida eliminada exitosamente');
    },
    onError: () => {
      alert('Error al eliminar la salida');
    }
  });
};

const autorizarSalida = (salidaId: number) => {
  const url = route('admin.docs.salidas_preview', { salida: salidaId });
  // Abrir en una nueva pestaña para que el personal pueda firmar
  window.open(url, '_blank');
};
</script>
