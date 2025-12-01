<template>
  <PrefectosLayout>
    <Head title="Dashboard Prefectura" />

    <div class="max-w-7xl mx-auto space-y-4 sm:space-y-6">
      <div class="bg-white rounded-xl shadow p-4 sm:p-6">
        <h2 class="text-lg sm:text-xl md:text-2xl font-bold" style="color: #212121;">Información General</h2>
      </div>

      <!-- Anuncios (solo visualización) -->
      <section class="rounded-2xl sm:rounded-[28px] shadow-2xl p-4 sm:p-6 md:p-8 relative overflow-hidden ring-1 ring-white/10"
               style="background: radial-gradient(1200px 600px at 10% 10%, #C62828 0%, #B71C1C 60%, #8B0000 100%);">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
          <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-white">Novedades y Comunicados</h3>
          <!-- Sin botón de ir a anuncios -->
          <span class="text-white/70 text-xs sm:text-sm">Solo lectura</span>
        </div>

        <div v-if="anunciosNorm.length" class="relative">
          <!-- Desktop/Tablet: Carrusel con 3 cards -->
          <div class="hidden md:block relative h-56 md:h-60">
            <transition-group name="carousel" tag="div">
              <div v-for="(item, i) in [anunciosNorm[currentSlide] ?? {id: 'empty'}]" :key="item.id"
                   class="absolute inset-0 flex">
                <div class="flex-1 bg-transparent grid grid-cols-3 gap-5">
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

            <div class="flex justify-center space-x-2 mt-5">
              <button v-for="(a, idx) in numSlides" :key="idx"
                      @click="goToSlide(idx)"
                      class="h-2 rounded-full transition-all"
                      :class="idx === currentSlide ? 'w-6 bg-white' : 'w-2 bg-white/50'"/>
            </div>
          </div>

          <!-- Mobile: Lista vertical de cards -->
          <div class="md:hidden space-y-3">
            <div v-for="card in anunciosNorm.slice(0, 4)" :key="card.id"
                 class="bg-white rounded-xl border p-4 shadow-lg" style="border-color: rgba(158, 158, 158, 0.3);">
              <div class="flex items-center justify-between mb-2">
                <span v-if="card.activo !== false"
                      class="inline-flex items-center text-xs font-semibold px-2 py-1 rounded-full ring-1" style="background-color: rgba(253, 216, 53, 0.2); color: #212121; border-color: rgba(253, 216, 53, 0.6);">Activo</span>
                <span v-else
                      class="inline-flex items-center text-xs font-semibold px-2 py-1 rounded-full ring-1" style="background-color: #F7F7F7; color: #9E9E9E; border-color: #E0E0E0;">Inactivo</span>
                <span class="inline-flex items-center gap-1 text-xs" style="color: #9E9E9E;">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  {{ formatearFecha(card.fecha) }}
                </span>
              </div>
              <h4 class="text-sm font-semibold mb-2 leading-snug" style="color: #212121;">{{ card.titulo }}</h4>
              <p class="text-xs leading-relaxed line-clamp-2" style="color: #424242;">{{ card.contenido }}</p>
            </div>
          </div>
        </div>
        <div v-else class="text-white/90 text-sm">No hay anuncios disponibles.</div>
      </section>

      <!-- Valores (tarjetas) -->
      <section>
        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4" style="color: #212121;">Misión / Visión / Valores</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
          <div class="bg-white rounded-xl shadow p-4 sm:p-6 border" style="border-color: rgba(198, 40, 40, 0.2);">
            <p class="text-xs sm:text-sm" style="color: #424242;">Formar estudiantes competentes y comprometidos con su comunidad mediante una educación integral.</p>
          </div>
          <div class="bg-white rounded-xl shadow p-4 sm:p-6 border" style="border-color: rgba(198, 40, 40, 0.2);">
            <p class="text-xs sm:text-sm" style="color: #424242;">Ser una institución de excelencia que contribuya al desarrollo integral de nuestros estudiantes.</p>
          </div>
          <div class="bg-white rounded-xl shadow p-4 sm:p-6 col-span-1 sm:col-span-2">
            <div class="flex flex-wrap gap-2">
              <span class="text-xs sm:text-sm px-2 sm:px-3 py-1.5 sm:py-2 rounded border" style="border-color: rgba(253, 216, 53, 0.5); background-color: rgba(253, 216, 53, 0.2); color: #212121;">Responsabilidad</span>
              <span class="text-xs sm:text-sm px-2 sm:px-3 py-1.5 sm:py-2 rounded border" style="border-color: rgba(253, 216, 53, 0.5); background-color: rgba(253, 216, 53, 0.2); color: #212121;">Respeto</span>
              <span class="text-xs sm:text-sm px-2 sm:px-3 py-1.5 sm:py-2 rounded border" style="border-color: rgba(253, 216, 53, 0.5); background-color: rgba(253, 216, 53, 0.2); color: #212121;">Honestidad</span>
              <span class="text-xs sm:text-sm px-2 sm:px-3 py-1.5 sm:py-2 rounded border" style="border-color: rgba(253, 216, 53, 0.5); background-color: rgba(253, 216, 53, 0.2); color: #212121;">Compromiso</span>
              <span class="text-xs sm:text-sm px-2 sm:px-3 py-1.5 sm:py-2 rounded border" style="border-color: rgba(253, 216, 53, 0.5); background-color: rgba(253, 216, 53, 0.2); color: #212121;">Excelencia</span>
            </div>
          </div>
        </div>
      </section>
    </div>
  </PrefectosLayout>
  </template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { computed, onMounted, onUnmounted, ref } from 'vue'
import PrefectosLayout from '../../layouts/PrefectosLayout.vue'

interface Anuncio { id: number|string; titulo: string; contenido?: string; fecha?: string; activo?: boolean|number }
const props = defineProps<{ anuncios_recientes: Anuncio[] }>()

const anunciosNorm = computed(() => (props.anuncios_recientes ?? []).map(a => ({
  id: a.id,
  titulo: a.titulo ?? 'Anuncio',
  contenido: a.contenido ?? '',
  fecha: a.fecha ?? '',
  activo: a.activo ?? true,
})))

const currentSlide = ref(0)
let slideInterval: any

const numSlides = computed(() => {
  const total = anunciosNorm.value.length
  if (total <= 3) return 1
  return Math.ceil(total / 3)
})

const goToSlide = (idx: number) => { currentSlide.value = (idx + numSlides.value) % numSlides.value }
const nextSlide = () => goToSlide(currentSlide.value + 1)
const prevSlide = () => goToSlide(currentSlide.value - 1)
const slice3 = (slideIdx: number) => { const start = slideIdx * 3; return anunciosNorm.value.slice(start, start + 3) }

onMounted(() => { if (anunciosNorm.value.length > 1) { slideInterval = setInterval(nextSlide, 5000) } })
onUnmounted(() => { if (slideInterval) clearInterval(slideInterval) })

const formatearFecha = (s?: string) => {
  if (!s) return ''
  if (s.includes('/')) { const p = s.split('/'); if (p.length === 3) { const d = new Date(parseInt(p[2]), parseInt(p[1]) - 1, parseInt(p[0])); return d.toLocaleDateString('es-MX', { day: 'numeric', month: 'short', year: 'numeric' }) } }
  const iso = s.includes('T') ? s : `${s}T00:00:00`
  const d = new Date(iso)
  if (isNaN(d.getTime())) return s
  return d.toLocaleDateString('es-MX', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>

<style scoped>
.carousel-enter-active, .carousel-leave-active { transition: all .4s ease; }
.carousel-enter-from { opacity: 0; transform: translateX(15px); }
.carousel-leave-to   { opacity: 0; transform: translateX(-15px); }
</style>
