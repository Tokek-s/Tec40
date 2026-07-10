<template>
  <AdminLayout :title="modoEdicion ? 'Editar Cuestionario' : 'Nuevo Cuestionario'">
    <div class="p-6 bg-gray-100 min-h-screen">
      <!-- Header con navegación -->
      <div class="mb-6">
        <Link
          :href="route('admin.inscripciones.inscripcion')"
          class="inline-flex items-center text-black hover:text-gray-700 mb-4 font-semibold"
        >
          ← Volver a la lista
        </Link>
        <h1 class="text-3xl font-bold text-black">
          {{ modoEdicion ? 'Editar Cuestionario' : 'Nuevo Cuestionario de Inscripción' }}
        </h1>
      </div>

      <form @submit.prevent="guardarCuestionario">
        <!-- Datos del Cuestionario -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <h2 class="text-xl font-bold text-black mb-4">📋 Datos del Cuestionario</h2>
          
          <!-- Descripción -->
          <div class="mb-4">
            <label class="block text-black font-semibold mb-2">Descripción</label>
            <textarea
              v-model="formulario.descripcion"
              rows="3"
              class="w-full border-2 border-gray-400 rounded-lg px-4 py-2 bg-gray-50 text-black font-semibold focus:ring-2 focus:ring-red-500 focus:border-transparent"
              placeholder="Descripción del cuestionario de inscripción..."
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
                  <p class="text-sm text-black font-semibold">El cuestionario estará disponible para inscripciones</p>
                </div>
              </label>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-4">
                <label class="flex items-center gap-3 p-3 bg-blue-100 rounded-lg cursor-pointer hover:bg-blue-200 transition-colors border-2 border-blue-300">
                  <input v-model="formulario.primero_activo" type="checkbox" class="w-5 h-5 text-blue-600" />
                  <span class="font-bold text-black text-lg">1er Grado</span>
                </label>
                
                <label class="flex items-center gap-3 p-3 bg-green-100 rounded-lg cursor-pointer hover:bg-green-200 transition-colors border-2 border-green-300">
                  <input v-model="formulario.segundo_activo" type="checkbox" class="w-5 h-5 text-green-600" />
                  <span class="font-bold text-black text-lg">2do Grado</span>
                </label>
                
                <label class="flex items-center gap-3 p-3 bg-purple-100 rounded-lg cursor-pointer hover:bg-purple-200 transition-colors border-2 border-purple-300">
                  <input v-model="formulario.tercero_activo" type="checkbox" class="w-5 h-5 text-purple-600" />
                  <span class="font-bold text-black text-lg">3er Grado</span>
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Campos Extra Dinámicos -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="text-xl font-bold text-black">✨ Campos Extra Personalizados</h2>
              <p class="text-sm text-black mt-1">
                Agrega campos adicionales que se guardarán en la base de datos
              </p>
            </div>
            <button
              v-if="!modoEdicion"
              type="button"
              @click="agregarCampoExtra"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors"
            >
              + Agregar Campo
            </button>
          </div>

          <!-- Lista de campos existentes (modo edición) -->
          <div v-if="modoEdicion && camposExtra.length > 0" class="space-y-2 mb-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
              <p class="text-sm text-blue-800">
                ℹ️ Los campos extra se gestionan globalmente. Los cambios afectarán a todos los cuestionarios.
              </p>
            </div>
            <div
              v-for="(campo, index) in camposExtra"
              :key="index"
              class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200"
            >
              <div class="flex-1">
                <span class="font-semibold text-black">{{ campo.nombre }}</span>
                <span class="text-sm text-black ml-2">(Tipo: {{ campo.tipo }})</span>
              </div>
              <button
                type="button"
                @click="eliminarCampo(campo.nombre)"
                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition-colors"
              >
                🗑️ Eliminar
              </button>
            </div>
          </div>

          <!-- Formulario de campos (modo creación) -->
          <div v-if="!modoEdicion">
            <div v-if="formulario.campos_extra.length === 0" class="text-center py-8">
              <div class="text-4xl mb-2">📝</div>
              <p class="text-black">No hay campos extra. Agrega uno para empezar.</p>
            </div>
            
            <div v-else class="space-y-3">
              <div
                v-for="(campo, index) in formulario.campos_extra"
                :key="index"
                class="flex gap-3 items-center p-4 bg-gray-50 rounded-lg border border-gray-200"
              >
                <div class="flex-1">
                  <input
                    v-model="campo.nombre"
                    type="text"
                    placeholder="Nombre del campo (ej: Ingresos mensuales, Habitantes en casa)"
                    required
                    class="w-full border-2 border-gray-400 rounded-lg px-4 py-2 bg-gray-50 text-black font-semibold focus:ring-2 focus:ring-green-500 focus:border-transparent"
                  />
                </div>
                <button
                  type="button"
                  @click="quitarCampoExtra(index)"
                  class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition-colors"
                >
                  ✗
                </button>
              </div>
            </div>
          </div>

          <!-- Botón para agregar campos en modo edición -->
          <div v-if="modoEdicion" class="mt-4">
            <button
              type="button"
              @click="agregarCampoExtraEdicion"
              class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors w-full"
            >
              + Agregar Nuevo Campo Extra
            </button>
          </div>
        </div>

        <!-- Botones de Acción -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex justify-end gap-4">
            <Link
              :href="route('admin.inscripciones.inscripcion')"
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

      <!-- Modal de Confirmación de Eliminación -->
      <div
        v-if="mostrarModalEliminar"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div class="bg-white rounded-lg p-8 w-full max-w-md shadow-2xl">
          <h2 class="text-2xl font-bold mb-4 text-red-600">⚠️ Confirmar Eliminación</h2>
          <p class="mb-4 text-black">
            Está a punto de eliminar el campo <strong class="text-red-600">{{ campoAEliminar }}</strong>.
          </p>
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
            <p class="text-sm text-yellow-800">
              Esta acción eliminará la columna de la base de datos y <strong>todos los datos asociados</strong>.
              Esta acción es <strong>irreversible</strong>.
            </p>
          </div>
          <p class="mb-4 font-semibold text-black">Ingrese su contraseña para confirmar:</p>
          
          <input
            v-model="passwordConfirmacion"
            type="password"
            placeholder="Contraseña"
            class="w-full border-2 border-gray-400 rounded-lg px-4 py-2 mb-4 bg-gray-50 text-black font-semibold focus:ring-2 focus:ring-red-500 focus:border-transparent"
            @keyup.enter="confirmarEliminacionCampo"
          />
          
          <div class="flex justify-end gap-4">
            <button
              @click="cancelarEliminacionCampo"
              class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="confirmarEliminacionCampo"
              class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded transition-colors"
            >
              Eliminar Campo
            </button>
          </div>
        </div>
      </div>

      <!-- Modal para agregar campo en modo edición -->
      <div
        v-if="mostrarModalAgregarCampo"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div class="bg-white rounded-lg p-8 w-full max-w-md shadow-2xl">
          <h2 class="text-2xl font-bold mb-4 text-black">Agregar Nuevo Campo</h2>
          <p class="mb-4 text-black">Ingrese el nombre del nuevo campo:</p>
          
          <input
            v-model="nuevoCampoNombre"
            type="text"
            placeholder="Nombre del campo"
            class="w-full border-2 border-gray-400 rounded-lg px-4 py-2 mb-4 bg-gray-50 text-black font-semibold focus:ring-2 focus:ring-green-500 focus:border-transparent"
            @keyup.enter="confirmarAgregarCampo"
          />
          
          <div class="flex justify-end gap-4">
            <button
              @click="cancelarAgregarCampo"
              class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="confirmarAgregarCampo"
              class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded transition-colors"
            >
              Agregar
            </button>
          </div>
        </div>
      </div>
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
  primero_activo: props.cuestionario?.primero_activo ?? true,
  segundo_activo: props.cuestionario?.segundo_activo ?? true,
  tercero_activo: props.cuestionario?.tercero_activo ?? true,
  campos_extra: [] as { nombre: string; tipo: string }[],
});

const camposExtra = ref<CampoExtra[]>(props.camposExtra);
const mostrarModalEliminar = ref(false);
const campoAEliminar = ref('');
const passwordConfirmacion = ref('');

const mostrarModalAgregarCampo = ref(false);
const nuevoCampoNombre = ref('');

function agregarCampoExtra() {
  formulario.value.campos_extra.push({ nombre: '', tipo: 'texto' });
}

function quitarCampoExtra(index: number) {
  formulario.value.campos_extra.splice(index, 1);
}

function agregarCampoExtraEdicion() {
  nuevoCampoNombre.value = '';
  mostrarModalAgregarCampo.value = true;
}

function cancelarAgregarCampo() {
  mostrarModalAgregarCampo.value = false;
  nuevoCampoNombre.value = '';
}

function confirmarAgregarCampo() {
  if (!nuevoCampoNombre.value.trim()) {
    alert('Debe ingresar un nombre para el campo');
    return;
  }

  router.post(
    '/admin/inscripciones/campo-extra/agregar',
    {
      nombre: nuevoCampoNombre.value.trim(),
    },
    {
      onSuccess: () => {
        cancelarAgregarCampo();
        router.reload({ only: ['camposExtra'] });
      },
      onError: (errors) => {
        alert(errors.error || 'Error al agregar el campo');
      },
    }
  );
}

function eliminarCampo(nombre: string) {
  campoAEliminar.value = nombre;
  passwordConfirmacion.value = '';
  mostrarModalEliminar.value = true;
}

function cancelarEliminacionCampo() {
  mostrarModalEliminar.value = false;
  campoAEliminar.value = '';
  passwordConfirmacion.value = '';
}

function confirmarEliminacionCampo() {
  if (!passwordConfirmacion.value) {
    alert('Debe ingresar su contraseña');
    return;
  }

  router.post(
    '/admin/inscripciones/campo-extra/eliminar',
    {
      nombre_campo: campoAEliminar.value,
      password: passwordConfirmacion.value,
    },
    {
      onSuccess: () => {
        cancelarEliminacionCampo();
        router.reload({ only: ['camposExtra'] });
      },
      onError: (errors) => {
        alert(errors.error || 'Error al eliminar el campo');
      },
    }
  );
}

function guardarCuestionario() {
  if (modoEdicion.value && props.cuestionario) {
    router.put(`/admin/inscripciones/inscripcion/${props.cuestionario.id}`, formulario.value, {
      onSuccess: () => {
        router.visit(route('admin.inscripciones.inscripcion'));
      },
      onError: (errors) => {
        if (errors.error) alert(errors.error);
      },
    });
  } else {
    router.post('/admin/inscripciones/inscripcion', formulario.value, {
      onSuccess: () => {
        router.visit(route('admin.inscripciones.inscripcion'));
      },
      onError: (errors) => {
        if (errors.error) alert(errors.error);
      },
    });
  }
}
</script>
