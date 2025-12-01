<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

interface AlumnoData {
    id: number;
    curp: string;
    matricula: string;
    nombre_completo: string;
    grado: number | null;
    grupo: string | null;
    turno: string | null;
    generacion: string;
    estatus: string;
    tipo_sangre: string | null;
    foto_url: string | null;
    alergias: string | null;
    telefono_tutor: string | null;
}

const props = defineProps<{ alumno: AlumnoData }>();
const alumno = props.alumno;
</script>

<template>
    <Head title="Portal de Padres - Dashboard" />
    
    <div class="min-h-screen" style="background: linear-gradient(135deg, #F5F5F5 0%, #E0E0E0 25%, #F5F5F5 50%, #E0E0E0 75%, #F5F5F5 100%);">
        <!-- Header -->
        <header class="fixed top-0 left-0 right-0 backdrop-blur-md shadow-xl z-50 border-b" style="background-color: rgba(198, 40, 40, 0.95); border-bottom-color: rgba(158, 158, 158, 0.3);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-3">
                    <!-- Logo y info -->
                    <div class="flex items-center space-x-3">
                        <img 
                            src="/images/logo-escuela.png" 
                            alt="Logo Escuela Técnica 40" 
                            class="h-10 w-10 object-contain"
                            onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiByeD0iNiIgZmlsbD0iI0ZGRkZGRiIvPgo8dGV4dCB4PSIyMCIgeT0iMjYiIGZpbGw9IiM5OTE5MWEiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSJib2xkIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5UNDQ8L3RleHQ+Cjwvc3ZnPgo='"
                        />
                        <div>
                            <h1 class="text-white font-medium text-sm">Portal de Padres</h1>
                            <p class="text-lg font-bold" style="color: #FDD835;">Técnica 40</p>
                        </div>
                    </div>

                    <!-- Botón de logout -->
                    <Link 
                        :href="route('tutor.logout')"
                        method="post"
                        as="button"
                        class="font-medium hover:shadow-lg text-sm transition-all duration-300 px-4 py-2 rounded-full flex items-center space-x-2"
                        style="background-color: #FFFFFF; color: #424242;"
                    >
                        <span>Cerrar Sesión</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </Link>
                </div>
            </div>
        </header>

        <!-- Contenido principal -->
        <main class="pt-20 px-4 py-8">
            <div class="max-w-6xl mx-auto">
                <!-- Bienvenida -->
                <div class="mb-8 p-6 rounded-2xl shadow-lg" style="background-color: rgba(255, 255, 255, 0.95);">
                    <h1 class="text-3xl font-bold mb-2" style="color: #212121;">
                        Bienvenido al Portal de Padres
                    </h1>
                    <p class="text-lg" style="color: #424242;">
                        Información académica de {{ alumno.nombre_completo }}
                    </p>
                </div>

                <!-- Información del estudiante -->
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <!-- Datos del estudiante -->
                    <div class="p-6 rounded-2xl shadow-lg" style="background-color: rgba(255, 255, 255, 0.95);">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-4" style="background-color: rgba(198, 40, 40, 0.1);">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #C62828;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold" style="color: #212121;">Información del Estudiante</h2>
                        </div>
                        
                        <!-- Foto del alumno -->
                        <div class="flex justify-center mb-4">
                            <img 
                                v-if="alumno.foto_url"
                                :src="alumno.foto_url" 
                                :alt="alumno.nombre_completo"
                                class="w-24 h-24 rounded-full object-cover border-4"
                                style="border-color: #C62828;"
                            />
                            <div v-else class="w-24 h-24 rounded-full border-4 flex items-center justify-center" style="background-color: #E0E0E0; border-color: #C62828;">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-semibold block mb-1" style="color: #9E9E9E;">Nombre Completo</label>
                                <p class="text-lg font-medium" style="color: #424242;">{{ alumno.nombre_completo }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-semibold block mb-1" style="color: #9E9E9E;">Matrícula</label>
                                    <p class="font-medium" style="color: #424242;">{{ alumno.matricula }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold block mb-1" style="color: #9E9E9E;">Grado/Grupo</label>
                                    <p class="font-medium" style="color: #424242;">
                                        <template v-if="alumno.grado && alumno.grupo">
                                            {{ alumno.grado }}° {{ alumno.grupo }}
                                        </template>
                                        <template v-else>
                                            <span style="color: #F44336;">Sin asignar</span>
                                        </template>
                                    </p>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-semibold block mb-1" style="color: #9E9E9E;">Generación</label>
                                <p class="font-medium" style="color: #424242;">{{ alumno.generacion }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Estado académico -->
                    <div class="p-6 rounded-2xl shadow-lg" style="background-color: rgba(255, 255, 255, 0.95);">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-4" style="background-color: rgba(76, 175, 80, 0.1);">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #4CAF50;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold" style="color: #212121;">Estado Académico</h2>
                        </div>
                        
                        <div class="text-center py-6">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4" style="background-color: rgba(76, 175, 80, 0.1);">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #4CAF50;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold mb-2" style="color: #4CAF50;">ACTIVO</p>
                            <p class="text-sm" style="color: #9E9E9E;">El estudiante se encuentra matriculado y activo en el ciclo escolar actual</p>
                        </div>
                    </div>
                </div>

                <!-- Funcionalidades disponibles -->
                <div class="grid md:grid-cols-3 gap-6">
                    <!-- Asistencia -->
                    <Link :href="route('tutor.asistencias')" class="p-6 rounded-2xl shadow-lg cursor-pointer transition-all duration-300 hover:shadow-xl hover:transform hover:scale-105" style="background-color: rgba(255, 255, 255, 0.95);">
                        <div class="text-center">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: rgba(33, 150, 243, 0.1);">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #2196F3;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold mb-2" style="color: #212121;">Consultar Asistencia</h3>
                            <p class="text-sm mb-4" style="color: #9E9E9E;">Revisa el registro de asistencia diaria</p>
                            <span class="inline-block px-4 py-2 rounded-lg text-sm font-semibold" style="background-color: rgba(33, 150, 243, 0.2); color: #2196F3;">
                                Disponible
                            </span>
                        </div>
                    </Link>

                    <!-- Avisos -->
                    <Link :href="route('tutor.anuncios')" class="p-6 rounded-2xl shadow-lg cursor-pointer transition-all duration-300 hover:shadow-xl hover:transform hover:scale-105" style="background-color: rgba(255, 255, 255, 0.95);">
                        <div class="text-center">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: rgba(255, 152, 0, 0.1);">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #FF9800;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM15 17H9a4 4 0 01-4-4V7a4 4 0 014-4h2a4 4 0 014 4v6a4 4 0 01-4 4z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold mb-2" style="color: #212121;">Avisos Importantes</h3>
                            <p class="text-sm mb-4" style="color: #9E9E9E;">Comunicados dirigidos a padres de familia</p>
                            <span class="inline-block px-4 py-2 rounded-lg text-sm font-semibold" style="background-color: rgba(255, 152, 0, 0.2); color: #FF9800;">
                                Disponible
                            </span>
                        </div>
                    </Link>

                    <!-- Contacto -->
                    <div class="p-6 rounded-2xl shadow-lg cursor-pointer transition-all duration-300 hover:shadow-xl hover:transform hover:scale-105" style="background-color: rgba(255, 255, 255, 0.95);">
                        <div class="text-center">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: rgba(76, 175, 80, 0.1);">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #4CAF50;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold mb-2" style="color: #212121;">Contactar Escuela</h3>
                            <p class="text-sm mb-4" style="color: #9E9E9E;">Información de contacto directo</p>
                            <span class="inline-block px-4 py-2 rounded-lg text-sm font-semibold" style="background-color: rgba(76, 175, 80, 0.1); color: #4CAF50;">
                                Disponible
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Información de contacto -->
                <div class="mt-8 p-6 rounded-2xl text-white" style="background-color: rgba(198, 40, 40, 0.9);">
                    <h3 class="text-xl font-bold mb-4" style="color: #FDD835;">Información de Contacto</h3>
                    <div class="grid md:grid-cols-3 gap-4 text-sm">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #FDD835;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>771 338 3165</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #FDD835;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>contacto@tecnica40.edu.mx</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #FDD835;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Lun-Vie: 7:00 AM - 7:30 PM</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
html {
    scroll-behavior: smooth;
}
</style>
