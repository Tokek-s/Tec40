<template>
  <AdminLayout title="Gestión de Inscripciones">
    <div class="p-6 bg-gray-100 min-h-screen">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Cuestionarios de Inscripción</h1>
        <Link
          :href="route('admin.inscripciones.crear')"
          class="w-full sm:w-auto text-center bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors"
        >
          + Nuevo Cuestionario
        </Link>
      </div>

      <!-- Lista de cuestionarios con estadísticas -->
      <div v-if="cuestionarios.length > 0" class="grid gap-4">
        <div
          v-for="cuestionario in cuestionarios"
          :key="cuestionario.id"
          class="bg-white rounded-lg shadow-md p-4 sm:p-6 hover:shadow-xl transition-shadow"
        >
          <div class="flex flex-col sm:flex-row justify-between items-start mb-4 gap-4">
            <div class="flex-1 w-full">
              <div class="flex flex-wrap items-center gap-3 mb-2">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-800">
                  {{ cuestionario.titulo }}
                </h3>
                <span
                  :class="cuestionario.activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                >
                  {{ cuestionario.activo ? '● Activo' : '○ Inactivo' }}
                </span>
              </div>
              <p class="text-gray-600 mb-4">{{ cuestionario.descripcion }}</p>
              
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-4">
                <div class="flex items-center gap-2">
                  <span class="font-semibold text-gray-700">📅 Inicio:</span>
                  <span class="text-gray-600">{{ formatearFecha(cuestionario.fecha_inicio) }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <span class="font-semibold text-gray-700">📅 Fin:</span>
                  <span class="text-gray-600">{{ formatearFecha(cuestionario.fecha_fin) }}</span>
                </div>
              </div>
            </div>

            <div class="flex gap-2 w-full sm:w-auto">
              <Link
                :href="route('admin.inscripciones.editar', cuestionario.id)"
                class="flex-1 sm:flex-none text-center bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded transition-colors"
              >
                ✏️ Editar
              </Link>
              <button
                @click="eliminarCuestionario(cuestionario.id)"
                class="flex-1 sm:flex-none text-center bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition-colors"
              >
                🗑️ Eliminar
              </button>
            </div>
          </div>

          <!-- Estadísticas por grado -->
          <div class="border-t pt-4">
            <h4 class="font-semibold text-gray-700 mb-3">📊 Estadísticas de Inscripción</h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <!-- Primer Grado -->
              <div
                :class="cuestionario.primero_activo ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200'"
                class="border-2 rounded-lg p-4"
              >
                <div class="flex items-center justify-between mb-2">
                  <span class="font-semibold text-gray-700">1er Grado</span>
                  <span
                    :class="cuestionario.primero_activo ? 'text-blue-600' : 'text-gray-400'"
                    class="text-sm"
                  >
                    {{ cuestionario.primero_activo ? '✓ Habilitado' : '✗ Deshabilitado' }}
                  </span>
                </div>
                <div class="text-center">
                  <div class="text-3xl font-bold text-gray-800">
                    {{ cuestionario.inscritos_primero || 0 }}
                  </div>
                  <div class="text-sm text-gray-500">alumnos inscritos</div>
                </div>
              </div>

              <!-- Segundo Grado -->
              <div
                :class="cuestionario.segundo_activo ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200'"
                class="border-2 rounded-lg p-4"
              >
                <div class="flex items-center justify-between mb-2">
                  <span class="font-semibold text-gray-700">2do Grado</span>
                  <span
                    :class="cuestionario.segundo_activo ? 'text-green-600' : 'text-gray-400'"
                    class="text-sm"
                  >
                    {{ cuestionario.segundo_activo ? '✓ Habilitado' : '✗ Deshabilitado' }}
                  </span>
                </div>
                <div class="text-center">
                  <div class="text-3xl font-bold text-gray-800">
                    {{ cuestionario.inscritos_segundo || 0 }}
                  </div>
                  <div class="text-sm text-gray-500">alumnos inscritos</div>
                </div>
              </div>

              <!-- Tercer Grado -->
              <div
                :class="cuestionario.tercero_activo ? 'bg-purple-50 border-purple-200' : 'bg-gray-50 border-gray-200'"
                class="border-2 rounded-lg p-4"
              >
                <div class="flex items-center justify-between mb-2">
                  <span class="font-semibold text-gray-700">3er Grado</span>
                  <span
                    :class="cuestionario.tercero_activo ? 'text-purple-600' : 'text-gray-400'"
                    class="text-sm"
                  >
                    {{ cuestionario.tercero_activo ? '✓ Habilitado' : '✗ Deshabilitado' }}
                  </span>
                </div>
                <div class="text-center">
                  <div class="text-3xl font-bold text-gray-800">
                    {{ cuestionario.inscritos_tercero || 0 }}
                  </div>
                  <div class="text-sm text-gray-500">alumnos inscritos</div>
                </div>
              </div>
            </div>

            <!-- Total general -->
            <div class="mt-4 bg-gradient-to-r from-red-50 to-red-100 border-2 border-red-200 rounded-lg p-4">
              <div class="flex items-center justify-between">
                <span class="font-bold text-gray-800 text-lg">📈 Total General</span>
                <span class="text-3xl font-bold text-red-600">
                  {{ (cuestionario.inscritos_primero || 0) + (cuestionario.inscritos_segundo || 0) + (cuestionario.inscritos_tercero || 0) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Estado vacío -->
      <div v-else class="bg-white rounded-lg shadow-md p-12 text-center">
        <div class="text-6xl mb-4">📋</div>
        <h3 class="text-2xl font-bold text-gray-800 mb-2">No hay cuestionarios</h3>
        <p class="text-gray-600 mb-6">Comienza creando un nuevo cuestionario de inscripción</p>
        <Link
          :href="route('admin.inscripciones.crear')"
          class="inline-block bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors"
        >
          + Crear Primer Cuestionario
        </Link>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { router, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AdminLayout from '../../../layouts/AdminLayout.vue';

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
  inscritos_primero?: number;
  inscritos_segundo?: number;
  inscritos_tercero?: number;
}

interface Props {
  cuestionarios: Cuestionario[];
}

defineProps<Props>();

function eliminarCuestionario(id: number) {
  if (confirm('¿Está seguro de eliminar este cuestionario? Se perderán todas las inscripciones asociadas.')) {
    router.delete(`/admin/inscripciones/inscripcion/${id}`);
  }
}

function formatearFecha(fecha: string): string {
  const date = new Date(fecha);
  return date.toLocaleDateString('es-MX', { year: 'numeric', month: 'long', day: 'numeric' });
}
</script>
