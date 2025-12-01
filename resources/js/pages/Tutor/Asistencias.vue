<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

interface Asistencia {
    id: number;
    fecha: string;
    presente: boolean;
    justificada: boolean;
    observaciones: string | null;
    registrado_por: string | null;
}

interface AlumnoData {
    id: number;
    nombre_completo: string;
    matricula: string;
    grado: number | null;
    grupo: string | null;
}

const props = defineProps<{ 
    alumno: AlumnoData;
}>();

// Estado
const asistencias = ref<Asistencia[]>([]);
const cargando = ref(false);

// Filtros
const filtroEstado = ref<'todas' | 'presente' | 'ausente'>('todas');
const filtroJustificada = ref<'todas' | 'si' | 'no'>('todas');
const fechaInicio = ref('');
const fechaFin = ref('');

// Computed
const asistenciasFiltradas = computed(() => {
    let resultado = asistencias.value;

    // Filtrar por estado (presente/ausente)
    if (filtroEstado.value === 'presente') {
        resultado = resultado.filter(a => a.presente);
    } else if (filtroEstado.value === 'ausente') {
        resultado = resultado.filter(a => !a.presente);
    }

    // Filtrar por justificada (solo aplica a ausencias)
    if (filtroJustificada.value !== 'todas') {
        resultado = resultado.filter(a => {
            if (!a.presente) {
                return filtroJustificada.value === 'si' ? a.justificada : !a.justificada;
            }
            return true;
        });
    }

    // Filtrar por rango de fechas
    if (fechaInicio.value) {
        resultado = resultado.filter(a => a.fecha >= fechaInicio.value);
    }
    if (fechaFin.value) {
        resultado = resultado.filter(a => a.fecha <= fechaFin.value);
    }

    return resultado;
});

// Estadísticas
const estadisticas = computed(() => {
    const total = asistencias.value.length;
    const presentes = asistencias.value.filter(a => a.presente).length;
    const ausencias = asistencias.value.filter(a => !a.presente).length;
    const justificadas = asistencias.value.filter(a => !a.presente && a.justificada).length;
    const injustificadas = asistencias.value.filter(a => !a.presente && !a.justificada).length;
    
    const porcentajeAsistencia = total > 0 ? ((presentes / total) * 100).toFixed(1) : '0.0';

    return {
        total,
        presentes,
        ausencias,
        justificadas,
        injustificadas,
        porcentajeAsistencia
    };
});

// Métodos
const cargarAsistencias = async () => {
    cargando.value = true;
    try {
        const response = await axios.get('/tutor/asistencias/data');
        asistencias.value = response.data;
    } catch (error) {
        console.error('Error al cargar asistencias:', error);
    } finally {
        cargando.value = false;
    }
};

const limpiarFiltros = () => {
    filtroEstado.value = 'todas';
    filtroJustificada.value = 'todas';
    fechaInicio.value = '';
    fechaFin.value = '';
};

const formatearFecha = (fecha: string) => {
    if (!fecha) return 'Fecha no disponible';
    
    // Crear la fecha en zona horaria local
    const date = new Date(fecha);
    
    // Verificar si la fecha es válida
    if (isNaN(date.getTime())) {
        return 'Fecha inválida';
    }
    
    return date.toLocaleDateString('es-MX', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// Lifecycle
onMounted(() => {
    cargarAsistencias();
});
</script>

<template>
    <Head title="Consultar Asistencia - Portal de Padres" />
    
    <div class="min-h-screen" style="background: linear-gradient(135deg, #F5F5F5 0%, #E0E0E0 25%, #F5F5F5 50%, #E0E0E0 75%, #F5F5F5 100%);">
        <!-- Header -->
        <header class="fixed top-0 left-0 right-0 backdrop-blur-md shadow-xl z-50 border-b" style="background-color: rgba(198, 40, 40, 0.95); border-bottom-color: rgba(158, 158, 158, 0.3);">
            <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-2">
                    <!-- Logo y info -->
                    <div class="flex items-center space-x-3">
                        <Link :href="route('tutor.dashboard')" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            <img 
                                src="/images/logo-escuela.png" 
                                alt="Logo Escuela Técnica 40" 
                                class="h-8 w-8 sm:h-10 sm:w-10 object-contain"
                                onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiByeD0iNiIgZmlsbD0iI0ZGRkZGRiIvPgo8dGV4dCB4PSIyMCIgeT0iMjYiIGZpbGw9IiM5OTE5MWEiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSJib2xkIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5UNDQ8L3RleHQ+Cjwvc3ZnPgo='"
                            />
                            <div>
                                <h1 class="text-white font-medium text-xs sm:text-sm">Consultar Asistencia</h1>
                                <p class="text-sm sm:text-lg font-bold" style="color: #FDD835;">Técnica 40</p>
                            </div>
                        </Link>
                    </div>

                    <!-- Botón de logout -->
                    <Link 
                        :href="route('tutor.logout')"
                        method="post"
                        as="button"
                        class="font-medium hover:shadow-lg text-xs sm:text-sm transition-all duration-300 px-3 py-1.5 sm:px-4 sm:py-2 rounded-full flex items-center space-x-1 sm:space-x-2"
                        style="background-color: #FFFFFF; color: #424242;"
                    >
                        <span class="hidden sm:inline">Cerrar Sesión</span>
                        <span class="sm:hidden">Salir</span>
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
                <!-- Información del alumno -->
                <div class="mb-4 p-4 sm:p-6 rounded-2xl shadow-lg" style="background-color: rgba(255, 255, 255, 0.95);">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <h1 class="text-base sm:text-2xl font-bold mb-1 sm:mb-2" style="color: #212121;">
                                Registro de Asistencia
                            </h1>
                            <p class="text-sm sm:text-lg" style="color: #424242;">
                                <span class="block sm:inline">{{ alumno.nombre_completo }}</span>
                                <span class="block sm:inline sm:ml-0"> - {{ alumno.matricula }}</span>
                                <span v-if="alumno.grado && alumno.grupo" class="inline-block mt-1 sm:mt-0 sm:ml-2 text-xs sm:text-sm px-2 py-0.5 sm:px-3 sm:py-1 rounded-full" style="background-color: rgba(198, 40, 40, 0.1); color: #C62828;">
                                    {{ alumno.grado }}° {{ alumno.grupo }}
                                </span>
                            </p>
                        </div>
                        <Link 
                            :href="route('tutor.dashboard')"
                            class="px-4 py-2 sm:px-6 sm:py-3 rounded-lg font-semibold text-white hover:shadow-lg transition-all duration-300 flex items-center justify-center space-x-2 text-sm sm:text-base"
                            style="background-color: #C62828;"
                        >
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            <span>Regresar</span>
                        </Link>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-2 sm:gap-4 mb-4 sm:mb-6">
                    <!-- Total -->
                    <div class="p-3 sm:p-4 rounded-xl shadow-lg" style="background-color: rgba(255, 255, 255, 0.95);">
                        <p class="text-xs sm:text-sm font-semibold mb-0.5 sm:mb-1" style="color: #9E9E9E;">Total</p>
                        <p class="text-xl sm:text-2xl font-bold" style="color: #424242;">{{ estadisticas.total }}</p>
                    </div>

                    <!-- Presentes -->
                    <div class="p-3 sm:p-4 rounded-xl shadow-lg" style="background-color: rgba(76, 175, 80, 0.1);">
                        <p class="text-xs sm:text-sm font-semibold mb-0.5 sm:mb-1" style="color: #4CAF50;">Presentes</p>
                        <p class="text-xl sm:text-2xl font-bold" style="color: #4CAF50;">{{ estadisticas.presentes }}</p>
                    </div>

                    <!-- Ausencias -->
                    <div class="p-3 sm:p-4 rounded-xl shadow-lg" style="background-color: rgba(244, 67, 54, 0.1);">
                        <p class="text-xs sm:text-sm font-semibold mb-0.5 sm:mb-1" style="color: #F44336;">Ausencias</p>
                        <p class="text-xl sm:text-2xl font-bold" style="color: #F44336;">{{ estadisticas.ausencias }}</p>
                    </div>

                    <!-- Justificadas -->
                    <div class="p-3 sm:p-4 rounded-xl shadow-lg" style="background-color: rgba(255, 152, 0, 0.1);">
                        <p class="text-xs sm:text-sm font-semibold mb-0.5 sm:mb-1" style="color: #FF9800;">Justificadas</p>
                        <p class="text-xl sm:text-2xl font-bold" style="color: #FF9800;">{{ estadisticas.justificadas }}</p>
                    </div>

                    <!-- Porcentaje -->
                    <div class="p-3 sm:p-4 rounded-xl shadow-lg" style="background-color: rgba(33, 150, 243, 0.1);">
                        <p class="text-xs sm:text-sm font-semibold mb-0.5 sm:mb-1" style="color: #2196F3;">Porcentaje</p>
                        <p class="text-xl sm:text-2xl font-bold" style="color: #2196F3;">{{ estadisticas.porcentajeAsistencia }}%</p>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="mb-4 sm:mb-6 p-4 sm:p-6 rounded-2xl shadow-lg" style="background-color: rgba(255, 255, 255, 0.95);">
                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                        <h2 class="text-base sm:text-lg font-bold" style="color: #212121;">Filtros</h2>
                        <button
                            @click="limpiarFiltros"
                            class="text-xs sm:text-sm px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg hover:shadow-md transition-all"
                            style="background-color: rgba(158, 158, 158, 0.1); color: #424242;"
                        >
                            Limpiar filtros
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 sm:gap-4">
                        <!-- Filtro por estado -->
                        <div>
                            <label class="block text-xs sm:text-sm font-medium mb-1 sm:mb-2" style="color: #424242;">Estado</label>
                            <select
                                v-model="filtroEstado"
                                class="w-full px-3 py-1.5 sm:px-4 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                style="color: #424242;"
                            >
                                <option value="todas">Todas</option>
                                <option value="presente">Presentes</option>
                                <option value="ausente">Ausencias</option>
                            </select>
                        </div>

                        <!-- Filtro por justificada -->
                        <div>
                            <label class="block text-xs sm:text-sm font-medium mb-1 sm:mb-2" style="color: #424242;">Justificación</label>
                            <select
                                v-model="filtroJustificada"
                                class="w-full px-3 py-1.5 sm:px-4 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                style="color: #424242;"
                            >
                                <option value="todas">Todas</option>
                                <option value="si">Justificadas</option>
                                <option value="no">No justificadas</option>
                            </select>
                        </div>

                        <!-- Fecha inicio -->
                        <div>
                            <label class="block text-xs sm:text-sm font-medium mb-1 sm:mb-2" style="color: #424242;">Desde</label>
                            <input
                                v-model="fechaInicio"
                                type="date"
                                class="w-full px-3 py-1.5 sm:px-4 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                style="color: #424242;"
                            />
                        </div>

                        <!-- Fecha fin -->
                        <div>
                            <label class="block text-xs sm:text-sm font-medium mb-1 sm:mb-2" style="color: #424242;">Hasta</label>
                            <input
                                v-model="fechaFin"
                                type="date"
                                class="w-full px-3 py-1.5 sm:px-4 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                style="color: #424242;"
                            />
                        </div>
                    </div>
                </div>

                <!-- Lista de asistencias -->
                <div class="rounded-2xl shadow-lg overflow-hidden" style="background-color: rgba(255, 255, 255, 0.95);">
                    <div class="p-4 sm:p-6">
                        <h2 class="text-base sm:text-lg font-bold mb-3 sm:mb-4" style="color: #212121;">
                            Registro Detallado
                            <span class="ml-2 text-xs sm:text-sm font-normal" style="color: #9E9E9E;">
                                ({{ asistenciasFiltradas.length }} registros)
                            </span>
                        </h2>

                        <!-- Mensaje de carga -->
                        <div v-if="cargando" class="text-center py-8 sm:py-12">
                            <svg class="animate-spin h-10 w-10 sm:h-12 sm:w-12 mx-auto mb-3 sm:mb-4" style="color: #C62828;" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-sm sm:text-base" style="color: #9E9E9E;">Cargando asistencias...</p>
                        </div>

                        <!-- Sin registros -->
                        <div v-else-if="asistenciasFiltradas.length === 0" class="text-center py-8 sm:py-12">
                            <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-3 sm:mb-4" style="color: #9E9E9E;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-base sm:text-lg font-medium" style="color: #424242;">No hay registros de asistencia</p>
                            <p class="text-xs sm:text-sm" style="color: #9E9E9E;">Prueba ajustando los filtros</p>
                        </div>

                        <!-- Tabla de asistencias -->
                        <div v-else class="space-y-2 sm:space-y-3">
                            <div
                                v-for="asistencia in asistenciasFiltradas"
                                :key="asistencia.id"
                                class="p-3 sm:p-4 rounded-xl border-2 transition-all hover:shadow-md"
                                :style="{
                                    borderColor: asistencia.presente ? '#4CAF50' : (asistencia.justificada ? '#FF9800' : '#F44336'),
                                    backgroundColor: asistencia.presente ? 'rgba(76, 175, 80, 0.05)' : (asistencia.justificada ? 'rgba(255, 152, 0, 0.05)' : 'rgba(244, 67, 54, 0.05)')
                                }"
                            >
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 sm:gap-3">
                                    <!-- Fecha -->
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs sm:text-sm font-semibold mb-0.5 sm:mb-1" style="color: #9E9E9E;">Fecha</p>
                                        <p class="font-medium capitalize text-sm sm:text-base truncate" style="color: #424242;">{{ formatearFecha(asistencia.fecha) }}</p>
                                    </div>

                                    <!-- Estado -->
                                    <div class="flex-1">
                                        <p class="text-xs sm:text-sm font-semibold mb-0.5 sm:mb-1" style="color: #9E9E9E;">Estado</p>
                                        <span
                                            class="inline-block px-2 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-bold"
                                            :style="{
                                                backgroundColor: asistencia.presente ? 'rgba(76, 175, 80, 0.2)' : 'rgba(244, 67, 54, 0.2)',
                                                color: asistencia.presente ? '#4CAF50' : '#F44336'
                                            }"
                                        >
                                            {{ asistencia.presente ? 'Presente' : 'Ausencia' }}
                                        </span>
                                    </div>

                                    <!-- Justificación -->
                                    <div class="flex-1" v-if="!asistencia.presente">
                                        <p class="text-sm font-semibold mb-1" style="color: #9E9E9E;">Justificación</p>
                                        <span
                                            class="inline-block px-3 py-1 rounded-full text-sm font-bold"
                                            :style="{
                                                backgroundColor: asistencia.justificada ? 'rgba(255, 152, 0, 0.2)' : 'rgba(158, 158, 158, 0.2)',
                                                color: asistencia.justificada ? '#FF9800' : '#9E9E9E'
                                            }"
                                        >
                                            {{ asistencia.justificada ? 'Justificada' : 'No justificada' }}
                                        </span>
                                    </div>

                                    <!-- Observaciones -->
                                    <div class="flex-1" v-if="asistencia.observaciones">
                                        <p class="text-sm font-semibold mb-1" style="color: #9E9E9E;">Observaciones</p>
                                        <p class="text-sm" style="color: #424242;">{{ asistencia.observaciones }}</p>
                                    </div>
                                </div>
                            </div>
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
