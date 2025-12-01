
<template>
	<AdminLayout>
		<Head title="Dashboard Administrativo - Técnica 40" />

		<div class="max-w-7xl mx-auto space-y-6">
			<!-- Encabezado página -->
			<div class="bg-white rounded-xl shadow p-4 md:p-6">
				<h2 class="text-xl md:text-2xl font-bold" style="color: #212121;">Información General</h2>
			</div>

			<!-- Novedades y comunicados (Carrusel) -->
			<section class="rounded-[28px] shadow-2xl p-6 md:p-8 relative overflow-hidden ring-1 ring-white/10"
				   style="background: radial-gradient(1200px 600px at 10% 10%, #C62828 0%, #B71C1C 60%, #8B0000 100%);">
				<div class="flex items-center justify-between mb-6">
					<h3 class="text-2xl font-bold text-white">Novedades y Comunicados</h3>
				    <Link :href="route('admin.anuncios.index')"
					    class="px-4 py-2 rounded-full text-white/90 hover:text-white hover:bg-white/15 transition ring-1 ring-white/20">
						Ver todos →
					</Link>
				</div>

				<div v-if="anunciosNorm.length" class="relative">
					<!-- Track -->
					<div class="relative h-56 md:h-60">
						<transition-group name="carousel" tag="div">
											<div v-for="(item, i) in [anunciosNorm[currentSlide] ?? {id: 'empty'}]" :key="item.id"
									 class="absolute inset-0 flex">
												<div class="flex-1 bg-transparent grid grid-cols-1 md:grid-cols-3 gap-5">
									<!-- Tarjetas (mostramos hasta 3 por slide si hay) -->
									<template v-for="(card, idx) in slice3(currentSlide)" :key="card.id">
														<div class="col-span-1 bg-white rounded-[20px] border p-5 shadow-[0_20px_25px_-15px_rgba(0,0,0,0.15)]" style="border-color: rgba(158, 158, 158, 0.3);">
															<div class="flex items-center justify-between mb-3">
																<span v-if="card.activo !== false"
																			class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full ring-1" style="background-color: rgba(253, 216, 53, 0.2); color: #212121; border-color: rgba(253, 216, 53, 0.6);">Activo</span>
																<span v-else
																			class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full ring-1" style="background-color: #F7F7F7; color: #9E9E9E; border-color: #E0E0E0;">Inactivo</span>
																<span class="inline-flex items-center gap-1 text-xs" style="color: #9E9E9E;">
																	<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																		<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
																	</svg>
																	{{ formatearFecha(card.fecha) }}
																</span>
															</div>
															<h4 class="font-semibold mb-2 leading-snug" style="color: #212121;">{{ card.titulo }}</h4>
															<p class="text-sm leading-relaxed line-clamp-3" style="color: #424242;">{{ card.contenido }}</p>
										</div>
									</template>
								</div>
							</div>
						</transition-group>
					</div>

					<!-- Controles -->
								<button @click="prevSlide"
												class="absolute left-3 top-1/2 -translate-y-1/2 p-2.5 rounded-full bg-white/90 hover:bg-white shadow ring-1" style="border-color: rgba(158, 158, 158, 0.3);">
						<svg class="w-5 h-5" style="color: #424242;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
						</svg>
					</button>
								<button @click="nextSlide"
												class="absolute right-3 top-1/2 -translate-y-1/2 p-2.5 rounded-full bg-white/90 hover:bg-white shadow ring-1" style="border-color: rgba(158, 158, 158, 0.3);">
						<svg class="w-5 h-5" style="color: #424242;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
						</svg>
					</button>

					<!-- Indicadores -->
					<div class="flex justify-center space-x-2 mt-5">
						<button v-for="(a, idx) in numSlides" :key="idx"
										@click="goToSlide(idx)"
										class="h-2 rounded-full transition-all"
										:class="idx === currentSlide ? 'w-6 bg-white' : 'w-2 bg-white/50'"/>
					</div>
				</div>
				<div v-else class="text-white/90">
					No hay anuncios disponibles.
				</div>
			</section>

			<!-- Resumen General -->
			<section>
				<h3 class="text-lg font-semibold" style="color: #212121; margin-bottom: 1rem;">Resumen General</h3>
						<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
					<ResumenCard titulo="Alumnos Inscritos" :valor="stats.total_alumnos ?? 0" color="bg-[#FDD835]/20" icon="users" />
					<ResumenCard titulo="Docentes Activos" :valor="stats.docentes_activos ?? 0" color="bg-[#C62828]/10" icon="teacher" />
					<ResumenCard titulo="Grupos" :valor="stats.grupos ?? 0" color="bg-[#FDD835]/20" icon="group" />
					<ResumenCard titulo="Anuncios Activos" :valor="stats.total_anuncios ?? 0" color="bg-[#C62828]/10" icon="bell" />
					<ResumenCard titulo="Cuestionarios Activos" :valor="stats.cuestionarios_activos ?? 0" color="bg-[#FDD835]/20" icon="form" />
				</div>
			</section>

			<!-- Dos columnas: Alertas del Sistema y Monitoreo de Asistencias -->
			<section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
				<!-- Alertas del Sistema -->
				<div class="bg-white rounded-xl shadow p-6">
					<div class="flex items-center gap-3 mb-4">
						<div class="text-2xl">⚠️</div>
						<h3 class="text-lg font-semibold" style="color: #212121;">Alertas del Sistema</h3>
					</div>
					
					<div v-if="alertas && alertas.length > 0" class="space-y-3 max-h-96 overflow-y-auto">
						<div
							v-for="alerta in alertas"
							:key="alerta.id"
							class="bg-red-50 rounded-lg p-4 border-l-4 border-red-600"
						>
							<div class="flex justify-between items-start gap-3">
								<div class="flex-1 min-w-0">
									<div class="flex items-center gap-2 mb-2 flex-wrap">
										<span
											class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
											:class="{
												'bg-red-100 text-red-800': alerta.tipo === 'sobrecupo',
												'bg-yellow-100 text-yellow-800': alerta.tipo === 'advertencia',
												'bg-blue-100 text-blue-800': alerta.tipo === 'info',
											}"
										>
											{{ alerta.tipo }}
										</span>
										<span class="text-xs" style="color: #9E9E9E;">
											{{ formatearFecha(alerta.fecha_creacion) }}
										</span>
									</div>
									<p class="text-sm font-medium mb-1" style="color: #212121;">{{ alerta.mensaje }}</p>
									<div v-if="alerta.grado || alerta.turno" class="text-xs" style="color: #757575;">
										<span v-if="alerta.grado">Grado: {{ alerta.grado }}</span>
										<span v-if="alerta.grado && alerta.turno"> • </span>
										<span v-if="alerta.turno">Turno: {{ alerta.turno }}</span>
									</div>
								</div>
								<button
									@click="resolverAlerta(alerta.id)"
									class="flex-shrink-0 bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs transition-colors"
									title="Marcar como resuelta"
								>
									Resolver
								</button>
							</div>
						</div>
					</div>
					<div v-else class="text-center py-8">
						<div class="text-4xl mb-2">✓</div>
						<p class="text-sm" style="color: #9E9E9E;">No hay alertas pendientes</p>
					</div>
				</div>

				<!-- Monitoreo de Asistencias -->
				<div class="bg-white rounded-xl shadow p-6">
					<div class="flex items-center gap-3 mb-4">
						<div class="text-2xl">📊</div>
						<div class="flex-1">
							<h3 class="text-lg font-semibold" style="color: #212121;">Monitoreo de Asistencias</h3>
							<p class="text-xs" style="color: #9E9E9E;">{{ asistencia_hoy?.fecha ? formatearFecha(asistencia_hoy.fecha) : 'Hoy' }}</p>
						</div>
					</div>

					<div v-if="asistencia_hoy">
						<!-- Resumen General -->
						<div class="mb-6 p-4 rounded-lg border-2"
							:class="{
								'border-green-500 bg-green-50': asistencia_hoy.nivel_alerta === 'normal',
								'border-yellow-500 bg-yellow-50': asistencia_hoy.nivel_alerta === 'medio',
								'border-orange-500 bg-orange-50': asistencia_hoy.nivel_alerta === 'alto',
								'border-red-600 bg-red-50': asistencia_hoy.nivel_alerta === 'critico',
							}">
							<div class="flex items-center justify-between mb-3">
								<div>
									<p class="text-sm font-medium" style="color: #424242;">Alumnos Inscritos</p>
									<p class="text-2xl font-bold" style="color: #212121;">{{ asistencia_hoy.total_alumnos }}</p>
								</div>
								<div class="text-right">
									<p class="text-sm font-medium" 
										:class="{
											'text-green-700': asistencia_hoy.nivel_alerta === 'normal',
											'text-yellow-700': asistencia_hoy.nivel_alerta === 'medio',
											'text-orange-700': asistencia_hoy.nivel_alerta === 'alto',
											'text-red-700': asistencia_hoy.nivel_alerta === 'critico',
										}">
										{{ asistencia_hoy.porcentaje_asistencia }}% Asistencia
									</p>
								</div>
							</div>

							<div class="grid grid-cols-2 gap-3 mb-3">
								<div class="bg-white rounded p-3 border" style="border-color: rgba(158, 158, 158, 0.2);">
									<p class="text-xs mb-1" style="color: #9E9E9E;">Presentes</p>
									<p class="text-xl font-bold text-green-600">{{ asistencia_hoy.presentes }}</p>
								</div>
								<div class="bg-white rounded p-3 border" style="border-color: rgba(158, 158, 158, 0.2);">
									<p class="text-xs mb-1" style="color: #9E9E9E;">Ausentes</p>
									<p class="text-xl font-bold text-red-600">{{ asistencia_hoy.ausentes }}</p>
								</div>
							</div>

							<!-- Nivel de Alerta -->
							<div v-if="asistencia_hoy.nivel_alerta !== 'normal'" class="flex items-center gap-2 p-2 rounded"
								:class="{
									'bg-yellow-100': asistencia_hoy.nivel_alerta === 'medio',
									'bg-orange-100': asistencia_hoy.nivel_alerta === 'alto',
									'bg-red-100': asistencia_hoy.nivel_alerta === 'critico',
								}">
								<span class="text-lg">
									{{ asistencia_hoy.nivel_alerta === 'medio' ? '⚠️' : asistencia_hoy.nivel_alerta === 'alto' ? '🔶' : '🔴' }}
								</span>
								<p class="text-xs font-semibold"
									:class="{
										'text-yellow-800': asistencia_hoy.nivel_alerta === 'medio',
										'text-orange-800': asistencia_hoy.nivel_alerta === 'alto',
										'text-red-800': asistencia_hoy.nivel_alerta === 'critico',
									}">
									{{ asistencia_hoy.nivel_alerta === 'medio' ? 'ALERTA: Más del 15% de ausencias' : 
									   asistencia_hoy.nivel_alerta === 'alto' ? 'ALERTA ALTA: Más del 20% de ausencias' : 
									   'CRÍTICO: Más del 30% de ausencias' }}
								</p>
							</div>
						</div>

						<!-- Desglose por Grado -->
						<div v-if="asistencia_hoy.por_grado && asistencia_hoy.por_grado.length > 0">
							<h4 class="text-sm font-semibold mb-3" style="color: #424242;">Desglose por Grado</h4>
							<div class="space-y-2 max-h-48 overflow-y-auto">
								<div v-for="grado in asistencia_hoy.por_grado" :key="grado.grado"
									class="flex items-center justify-between p-2 rounded border" style="border-color: rgba(158, 158, 158, 0.2); background-color: #FAFAFA;">
									<div class="flex-1">
										<p class="text-sm font-medium" style="color: #212121;">{{ grado.grado }}</p>
										<p class="text-xs" style="color: #757575;">{{ grado.presentes }}/{{ grado.total }} alumnos</p>
									</div>
									<div class="text-right">
										<p class="text-sm font-bold"
											:class="{
												'text-green-600': grado.porcentaje_ausencia < 15,
												'text-yellow-600': grado.porcentaje_ausencia >= 15 && grado.porcentaje_ausencia < 20,
												'text-orange-600': grado.porcentaje_ausencia >= 20 && grado.porcentaje_ausencia < 30,
												'text-red-600': grado.porcentaje_ausencia >= 30,
											}">
											{{ grado.porcentaje_asistencia }}%
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div v-else class="text-center py-8">
						<p class="text-sm" style="color: #9E9E9E;">No hay datos de asistencia disponibles</p>
					</div>
				</div>
			</section>

			<!-- Usuarios recientes y Misión/Visión/Valores -->
			<section class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-8">
				<!-- Usuarios recientes -->
				<div class="bg-white rounded-xl shadow p-6">
					<div class="flex items-center justify-between mb-4">
						<h3 class="text-lg font-semibold" style="color: #212121;">Usuarios recientes</h3>
						<span class="text-xs" style="color: #9E9E9E;">Últimos {{ Math.min(5, usuarios_recientes.length) }}</span>
					</div>
					<div v-if="usuarios_recientes.length" class="divide-y divide-slate-100">
						<div v-for="u in usuarios_recientes.slice(0,5)" :key="u.id" class="py-3 flex items-center justify-between">
							<div>
								<p class="font-medium" style="color: #212121;">{{ u.nombre_completo ?? u.nombre }}</p>
								<p class="text-xs" style="color: #9E9E9E;">{{ u.correo }}</p>
							</div>
							<span class="text-xs px-2 py-1 rounded" style="background-color: #F7F7F7; color: #424242;">{{ u.rol ?? 'Usuario' }}</span>
						</div>
					</div>
					<div v-else class="text-sm" style="color: #9E9E9E;">No hay registros recientes.</div>
				</div>

				<!-- Misión / Visión / Valores -->
				<div class="bg-white rounded-xl shadow p-6">
					<h3 class="text-lg font-semibold mb-4" style="color: #212121;">Misión / Visión / Valores</h3>
					<div class="space-y-4">
						<div class="p-4 rounded-lg border" style="border-color: rgba(198, 40, 40, 0.2);">
							<p class="text-sm" style="color: #424242;">
								Formar estudiantes competentes y comprometidos con su comunidad mediante una educación integral.
							</p>
						</div>
						<div class="p-4 rounded-lg border" style="border-color: rgba(198, 40, 40, 0.2);">
							<p class="text-sm" style="color: #424242;">
								Ser una institución de excelencia que contribuya al desarrollo integral de nuestros estudiantes.
							</p>
						</div>
						<div class="grid grid-cols-2 gap-3">
							<span class="text-sm px-3 py-2 rounded border" style="border-color: rgba(253, 216, 53, 0.5); background-color: rgba(253, 216, 53, 0.2); color: #212121;">Responsabilidad</span>
							<span class="text-sm px-3 py-2 rounded border" style="border-color: rgba(253, 216, 53, 0.5); background-color: rgba(253, 216, 53, 0.2); color: #212121;">Respeto</span>
							<span class="text-sm px-3 py-2 rounded border" style="border-color: rgba(253, 216, 53, 0.5); background-color: rgba(253, 216, 53, 0.2); color: #212121;">Honestidad</span>
							<span class="text-sm px-3 py-2 rounded border" style="border-color: rgba(253, 216, 53, 0.5); background-color: rgba(253, 216, 53, 0.2); color: #212121;">Compromiso</span>
							<span class="text-sm px-3 py-2 rounded border" style="border-color: rgba(253, 216, 53, 0.5); background-color: rgba(253, 216, 53, 0.2); color: #212121;">Excelencia</span>
						</div>
					</div>
				</div>
			</section>
		</div>
	</AdminLayout>
</template>

<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import AdminLayout from '../../layouts/AdminLayout.vue';

interface Usuario {
	id: number;
	nombre_completo: string;
	correo: string;
	rol: string;
}

interface Anuncio {
	id: number|string;
	titulo: string;
	contenido?: string;
	fecha?: string;
	activo?: boolean|number;
}

interface Alerta {
	id: number;
	tipo: string;
	mensaje: string;
	grado?: string;
	turno?: string;
	fecha_creacion: string;
}

interface Props {
	user: Usuario;
	stats: any;
	usuarios_recientes: any[];
	anuncios_recientes: Anuncio[];
	alertas?: Alerta[];
	asistencia_hoy?: {
		fecha: string;
		total_alumnos: number;
		presentes: number;
		ausentes: number;
		porcentaje_asistencia: number;
		porcentaje_ausencia: number;
		nivel_alerta: 'normal' | 'medio' | 'alto' | 'critico';
		por_grado: Array<{
			grado: string;
			total: number;
			presentes: number;
			ausentes: number;
			porcentaje_asistencia: number;
			porcentaje_ausencia: number;
		}>;
	};
}

const props = defineProps<Props>();
const { user, stats, usuarios_recientes, alertas, asistencia_hoy } = props;

// Normalizar anuncios y carrusel
const anunciosNorm = computed(() => (props.anuncios_recientes ?? []).map(a => ({
	id: a.id,
	titulo: a.titulo ?? 'Anuncio',
	contenido: a.contenido ?? '',
	fecha: a.fecha ?? '',
	activo: a.activo ?? true,
})));

const currentSlide = ref(0);
let slideInterval: any;

const numSlides = computed(() => {
	// Mostramos 3 tarjetas por slide. Calcular cuántos slides hacen falta.
	const total = anunciosNorm.value.length;
	if (total <= 3) return 1;
	return Math.ceil(total / 3);
});

const goToSlide = (idx: number) => {
	currentSlide.value = (idx + numSlides.value) % numSlides.value;
};

const nextSlide = () => goToSlide(currentSlide.value + 1);
const prevSlide = () => goToSlide(currentSlide.value - 1);

const slice3 = (slideIdx: number) => {
	const start = slideIdx * 3;
	return anunciosNorm.value.slice(start, start + 3);
};

onMounted(() => {
	if (anunciosNorm.value.length > 1) {
		slideInterval = setInterval(nextSlide, 5000);
	}
});

onUnmounted(() => {
	if (slideInterval) clearInterval(slideInterval);
});

const formatearFecha = (s?: string) => {
	if (!s) return '';
	// Acepta dd/mm/yyyy o yyyy-mm-dd o ISO simple
	if (s.includes('/')) {
		const p = s.split('/');
		if (p.length === 3) {
			const d = new Date(parseInt(p[2]), parseInt(p[1]) - 1, parseInt(p[0]));
			return d.toLocaleDateString('es-MX', { day: 'numeric', month: 'short', year: 'numeric' });
		}
	}
	const iso = s.includes('T') ? s : `${s}T00:00:00`;
	const d = new Date(iso);
	if (isNaN(d.getTime())) return s;
	return d.toLocaleDateString('es-MX', { day: 'numeric', month: 'short', year: 'numeric' });
};

function resolverAlerta(alertaId: number) {
	if (confirm('¿Está seguro de marcar esta alerta como resuelta?')) {
		router.post(`/admin/alertas/${alertaId}/resolver`, {}, {
			onSuccess: () => {
				router.reload({ only: ['alertas'] });
			},
		});
	}
}
</script>

<script lang="ts">
// Tarjeta de resumen simple
export default {
	components: {
		ResumenCard: {
			props: {
				titulo: { type: String, required: true },
				valor: { type: [String, Number], required: true },
				color: { type: String, default: 'bg-slate-50' },
				icon: { type: String, default: 'info' },
			},
			template: `
				<div class="rounded-xl shadow-sm p-4 border bg-white" style="border-color: rgba(158, 158, 158, 0.2);">
					<div class="flex items-center">
						<div class="p-3 rounded-full mr-3" :class="color">
							<span class="block w-6 h-6" style="color: #424242;">★</span>
						</div>
						<div>
							<p class="text-sm" style="color: #9E9E9E;">{{ titulo }}</p>
							<p class="text-2xl font-bold" style="color: #212121;">{{ valor }}</p>
						</div>
					</div>
				</div>
			`,
		},
	},
};
</script>

<style scoped>
.carousel-enter-active, .carousel-leave-active {
	transition: all .4s ease;
}
.carousel-enter-from { opacity: 0; transform: translateX(15px); }
.carousel-leave-to   { opacity: 0; transform: translateX(-15px); }

button:hover { transform: translateY(-2px); }
</style>

