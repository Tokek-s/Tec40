<template>
  <AdminLayout :title="modoEdicion ? 'Editar Cuestionario Reinscripción' : 'Nuevo Cuestionario Reinscripción'">
    <div class="p-6 bg-gray-100 min-h-screen">
      <!-- Header con navegación -->
      <div class="mb-6">
        <Link
          :href="route('admin.inscripciones.reinscripcion')"
          class="inline-flex items-center text-black hover:text-gray-700 mb-4 font-semibold"
        >
          ← Volver a la lista
        </Link>
        <h1 class="text-3xl font-bold text-black">
          {{ modoEdicion ? 'Editar Cuestionario' : 'Nuevo Cuestionario de Reinscripción' }}
        </h1>
      </div>

      <form @submit.prevent="guardarCuestionario">
        <!-- Datos del Cuestionario -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <h2 class="text-xl font-bold text-black mb-4">🔄 Datos del Cuestionario</h2>
          
          <!-- Descripción -->
          <div class="mb-4">
            <label class="block text-black font-semibold mb-2">Descripción</label>
            <textarea
              v-model="formulario.descripcion"
              rows="3"
              class="w-full border-2 border-gray-400 rounded-lg px-4 py-2 bg-gray-50 text-black font-semibold focus:ring-2 focus:ring-red-500 focus:border-transparent"
              placeholder="Descripción del cuestionario de reinscripción..."
            ></textarea>
            <p class="text-sm text-black mt-1">Opcional. Aparecerá en el formulario público.</p>
          </div>

          <!-- Fechas -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-black font-semibold mb-2">Fecha de Inicio *</label>
              <input
                v-model="formulario.fecha_inicio"
                type="date"
                required
                class="w-full border-2 border-gray-400 rounded-lg px-4 py-2 bg-gray-50 text-black font-semibold focus:ring-2 focus:ring-red-500 focus:border-transparent"
              />
            </div>
            <div>
              <label class="block text-black font-semibold mb-2">Fecha de Fin *</label>
              <input
                v-model="formulario.fecha_fin"
                type="date"
                required
                class="w-full border-2 border-gray-400 rounded-lg px-4 py-2 bg-gray-50 text-black font-semibold focus:ring-2 focus:ring-red-500 focus:border-transparent"
              />
            </div>
          </div>

          <!-- Estados de Activación -->
          <div class="border-t pt-4">
            <h3 class="font-semibold text-black mb-3">Estado de Activación</h3>
            <div class="space-y-3">
              <label class="flex items-center gap-3 p-3 bg-purple-100 rounded-lg cursor-pointer hover:bg-purple-200 transition-colors border-2 border-purple-300">
                <input v-model="formulario.activo" type="checkbox" class="w-5 h-5 text-purple-600" />
                <div>
                  <span class="font-bold text-black">Cuestionario Activo</span>
                  <p class="text-sm text-black font-semibold">El cuestionario estará disponible para reinscripciones</p>
                </div>
              </label>
              
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <p class="text-sm text-blue-800">
                  ℹ️ <strong>Importante:</strong> Los grados habilitados son para los alumnos que <strong>pasarán</strong> a ese grado.
                  Ejemplo: Habilitar 2° permite que alumnos de 1° se reinscriban para pasar a 2°.
                </p>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-4">
                <label class="flex items-center gap-3 p-3 bg-green-100 rounded-lg cursor-pointer hover:bg-green-200 transition-colors border-2 border-green-300">
                  <input v-model="formulario.segundo_activo" type="checkbox" class="w-5 h-5 text-green-600" />
                  <div>
                    <span class="font-bold text-black text-lg">2do Grado</span>
                    <p class="text-xs text-gray-600">(de 1° → 2°)</p>
                  </div>
                </label>
                
                <label class="flex items-center gap-3 p-3 bg-purple-100 rounded-lg cursor-pointer hover:bg-purple-200 transition-colors border-2 border-purple-300">
                  <input v-model="formulario.tercero_activo" type="checkbox" class="w-5 h-5 text-purple-600" />
                  <div>
                    <span class="font-bold text-black text-lg">3er Grado</span>
                    <p class="text-xs text-gray-600">(de 2° → 3°)</p>
                  </div>
                </label>

                <label class="flex items-center gap-3 p-3 bg-blue-100 rounded-lg cursor-pointer hover:bg-blue-200 transition-colors border-2 border-blue-300">
                  <input v-model="formulario.primero_activo" type="checkbox" class="w-5 h-5 text-blue-600" />
                  <div>
                    <span class="font-bold text-black text-lg">1er Grado</span>
                    <p class="text-xs text-gray-600">(repiten)</p>
                  </div>
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Botones de Acción -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex justify-end gap-4">
            <Link
              :href="route('admin.inscripciones.reinscripcion')"
              class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition-colors"
            >
              Cancelar
            </Link>
            <button
              type="submit"
              class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors"
            >
              {{ modoEdicion ? '💾 Guardar Cambios' : '✓ Crear Cuestionario' }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AdminLayout from '../../../layouts/AdminLayout.vue';

interface CampoExtra {
  nombre: string;
  tipo: string;
}

interface Cuestionario {
  id: number;
  titulo: string;
  descripcion: string;
  fecha_inicio: string;
  fecha_fin: string;
  activo: boolean;
  primero_activo: boolean;
  segundo_activo: boolean;
  tercero_activo: boolean;
}

interface Props {
  cuestionario?: Cuestionario;
  camposExtra: CampoExtra[];
}

const props = defineProps<Props>();

const modoEdicion = ref(!!props.cuestionario);

const formulario = ref({
  descripcion: props.cuestionario?.descripcion || '',
  fecha_inicio: props.cuestionario?.fecha_inicio || '',
  fecha_fin: props.cuestionario?.fecha_fin || '',
  activo: props.cuestionario?.activo ?? true,
  primero_activo: props.cuestionario?.primero_activo ?? false,
  segundo_activo: props.cuestionario?.segundo_activo ?? true,
  tercero_activo: props.cuestionario?.tercero_activo ?? true,
});

function guardarCuestionario() {
  if (modoEdicion.value && props.cuestionario) {
    router.put(`/admin/inscripciones/reinscripcion/${props.cuestionario.id}`, formulario.value, {
      onSuccess: () => {
        router.visit(route('admin.inscripciones.reinscripcion'));
      },
      onError: (errors) => {
        if (errors.error) alert(errors.error);
      },
    });
  } else {
    router.post('/admin/inscripciones/reinscripcion', formulario.value, {
      onSuccess: () => {
        router.visit(route('admin.inscripciones.reinscripcion'));
      },
      onError: (errors) => {
        if (errors.error) alert(errors.error);
      },
    });
  }
}
</script>
