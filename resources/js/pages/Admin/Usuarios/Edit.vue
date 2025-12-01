<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface Usuario {
  id: number;
  nombre_s: string;
  apellido_paterno: string;
  apellido_materno: string | null;
  correo: string;
  rol: string;
  activo: boolean;
}

interface Props {
  usuario: Usuario;
  roles: string[];
}

const props = defineProps<Props>();

const form = ref({
  nombre_s: props.usuario.nombre_s,
  apellido_paterno: props.usuario.apellido_paterno,
  apellido_materno: props.usuario.apellido_materno || '',
  correo: props.usuario.correo,
  contrasena: '',
  rol: props.usuario.rol,
  activo: props.usuario.activo,
});

const actualizarUsuario = () => {
  router.put(`/admin/usuarios/${props.usuario.id}`, form.value);
};

const volver = () => {
  router.get('/admin/usuarios');
};
</script>

<template>
  <AdminLayout>
    <Head title="Editar Usuario" />
    
    <div class="max-w-4xl mx-auto">
      <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Usuario</h1>
            <p class="text-gray-600 mt-1">Actualizar información del usuario</p>
          </div>
          <button
            @click="volver"
            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors"
          >
            ← Volver
          </button>
        </div>

        <form @submit.prevent="actualizarUsuario" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Nombre -->
            <div>
              <label class="block text-sm font-medium text-black mb-2">Nombre(s) *</label>
              <input
                v-model="form.nombre_s"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
              />
            </div>

            <!-- Apellido Paterno -->
            <div>
              <label class="block text-sm font-medium text-black mb-2">Apellido Paterno *</label>
              <input
                v-model="form.apellido_paterno"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
              />
            </div>

            <!-- Apellido Materno -->
            <div>
              <label class="block text-sm font-medium text-black mb-2">Apellido Materno</label>
              <input
                v-model="form.apellido_materno"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
              />
            </div>

            <!-- Correo -->
            <div>
              <label class="block text-sm font-medium text-black mb-2">Correo Electrónico *</label>
              <input
                v-model="form.correo"
                type="email"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
              />
            </div>

            <!-- Nueva Contraseña -->
            <div>
              <label class="block text-sm font-medium text-black mb-2">Nueva Contraseña</label>
              <input
                v-model="form.contrasena"
                type="password"
                placeholder="Dejar vacío para no cambiar"
                minlength="8"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
              />
              <p class="text-xs text-gray-500 mt-1">Dejar vacío si no desea cambiar la contraseña</p>
            </div>

            <!-- Rol -->
            <div>
              <label class="block text-sm font-medium text-black mb-2">Rol *</label>
              <select
                v-model="form.rol"
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
              v-model="form.activo"
              type="checkbox"
              id="activo"
              class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
            />
            <label for="activo" class="ml-2 text-sm text-black">Usuario activo</label>
          </div>

          <!-- Botones -->
          <div class="flex gap-3 pt-4 border-t">
            <button
              type="button"
              @click="volver"
              class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors"
            >
              Cancelar
            </button>
            <button
              type="submit"
              class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
            >
              Actualizar Usuario
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
