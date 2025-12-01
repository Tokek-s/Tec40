<template>
  <PrefectosLayout>
    <Head :title="`Perfil de ${alumno.nombre_completo}`" />

    <div class="space-y-4 sm:space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h1 class="text-xl sm:text-2xl font-bold text-black">Perfil del Alumno</h1>
          <p class="text-xs sm:text-sm text-black mt-1">Información completa del estudiante (solo lectura)</p>
        </div>
        <button
          @click="volver"
          class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Volver
        </button>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Columna Izquierda: Foto y Estado -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
            <!-- Foto -->
            <div class="text-center mb-4 sm:mb-6">
              <img
                :src="alumno.foto_url"
                :alt="alumno.nombre_completo"
                class="w-24 h-24 sm:w-32 sm:h-32 rounded-full mx-auto object-cover border-4 border-gray-200"
              />
            </div>

            <!-- Estado -->
            <div class="mb-4">
              <label class="block text-xs sm:text-sm font-medium text-black mb-2">Estado</label>
              <div class="px-3 sm:px-4 py-2 rounded-lg text-center" :class="{
                'bg-green-100': alumno.estatus === 'activo' || alumno.estatus === 'inscrito',
                'bg-red-100': alumno.estatus === 'baja',
                'bg-blue-100': alumno.estatus === 'egresado',
                'bg-gray-100': !alumno.estatus
              }">
                <span :class="{
                  'text-green-700': alumno.estatus === 'activo' || alumno.estatus === 'inscrito',
                  'text-red-700': alumno.estatus === 'baja',
                  'text-blue-700': alumno.estatus === 'egresado',
                  'text-gray-500': !alumno.estatus
                }" class="font-semibold text-sm sm:text-base">
                  {{ alumno.estatus === 'activo' ? 'Activo' : 
                     alumno.estatus === 'inscrito' ? 'Inscrito' : 
                     alumno.estatus === 'baja' ? 'Baja' : 
                     alumno.estatus === 'egresado' ? 'Egresado' : 
                     'Sin estado' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Columna Derecha: Datos -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
          <!-- Datos Personales -->
          <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
            <h2 class="text-base sm:text-xl font-bold text-black mb-3 sm:mb-4">Datos Personales</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
              <div>
                <label class="block text-xs sm:text-sm font-medium text-black mb-1">Nombre(s)</label>
                <p class="text-sm sm:text-base text-black bg-gray-50 px-3 sm:px-4 py-2 rounded-lg">{{ alumno.nombre_s }}</p>
              </div>
              <div>
                <label class="block text-xs sm:text-sm font-medium text-black mb-1">Apellido Paterno</label>
                <p class="text-sm sm:text-base text-black bg-gray-50 px-3 sm:px-4 py-2 rounded-lg">{{ alumno.apellido_paterno }}</p>
              </div>
              <div>
                <label class="block text-xs sm:text-sm font-medium text-black mb-1">Apellido Materno</label>
                <p class="text-sm sm:text-base text-black bg-gray-50 px-3 sm:px-4 py-2 rounded-lg">{{ alumno.apellido_materno }}</p>
              </div>
              <div>
                <label class="block text-xs sm:text-sm font-medium text-black mb-1">Fecha de Nacimiento</label>
                <p class="text-sm sm:text-base text-black bg-gray-50 px-3 sm:px-4 py-2 rounded-lg">{{ alumno.fecha_nacimiento }}</p>
              </div>
              <div>
                <label class="block text-xs sm:text-sm font-medium text-black mb-1">CURP</label>
                <p class="text-xs sm:text-sm text-black bg-gray-50 px-3 sm:px-4 py-2 rounded-lg break-all">{{ alumno.curp }}</p>
              </div>
              <div>
                <label class="block text-xs sm:text-sm font-medium text-black mb-1">Sexo</label>
                <p class="text-sm sm:text-base text-black bg-gray-50 px-3 sm:px-4 py-2 rounded-lg">{{ alumno.sexo === 'M' ? 'Masculino' : 'Femenino' }}</p>
              </div>
            </div>
          </div>

          <!-- Datos Académicos -->
          <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
            <h2 class="text-base sm:text-xl font-bold text-black mb-3 sm:mb-4">Datos Académicos</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
              <div>
                <label class="block text-xs sm:text-sm font-medium text-black mb-1">Grado</label>
                <p class="text-sm sm:text-base text-black bg-gray-50 px-3 sm:px-4 py-2 rounded-lg">{{ alumno.grado }}°</p>
              </div>
              <div>
                <label class="block text-xs sm:text-sm font-medium text-black mb-1">Grupo</label>
                <p class="text-sm sm:text-base text-black bg-gray-50 px-3 sm:px-4 py-2 rounded-lg">{{ alumno.grupo }}</p>
              </div>
              <div>
                <label class="block text-xs sm:text-sm font-medium text-black mb-1">Turno</label>
                <div class="text-sm sm:text-base text-black bg-gray-50 px-3 sm:px-4 py-2 rounded-lg">
                  <span v-if="alumno.turno" class="px-2 py-1 text-xs font-medium rounded-full" :class="{
                    'bg-yellow-100 text-yellow-800': alumno.turno === 'Matutino',
                    'bg-blue-100 text-blue-800': alumno.turno === 'Vespertino'
                  }">
                    {{ alumno.turno }}
                  </span>
                  <span v-else class="text-gray-400 text-xs">No especificado</span>
                </div>
              </div>
              <div>
                <label class="block text-xs sm:text-sm font-medium text-black mb-1">Generación</label>
                <p class="text-sm sm:text-base text-black bg-gray-50 px-3 sm:px-4 py-2 rounded-lg">{{ alumno.generacion }}</p>
              </div>
              <div>
                <label class="block text-xs sm:text-sm font-medium text-black mb-1">Matrícula</label>
                <p class="text-sm sm:text-base text-black bg-gray-50 px-3 sm:px-4 py-2 rounded-lg">{{ alumno.matricula }}</p>
              </div>
            </div>
          </div>

          <!-- Datos de Salud -->
          <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
            <h2 class="text-base sm:text-xl font-bold text-black mb-3 sm:mb-4">Datos de Salud</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
              <div>
                <label class="block text-xs sm:text-sm font-medium text-black mb-1">Tipo de Sangre</label>
                <p class="text-sm sm:text-base text-black bg-gray-50 px-3 sm:px-4 py-2 rounded-lg">{{ alumno.salud?.tipo_sangre || 'No especificado' }}</p>
              </div>
              <div>
                <label class="block text-xs sm:text-sm font-medium text-black mb-1">Alergias</label>
                <p class="text-sm sm:text-base text-black bg-gray-50 px-3 sm:px-4 py-2 rounded-lg break-words">{{ alumno.salud?.alergias || 'Ninguna' }}</p>
              </div>
              <div class="sm:col-span-2">
                <label class="block text-xs sm:text-sm font-medium text-black mb-1">Enfermedades Crónicas</label>
                <p class="text-sm sm:text-base text-black bg-gray-50 px-3 sm:px-4 py-2 rounded-lg break-words">{{ alumno.salud?.enfermedades_cronicas || 'Ninguna' }}</p>
              </div>
            </div>
          </div>

          <!-- Contactos -->
          <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
            <h2 class="text-base sm:text-xl font-bold text-black mb-3 sm:mb-4">Contactos</h2>
            <div class="space-y-3">
              <div v-if="alumno.contactos && alumno.contactos.length > 0">
                <div
                  v-for="contacto in alumno.contactos"
                  :key="contacto.id"
                  class="border border-gray-200 rounded-lg p-3 sm:p-4"
                  :class="{ 'border-red-300 bg-red-50': contacto.es_principal }"
                >
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <div class="flex flex-wrap items-center gap-2 mb-2">
                        <h3 class="text-sm sm:text-base font-semibold text-black">
                          {{ contacto.nombre_completo }}
                        </h3>
                        <span v-if="contacto.es_principal" class="px-2 py-0.5 bg-red-600 text-white text-xs rounded-full flex-shrink-0">
                          Principal
                        </span>
                        <span v-if="!contacto.activo" class="px-2 py-0.5 bg-gray-400 text-white text-xs rounded-full flex-shrink-0">
                          Inactivo
                        </span>
                      </div>
                      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs sm:text-sm text-black">
                        <div>
                          <span class="font-medium">Parentesco:</span> {{ contacto.parentesco }}
                        </div>
                        <div>
                          <span class="font-medium">Teléfono:</span> {{ contacto.telefono }}
                        </div>
                        <div class="break-all">
                          <span class="font-medium">Correo:</span> {{ contacto.correo || 'No especificado' }}
                        </div>
                        <div>
                          <span class="font-medium">Autorizado a recoger:</span>
                          {{ contacto.autorizado_recoger ? 'Sí' : 'No' }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-4 text-sm text-black">
                No hay contactos registrados
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </PrefectosLayout>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import PrefectosLayout from '../../../layouts/PrefectosLayout.vue'

// Props
const props = defineProps<{
  alumno: any
}>()

// Métodos
const volver = () => {
  router.visit('/prefectos/alumnos/lista')
}
</script>
