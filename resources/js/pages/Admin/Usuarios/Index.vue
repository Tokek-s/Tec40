<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface Usuario {
  id: number;
  nombre_completo: string;
  correo: string;
  rol: string;
  activo: boolean;
}

interface Props {
  usuarios: {
    data: Usuario[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
  };
  filtros: {
    busqueda: string | null;
    rol: string | null;
  };
  roles: string[];
}

const props = defineProps<Props>();

const page = usePage();
const userRol = computed(() => (page.props.auth as any)?.user?.rol);
const puedeEditar = computed(() => ['Direccion', 'Sistemas'].includes(userRol.value));

// Estado del modal de crear usuario
const mostrarModalCrear = ref(false);
const usuarioNuevo = ref({
  nombre_s: '',
  apellido_paterno: '',
  apellido_materno: '',
  correo: '',
  contrasena: '',
  rol: 'Administrativo',
  activo: true,
});

// Estado de filtros
const busqueda = ref(props.filtros.busqueda || '');
const rolFiltro = ref(props.filtros.rol || 'todos');

// Funciones
const aplicarFiltros = () => {
  router.get('/admin/usuarios', {
    busqueda: busqueda.value || undefined,
    rol: rolFiltro.value !== 'todos' ? rolFiltro.value : undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const limpiarFiltros = () => {
  busqueda.value = '';
  rolFiltro.value = 'todos';
  router.get('/admin/usuarios');
};

const crearUsuario = () => {
  router.post('/admin/usuarios', usuarioNuevo.value, {
    onSuccess: () => {
      mostrarModalCrear.value = false;
      usuarioNuevo.value = {
        nombre_s: '',
        apellido_paterno: '',
        apellido_materno: '',
        correo: '',
        contrasena: '',
        rol: 'Administrativo',
        activo: true,
      };
    },
  });
};

const editarUsuario = (id: number) => {
  router.get(`/admin/usuarios/${id}/edit`);
};

const eliminarUsuario = (id: number, nombre: string) => {
  if (confirm(`¿Estás seguro de eliminar al usuario ${nombre}?`)) {
    router.delete(`/admin/usuarios/${id}`);
  }
};

const getRolColor = (rol: string) => {
  const colors: Record<string, string> = {
    'Direccion': 'bg-red-100 text-red-800',
    'Subdireccion': 'bg-orange-100 text-orange-800',
    'Sistemas': 'bg-purple-100 text-purple-800',
    'Administrativo': 'bg-green-100 text-green-800',
    'Prefecto': 'bg-blue-100 text-blue-800',
    'Medico': 'bg-teal-100 text-teal-800',
    'Psicologo': 'bg-indigo-100 text-indigo-800',
  };
  return colors[rol] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
  <AdminLayout>
    <Head title="Gestión de Usuarios" />
    
    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Header -->
      <div class="bg-white rounded-xl shadow p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Usuarios</h1>
            <p class="text-gray-600 mt-1">Administración de usuarios del sistema</p>
          </div>
          
          <button
            v-if="puedeEditar"
            @click="mostrarModalCrear = true"
            class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Agregar Usuario
          </button>
        </div>
      </div>

      <!-- Filtros -->
      <div class="bg-white rounded-xl shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Búsqueda -->
          <div>
            <label class="block text-sm font-medium text-black mb-2">Buscar</label>
            <input
              v-model="busqueda"
              type="text"
              placeholder="Nombre o correo..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
              @keyup.enter="aplicarFiltros"
            />
          </div>

          <!-- Filtro por Rol -->
          <div>
            <label class="block text-sm font-medium text-black mb-2">Rol</label>
            <select
              v-model="rolFiltro"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
            >
              <option value="todos">Todos los roles</option>
              <option v-for="rol in roles" :key="rol" :value="rol">{{ rol }}</option>
            </select>
          </div>

          <!-- Botones -->
          <div class="flex items-end gap-2">
            <button
              @click="aplicarFiltros"
              class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
            >
              Filtrar
            </button>
            <button
              @click="limpiarFiltros"
              class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors"
            >
              Limpiar
            </button>
          </div>
        </div>
      </div>

      <!-- Tabla de usuarios -->
      <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                <th v-if="puedeEditar" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="usuario in usuarios.data" :key="usuario.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-black">{{ usuario.nombre_completo }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-600">{{ usuario.correo }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', getRolColor(usuario.rol)]">
                    {{ usuario.rol }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', usuario.activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                    {{ usuario.activo ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                <td v-if="puedeEditar" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex justify-end gap-2">
                    <button
                      @click="editarUsuario(usuario.id)"
                      class="text-blue-600 hover:text-blue-900"
                      title="Editar usuario"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>
                    <button
                      @click="eliminarUsuario(usuario.id, usuario.nombre_completo)"
                      class="text-red-600 hover:text-red-900"
                      title="Eliminar usuario"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        <div v-if="usuarios.last_page > 1" class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Mostrando {{ ((usuarios.current_page - 1) * usuarios.per_page) + 1 }} a {{ Math.min(usuarios.current_page * usuarios.per_page, usuarios.total) }} de {{ usuarios.total }} usuarios
            </div>
            <div class="flex gap-2">
              <a
                v-for="link in usuarios.links"
                :key="link.label"
                :href="link.url || '#'"
                :class="[
                  'px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                  link.active ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100',
                  !link.url && 'opacity-50 cursor-not-allowed'
                ]"
                v-html="link.label"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Crear Usuario -->
    <div
      v-if="mostrarModalCrear"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="mostrarModalCrear = false"
    >
      <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-black">Agregar Nuevo Usuario</h3>
            <button
              @click="mostrarModalCrear = false"
              class="text-gray-400 hover:text-gray-600"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <form @submit.prevent="crearUsuario" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Nombre -->
              <div>
                <label class="block text-sm font-medium text-black mb-2">Nombre(s) *</label>
                <input
                  v-model="usuarioNuevo.nombre_s"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                />
              </div>

              <!-- Apellido Paterno -->
              <div>
                <label class="block text-sm font-medium text-black mb-2">Apellido Paterno *</label>
                <input
                  v-model="usuarioNuevo.apellido_paterno"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                />
              </div>

              <!-- Apellido Materno -->
              <div>
                <label class="block text-sm font-medium text-black mb-2">Apellido Materno</label>
                <input
                  v-model="usuarioNuevo.apellido_materno"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                />
              </div>

              <!-- Correo -->
              <div>
                <label class="block text-sm font-medium text-black mb-2">Correo Electrónico *</label>
                <input
                  v-model="usuarioNuevo.correo"
                  type="email"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                />
              </div>

              <!-- Contraseña -->
              <div>
                <label class="block text-sm font-medium text-black mb-2">Contraseña *</label>
                <input
                  v-model="usuarioNuevo.contrasena"
                  type="password"
                  required
                  minlength="8"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                />
              </div>

              <!-- Rol -->
              <div>
                <label class="block text-sm font-medium text-black mb-2">Rol *</label>
                <select
                  v-model="usuarioNuevo.rol"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                >
                  <option v-for="rol in roles" :key="rol" :value="rol">{{ rol }}</option>
                </select>
              </div>
            </div>

            <!-- Estado Activo -->
            <div class="flex items-center">
              <input
                v-model="usuarioNuevo.activo"
                type="checkbox"
                id="activo"
                class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
              />
              <label for="activo" class="ml-2 text-sm text-black">Usuario activo</label>
            </div>

            <!-- Botones -->
            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="mostrarModalCrear = false"
                class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors"
              >
                Cancelar
              </button>
              <button
                type="submit"
                class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
              >
                Crear Usuario
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
