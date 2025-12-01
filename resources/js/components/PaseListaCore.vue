<template>
  <div class="space-y-4 lg:space-y-0 lg:grid lg:grid-cols-12 lg:gap-6 text-slate-800">
    <!-- Panel de asistencia - Primero en móvil, a la derecha en desktop -->
    <div class="lg:hidden lg:col-span-4 order-first">
      <div class="bg-white rounded-xl border border-slate-200 p-4 text-slate-800">
        <div v-if="selected">
          <div class="text-center">
            <img :src="selected.foto_url" :alt="selected.nombre_completo" class="w-20 h-20 rounded-full mx-auto mb-3 object-cover" />
            <div class="text-slate-600 text-xs">{{ selected.matricula }}</div>
            <div class="font-semibold text-slate-800 text-sm">{{ selected.nombre_completo }}</div>
            <div class="text-slate-700 text-xs">{{ selected.grupo }}</div>
          </div>
          <div class="grid grid-cols-3 gap-2 mt-4">
            <button type="button" @click.prevent="quitarFalta(selected)" class="px-2 py-2 rounded-md bg-emerald-100 text-emerald-800 hover:bg-emerald-200 w-full text-xs">Asistió</button>
            <button type="button" @click.prevent="marcarFalta(selected)" class="px-2 py-2 rounded-md bg-red-100 text-red-800 hover:bg-red-200 w-full text-xs">No Asistió</button>
            <button type="button" @click.prevent="marcarJustificado(selected)" class="px-2 py-2 rounded-md bg-blue-100 text-blue-800 hover:bg-blue-200 w-full text-xs">Justificado</button>
          </div>
        </div>
        <div v-else class="text-slate-500 text-center text-sm py-4">Selecciona un alumno</div>
      </div>
    </div>

    <!-- Lista de alumnos -->
    <div class="lg:col-span-8">
      <div class="flex flex-col md:flex-row md:items-center gap-3 mb-4">
        <div class="flex gap-2 w-full md:w-auto">
          <select v-model="filtros.grupo" class="rounded-lg border border-slate-300 px-2 py-2 text-sm">
            <option value="">Grupo/Grado</option>
            <option v-for="g in grupos" :key="g" :value="g">{{ g }}</option>
          </select>
          <select v-model="filtros.turno" class="rounded-lg border border-slate-300 px-2 py-2 text-sm">
            <option value="">Turno</option>
            <option value="Matutino">Matutino</option>
            <option value="Vespertino">Vespertino</option>
          </select>
          <input v-model="filtros.fecha" type="date" class="rounded-lg border border-slate-300 px-2 py-2 text-sm" />
        </div>
        <div class="flex gap-2 w-full">
          <input v-model="filtros.q" type="text" placeholder="Buscar por grupo, grado, nombre o matrícula"
                 class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
      </div>

      <!-- Botón Pasar Lista -->
      <div class="mb-4">
        <button 
          type="button" 
          @click="pasarLista" 
          :disabled="guardandoLista || alumnos.length === 0"
          class="w-full md:w-auto px-6 py-3 rounded-lg font-semibold text-white transition-colors"
          :class="guardandoLista || alumnos.length === 0 ? 'bg-slate-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
        >
          {{ guardandoLista ? 'Guardando...' : 'Pasar Lista' }}
        </button>
      </div>

      <!-- Tabla escritorio -->
      <div class="overflow-hidden rounded-xl border border-slate-200 hidden md:block">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Nombre</th>
              <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Matrícula</th>
              <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Grupo</th>
              <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Falta hoy</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200 bg-white">
            <tr v-for="a in alumnos" :key="a.id" :class="selected?.id === a.id ? 'bg-blue-50' : ''" @click="selected = a">
              <td class="px-4 py-2">{{ a.nombre_completo }}</td>
              <td class="px-4 py-2">{{ a.matricula }}</td>
              <td class="px-4 py-2">{{ a.grupo }}</td>
              <td class="px-4 py-2">
                <span v-if="a.justificado" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">Justificado</span>
                <span v-else-if="a.tiene_falta" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">No asistió</span>
                <span v-else class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-emerald-100 text-emerald-700">Asistió</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Lista móvil (cards) -->
      <div class="md:hidden space-y-2">
        <div v-for="a in alumnos" :key="a.id"
             class="bg-white border border-slate-200 rounded-xl p-4 flex items-center justify-between"
             :class="selected?.id === a.id ? 'border-blue-400 bg-blue-50' : ''"
             @click="selected = a">
          <div>
            <div class="font-semibold text-slate-800 leading-tight text-sm">{{ a.nombre_completo }}</div>
            <div class="text-slate-600 text-xs">Matrícula: {{ a.matricula }}</div>
            <div class="text-slate-600 text-xs">Grupo: {{ a.grupo }}</div>
          </div>
          <div>
            <span v-if="a.justificado" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">Justificado</span>
            <span v-else-if="a.tiene_falta" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">No asistió</span>
            <span v-else class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-emerald-100 text-emerald-800">Asistió</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Panel de asistencia - Desktop (a la derecha) -->
    <div class="hidden lg:block lg:col-span-4">
      <div class="bg-white rounded-xl border border-slate-200 p-6 text-slate-800">
        <div v-if="selected">
          <div class="text-center">
            <img :src="selected.foto_url" :alt="selected.nombre_completo" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover" />
            <div class="text-slate-600 text-sm">{{ selected.matricula }}</div>
            <div class="font-semibold text-slate-800">{{ selected.nombre_completo }}</div>
            <div class="text-slate-700 text-sm">{{ selected.grupo }}</div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-6">
            <button type="button" @click.prevent="quitarFalta(selected)" class="px-4 py-3 rounded-md bg-emerald-100 text-emerald-800 hover:bg-emerald-200 w-full">Asistió</button>
            <button type="button" @click.prevent="marcarFalta(selected)" class="px-4 py-3 rounded-md bg-red-100 text-red-800 hover:bg-red-200 w-full">No Asistió</button>
            <button type="button" @click.prevent="marcarJustificado(selected)" class="px-4 py-3 rounded-md bg-blue-100 text-blue-800 hover:bg-blue-200 w-full">Justificado</button>
          </div>
        </div>
        <div v-else class="text-slate-500 text-center">Selecciona un alumno</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, reactive, ref, watch } from 'vue'
import axios from 'axios'

interface AlumnoRow { id: number; nombre_completo: string; matricula: string; grupo: string; tiene_falta: boolean; justificado: boolean; foto_url: string }

const alumnos = ref<AlumnoRow[]>([])
const selected = ref<AlumnoRow | null>(null)
const filtros = reactive({ q: '', fecha: new Date().toISOString().slice(0,10), grupo: '', turno: '' })
const grupos = ['1A','1B','1C','2A','2B','2C','3A','3B','3C']
const guardandoLista = ref(false)

let debounceTimer: ReturnType<typeof setTimeout> | null = null

async function fetchAlumnos() {
  const params = new URLSearchParams()
  if (filtros.q) params.set('q', filtros.q)
  if (filtros.fecha) params.set('fecha', filtros.fecha)
  if (filtros.grupo) params.set('grupo', filtros.grupo)
  if (filtros.turno) params.set('turno', filtros.turno)
  try {
    const { data } = await axios.get(`/asistencias?${params.toString()}`)
    alumnos.value = data.alumnos ?? []
    if (alumnos.value.length && !selected.value) selected.value = alumnos.value[0]
  } catch (e) {
    console.error('Error al cargar asistencias', e)
  }
}

// Búsqueda automática con debounce para la búsqueda de texto
watch(() => filtros.q, () => {
  if (debounceTimer) clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    fetchAlumnos()
  }, 300)
})

// Búsqueda inmediata para fecha, grupo y turno
watch(() => [filtros.fecha, filtros.grupo, filtros.turno], () => {
  fetchAlumnos()
})

async function marcarFalta(a: AlumnoRow) {
  try {
    await axios.post('/asistencias/falta', {
      alumno_id: a.id,
      fecha: filtros.fecha,
      grupo: filtros.grupo || null,
      turno: filtros.turno || null
    })
    await fetchAlumnos()
    alert('Se marcó la inasistencia correctamente.')
  } catch (e: any) {
    console.error('Error al marcar falta', e)
    alert('No se pudo marcar la inasistencia (' + (e.response?.status || 'error') + ').')
  }
}

async function quitarFalta(a: AlumnoRow) {
  try {
    await axios.delete('/asistencias/falta', {
      params: { alumno_id: a.id, fecha: filtros.fecha }
    })
    await fetchAlumnos()
    alert('Se marcó asistencia correctamente.')
  } catch (e: any) {
    console.error('Error al quitar falta', e)
    alert('No se pudo quitar la inasistencia (' + (e.response?.status || 'error') + ').')
  }
}

async function marcarJustificado(a: AlumnoRow) {
  try {
    await axios.post('/asistencias/justificado', {
      alumno_id: a.id,
      fecha: filtros.fecha,
      grupo: filtros.grupo || null,
      turno: filtros.turno || null
    })
    await fetchAlumnos()
    alert('Se marcó como justificado correctamente.')
  } catch (e: any) {
    console.error('Error al marcar justificado', e)
    alert('No se pudo marcar como justificado (' + (e.response?.status || 'error') + ').')
  }
}

async function pasarLista() {
  if (alumnos.value.length === 0) {
    alert('No hay alumnos para guardar.')
    return
  }

  const confirmacion = confirm(`¿Deseas guardar la lista de asistencia para ${alumnos.value.length} alumnos en la fecha ${filtros.fecha}?`)
  if (!confirmacion) return

  guardandoLista.value = true

  try {
    // Preparar datos: convertir el estado actual de cada alumno a formato ENUM
    const alumnosData = alumnos.value.map(a => ({
      id: a.id,
      estado: a.justificado ? 'justificado' : (a.tiene_falta ? 'falta' : 'presente')
    }))

    const { data } = await axios.post('/asistencias/pasar-lista', {
      fecha: filtros.fecha,
      alumnos: alumnosData
    })

    alert(data.mensaje || 'Lista guardada exitosamente.')
    await fetchAlumnos()
  } catch (e: any) {
    console.error('Error al pasar lista', e)
    alert('No se pudo guardar la lista de asistencia (' + (e.response?.status || 'error') + ').')
  } finally {
    guardandoLista.value = false
  }
}

onMounted(fetchAlumnos)
</script>

<style scoped>
</style>
