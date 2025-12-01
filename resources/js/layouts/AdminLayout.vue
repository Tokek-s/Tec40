<template>
	<div class="min-h-screen flex bg-slate-100">
		<!-- Sidebar Desktop -->
		<aside class="hidden lg:flex w-72 bg-[#B50505] text-white flex-col">
			<div class="p-6 border-b border-white/10">
				<Link :href="route('admin.dashboard')" class="flex items-center space-x-3 hover:opacity-80 transition-opacity cursor-pointer">
					<img src="/images/logo-escuela.png" class="h-10 w-10 object-contain" alt="Logo" />
					<div>
						<p class="text-sm text-white/80">Escuela Técnica 40</p>
					</div>
				</Link>

				<!-- Botón Cerrar Sesión visible al inicio -->
				<div class="mt-4">
					<Link :href="route('admin.logout')" method="post" as="button"
						  class="w-full inline-flex items-center justify-center gap-2 bg-white text-[#B50505] font-semibold py-2.5 rounded-md shadow hover:shadow-md hover:bg-white/90 transition">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
						</svg>
						<span>Cerrar Sesión</span>
					</Link>
				</div>
			</div>

			<nav class="flex-1 overflow-y-auto p-3 space-y-1">
				<Link :href="route('admin.dashboard')" :class="navItemClass(route().current('admin.dashboard'))">
					<span class="i">🏠</span>
					<span>Información Gral</span>
				</Link>

				<div class="mb-1">
					<button class="w-full flex items-center justify-between px-4 py-2 rounded-md hover:bg-white/10" @click="open.inscripciones = !open.inscripciones">
						<span class="flex items-center space-x-2">
							<span class="i">🏠</span>
							<span>Inscripción/Reins</span>
						</span>
						<span class="text-white/70">{{ open.inscripciones ? '▾' : '▸' }}</span>
					</button>
					<div v-if="open.inscripciones" class="mt-1 ml-4 space-y-1">
						<Link :href="route('admin.inscripciones.inscripcion')" :class="subItemClass(route().current('admin.inscripciones.inscripcion'))">
							<span class="pr-2">›</span> Inscripción
						</Link>
						<Link :href="route('admin.inscripciones.reinscripcion')" :class="subItemClass(route().current('admin.inscripciones.reinscripcion'))">
							<span class="pr-2">›</span> Reinscripción
						</Link>
					</div>
				</div>

				<div class="mb-1">
					<button class="w-full flex items-center justify-between px-4 py-2 rounded-md hover:bg-white/10" @click="open.alumnos = !open.alumnos">
						<span class="flex items-center space-x-2">
							<span class="i">🎓</span>
							<span>Alumnos</span>
						</span>
						<span class="text-white/70">{{ open.alumnos ? '▾' : '▸' }}</span>
					</button>
					<div v-if="open.alumnos" class="mt-1 ml-4 space-y-1">
						<Link :href="route('admin.alumnos.pase_lista')" :class="subItemClass(route().current('admin.alumnos.pase_lista'))">
							<span class="pr-2">›</span> Pase de Lista
						</Link>
						<Link :href="route('admin.alumnos.lista')" :class="subItemClass(route().current('admin.alumnos.lista'))">
							<span class="pr-2">›</span> Lista alumnos
						</Link>
						<Link :href="route('admin.alumnos.grupos')" :class="subItemClass(route().current('admin.alumnos.grupos'))">
							<span class="pr-2">›</span> Grupos
						</Link>
						<Link :href="route('admin.alumnos.contactos')" :class="subItemClass(route().current('admin.alumnos.contactos'))">
							<span class="pr-2">›</span> Contactos Alumnos
						</Link>
						<Link :href="route('admin.alumnos.salud')" :class="subItemClass(route().current('admin.alumnos.salud'))">
							<span class="pr-2">›</span> Salud y Bienestar
						</Link>
					</div>
				</div>

				<div class="mb-1">
					<button class="w-full flex items-center justify-between px-4 py-2 rounded-md hover:bg-white/10" @click="open.docs = !open.docs">
						<span class="flex items-center space-x-2">
							<span class="i">📁</span>
							<span>Generar Docs.</span>
						</span>
						<span class="text-white/70">{{ open.docs ? '▾' : '▸' }}</span>
					</button>
					<div v-if="open.docs" class="mt-1 ml-4 space-y-1">
						<Link :href="route('admin.docs.incidencias_index')" :class="subItemClass(route().current('admin.docs.incidencias*'))">
							<span class="pr-2">›</span> Incidencias
						</Link>
						<Link :href="route('admin.docs.autorizacion_salida')" :class="subItemClass(route().current('admin.docs.autorizacion_salida'))">
							<span class="pr-2">›</span> Autorización salida
						</Link>
						<Link :href="route('admin.docs.credenciales')" :class="subItemClass(route().current('admin.docs.credenciales'))">
							<span class="pr-2">›</span> Credenciales
						</Link>
					</div>
				</div>

				<Link :href="route('admin.contrasenas')" :class="navItemClass(route().current('admin.contrasenas'))">
					<span class="i">🔐</span>
					<span>Contraseñas</span>
				</Link>
				<Link :href="route('admin.formularios.index')" :class="navItemClass(route().current('admin.formularios.index'))">
					<span class="i">🧾</span>
					<span>Formularios</span>
				</Link>
				<Link :href="route('admin.anuncios.index')" :class="navItemClass(route().current('admin.anuncios.index'))">
					<span class="i">🔔</span>
					<span>Anuncios</span>
				</Link>
			</nav>
		</aside>

		<!-- Sidebar Mobile (Overlay) -->
		<Teleport to="body">
			<Transition name="sidebar">
				<div v-if="mobileMenuOpen" class="fixed inset-0 z-50 lg:hidden">
					<!-- Backdrop -->
					<div class="fixed inset-0 bg-black/50" @click="mobileMenuOpen = false"></div>
					
					<!-- Sidebar -->
					<aside class="fixed top-0 left-0 bottom-0 w-80 bg-[#B50505] text-white flex flex-col shadow-xl">
						<div class="p-6 border-b border-white/10">
							<div class="flex items-center justify-between mb-4">
								<Link :href="route('admin.dashboard')" class="flex items-center space-x-3 hover:opacity-80 transition-opacity cursor-pointer" @click="mobileMenuOpen = false">
									<img src="/images/logo-escuela.png" class="h-10 w-10 object-contain" alt="Logo" />
									<div>
										<p class="text-sm text-white/80">Escuela Técnica 40</p>
									</div>
								</Link>
								<button @click="mobileMenuOpen = false" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
									<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
									</svg>
								</button>
							</div>
							
							<!-- Botón Cerrar Sesión -->
							<Link :href="route('admin.logout')" method="post" as="button"
								  class="w-full inline-flex items-center justify-center gap-2 bg-white text-[#B50505] font-semibold py-2.5 rounded-md shadow hover:shadow-md hover:bg-white/90 transition">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
								</svg>
								<span>Cerrar Sesión</span>
							</Link>
						</div>

						<nav class="flex-1 overflow-y-auto p-3 space-y-1">
							<Link :href="route('admin.dashboard')" :class="navItemClass(route().current('admin.dashboard'))" @click="mobileMenuOpen = false">
								<span class="i">🏠</span>
								<span>Información Gral</span>
							</Link>

							<div class="mb-1">
								<button class="w-full flex items-center justify-between px-4 py-2 rounded-md hover:bg-white/10" @click="open.inscripciones = !open.inscripciones">
									<span class="flex items-center space-x-2">
										<span class="i">🏠</span>
										<span>Inscripción/Reins</span>
									</span>
									<span class="text-white/70">{{ open.inscripciones ? '▾' : '▸' }}</span>
								</button>
								<div v-if="open.inscripciones" class="mt-1 ml-4 space-y-1">
									<Link :href="route('admin.inscripciones.inscripcion')" :class="subItemClass(route().current('admin.inscripciones.inscripcion'))" @click="mobileMenuOpen = false">
										<span class="pr-2">›</span> Inscripción
									</Link>
									<Link :href="route('admin.inscripciones.reinscripcion')" :class="subItemClass(route().current('admin.inscripciones.reinscripcion'))" @click="mobileMenuOpen = false">
										<span class="pr-2">›</span> Reinscripción
									</Link>
								</div>
							</div>

							<div class="mb-1">
								<button class="w-full flex items-center justify-between px-4 py-2 rounded-md hover:bg-white/10" @click="open.alumnos = !open.alumnos">
									<span class="flex items-center space-x-2">
										<span class="i">🎓</span>
										<span>Alumnos</span>
									</span>
									<span class="text-white/70">{{ open.alumnos ? '▾' : '▸' }}</span>
								</button>
								<div v-if="open.alumnos" class="mt-1 ml-4 space-y-1">
									<Link :href="route('admin.alumnos.pase_lista')" :class="subItemClass(route().current('admin.alumnos.pase_lista'))" @click="mobileMenuOpen = false">
										<span class="pr-2">›</span> Pase de Lista
									</Link>
									<Link :href="route('admin.alumnos.lista')" :class="subItemClass(route().current('admin.alumnos.lista'))" @click="mobileMenuOpen = false">
										<span class="pr-2">›</span> Lista alumnos
									</Link>
									<Link :href="route('admin.alumnos.grupos')" :class="subItemClass(route().current('admin.alumnos.grupos'))" @click="mobileMenuOpen = false">
										<span class="pr-2">›</span> Grupos
									</Link>
									<Link :href="route('admin.alumnos.contactos')" :class="subItemClass(route().current('admin.alumnos.contactos'))" @click="mobileMenuOpen = false">
										<span class="pr-2">›</span> Contactos Alumnos
									</Link>
									<Link :href="route('admin.alumnos.salud')" :class="subItemClass(route().current('admin.alumnos.salud'))" @click="mobileMenuOpen = false">
										<span class="pr-2">›</span> Salud y Bienestar
									</Link>
								</div>
							</div>

							<div class="mb-1">
								<button class="w-full flex items-center justify-between px-4 py-2 rounded-md hover:bg-white/10" @click="open.docs = !open.docs">
									<span class="flex items-center space-x-2">
										<span class="i">📁</span>
										<span>Generar Docs.</span>
									</span>
									<span class="text-white/70">{{ open.docs ? '▾' : '▸' }}</span>
								</button>
								<div v-if="open.docs" class="mt-1 ml-4 space-y-1">
									<Link :href="route('admin.docs.incidencias_index')" :class="subItemClass(route().current('admin.docs.incidencias*'))" @click="mobileMenuOpen = false">
										<span class="pr-2">›</span> Incidencias
									</Link>
									<Link :href="route('admin.docs.autorizacion_salida')" :class="subItemClass(route().current('admin.docs.autorizacion_salida'))" @click="mobileMenuOpen = false">
										<span class="pr-2">›</span> Autorización salida
									</Link>
									<Link :href="route('admin.docs.credenciales')" :class="subItemClass(route().current('admin.docs.credenciales'))" @click="mobileMenuOpen = false">
										<span class="pr-2">›</span> Credenciales
									</Link>
								</div>
							</div>

							<Link :href="route('admin.contrasenas')" :class="navItemClass(route().current('admin.contrasenas'))" @click="mobileMenuOpen = false">
								<span class="i">🔐</span>
								<span>Contraseñas</span>
							</Link>
							<Link :href="route('admin.formularios.index')" :class="navItemClass(route().current('admin.formularios.index'))" @click="mobileMenuOpen = false">
								<span class="i">🧾</span>
								<span>Formularios</span>
							</Link>
							<Link :href="route('admin.anuncios.index')" :class="navItemClass(route().current('admin.anuncios.index'))" @click="mobileMenuOpen = false">
								<span class="i">🔔</span>
								<span>Anuncios</span>
							</Link>
						</nav>
					</aside>
				</div>
			</Transition>
		</Teleport>

		<!-- Main Content -->
		<div class="flex-1 flex flex-col min-w-0">
			<!-- Mobile Header -->
			<header class="lg:hidden bg-white shadow-sm px-4 py-3 flex items-center justify-between sticky top-0 z-40">
				<button @click="mobileMenuOpen = true" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
					<svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
					</svg>
				</button>
				<div class="flex items-center space-x-2">
					<img src="/images/logo-escuela.png" class="h-8 w-8 object-contain" alt="Logo" />
					<span class="text-sm font-semibold text-gray-700">Escuela Técnica 40</span>
				</div>
				<div class="w-10"></div> <!-- Spacer for centering -->
			</header>

			<main class="flex-1 p-4 sm:p-6 overflow-x-hidden">
				<slot />
			</main>
		</div>
	</div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { reactive, ref } from 'vue';

const open = reactive({ inscripciones: false, alumnos: false, docs: false });
const mobileMenuOpen = ref(false);

const navItemClass = (active: boolean) => [
	'flex items-center space-x-3 px-4 py-2 rounded-md transition-colors',
	active ? 'bg-white/15' : 'hover:bg-white/10'
];

const subItemClass = (active: boolean) => [
	'block px-4 py-2 rounded-md text-sm transition-colors',
	active ? 'bg-white/15' : 'hover:bg-white/10'
];
</script>

<style scoped>
.i { width: 1.2rem; display: inline-flex; justify-content: center; }

.sidebar-enter-active,
.sidebar-leave-active {
  transition: opacity 0.3s ease;
}

.sidebar-enter-active aside,
.sidebar-leave-active aside {
  transition: transform 0.3s ease;
}

.sidebar-enter-from,
.sidebar-leave-to {
  opacity: 0;
}

.sidebar-enter-from aside,
.sidebar-leave-to aside {
  transform: translateX(-100%);
}

.sidebar-enter-to aside,
.sidebar-leave-from aside {
  transform: translateX(0);
}
</style>
