<template>
  <AdminLayout>
    <Head title="Contactos Alumnos" />
    
    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Header -->
      <div class="bg-white rounded-xl shadow p-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Contactos Alumnos</h1>
          <p class="text-gray-600 mt-1">Gestión y consulta de contactos de los alumnos</p>
        </div>
      </div>

      <!-- Filtros y búsqueda -->
      <div class="bg-white rounded-xl shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Búsqueda -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-black mb-2">Buscar</label>
            <div class="relative">
              <input
                v-model="busqueda"
                type="text"
                placeholder="Nombre contacto, alumno o matrícula..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
              />
              <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
          </div>

          <!-- Filtro por grado -->
          <div>
            <label class="block text-sm font-medium text-black mb-2">Grado del Alumno</label>
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
            <label class="block text-sm font-medium text-black mb-2">Grupo del Alumno</label>
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
        </div>
      </div>

      <!-- Tabla de contactos -->
      <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Foto</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Nombre Contacto</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Alumno</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Matrícula</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Parentesco</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="contacto in contactosFiltrados" :key="contacto.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <img
                    v-if="contacto.foto_url"
                    :src="contacto.foto_url"
                    :alt="contacto.nombre_completo"
                    class="w-10 h-10 rounded-full object-cover"
                  />
                  <div v-else class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">
                  {{ contacto.nombre_completo }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                  {{ contacto.alumno_nombre }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                  {{ contacto.alumno_matricula }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                  <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                    {{ contacto.parentesco }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <button
                    @click="verPerfil(contacto.alumno_id)"
                    class="text-blue-600 hover:text-blue-900 font-medium"
                    title="Ver perfil del alumno"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                  </button>
                </td>
              </tr>
              
              <tr v-if="contactosFiltrados.length === 0">
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                  No se encontraron contactos con los filtros aplicados
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        <div v-if="contactosFiltrados.length > 0" class="bg-gray-50 px-6 py-4 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <p class="text-sm text-gray-700">
              Mostrando <span class="font-medium">{{ contactosFiltrados.length }}</span> contactos
            </p>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '../../../layouts/AdminLayout.vue';
import axios from 'axios';

interface Contacto {
  id: number;
  nombre_completo: string;
  parentesco: string;
  foto_url: string | null;
  alumno_nombre: string;
  alumno_matricula: string;
  alumno_id: number;
  alumno_grado: number;
  alumno_grupo: string;
}

// Estado
const contactos = ref<Contacto[]>([]);
const busqueda = ref('');
const filtroGrado = ref('');
const filtroGrupo = ref('');

// Computed
const contactosFiltrados = computed(() => {
  let resultado = contactos.value;

  // Filtrar por búsqueda (nombre del contacto, nombre del alumno o matrícula)
  if (busqueda.value) {
    const termino = busqueda.value.toLowerCase();
    resultado = resultado.filter(c =>
      c.nombre_completo.toLowerCase().includes(termino) ||
      c.alumno_nombre.toLowerCase().includes(termino) ||
      c.alumno_matricula.toLowerCase().includes(termino)
    );
  }

  // Filtrar por grado del alumno
  if (filtroGrado.value) {
    resultado = resultado.filter(c => c.alumno_grado && c.alumno_grado.toString() === filtroGrado.value);
  }

  // Filtrar por grupo del alumno
  if (filtroGrupo.value) {
    resultado = resultado.filter(c => c.alumno_grupo === filtroGrupo.value);
  }

  return resultado;
});

// Métodos
const cargarContactos = async () => {
  try {
    console.log('Cargando contactos...');
    const response = await axios.get('/admin/alumnos/contactos-datos');
    console.log('Respuesta recibida:', response.data);
    contactos.value = response.data;
  } catch (error) {
    console.error('Error al cargar contactos:', error);
  }
};

// Navegar al perfil del alumno
const verPerfil = (alumnoId: number) => {
  router.visit(`/admin/alumnos/${alumnoId}/perfil`);
};

// Lifecycle
onMounted(() => {
  cargarContactos();
});
</script>
