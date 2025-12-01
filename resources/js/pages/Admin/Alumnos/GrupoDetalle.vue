<template>
  <AdminLayout>
    <Head :title="`Grupo ${grupo.nombre}`" />
    
    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Header -->
      <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <button
              @click="router.visit('/admin/alumnos/grupos')"
              class="text-gray-600 hover:text-gray-900"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
              </svg>
            </button>
            <div>
              <h1 class="text-2xl font-bold text-gray-900">{{ grupo.nombre }}</h1>
              <p class="text-gray-600 mt-1">{{ alumnos.length }} alumnos inscritos</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Lista de Alumnos -->
      <div class="bg-white rounded-xl shadow">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                  Foto
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                  Matrícula
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                  Nombre Completo
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                  Estatus
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                  Acciones
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="alumno in alumnos" :key="alumno.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <img
                    :src="alumno.foto_url"
                    :alt="alumno.nombre_completo"
                    class="w-12 h-12 rounded-full object-cover"
                  />
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">
                  {{ alumno.matricula }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                  {{ alumno.nombre_completo }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="{
                      'bg-green-100 text-green-800': alumno.estatus === 'activo' || alumno.estatus === 'inscrito',
                      'bg-red-100 text-red-800': alumno.estatus === 'baja',
                      'bg-blue-100 text-blue-800': alumno.estatus === 'egresado',
                      'bg-gray-100 text-black': !['activo', 'inscrito', 'baja', 'egresado'].includes(alumno.estatus)
                    }"
                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                  >
                    {{ alumno.estatus || 'Sin estatus' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <button
                    @click="router.visit(`/admin/alumnos/${alumno.id}/perfil`)"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    Ver Perfil
                  </button>
                </td>
              </tr>

              <!-- Mensaje si no hay alumnos -->
              <tr v-if="alumnos.length === 0">
                <td colspan="5" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <p class="text-gray-500 text-lg">No hay alumnos en este grupo</p>
                    <p class="text-gray-400 text-sm mt-2">Los alumnos aparecerán aquí cuando sean asignados a este grupo</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '../../../layouts/AdminLayout.vue'

interface Alumno {
  id: number
  matricula: string
  nombre_completo: string
  foto_url: string
  estatus: string
}

interface Grupo {
  id: number
  grado: number
  clave: string
  turno: string
  nombre: string
}

interface Props {
  grupo: Grupo
  alumnos: Alumno[]
}

defineProps<Props>()
</script>
