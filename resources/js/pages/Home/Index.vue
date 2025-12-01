<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { route } from 'ziggy-js';

interface Anuncio {
    id: number;
    titulo: string;
    contenido: string;
    ruta_imagen?: string;
    image_url?: string;
    fecha: string;
    activo: number;
}

interface Cuestionario {
    id: number;
    titulo: string;
    descripcion?: string;
    fecha_inicio: string;
    fecha_fin: string;
    // Desde la API puede venir como 0/1 o booleano
    activo: number | boolean;
    primero_activo: number | boolean;
    segundo_activo: number | boolean;
    tercero_activo: number | boolean;
    vigente?: boolean;
    link_primero?: string;
    link_segundo?: string;
    link_tercero?: string;
}

interface Props {
    anuncios: Anuncio[];
    cuestionarios: Cuestionario[];
    cuestionariosExternos: Cuestionario[];
}

const { anuncios, cuestionarios, cuestionariosExternos } = defineProps<Props>();

// Carrusel de anuncios
const currentSlide = ref(0);
let slideInterval: ReturnType<typeof setInterval>;

const nextSlide = () => {
    const anunciosActivos = anuncios.filter(anuncio => {
        if (!isTruthy(anuncio.activo)) return false;
        const fechaAnuncio = parseFecha(anuncio.fecha);
        if (!fechaAnuncio) return true;
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        fechaAnuncio.setHours(0, 0, 0, 0);
        const diasDiferencia = Math.floor((hoy.getTime() - fechaAnuncio.getTime()) / (1000 * 60 * 60 * 24));
        return diasDiferencia >= 0 && diasDiferencia <= 30;
    });
    
    if (anunciosActivos.length > 0) {
        currentSlide.value = (currentSlide.value + 1) % anunciosActivos.length;
    }
};

const prevSlide = () => {
    const anunciosActivos = anuncios.filter(anuncio => {
        if (!isTruthy(anuncio.activo)) return false;
        const fechaAnuncio = parseFecha(anuncio.fecha);
        if (!fechaAnuncio) return true;
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        fechaAnuncio.setHours(0, 0, 0, 0);
        const diasDiferencia = Math.floor((hoy.getTime() - fechaAnuncio.getTime()) / (1000 * 60 * 60 * 24));
        return diasDiferencia >= 0 && diasDiferencia <= 30;
    });
    
    if (anunciosActivos.length > 0) {
        currentSlide.value = currentSlide.value === 0 ? anunciosActivos.length - 1 : currentSlide.value - 1;
    }
};

const goToSlide = (index: number) => {
    currentSlide.value = index;
};

onMounted(() => {
    const anunciosActivos = anuncios.filter(anuncio => {
        if (!isTruthy(anuncio.activo)) return false;
        const fechaAnuncio = parseFecha(anuncio.fecha);
        if (!fechaAnuncio) return true;
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        fechaAnuncio.setHours(0, 0, 0, 0);
        const diasDiferencia = Math.floor((hoy.getTime() - fechaAnuncio.getTime()) / (1000 * 60 * 60 * 24));
        return diasDiferencia >= 0 && diasDiferencia <= 30;
    });
    
    if (anunciosActivos.length > 1) {
        slideInterval = setInterval(nextSlide, 5000); 
    }
});

onUnmounted(() => {
    if (slideInterval) {
        clearInterval(slideInterval);
    }
});

// Helpers: formatear fechas y normalizar booleanos
const parseFecha = (s: string) => {
    // Soporta: dd/mm/yyyy, yyyy-mm-dd, ISO con T y microsegundos
    if (!s) return null;
    // dd/mm/yyyy
    if (s.includes('/')) {
        const partes = s.split('/');
        if (partes.length === 3) {
            return new Date(parseInt(partes[2]), parseInt(partes[1]) - 1, parseInt(partes[0]));
        }
    }
    // ISO: si ya trae 'T', úsalo directo; si no, agrega 'T00:00:00'
    const iso = s.includes('T') ? s : `${s}T00:00:00`;
    const d = new Date(iso);
    return isNaN(d.getTime()) ? null : d;
};

const formatearFecha = (fechaString: string) => {
    const d = parseFecha(fechaString);
    if (!d) return fechaString || '';
    return d.toLocaleDateString('es-MX', { day: 'numeric', month: 'long' });
};

const formatearFechaCompleta = (fechaString: string) => {
    const d = parseFecha(fechaString);
    if (!d) return fechaString || '';
    return d.toLocaleDateString('es-MX', { day: 'numeric', month: 'long', year: 'numeric' });
};

const isTruthy = (v: unknown) => v === true || v === 1 || v === '1';

// Filtrar solo cuestionarios externos activos y vigentes
const cuestionariosExternosActivos = cuestionariosExternos.filter(c => 
    isTruthy(c.activo) && c.vigente
);

// Filtrar solo anuncios activos y que no hayan pasado de fecha
const anunciosActivos = anuncios.filter(anuncio => {
    if (!isTruthy(anuncio.activo)) return false;
    
    const fechaAnuncio = parseFecha(anuncio.fecha);
    if (!fechaAnuncio) return true; // Si no tiene fecha válida, mostrarlo
    
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    fechaAnuncio.setHours(0, 0, 0, 0);
    
    // Mostrar solo anuncios de hoy o anteriores (no futuros)
    // y que no tengan más de 30 días de antigüedad
    const diasDiferencia = Math.floor((hoy.getTime() - fechaAnuncio.getTime()) / (1000 * 60 * 60 * 24));
    return diasDiferencia >= 0 && diasDiferencia <= 30;
});

const escuelaInfo = {
    nombre: "Escuela Secundaria Técnica 40",
    descripcion: "Educamos integralmente brindando herramientas educativas, deportivas, científicas y éticas para su vida ciudadana.",
    mision: "Formar estudiantes competentes y comprometidos con su comunidad",
    vision: "Ser una institución educativa de excelencia que contribuya al desarrollo integral de nuestros estudiantes",
    valores: [
        "Responsabilidad",
        "Respeto", 
        "Honestidad",
        "Compromiso",
        "Excelencia"
    ]
};
</script>

<template>
    <Head title="Escuela Secundaria Técnica 40" />
    
    <div class="min-h-screen" style="background: linear-gradient(135deg, #C62828 0%, #B71C1C 25%, #C62828 50%, #B71C1C 75%, #C62828 100%);">
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

                    <!-- Navegación principal -->
                    <nav class="hidden md:flex items-center space-x-6">
                        <a href="#inicio" class="text-white font-medium text-sm transition-colors duration-300">
                            Inicio
                        </a>
                        <a href="#anuncios" class="text-white font-medium text-sm transition-colors duration-300">
                            Anuncios
                        </a>
                        <a href="#informacion" class="text-white font-medium text-sm transition-colors duration-300">
                            Información
                        </a>
                        <a href="#inscripciones" class="text-white font-medium text-sm transition-colors duration-300">
                            Inscripciones
                        </a>
                        <a href="#formularios" class="text-white font-medium text-sm transition-colors duration-300">
                            Formularios
                        </a>
                        <a href="#contacto" class="text-white font-medium text-sm transition-colors duration-300">
                            Contacto
                        </a>
                    </nav>

                    <!-- Botón Info Asistencia -->
                    <Link 
                        :href="route('tutor.login')"
                        class="font-medium hover:shadow-lg text-sm transition-all duration-300 px-4 py-2 rounded-full info-button"
                        style="background-color: #FFFFFF; color: #424242;"
                    >
                        Info Asistencia
                    </Link>
                </div>
            </div>
        </header>

        <!-- Contenido principal -->
        <main class="pt-16">
            <section id="inicio" class="py-16 text-center text-white relative">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-20 left-10 w-32 h-32 bg-white/5 rounded-full"></div>
                    <div class="absolute top-40 right-16 w-24 h-24 bg-white/5 rounded-full"></div>
                    <div class="absolute bottom-32 left-1/4 w-16 h-16 bg-white/5 rounded-full"></div>
                </div>
                
                <div class="max-w-5xl mx-auto px-4 relative z-10">
                    <div class="mb-8">
                        <span class="text-sm font-medium px-4 py-2 rounded-full" style="color: #212121; background-color: #FDD835;">
                            Aprender, Crear y Transformar
                        </span>
                    </div>
                    <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                        Escuela Secundaria<br>
                        <span class="font-serif italic" style="color: #FDD835;">Técnica 40</span>
                    </h1>
                    <p class="text-lg md:text-xl mb-8 max-w-3xl mx-auto leading-relaxed" style="color: rgba(255, 255, 255, 0.9);">
                        {{ escuelaInfo.descripcion }}
                    </p>
                </div>
            </section>

            <!-- Sección de Anuncios -->
            <section id="anuncios" class="py-16" style="background-color: #F7F7F7;">
                <div class="max-w-6xl mx-auto px-4">
                    <h2 class="text-4xl font-bold text-center mb-12" style="color: #212121;">
                        Anuncios Escolares
                    </h2>
                    
                    <div v-if="anunciosActivos.length > 0" class="relative">
                        <!-- Carrusel -->
                        <div class="relative h-80 overflow-hidden rounded-xl shadow-2xl" style="background: linear-gradient(to right, #FFFFFF, #F7F7F7);">
                            <!-- Slides container -->
                            <div class="relative w-full h-full">
                                <div 
                                    v-for="(anuncio, index) in anunciosActivos" 
                                    :key="anuncio.id"
                                    :class="[
                                        'absolute inset-0 w-full h-full transition-all duration-500 ease-in-out',
                                        index === currentSlide ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-full'
                                    ]"
                                    :style="{ 
                                        transform: `translateX(${(index - currentSlide) * 100}%)`,
                                        zIndex: index === currentSlide ? 10 : 1
                                    }"
                                >
                                    <div class="flex h-full">
                                        <div v-if="anuncio.image_url || anuncio.ruta_imagen" class="w-2/5" style="background: linear-gradient(to bottom right, #F7F7F7, #E0E0E0);">
                                            <img 
                                                :src="anuncio.image_url || anuncio.ruta_imagen" 
                                                :alt="anuncio.titulo"
                                                class="w-full h-full object-cover"
                                            />
                                        </div>
                                        
                                        <div :class="(anuncio.image_url || anuncio.ruta_imagen) ? 'w-3/5' : 'w-full'" class="p-8 flex flex-col justify-center" style="background-color: #FFFFFF;">
                                            <div class="mb-2">
                                                <span class="text-xs font-semibold px-3 py-1 rounded-full" style="color: white; background-color: #C62828;">
                                                    ANUNCIO OFICIAL
                                                </span>
                                            </div>
                                            <h3 class="text-2xl font-bold mb-4 leading-tight" style="color: #212121;">
                                                {{ anuncio.titulo }}
                                            </h3>
                                            <p class="text-base leading-relaxed mb-6" style="color: #424242;">
                                                {{ anuncio.contenido }}
                                            </p>
                                            <div class="mt-auto">
                                                <span class="text-sm flex items-center" style="color: #9E9E9E;">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    {{ formatearFechaCompleta(anuncio.fecha) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Controles del carrusel -->
                        <button 
                            @click="prevSlide"
                            class="absolute left-4 top-1/2 transform -translate-y-1/2 p-3 rounded-full shadow-xl transition-all hover:scale-110 z-20 carousel-control"
                            style="background-color: rgba(255, 255, 255, 0.95); color: #C62828;"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button 
                            @click="nextSlide"
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 p-3 rounded-full shadow-xl transition-all hover:scale-110 z-20 carousel-control"
                            style="background-color: rgba(255, 255, 255, 0.95); color: #C62828;"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>

                        <!-- Indicadores -->
                        <div class="flex justify-center space-x-2 mt-8">
                            <button
                                v-for="(anuncio, index) in anunciosActivos"
                                :key="anuncio.id"
                                @click="goToSlide(index)"
                                :class="[
                                    'transition-all duration-300',
                                    index === currentSlide 
                                        ? 'w-8 h-3 rounded-full' 
                                        : 'w-3 h-3 rounded-full'
                                ]"
                                :style="{
                                    backgroundColor: index === currentSlide ? '#C62828' : '#9E9E9E'
                                }"
                            />
                        </div>
                    </div>
                    
                    <div v-else class="text-center py-12">
                        <div class="max-w-md mx-auto">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                            <p class="text-gray-500 text-lg">No hay anuncios disponibles en este momento.</p>
                            <p class="text-gray-400 text-sm mt-2">Mantente atento para futuras actualizaciones.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Sección de Información de la Escuela -->
            <section id="informacion" class="py-16" style="background: linear-gradient(to bottom right, #FFFFFF, #F7F7F7);">
                <div class="max-w-6xl mx-auto px-4">
                    <div class="text-center mb-16">
                        <h2 class="text-4xl font-bold mb-4" style="color: #212121;">
                            Información de la Escuela
                        </h2>
                        <p class="text-lg max-w-2xl mx-auto" style="color: #424242;">
                            Conoce más sobre nuestra institución, nuestros valores y compromiso con la educación de calidad.
                        </p>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-8 lg:gap-12">
                        <!-- Misión y Visión -->
                        <div class="space-y-8">
                            <div class="p-8 rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl" style="background-color: #FFFFFF; border: 1px solid rgba(198, 40, 40, 0.2);">
                                <div class="flex items-center mb-4">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-4" style="background-color: rgba(198, 40, 40, 0.1);">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #C62828;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold" style="color: #C62828;">Nuestra Misión</h3>
                                </div>
                                <p class="leading-relaxed" style="color: #424242;">{{ escuelaInfo.mision }}</p>
                            </div>
                            
                            <div class="p-8 rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl" style="background-color: #FFFFFF; border: 1px solid rgba(158, 158, 158, 0.3);">
                                <div class="flex items-center mb-4">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-4" style="background-color: rgba(158, 158, 158, 0.2);">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #9E9E9E;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold" style="color: #424242;">Nuestra Visión</h3>
                                </div>
                                <p class="leading-relaxed" style="color: #424242;">{{ escuelaInfo.vision }}</p>
                            </div>
                        </div>

                        <!-- Valores -->
                        <div class="p-8 rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl" style="background-color: #FFFFFF; border: 1px solid rgba(158, 158, 158, 0.3);">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-4" style="background-color: rgba(253, 216, 53, 0.2);">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #FDD835;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold" style="color: #212121;">Nuestros Valores</h3>
                            </div>
                            <div class="space-y-4">
                                <div 
                                    v-for="(valor, index) in escuelaInfo.valores" 
                                    :key="valor"
                                    class="flex items-center space-x-3 p-3 rounded-lg transition-colors values-item"
                                    style="background-color: #F7F7F7;"
                                >
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm" style="background-color: rgba(253, 216, 53, 0.2); color: #212121;">
                                        {{ index + 1 }}
                                    </div>
                                    <span class="font-medium" style="color: #424242;">{{ valor }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Sección de Inscripciones -->
            <section id="inscripciones" class="py-16" style="background-color: #FFFFFF;">
                <div class="max-w-6xl mx-auto px-4">
                    <div class="text-center mb-16">
                        <h2 class="text-4xl font-bold mb-4" style="color: #212121;">
                            Cuestionarios e Inscripciones
                        </h2>
                        <p class="text-lg max-w-2xl mx-auto" style="color: #424242;">
                            Información sobre los períodos de inscripción y acceso a formularios por grado.
                        </p>
                    </div>
                    
                    <div v-if="cuestionarios.length > 0" class="space-y-8">
                        <!-- Múltiples cuestionarios -->
                        <div v-for="(cuestionario, index) in cuestionarios" :key="cuestionario.id" 
                             class="rounded-2xl p-8 text-white shadow-2xl" 
                             style="background: linear-gradient(to right, #C62828, #B71C1C, #C62828);">
                            <div class="text-center mb-8">
                                <div class="inline-flex items-center rounded-full px-4 py-2 mb-4" style="background-color: rgba(255, 255, 255, 0.25);">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm font-semibold">{{ (isTruthy(cuestionario.activo) && cuestionario.vigente) ? 'CUESTIONARIO DISPONIBLE' : 'NO DISPONIBLE' }}</span>
                                </div>
                                <h3 class="text-3xl font-bold mb-4">{{ cuestionario.titulo || 'Evaluación Disponible' }}</h3>
                                <p v-if="cuestionario.descripcion" class="text-lg mb-6 max-w-3xl mx-auto" style="color: rgba(255, 255, 255, 0.9);">
                                    {{ cuestionario.descripcion }}
                                </p>
                                <div class="inline-flex items-center rounded-lg px-6 py-3" style="background-color: rgba(255, 255, 255, 0.15);">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #FDD835;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="font-semibold">
                                        {{ formatearFecha(cuestionario.fecha_inicio) }} - 
                                        {{ formatearFechaCompleta(cuestionario.fecha_fin) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Formularios por grado para este cuestionario -->
                            <div class="grid md:grid-cols-3 gap-6">
                                <!-- Primer grado -->
                                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center border border-white/20 hover:bg-white/20 transition-all duration-300">
                                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-2xl font-bold">1°</span>
                                    </div>
                                    <h4 class="text-xl font-bold mb-4">Primer Grado</h4>
                                    <p class="text-white/80 text-sm mb-6">Formulario de evaluación para estudiantes de primer grado</p>
                                    <a 
                                        v-if="(isTruthy(cuestionario.activo) && cuestionario.vigente) && isTruthy(cuestionario.primero_activo) && cuestionario.link_primero"
                                        :href="cuestionario.link_primero"
                                        target="_blank"
                                        class="inline-block w-full px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1 inscription-button-active"
                                        style="background-color: #FFFFFF; color: #424242;"
                                    >
                                        <span>Acceder al Formulario</span>
                                    </a>
                                    <div 
                                        v-else
                                        class="inline-block w-full px-6 py-3 rounded-lg font-medium cursor-not-allowed"
                                        style="background-color: #9E9E9E; color: #616161;"
                                    >
                                        <span>{{ (!cuestionario.link_primero && (isTruthy(cuestionario.activo) && cuestionario.vigente) && isTruthy(cuestionario.primero_activo)) ? 'Sin Link' : 'No Disponible' }}</span>
                                    </div>
                                </div>

                                <!-- Segundo grado -->
                                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center border border-white/20 hover:bg-white/20 transition-all duration-300">
                                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-2xl font-bold">2°</span>
                                    </div>
                                    <h4 class="text-xl font-bold mb-4">Segundo Grado</h4>
                                    <p class="text-white/80 text-sm mb-6">Formulario de evaluación para estudiantes de segundo grado</p>
                                    <a 
                                        v-if="(isTruthy(cuestionario.activo) && cuestionario.vigente) && isTruthy(cuestionario.segundo_activo) && cuestionario.link_segundo"
                                        :href="cuestionario.link_segundo"
                                        target="_blank"
                                        class="inline-block w-full px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1 inscription-button-active"
                                        style="background-color: #FFFFFF; color: #424242;"
                                    >
                                        <span>Acceder al Formulario</span>
                                    </a>
                                    <div 
                                        v-else
                                        class="inline-block w-full px-6 py-3 rounded-lg font-medium cursor-not-allowed"
                                        style="background-color: #9E9E9E; color: #616161;"
                                    >
                                        <span>{{ (!cuestionario.link_segundo && (isTruthy(cuestionario.activo) && cuestionario.vigente) && isTruthy(cuestionario.segundo_activo)) ? 'Sin Link' : 'No Disponible' }}</span>
                                    </div>
                                </div>

                                <!-- Tercer grado -->
                                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center border border-white/20 hover:bg-white/20 transition-all duration-300">
                                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-2xl font-bold">3°</span>
                                    </div>
                                    <h4 class="text-xl font-bold mb-4">Tercer Grado</h4>
                                    <p class="text-white/80 text-sm mb-6">Formulario de evaluación para estudiantes de tercer grado</p>
                                    <a 
                                        v-if="(isTruthy(cuestionario.activo) && cuestionario.vigente) && isTruthy(cuestionario.tercero_activo) && cuestionario.link_tercero"
                                        :href="cuestionario.link_tercero"
                                        target="_blank"
                                        class="inline-block w-full px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1 inscription-button-active"
                                        style="background-color: #FFFFFF; color: #424242;"
                                    >
                                        <span>Acceder al Formulario</span>
                                    </a>
                                    <div 
                                        v-else
                                        class="inline-block w-full px-6 py-3 rounded-lg font-medium cursor-not-allowed"
                                        style="background-color: #9E9E9E; color: #616161;"
                                    >
                                        <span>{{ (!cuestionario.link_tercero && (isTruthy(cuestionario.activo) && cuestionario.vigente) && isTruthy(cuestionario.tercero_activo)) ? 'Sin Link' : 'No Disponible' }}</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    
                    <!-- Información adicional si hay cuestionarios -->
                    <div v-if="cuestionarios.length > 0" class="mt-8 rounded-xl p-6" style="background-color: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25);">
                        <h4 class="text-lg font-semibold mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #FDD835;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Información Importante
                        </h4>
                        <p class="text-sm" style="color: rgba(255, 255, 255, 0.9);">
                            Los formularios solo estarán disponibles durante el período establecido. 
                            Asegúrate de completar la evaluación antes de la fecha límite.
                        </p>
                    </div>
                    
                    <!-- Mensaje cuando no hay cuestionarios -->
                    <div v-else class="text-center py-16">
                        <div class="max-w-md mx-auto">
                            <svg class="w-20 h-20 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">Sin información de inscripciones</h3>
                            <p class="text-gray-500">No hay información de inscripciones disponible en este momento.</p>
                            <p class="text-gray-400 text-sm mt-2">Consulta nuevamente más tarde o contacta a la administración.</p>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Sección de Formularios Externos (Solo visible si hay datos) -->
            <section v-if="cuestionariosExternosActivos && cuestionariosExternosActivos.length > 0" id="formularios" class="py-16" style="background-color: #F7F7F7;">
                <div class="max-w-6xl mx-auto px-4">
                    <div class="text-center mb-16">
                        <h2 class="text-4xl font-bold mb-4" style="color: #212121;">
                            Otros Formularios y Consultas
                        </h2>
                        <p class="text-lg max-w-2xl mx-auto" style="color: #424242;">
                            Encuestas, registros y otros formularios informativos.
                        </p>
                    </div>
                    
                    <div class="space-y-8">
                        <div v-for="(cuestionario, index) in cuestionariosExternosActivos" :key="cuestionario.id" 
                             class="rounded-2xl p-8 text-white shadow-2xl" 
                             style="background: linear-gradient(to right, #8B1538, #6D0F2B, #8B1538);">
                            <div class="text-center mb-8">
                                <div class="inline-flex items-center rounded-full px-4 py-2 mb-4" style="background-color: rgba(255, 255, 255, 0.25);">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <span class="text-sm font-semibold">DISPONIBLE</span>
                                </div>
                                <h3 class="text-3xl font-bold mb-4">{{ cuestionario.titulo }}</h3>
                                <p v-if="cuestionario.descripcion" class="text-lg mb-6 max-w-3xl mx-auto" style="color: rgba(255, 255, 255, 0.9);">
                                    {{ cuestionario.descripcion }}
                                </p>
                                <div class="inline-flex items-center rounded-lg px-6 py-3" style="background-color: rgba(255, 255, 255, 0.15);">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #FDD835;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="font-semibold">
                                        {{ formatearFecha(cuestionario.fecha_inicio) }} - 
                                        {{ formatearFechaCompleta(cuestionario.fecha_fin) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Botones de grado -->
                            <div class="grid md:grid-cols-3 gap-6">
                                <!-- Primer grado -->
                                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center border border-white/20 hover:bg-white/20 transition-all duration-300">
                                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-2xl font-bold">1°</span>
                                    </div>
                                    <h4 class="text-xl font-bold mb-4">Primer Grado</h4>
                                    <a 
                                        v-if="isTruthy(cuestionario.primero_activo) && cuestionario.link_primero"
                                        :href="cuestionario.link_primero"
                                        target="_blank"
                                        class="inline-block w-full px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1"
                                        style="background-color: #FFFFFF; color: #8B1538;"
                                    >
                                        <span>Ir al Formulario</span>
                                    </a>
                                    <div 
                                        v-else
                                        class="inline-block w-full px-6 py-3 rounded-lg font-medium cursor-not-allowed"
                                        style="background-color: rgba(255,255,255,0.3); color: rgba(255,255,255,0.7);"
                                    >
                                        <span>{{ (!cuestionario.link_primero && isTruthy(cuestionario.primero_activo)) ? 'Sin Link' : 'No Disponible' }}</span>
                                    </div>
                                </div>

                                <!-- Segundo grado -->
                                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center border border-white/20 hover:bg-white/20 transition-all duration-300">
                                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-2xl font-bold">2°</span>
                                    </div>
                                    <h4 class="text-xl font-bold mb-4">Segundo Grado</h4>
                                    <a 
                                        v-if="isTruthy(cuestionario.segundo_activo) && cuestionario.link_segundo"
                                        :href="cuestionario.link_segundo"
                                        target="_blank"
                                        class="inline-block w-full px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1"
                                        style="background-color: #FFFFFF; color: #8B1538;"
                                    >
                                        <span>Ir al Formulario</span>
                                    </a>
                                    <div 
                                        v-else
                                        class="inline-block w-full px-6 py-3 rounded-lg font-medium cursor-not-allowed"
                                        style="background-color: rgba(255,255,255,0.3); color: rgba(255,255,255,0.7);"
                                    >
                                        <span>{{ (!cuestionario.link_segundo && isTruthy(cuestionario.segundo_activo)) ? 'Sin Link' : 'No Disponible' }}</span>
                                    </div>
                                </div>

                                <!-- Tercer grado -->
                                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center border border-white/20 hover:bg-white/20 transition-all duration-300">
                                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-2xl font-bold">3°</span>
                                    </div>
                                    <h4 class="text-xl font-bold mb-4">Tercer Grado</h4>
                                    <a 
                                        v-if="isTruthy(cuestionario.tercero_activo) && cuestionario.link_tercero"
                                        :href="cuestionario.link_tercero"
                                        target="_blank"
                                        class="inline-block w-full px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1"
                                        style="background-color: #FFFFFF; color: #8B1538;"
                                    >
                                        <span>Ir al Formulario</span>
                                    </a>
                                    <div 
                                        v-else
                                        class="inline-block w-full px-6 py-3 rounded-lg font-medium cursor-not-allowed"
                                        style="background-color: rgba(255,255,255,0.3); color: rgba(255,255,255,0.7);"
                                    >
                                        <span>{{ (!cuestionario.link_tercero && isTruthy(cuestionario.tercero_activo)) ? 'Sin Link' : 'No Disponible' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer id="contacto" class="text-white py-16" style="background: linear-gradient(to right, #C62828, #B71C1C, #C62828);">
            <div class="max-w-6xl mx-auto px-4">
                <div class="grid md:grid-cols-4 gap-8">
                    <!-- Logo y descripción -->
                    <div class="md:col-span-1">
                        <div class="flex items-center space-x-3 mb-4">
                            <img 
                                src="/images/logo-escuela.png" 
                                alt="Logo Escuela Técnica 40" 
                                class="h-8 w-8 object-contain"
                                onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIiByeD0iNCIgZmlsbD0iI0ZGRkZGRiIvPgo8dGV4dCB4PSIxNiIgeT0iMjAiIGZpbGw9IiM5OTE5MWEiIGZvbnQtc2l6ZT0iMTIiIGZvbnQtd2VpZ2h0PSJib2xkIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5UNDQ8L3RleHQ+Cjwvc3ZnPgo='"
                            />
                            <div>
                                <h3 class="font-bold text-lg">Técnica 40</h3>
                                <p class="text-sm" style="color: #FDD835;">Secundaria Técnica</p>
                            </div>
                        </div>
                        <p class="text-sm leading-relaxed" style="color: rgba(255, 255, 255, 0.9);">
                            Formando el futuro de México con educación de calidad, valores sólidos y compromiso social.
                        </p>
                    </div>

                    <!-- Información de contacto -->
                    <div>
                        <h3 class="text-lg font-bold mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1l-4 4z"/>
                            </svg>
                            Contacto
                        </h3>
                        <div class="space-y-3 text-sm" style="color: rgba(255, 255, 255, 0.9);">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #9E9E9E;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>Boulevard Luis Donaldo Colosio S/N, C.P. 42088</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #9E9E9E;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <span>771 338 3165</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #9E9E9E;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span>contacto@tecnica40.edu.mx</span>
                            </div>
                        </div>
                    </div>

                    <!-- Horarios -->
                    <div>
                        <h3 class="text-lg font-bold mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Horarios de Atención
                        </h3>
                        <div class="space-y-2 text-sm" style="color: rgba(255, 255, 255, 0.9);">
                            <div class="pl-3" style="border-left: 2px solid #9E9E9E;">
                                <p class="font-semibold">Lunes a Viernes</p>
                                <p>Turno Matutino: 7:00 AM - 1:00 PM</p>
                                <p>Turno Vespertino: 1:30 PM - 7:30 PM</p>
                            </div>
                        </div>
                    </div>

                    <!-- Enlaces rápidos -->
                    <div>
                        <h3 class="text-lg font-bold mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            Enlaces Rápidos
                        </h3>
                        <div class="space-y-2">
                            <a href="#inicio" class="block text-sm transition-colors hover:pl-2 duration-300 footer-link" style="color: rgba(255, 255, 255, 0.8);">
                                → Inicio
                            </a>
                            <a href="#anuncios" class="block text-sm transition-colors hover:pl-2 duration-300 footer-link" style="color: rgba(255, 255, 255, 0.8);">
                                → Anuncios
                            </a>
                            <a href="#informacion" class="block text-sm transition-colors hover:pl-2 duration-300 footer-link" style="color: rgba(255, 255, 255, 0.8);">
                                → Información
                            </a>
                            <a href="#inscripciones" class="block text-sm transition-colors hover:pl-2 duration-300 footer-link" style="color: rgba(255, 255, 255, 0.8);">
                                → Inscripciones
                            </a>
                            <Link 
                                :href="route('tutor.login')" 
                                class="block text-sm transition-colors hover:pl-2 duration-300 footer-link"
                                style="color: rgba(255, 255, 255, 0.8);"
                            >
                                → Portal de Padres
                            </Link>
                        </div>
                    </div>
                </div>
                
                <div class="mt-12 pt-8" style="border-top: 1px solid #9E9E9E;">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <p class="text-sm" style="color: rgba(255, 255, 255, 0.8);">
                            &copy; 2025 Escuela Secundaria Técnica 40. Todos los derechos reservados.
                        </p>
                        <div class="flex space-x-6 mt-4 md:mt-0">
                            <span class="text-xs" style="color: #FDD835;">Desarrollado con ❤️ para la educación</span>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Animaciones personalizadas */
.starting\:opacity-0 {
    opacity: 0;
}

/* Smooth scroll */
html {
    scroll-behavior: smooth;
}

/* Carrusel personalizado */
.carousel-enter-active, .carousel-leave-active {
    transition: transform 0.5s ease-in-out;
}

.carousel-enter-from {
    transform: translateX(100%);
}

.carousel-leave-to {
    transform: translateX(-100%);
}

/* Efectos hover personalizados con la nueva paleta */
nav a:hover {
    color: #FDD835 !important;
}

.footer-link:hover {
    color: #FDD835 !important;
}

.info-button:hover {
    background-color: #a1a1a1 !important;
    color: #212121 !important;
    transform: translateY(-1px);
}

.inscription-button-active:hover {
    background-color: #a1a1a1 !important;
    color: #212121 !important;
    transform: translateY(-1px);
}

.values-item:hover {
    background-color: rgba(253, 216, 53, 0.1) !important;
}

.carousel-control:hover {
    background-color: white !important;
}
</style>
