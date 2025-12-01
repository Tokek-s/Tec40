<template>
	<AdminLayout>
		<Head title="Anuncios" />

		<div class="max-w-7xl mx-auto space-y-6">
			<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
				<h1 class="text-2xl font-bold text-slate-800">Anuncios</h1>
				<div class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-center">
					<input v-model="q" @keyup.enter="buscar" type="text" placeholder="Buscar por título o contenido"
								 class="w-full sm:w-72 px-3 py-2 rounded-md border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
					<button @click="buscar" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 text-center">Buscar</button>
					<Link :href="route('admin.anuncios.create')" class="px-4 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700 text-center">Nuevo</Link>
				</div>
			</div>

			<!-- Tabla -->
			<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
				<div class="overflow-x-auto">
					<table class="min-w-full">
						<thead class="bg-slate-50">
							<tr class="text-left text-slate-600">
								<th class="px-4 py-3 whitespace-nowrap">Título</th>
								<th class="px-4 py-3 whitespace-nowrap">Fecha</th>
								<th class="px-4 py-3 whitespace-nowrap">Activo</th>
								<th class="px-4 py-3 whitespace-nowrap">Imagen</th>
								<th class="px-4 py-3 text-right whitespace-nowrap">Acciones</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="a in anuncios.data" :key="a.id" class="border-t border-slate-100">
								<td class="px-4 py-3 font-medium text-slate-800 whitespace-nowrap">{{ a.titulo }}</td>
								<td class="px-4 py-3 text-slate-600 whitespace-nowrap">{{ a.fecha }}</td>
								<td class="px-4 py-3 whitespace-nowrap">
									<span :class="a.activo ? 'text-emerald-700 bg-emerald-50 ring-emerald-200' : 'text-slate-600 bg-slate-100 ring-slate-300'"
												class="inline-flex text-xs font-semibold px-2.5 py-1 rounded-full ring-1">
										{{ a.activo ? 'Sí' : 'No' }}
									</span>
								</td>
								<td class="px-4 py-3 whitespace-nowrap">
									<img v-if="a.image_url" :src="a.image_url" alt="imagen" class="h-10 w-10 object-cover rounded" />
									<span v-else class="text-slate-400 text-xs">(sin imagen)</span>
								</td>
								<td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
									<Link :href="route('admin.anuncios.edit', { anuncio: a.id })" class="px-3 py-1.5 rounded-md bg-slate-800 text-white hover:bg-slate-700">Editar</Link>
									<button @click="eliminar(a)" class="px-3 py-1.5 rounded-md bg-rose-600 text-white hover:bg-rose-700">Eliminar</button>
								</td>
							</tr>
							<tr v-if="!anuncios.data.length">
								<td colspan="5" class="px-4 py-6 text-center text-slate-500">No hay anuncios.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<!-- Paginación simple -->
			<div class="flex justify-between items-center text-sm text-slate-500">
				<span>Página {{ anuncios.current_page }} de {{ anuncios.last_page }}</span>
				<div class="space-x-2">
					<button :disabled="!anuncios.prev_page_url" @click="ir(anuncios.prev_page_url)" class="px-3 py-1 rounded bg-slate-100 disabled:opacity-50">Anterior</button>
					<button :disabled="!anuncios.next_page_url" @click="ir(anuncios.next_page_url)" class="px-3 py-1 rounded bg-slate-100 disabled:opacity-50">Siguiente</button>
				</div>
			</div>
		</div>

		<!-- Modal deshabilitado: usamos páginas dedicadas para crear/editar -->
	</AdminLayout>
</template>

<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';
import AdminLayout from '../../../layouts/AdminLayout.vue';

interface Paginacion<T> {
	data: T[];
	current_page: number;
	last_page: number;
	next_page_url?: string|null;
	prev_page_url?: string|null;
}

interface AnuncioDto {
	id: number;
	titulo: string;
	contenido?: string;
	fecha?: string;
	activo: boolean;
	ruta_imagen?: string|null;
	image_url?: string|null;
}

const props = defineProps<{ anuncios: Paginacion<AnuncioDto>, filters: { q: string } }>();

const q = ref(props.filters?.q ?? '');
const buscar = () => router.get(route('admin.anuncios.index'), { q: q.value }, { preserveState: true, preserveScroll: true });

const anuncios = props.anuncios;

const ir = (url?: string|null) => { if (url) router.visit(url, { preserveState: true, preserveScroll: true }); };

const eliminar = (a: AnuncioDto) => {
	if (!confirm('¿Eliminar este anuncio?')) return;
	router.delete(route('admin.anuncios.destroy', { anuncio: a.id }), {
		onSuccess: () => {},
	});
};
</script>

<!-- Modal eliminado: usamos páginas dedicadas -->
