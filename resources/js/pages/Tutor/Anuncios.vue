<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ref, computed } from 'vue';

interface Anuncio {
    id: number;
    titulo: string;
    contenido: string;
    fecha: string;
    ruta_imagen: string | null;
    image_url: string | null;
}

interface AlumnoData {
    id: number;
    nombre_completo: string;
    matricula: string;
}

const props = defineProps<{ 
    anuncios: Anuncio[];
    alumno: AlumnoData;
}>();

// Filtros
const busqueda = ref('');
const mesSeleccionado = ref('');

// Computed
const anunciosFiltrados = computed(() => {
    let resultado = props.anuncios;

    // Filtrar por búsqueda
    if (busqueda.value.trim()) {
        const termino = busqueda.value.toLowerCase();
        resultado = resultado.filter(a => 
            a.titulo.toLowerCase().includes(termino) || 
            a.contenido.toLowerCase().includes(termino)
        );
    }

    // Filtrar por mes
    if (mesSeleccionado.value) {
        resultado = resultado.filter(a => a.fecha.substring(0, 7) === mesSeleccionado.value);
    }

    return resultado;
});

// Obtener meses únicos de los anuncios
const mesesDisponibles = computed(() => {
    const meses = new Set<string>();
    props.anuncios.forEach(a => {
        meses.add(a.fecha.substring(0, 7));
    });
    return Array.from(meses).sort().reverse();
});

// Métodos
const formatearFecha = (fecha: string) => {
    const date = new Date(fecha + 'T00:00:00');
    return date.toLocaleDateString('es-MX', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatearMes = (mesAno: string) => {
    const [year, month] = mesAno.split('-');
    const date = new Date(parseInt(year), parseInt(month) - 1);
    return date.toLocaleDateString('es-MX', {
        year: 'numeric',
        month: 'long'
    });
};

const limpiarFiltros = () => {
    busqueda.value = '';
    mesSeleccionado.value = '';
};
</script>

<template>
    <Head title="Avisos Importantes - Portal de Padres" />
    
    <div class="min-h-screen" style="background: linear-gradient(135deg, #F5F5F5 0%, #E0E0E0 25%, #F5F5F5 50%, #E0E0E0 75%, #F5F5F5 100%);">
        <!-- Header -->
        <header class="fixed top-0 left-0 right-0 backdrop-blur-md shadow-xl z-50 border-b" style="background-color: rgba(198, 40, 40, 0.95); border-bottom-color: rgba(158, 158, 158, 0.3);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-3">
                    <!-- Logo y info -->
                    <div class="flex items-center space-x-3">
                        <Link :href="route('tutor.dashboard')" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            <img 
                                src="/images/logo-escuela.png" 
                                alt="Logo Escuela Técnica 40" 
                                class="h-10 w-10 object-contain"
                                onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiByeD0iNiIgZmlsbD0iI0ZGRkZGRiIvPgo8dGV4dCB4PSIyMCIgeT0iMjYiIGZpbGw9IiM5OTE5MWEiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSJib2xkIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5UNDQ8L3RleHQ+Cjwvc3ZnPgo='"
                            />
                            <div>
                                <h1 class="text-white font-medium text-sm">Avisos Importantes</h1>
                                <p class="text-lg font-bold" style="color: #FDD835;">Técnica 40</p>
                            </div>
                        </Link>
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
            <div class="max-w-7xl mx-auto">
                <!-- Encabezado -->
                <div class="mb-6 p-6 rounded-2xl shadow-lg" style="background-color: rgba(255, 255, 255, 0.95);">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h1 class="text-2xl font-bold mb-2" style="color: #212121;">
                                Avisos Importantes
                            </h1>
                            <p class="text-lg" style="color: #424242;">
                                {{ alumno.nombre_completo }} - {{ alumno.matricula }}
                            </p>
                        </div>
                        <Link 
                            :href="route('tutor.dashboard')"
                            class="px-6 py-3 rounded-lg font-semibold text-white hover:shadow-lg transition-all duration-300 flex items-center space-x-2"
                            style="background-color: #C62828;"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            <span>Regresar</span>
                        </Link>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="mb-6 p-6 rounded-2xl shadow-lg" style="background-color: rgba(255, 255, 255, 0.95);">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold" style="color: #212121;">Filtros</h2>
                        <button
                            @click="limpiarFiltros"
                            class="text-sm px-4 py-2 rounded-lg hover:shadow-md transition-all"
                            style="background-color: rgba(158, 158, 158, 0.1); color: #424242;"
                        >
                            Limpiar filtros
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Búsqueda -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #424242;">Buscar</label>
                            <input
                                v-model="busqueda"
                                type="text"
                                placeholder="Buscar por título o contenido..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                style="color: #424242;"
                            />
                        </div>

                        <!-- Filtro por mes -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #424242;">Mes</label>
                            <select
                                v-model="mesSeleccionado"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                style="color: #424242;"
                            >
                                <option value="">Todos los meses</option>
                                <option v-for="mes in mesesDisponibles" :key="mes" :value="mes">
                                    {{ formatearMes(mes) }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contador de anuncios -->
                <div class="mb-6">
                    <p class="text-lg font-semibold" style="color: #424242;">
                        {{ anunciosFiltrados.length }} {{ anunciosFiltrados.length === 1 ? 'anuncio encontrado' : 'anuncios encontrados' }}
                    </p>
                </div>

                <!-- Lista de anuncios -->
                <div v-if="anunciosFiltrados.length === 0" class="text-center py-16 rounded-2xl shadow-lg" style="background-color: rgba(255, 255, 255, 0.95);">
                    <svg class="w-20 h-20 mx-auto mb-4" style="color: #9E9E9E;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h3 class="text-xl font-bold mb-2" style="color: #424242;">No hay anuncios disponibles</h3>
                    <p class="text-sm" style="color: #9E9E9E;">No se encontraron anuncios que coincidan con los filtros seleccionados</p>
                </div>

                <div v-else class="space-y-6">
                    <div
                        v-for="anuncio in anunciosFiltrados"
                        :key="anuncio.id"
                        class="p-6 rounded-2xl shadow-lg transition-all hover:shadow-xl"
                        style="background-color: rgba(255, 255, 255, 0.95);"
                    >
                        <!-- Fecha -->
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3" style="background-color: rgba(255, 152, 0, 0.1);">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #FF9800;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-semibold capitalize" style="color: #FF9800;">
                                {{ formatearFecha(anuncio.fecha) }}
                            </span>
                        </div>

                        <!-- Título -->
                        <h3 class="text-xl font-bold mb-3" style="color: #212121;">
                            {{ anuncio.titulo }}
                        </h3>

                        <!-- Imagen (si existe) -->
                        <div v-if="anuncio.image_url" class="mb-4">
                            <img 
                                :src="anuncio.image_url" 
                                :alt="anuncio.titulo"
                                class="w-full max-h-96 object-cover rounded-xl"
                            />
                        </div>

                        <!-- Contenido -->
                        <div class="prose max-w-none">
                            <p class="text-base whitespace-pre-wrap" style="color: #424242;">
                                {{ anuncio.contenido }}
                            </p>
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
