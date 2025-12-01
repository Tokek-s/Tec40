<template>
  <AdminLayout>
    <Head title="Generar Credenciales" />
    
    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Header -->
      <div class="bg-white rounded-xl shadow p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Generar Credenciales</h1>
            <p class="text-gray-600 mt-1">Gestión y consulta de alumnos registrados</p>
          </div>
          
          <button
            @click="mostrarModalCredencial = true"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors w-full md:w-auto"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
            </svg>
            Generar Credencial
          </button>
        </div>
      </div>

      <!-- Filtros y búsqueda -->
      <div class="bg-white rounded-xl shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
          <!-- Búsqueda -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-black mb-2">Buscar</label>
            <div class="relative">
              <input
                v-model="busqueda"
                type="text"
                placeholder="Nombre, CURP o Matrícula..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
              />
              <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
          </div>

          <!-- Filtro por grado -->
          <div>
            <label class="block text-sm font-medium text-black mb-2">Grado</label>
            <select
              v-model="filtroGrado"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
            >
              <option value="">Todos</option>
              <option value="1">1° (Primero)</option>
              <option value="2">2° (Segundo)</option>
              <option value="3">3° (Tercero)</option>
            </select>
          </div>

          <!-- Filtro por grupo -->
          <div>
            <label class="block text-sm font-medium text-black mb-2">Grupo</label>
            <select
              v-model="filtroGrupo"
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

          <!-- Filtro por turno -->
          <div>
            <label class="block text-sm font-medium text-black mb-2">Turno</label>
            <select
              v-model="filtroTurno"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
            >
              <option value="">Todos</option>
              <option value="Matutino">Matutino</option>
              <option value="Vespertino">Vespertino</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Tabla de alumnos -->
      <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Foto</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Matrícula</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">CURP</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Grado/Grupo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Turno</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Sangre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="alumno in alumnosFiltrados" :key="alumno.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <img
                    v-if="alumno.foto_url"
                    :src="alumno.foto_url"
                    :alt="alumno.nombre_completo"
                    class="w-10 h-10 rounded-full object-cover"
                  />
                  <div v-else class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">
                  {{ alumno.matricula }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                  {{ alumno.nombre_completo }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                  {{ alumno.curp }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                  {{ alumno.grado }}° {{ alumno.grupo }}
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
                <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                  {{ alumno.tipo_sangre || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <button
                    @click="generarCredencialIndividual(alumno.matricula)"
                    class="text-red-600 hover:text-red-900 font-medium"
                    title="Generar credencial"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                    </svg>
                  </button>
                </td>
              </tr>
              
              <tr v-if="alumnosFiltrados.length === 0">
                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                  No se encontraron alumnos con los filtros aplicados
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        <div v-if="alumnosFiltrados.length > 0" class="bg-gray-50 px-6 py-4 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <p class="text-sm text-gray-700">
              Mostrando <span class="font-medium">{{ alumnosFiltrados.length }}</span> alumnos
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para seleccionar tipo de generación -->
    <Teleport to="body">
      <div
        v-if="mostrarModalCredencial"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        @click.self="mostrarModalCredencial = false"
      >
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-auto">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-xl font-bold text-black">Generar Credenciales</h3>
              <button
                @click="mostrarModalCredencial = false"
                class="text-gray-400 hover:text-gray-600"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <p class="text-black mb-6">¿Cómo desea generar las credenciales?</p>

            <!-- Opción: Por alumno -->
            <div class="space-y-4">
              <div
                @click="seleccionarTipoGeneracion('individual')"
                class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-red-500 hover:bg-red-50 transition-all"
                :class="{ 'border-red-500 bg-red-50': tipoGeneracion === 'individual' }"
              >
                <div class="flex items-center gap-3">
                  <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                       :class="tipoGeneracion === 'individual' ? 'border-red-500' : 'border-gray-300'">
                    <div v-if="tipoGeneracion === 'individual'" class="w-3 h-3 rounded-full bg-red-500"></div>
                  </div>
                  <div>
                    <h4 class="font-semibold text-black">Por Alumno</h4>
                    <p class="text-sm text-black">Genera la credencial de un alumno específico</p>
                  </div>
                </div>
              </div>

              <!-- Opción: Masivo -->
              <div
                @click="seleccionarTipoGeneracion('masivo')"
                class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-red-500 hover:bg-red-50 transition-all"
                :class="{ 'border-red-500 bg-red-50': tipoGeneracion === 'masivo' }"
              >
                <div class="flex items-center gap-3">
                  <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                       :class="tipoGeneracion === 'masivo' ? 'border-red-500' : 'border-gray-300'">
                    <div v-if="tipoGeneracion === 'masivo'" class="w-3 h-3 rounded-full bg-red-500"></div>
                  </div>
                  <div>
                    <h4 class="font-semibold text-black">Masivo</h4>
                    <p class="text-sm text-black">Genera credenciales para todos los alumnos</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Input para matrícula (solo si es individual) -->
            <div v-if="tipoGeneracion === 'individual'" class="mt-4">
              <label class="block text-sm font-medium text-black mb-2">Matrícula del alumno</label>
              <input
                v-model="matriculaInput"
                type="text"
                placeholder="Ingrese la matrícula..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                @keyup.enter="confirmarGeneracion"
              />
            </div>

            <!-- Botones -->
            <div class="flex gap-3 mt-6">
              <button
                @click="mostrarModalCredencial = false"
                class="flex-1 px-4 py-2 border border-gray-300 text-black rounded-lg hover:bg-gray-50 transition-colors"
              >
                Cancelar
              </button>
              <button
                @click="confirmarGeneracion"
                :disabled="procesando || (tipoGeneracion === 'individual' && !matriculaInput)"
                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed"
              >
                <span v-if="!procesando">Generar</span>
                <span v-else class="flex items-center justify-center gap-2">
                  <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Generando...
                </span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '../../../layouts/AdminLayout.vue';
import axios from 'axios';

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
const alumnos = ref<Alumno[]>([]);
const busqueda = ref('');
const filtroGrado = ref('');
const filtroGrupo = ref('');
const filtroTurno = ref('');
const mostrarModalCredencial = ref(false);
const tipoGeneracion = ref<'individual' | 'masivo' | null>(null);
const matriculaInput = ref('');
const procesando = ref(false);

// Computed
const alumnosFiltrados = computed(() => {
  let resultado = alumnos.value;

  // Filtrar por búsqueda
  if (busqueda.value) {
    const termino = busqueda.value.toLowerCase();
    resultado = resultado.filter(a =>
      a.nombre_completo.toLowerCase().includes(termino) ||
      a.curp.toLowerCase().includes(termino) ||
      a.matricula.toLowerCase().includes(termino)
    );
  }

  // Filtrar por grado
  if (filtroGrado.value) {
    resultado = resultado.filter(a => a.grado && a.grado.toString() === filtroGrado.value);
  }

  // Filtrar por grupo
  if (filtroGrupo.value) {
    resultado = resultado.filter(a => a.grupo === filtroGrupo.value);
  }

  // Filtrar por turno
  if (filtroTurno.value) {
    resultado = resultado.filter(a => a.turno === filtroTurno.value);
  }

  return resultado;
});

// Métodos
const cargarAlumnos = async () => {
  try {
    const response = await axios.get('/admin/alumnos/lista-datos');
    alumnos.value = response.data;
  } catch (error) {
    console.error('Error al cargar alumnos:', error);
  }
};

const seleccionarTipoGeneracion = (tipo: 'individual' | 'masivo') => {
  tipoGeneracion.value = tipo;
  if (tipo === 'masivo') {
    matriculaInput.value = '';
  }
};

const confirmarGeneracion = async () => {
  if (tipoGeneracion.value === 'individual' && !matriculaInput.value) {
    alert('Por favor ingrese una matrícula');
    return;
  }

  procesando.value = true;

  try {
    if (tipoGeneracion.value === 'individual') {
      await generarCredencialIndividual(matriculaInput.value);
    } else {
      await generarCredencialesMasivo();
    }
    
    mostrarModalCredencial.value = false;
    tipoGeneracion.value = null;
    matriculaInput.value = '';
  } catch (error) {
    console.error('Error al generar credencial:', error);
    alert('Error al generar la credencial. Por favor intente nuevamente.');
  } finally {
    procesando.value = false;
  }
};

const generarCredencialIndividual = async (matricula: string) => {
  try {
    const response = await axios.post('/admin/credenciales/generar', {
      matricula: matricula,
      tipo: 'individual'
    }, {
      responseType: 'blob'
    });

    // Descargar PDF
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `credencial_${matricula}.pdf`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  } catch (error: any) {
    if (error.response?.status === 422) {
      // Error de validación
      const reader = new FileReader();
      reader.onload = () => {
        const text = reader.result as string;
        try {
          const errorData = JSON.parse(text);
          alert(errorData.error || 'Error al generar la credencial');
        } catch {
          alert('El alumno no tiene grado o grupo asignado');
        }
      };
      reader.readAsText(error.response.data);
    } else {
      alert('Error al generar la credencial. Verifique que el alumno tenga todos los datos completos.');
    }
    throw error;
  }
};

const generarCredencialesMasivo = async () => {
  try {
    const response = await axios.post('/admin/credenciales/generar', {
      tipo: 'masivo'
    }, {
      responseType: 'blob'
    });

    // Descargar ZIP con todos los PDFs
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `credenciales_todas_${Date.now()}.zip`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  } catch (error: any) {
    if (error.response?.status === 422) {
      // Error de validación
      const reader = new FileReader();
      reader.onload = () => {
        const text = reader.result as string;
        try {
          const errorData = JSON.parse(text);
          alert(errorData.error || 'Error al generar las credenciales');
        } catch {
          alert('No hay alumnos con grado y grupo asignados');
        }
      };
      reader.readAsText(error.response.data);
    } else {
      alert('Error al generar las credenciales masivas.');
    }
    throw error;
  }
};

// Navegar al perfil del alumno
const verPerfil = (alumnoId: number) => {
  router.visit(`/admin/alumnos/${alumnoId}/perfil`);
};

// Lifecycle
onMounted(() => {
  cargarAlumnos();
});
</script>
