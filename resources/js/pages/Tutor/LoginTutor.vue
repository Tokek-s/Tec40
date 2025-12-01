<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';

const form = useForm({
    curp: '',
    matricula: '',
});

const submit = () => {
    form.post(route('tutor.login.submit'), {
        onFinish: () => form.reset('matricula'),
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Portal de Padres - Acceso" />
    
    <div class="min-h-screen" style="background: linear-gradient(135deg, #F5F5F5 0%, #E0E0E0 25%, #F5F5F5 50%, #E0E0E0 75%, #F5F5F5 100%);">
        <!-- Header fijo -->
        <header class="fixed top-0 left-0 right-0 backdrop-blur-md shadow-xl z-50 border-b" style="background-color: rgba(198, 40, 40, 0.95); border-bottom-color: rgba(158, 158, 158, 0.3);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-3">
                    <!-- Logo y nombre de la escuela -->
                    <div class="flex items-center space-x-3">
                        <img 
                            src="/images/logo-escuela.png" 
                            alt="Logo Escuela Técnica 40" 
                            class="h-10 w-10 object-contain"
                            onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiByeD0iNiIgZmlsbD0iI0ZGRkZGRiIvPgo8dGV4dCB4PSIyMCIgeT0iMjYiIGZpbGw9IiM5OTE5MWEiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSJib2xkIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5UNDQ8L3RleHQ+Cjwvc3ZnPgo='"
                        />
                        <div>
                            <h1 class="text-white font-medium text-sm">Escuela Secundaria</h1>
                            <p class="text-lg font-bold" style="color: #FDD835;">Técnica 40</p>
                        </div>
                    </div>

                    <Link 
                        href="/" 
                        class="font-medium hover:shadow-lg text-sm transition-all duration-300 px-4 py-2 rounded-full flex items-center space-x-2 info-button"
                        style="background-color: #FFFFFF; color: #424242;"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span>Regresar al Menú</span>
                    </Link>
                </div>
            </div>
        </header>

        <div class="flex items-center justify-center min-h-screen px-4 py-12 pt-24">
            <div class="w-full max-w-lg">
                <div class="rounded-2xl shadow-2xl p-8 backdrop-blur-sm" style="background-color: rgba(255, 255, 255, 0.95);">
                    <div class="text-center mb-8">
                        <div class="mb-6">
                            <img 
                                src="/images/logo-escuela.png" 
                                alt="Logo Escuela Técnica 40" 
                                class="h-16 w-16 object-contain mx-auto"
                                onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjY0IiBoZWlnaHQ9IjY0IiByeD0iMTIiIGZpbGw9IiNDNjI4MjgiLz4KPHRleHQgeD0iMzIiIHk9IjQwIiBmaWxsPSJ3aGl0ZSIgZm9udC1zaXplPSIyNCIgZm9udC13ZWlnaHQ9ImJvbGQiIHRleHQtYW5jaG9yPSJtaWRkbGUiPlQ0PC90ZXh0Pgo8L3N2Zz4K'"
                            />
                        </div>
                        <h1 class="text-3xl font-bold mb-3" style="color: #212121;">
                            Portal de Padres
                        </h1>
                        <p class="text-lg" style="color: #424242;">
                            Consulta información académica de tu hijo/a
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- CURP -->
                        <div>
                            <label for="curp" class="block text-sm font-semibold mb-2" style="color: #424242;">
                                CURP del Estudiante
                            </label>
                            <input
                                id="curp"
                                v-model="form.curp"
                                type="text"
                                required
                                maxlength="18"
                                autocomplete="off"
                                class="w-full px-4 py-3 border rounded-lg transition-all duration-300 focus:outline-none focus:ring-2"
                                style="border-color: #9E9E9E; color: #212121; &:focus { border-color: #C62828; box-shadow: 0 0 0 2px rgba(198, 40, 40, 0.2); }"
                                placeholder="CURP de 18 caracteres"
                                @input="form.curp = (($event.target as HTMLInputElement)?.value || '').toUpperCase()"
                            />
                            <div v-if="form.errors.curp" class="mt-2 text-sm" style="color: #C62828;">
                                {{ form.errors.curp }}
                            </div>
                            <p class="mt-1 text-xs" style="color: #9E9E9E;">
                                Ejemplo: ABCD123456HDFXXX09
                            </p>
                        </div>

                        <!-- Matrícula -->
                        <div>
                            <label for="matricula" class="block text-sm font-semibold mb-2" style="color: #424242;">
                                Matrícula del Estudiante
                            </label>
                            <input
                                id="matricula"
                                v-model="form.matricula"
                                type="text"
                                required
                                maxlength="20"
                                autocomplete="off"
                                class="w-full px-4 py-3 border rounded-lg transition-all duration-300 focus:outline-none focus:ring-2"
                                style="border-color: #9E9E9E; color: #212121; &:focus { border-color: #C62828; box-shadow: 0 0 0 2px rgba(198, 40, 40, 0.2); }"
                                placeholder="Matrícula asignada"
                                @input="form.matricula = (($event.target as HTMLInputElement)?.value || '').toUpperCase()"
                            />
                            <div v-if="form.errors.matricula" class="mt-2 text-sm" style="color: #C62828;">
                                {{ form.errors.matricula }}
                            </div>
                            <p class="mt-1 text-xs" style="color: #9E9E9E;">
                                Matrícula proporcionada por la institución
                            </p>
                        </div>

                        <!-- Botón de submit -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-4 px-6 rounded-lg font-semibold text-white transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed transform hover:scale-105"
                            style="background-color: #C62828; &:hover { background-color: #B71C1C; } &:focus { box-shadow: 0 0 0 2px rgba(198, 40, 40, 0.5); }"
                        >
                            <div v-if="form.processing" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                                Verificando información...
                            </div>
                            <span v-else>Acceder</span>
                        </button>
                    </form>

 
                    <div class="mt-8 p-4 rounded-xl" style="background-color: rgba(158, 158, 158, 0.1); border: 1px solid rgba(158, 158, 158, 0.2);">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #9E9E9E;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-sm mb-1" style="color: #424242;">
                                    Información Importante
                                </h4>
                                <p class="text-xs leading-relaxed" style="color: #9E9E9E;">
                                    Solo se verificarán las credenciales del estudiante para acceso seguro. 
                                    No se almacenan datos adicionales durante la consulta.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Ayuda y contacto -->
                    <div class="mt-6 pt-6" style="border-top: 1px solid rgba(158, 158, 158, 0.2);">
                        <div class="text-center">
                            <p class="text-sm font-medium mb-3" style="color: #424242;">
                                ¿Necesitas ayuda?
                            </p>
                            <div class="space-y-2 text-xs" style="color: #9E9E9E;">
                                <div class="flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span>Administración escolar: 771 338 3165</span>
                                </div>
                                <div class="flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span>contacto@tecnica40.edu.mx</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información sobre el portal -->
                <div class="mt-8 p-6 rounded-2xl text-white text-center" style="background-color: rgba(198, 40, 40, 0.8); backdrop-filter: blur(10px);">
                    <h3 class="font-bold text-lg mb-4" style="color: #FDD835;">
                        ¿Qué información puedes consultar?
                    </h3>
                    <div class="flex justify-center space-x-8">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #FDD835;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Asistencia diaria</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #FDD835;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM15 17H9a4 4 0 01-4-4V7a4 4 0 014-4h2a4 4 0 014 4v6a4 4 0 01-4 4z"/>
                            </svg>
                            <span>Avisos importantes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

.info-button:hover {
    background-color: #E0E0E0 !important;
    color: #212121 !important;
    transform: translateY(-1px);
}


html {
    scroll-behavior: smooth;
}
</style>
