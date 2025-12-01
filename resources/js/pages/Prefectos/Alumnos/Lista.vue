<template>
  <PrefectosLayout>
    <Head title="Lista de Alumnos (Prefectura)" />

    <div class="space-y-4 sm:space-y-6">
      <!-- Encabezado -->
      <div class="mb-4 sm:mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-black">Lista de Alumnos</h1>
        <p class="text-xs sm:text-sm text-black mt-1">Consulta la información de los alumnos</p>
      </div>

      <!-- Filtros -->
      <div class="bg-white rounded-lg shadow-sm p-3 sm:p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
          <!-- Búsqueda -->
          <div class="sm:col-span-2">
            <label class="block text-xs sm:text-sm font-medium text-black mb-2">
              Buscar alumno
            </label>
            <input
              v-model="filtros.busqueda"
              type="text"
              placeholder="Nombre, CURP o matrícula..."
              class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-black"
              @input="filtrarAlumnos"
            />
          </div>

          <!-- Grado -->
          <div>
            <label class="block text-xs sm:text-sm font-medium text-black mb-2">
              Grado
            </label>
            <select
              v-model="filtros.grado"
              class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-black"
              @change="filtrarAlumnos"
            >
              <option value="">Todos</option>
              <option value="1">1°</option>
              <option value="2">2°</option>
              <option value="3">3°</option>
            </select>
          </div>

          <!-- Grupo -->
          <div>
            <label class="block text-xs sm:text-sm font-medium text-black mb-2">
              Grupo
            </label>
            <select
              v-model="filtros.grupo"
              class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-black"
              @change="filtrarAlumnos"
            >
              <option value="">Todos</option>
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
            </select>
          </div>

          <!-- Turno -->
          <div>
            <label class="block text-xs sm:text-sm font-medium text-black mb-2">
              Turno
            </label>
            <select
              v-model="filtros.turno"
              class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-black"
              @change="filtrarAlumnos"
            >
              <option value="">Todos</option>
              <option value="Matutino">Matutino</option>
              <option value="Vespertino">Vespertino</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Tabla de alumnos - Desktop -->
      <div class="hidden md:block bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                  Foto
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                  Nombre
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                  Matrícula
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                  Grado
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                  Grupo
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                  Turno
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                  Acciones
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="alumnosFiltrados.length === 0">
                <td colspan="7" class="px-6 py-8 text-center text-black">
                  No se encontraron alumnos
                </td>
              </tr>
              <tr v-for="alumno in alumnosFiltrados" :key="alumno.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <img
                    :src="alumno.foto_url"
                    :alt="alumno.nombre_completo"
                    class="h-10 w-10 rounded-full object-cover"
                  />
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-black">
                    {{ alumno.nombre_completo }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                  {{ alumno.matricula }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                  {{ alumno.grado }}°
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                  {{ alumno.grupo }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                  <span v-if="alumno.turno" class="px-2 py-1 text-xs font-medium rounded-full" :class="{
                    'bg-yellow-100 text-yellow-800': alumno.turno === 'Matutino',
                    'bg-blue-100 text-blue-800': alumno.turno === 'Vespertino'
                  }">
                    {{ alumno.turno }}
                  </span>
                  <span v-else class="text-gray-400">N/A</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <button
                    @click="verPerfil(alumno.id)"
                    class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Ver Perfil
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Cards de alumnos - Mobile -->
      <div class="md:hidden space-y-3">
        <div v-if="alumnosFiltrados.length === 0" class="bg-white rounded-lg shadow-sm p-6 text-center text-gray-500">
          No se encontraron alumnos
        </div>
        <div v-for="alumno in alumnosFiltrados" :key="alumno.id" class="bg-white rounded-lg shadow-sm p-4">
          <div class="flex items-start space-x-3">
            <img
              :src="alumno.foto_url"
              :alt="alumno.nombre_completo"
              class="h-16 w-16 rounded-full object-cover flex-shrink-0"
            />
            <div class="flex-1 min-w-0">
              <h3 class="text-sm font-semibold text-black truncate">{{ alumno.nombre_completo }}</h3>
              <div class="mt-1 space-y-1">
                <p class="text-xs text-gray-600">
                  <span class="font-medium">Matrícula:</span> {{ alumno.matricula }}
                </p>
                <p class="text-xs text-gray-600">
                  <span class="font-medium">Grado/Grupo:</span> {{ alumno.grado }}° {{ alumno.grupo }}
                </p>
                <div v-if="alumno.turno" class="flex items-center gap-1">
                  <span class="text-xs text-gray-600 font-medium">Turno:</span>
                  <span class="px-2 py-0.5 text-xs font-medium rounded-full" :class="{
                    'bg-yellow-100 text-yellow-800': alumno.turno === 'Matutino',
                    'bg-blue-100 text-blue-800': alumno.turno === 'Vespertino'
                  }">
                    {{ alumno.turno }}
                  </span>
                </div>
              </div>
              <button
                @click="verPerfil(alumno.id)"
                class="mt-3 w-full inline-flex items-center justify-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
              >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Ver Perfil
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Resumen -->
      <div class="text-xs sm:text-sm text-black px-2 sm:px-0">
        Mostrando {{ alumnosFiltrados.length }} de {{ alumnos.length }} alumnos
      </div>
    </div>
  </PrefectosLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import PrefectosLayout from '../../../layouts/PrefectosLayout.vue'
import axios from 'axios'

interface Alumno {
  id: number;
  matricula: string;
  nombre_completo: string;
  curp: string;
  grado: number;
  grupo: string;
  turno: string | null;
  tipo_sangre: string | null;
  foto_url: string | null;
  alergias: string | null;
  telefono_tutor: string | null;
}

// Estado
const alumnos = ref<Alumno[]>([])
const filtros = ref({
  busqueda: '',
  grado: '',
  grupo: '',
  turno: ''
})

// Computed
const alumnosFiltrados = computed(() => {
  let resultado = [...alumnos.value]

  // Filtro de búsqueda
  if (filtros.value.busqueda) {
    const busqueda = filtros.value.busqueda.toLowerCase()
    resultado = resultado.filter((alumno) => 
      alumno.nombre_completo.toLowerCase().includes(busqueda) ||
      alumno.matricula?.toLowerCase().includes(busqueda) ||
      alumno.curp?.toLowerCase().includes(busqueda)
    )
  }

  // Filtro de grado
  if (filtros.value.grado) {
    resultado = resultado.filter((alumno) => alumno.grado && alumno.grado.toString() === filtros.value.grado)
  }

  // Filtro de grupo
  if (filtros.value.grupo) {
    resultado = resultado.filter((alumno) => alumno.grupo === filtros.value.grupo)
  }

  // Filtro de turno
  if (filtros.value.turno) {
    resultado = resultado.filter((alumno) => alumno.turno === filtros.value.turno)
  }

  return resultado
})

// Métodos
const cargarAlumnos = async () => {
  try {
    const response = await axios.get('/prefectos/alumnos/lista-datos')
    alumnos.value = response.data
  } catch (error) {
    console.error('Error al cargar alumnos:', error)
  }
}

const filtrarAlumnos = () => {
  // La reactividad de computed se encarga de actualizar automáticamente
}

const verPerfil = (alumnoId: number) => {
  router.visit(`/prefectos/alumnos/${alumnoId}/perfil`)
}

// Lifecycle
onMounted(() => {
  cargarAlumnos()
})
</script>
