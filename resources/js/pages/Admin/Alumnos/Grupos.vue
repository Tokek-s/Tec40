<template>
  <AdminLayout>
    <Head title="Grupos" />
    
    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Header -->
      <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Grupos</h1>
            <p class="text-gray-600 mt-1">Gestión de grupos escolares</p>
          </div>
          
          <button
            @click="mostrarModalCrear = true; resetFormGrupo()"
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors"
          >
            + Nuevo Grupo
          </button>
        </div>
      </div>

      <!-- Filtros -->
      <div class="bg-white rounded-xl shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Grado</label>
            <select
              v-model="filtros.grado"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
            >
              <option value="">Todos</option>
              <option value="1">1°</option>
              <option value="2">2°</option>
              <option value="3">3°</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Grupo</label>
            <select
              v-model="filtros.clave"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
            >
              <option value="">Todos</option>
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
              <option value="E">E</option>
              <option value="F">F</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Turno</label>
            <select
              v-model="filtros.turno"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
            >
              <option value="">Todos</option>
              <option value="Matutino">Matutino</option>
              <option value="Vespertino">Vespertino</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Lista de Grupos -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div
          v-for="grupo in grupos"
          :key="grupo.id"
          class="bg-white rounded-lg shadow hover:shadow-md transition-shadow border border-gray-200 overflow-hidden cursor-pointer"
          @click="verAlumnos(grupo.id)"
        >
          <div class="p-4">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-lg font-bold text-gray-900">{{ grupo.nombre }}</h3>
              <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">
                {{ grupo.alumnos_count }} alumnos
              </span>
            </div>
            
            <div class="space-y-1.5 text-xs text-gray-600">
              <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Grado: {{ grupo.grado }}°
              </div>
              <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Grupo: {{ grupo.clave }}
              </div>
              <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Turno: {{ grupo.turno }}
              </div>
            </div>
          </div>

          <div class="bg-gray-50 px-4 py-2 flex gap-2">
            <button
              @click.stop="editarGrupo(grupo)"
              class="flex-1 px-2 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg transition-colors"
            >
              Editar
            </button>
            <button
              @click.stop="confirmarEliminar(grupo)"
              class="flex-1 px-2 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs rounded-lg transition-colors"
            >
              Eliminar
            </button>
          </div>
        </div>

        <!-- Mensaje si no hay grupos -->
        <div v-if="grupos.length === 0" class="col-span-full bg-white rounded-xl shadow p-12 text-center">
          <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
          </svg>
          <p class="text-gray-500 text-lg">No se encontraron grupos</p>
          <p class="text-gray-400 text-sm mt-2">Intenta ajustar los filtros o crea un nuevo grupo</p>
        </div>
      </div>
    </div>

    <!-- Modal Crear/Editar Grupo -->
    <div v-if="mostrarModalCrear || mostrarModalEditar" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="cerrarModales">
      <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-xl font-bold text-gray-900">
            {{ mostrarModalEditar ? 'Editar Grupo' : 'Nuevo Grupo' }}
          </h2>
        </div>

        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Grado *</label>
            <select
              v-model="formGrupo.grado"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
            >
              <option value="">Seleccionar</option>
              <option value="1">1°</option>
              <option value="2">2°</option>
              <option value="3">3°</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Grupo *</label>
            <select
              v-model="formGrupo.clave"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
            >
              <option value="">Seleccionar</option>
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
              <option value="E">E</option>
              <option value="F">F</option>
              <option value="G">G</option>
              <option value="H">H</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Turno *</label>
            <select
              v-model="formGrupo.turno"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
            >
              <option value="">Seleccionar</option>
              <option value="Matutino">Matutino</option>
              <option value="Vespertino">Vespertino</option>
            </select>
          </div>
        </div>

        <div class="p-6 bg-gray-50 flex gap-3 rounded-b-xl">
          <button
            @click="cerrarModales"
            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors"
          >
            Cancelar
          </button>
          <button
            @click="guardarGrupo"
            :disabled="guardando"
            class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors disabled:bg-gray-300"
          >
            {{ guardando ? 'Guardando...' : 'Guardar' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Confirmar Eliminación -->
    <div v-if="mostrarModalEliminar" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="cerrarModalEliminar">
      <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-xl font-bold text-gray-900">Confirmar Eliminación</h2>
        </div>

        <form @submit.prevent="eliminarGrupo">
          <div class="p-6 space-y-4">
            <div class="flex items-center gap-3 p-4 bg-red-50 rounded-lg border border-red-200">
              <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
              <div>
                <p class="text-sm font-medium text-red-900">¿Estás seguro de eliminar el grupo?</p>
                <p class="text-sm text-red-700 mt-1">{{ grupoEliminar?.nombre }}</p>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Ingresa tu contraseña para confirmar *</label>
              <input
                v-model="passwordEliminar"
                type="password"
                placeholder="Contraseña"
                autocomplete="current-password"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
              />
            </div>
          </div>

          <div class="p-6 bg-gray-50 flex gap-3 rounded-b-xl">
            <button
              type="button"
              @click="cerrarModalEliminar"
              class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="eliminando || !passwordEliminar"
              class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors disabled:bg-gray-300"
            >
              {{ eliminando ? 'Eliminando...' : 'Eliminar Grupo' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '../../../layouts/AdminLayout.vue'

interface Grupo {
  id: number
  grado: number
  clave: string
  turno: string
  nombre: string
  alumnos_count: number
}

interface Props {
  grupos: Grupo[]
  filtros: {
    grado: string
    clave: string
    turno: string
  }
}

const props = defineProps<Props>()

const filtros = ref({
  grado: props.filtros.grado || '',
  clave: props.filtros.clave || '',
  turno: props.filtros.turno || '',
})

const mostrarModalCrear = ref(false)
const mostrarModalEditar = ref(false)
const mostrarModalEliminar = ref(false)
const guardando = ref(false)
const eliminando = ref(false)
const grupoEditando = ref<Grupo | null>(null)
const grupoEliminar = ref<Grupo | null>(null)
const passwordEliminar = ref('')

const formGrupo = ref({
  grado: '',
  clave: '',
  turno: '',
})

// Watch para filtros
watch(filtros, () => {
  router.get('/admin/alumnos/grupos', filtros.value, {
    preserveState: true,
    preserveScroll: true,
  })
}, { deep: true })

function resetFormGrupo() {
  formGrupo.value = {
    grado: '',
    clave: '',
    turno: '',
  }
  grupoEditando.value = null
}

function editarGrupo(grupo: Grupo) {
  grupoEditando.value = grupo
  formGrupo.value = {
    grado: grupo.grado.toString(),
    clave: grupo.clave,
    turno: grupo.turno,
  }
  mostrarModalEditar.value = true
}

function guardarGrupo() {
  if (!formGrupo.value.grado || !formGrupo.value.clave || !formGrupo.value.turno) {
    alert('Por favor completa todos los campos')
    return
  }

  guardando.value = true

  const url = mostrarModalEditar.value
    ? `/admin/alumnos/grupos/${grupoEditando.value!.id}`
    : '/admin/alumnos/grupos/crear'

  const method = mostrarModalEditar.value ? 'put' : 'post'

  router[method](url, formGrupo.value, {
    onSuccess: () => {
      cerrarModales()
      resetFormGrupo()
    },
    onError: (errors) => {
      alert(errors.error || 'Ocurrió un error al guardar el grupo')
    },
    onFinish: () => {
      guardando.value = false
    },
  })
}

function confirmarEliminar(grupo: Grupo) {
  grupoEliminar.value = grupo
  passwordEliminar.value = ''
  mostrarModalEliminar.value = true
}

function eliminarGrupo() {
  if (!passwordEliminar.value) {
    alert('Por favor ingresa tu contraseña')
    return
  }

  eliminando.value = true

  router.delete(`/admin/alumnos/grupos/${grupoEliminar.value!.id}`, {
    data: {
      password: passwordEliminar.value
    },
    onSuccess: () => {
      cerrarModalEliminar()
    },
    onError: (errors) => {
      alert(errors.error || errors.password || 'No se pudo eliminar el grupo')
    },
    onFinish: () => {
      eliminando.value = false
    },
  })
}

function verAlumnos(grupoId: number) {
  router.visit(`/admin/alumnos/grupos/${grupoId}/alumnos`)
}

function cerrarModales() {
  mostrarModalCrear.value = false
  mostrarModalEditar.value = false
  resetFormGrupo()
}

function cerrarModalEliminar() {
  mostrarModalEliminar.value = false
  grupoEliminar.value = null
  passwordEliminar.value = ''
}
</script>

