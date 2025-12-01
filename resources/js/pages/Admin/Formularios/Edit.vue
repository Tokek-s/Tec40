<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AdminLayout from '../../../layouts/AdminLayout.vue';

const props = defineProps<{
    formulario: {
        id: number;
        titulo: string;
        descripcion: string;
        fecha_inicio: string;
        fecha_fin: string;
        link_primero: string;
        link_segundo: string;
        link_tercero: string;
        activo: boolean;
        primero_activo: boolean;
        segundo_activo: boolean;
        tercero_activo: boolean;
    }
}>();

const form = useForm({
    id: props.formulario.id,
    titulo: props.formulario.titulo,
    descripcion: props.formulario.descripcion,
    fecha_inicio: props.formulario.fecha_inicio,
    fecha_fin: props.formulario.fecha_fin,
    link_primero: props.formulario.link_primero,
    link_segundo: props.formulario.link_segundo,
    link_tercero: props.formulario.link_tercero,
    activo: Boolean(props.formulario.activo),
    primero_activo: Boolean(props.formulario.primero_activo),
    segundo_activo: Boolean(props.formulario.segundo_activo),
    tercero_activo: Boolean(props.formulario.tercero_activo),
});

const submit = () => {
    form.put(route('admin.formularios.update', form.id));
};
</script>

<template>
    <AdminLayout>
        <Head title="Editar Formulario Externo" />

        <div class="max-w-4xl mx-auto py-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Editar Formulario Externo</h2>
                <Link :href="route('admin.formularios.index')" class="text-indigo-600 hover:text-indigo-900 font-medium">
                    &larr; Volver
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    
                    <!-- Información Básica -->
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="titulo" class="block text-sm font-medium text-gray-700">Título del Formulario</label>
                            <input id="titulo" v-model="form.titulo" type="text" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-900" />
                            <div v-if="form.errors.titulo" class="text-red-500 text-xs mt-1">{{ form.errors.titulo }}</div>
                        </div>

                        <div>
                            <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                            <textarea id="descripcion" v-model="form.descripcion" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-900"></textarea>
                            <div v-if="form.errors.descripcion" class="text-red-500 text-xs mt-1">{{ form.errors.descripcion }}</div>
                        </div>
                    </div>

                    <!-- Fechas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                            <input id="fecha_inicio" v-model="form.fecha_inicio" type="date" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-900" />
                            <div v-if="form.errors.fecha_inicio" class="text-red-500 text-xs mt-1">{{ form.errors.fecha_inicio }}</div>
                        </div>

                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Fecha de Fin</label>
                            <input id="fecha_fin" v-model="form.fecha_fin" type="date" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-900" />
                            <div v-if="form.errors.fecha_fin" class="text-red-500 text-xs mt-1">{{ form.errors.fecha_fin }}</div>
                        </div>
                    </div>

                    <hr class="border-gray-200" />

                    <!-- Links y Activación por Grado -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Enlaces y Configuración por Grado</h3>
                        <p class="text-sm text-gray-500 mb-4">Ingresa los enlaces a los formularios (Google Forms, Microsoft Forms, etc.) y marca qué grados están activos.</p>
                        
                        <div class="space-y-4">
                            <!-- 1er Grado -->
                            <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-md border border-gray-200">
                                <div class="flex items-center h-5 mt-2">
                                    <input id="primero_activo" v-model="form.primero_activo" type="checkbox"
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                                </div>
                                <div class="flex-1">
                                    <label for="primero_activo" class="font-medium text-gray-700">Activar para 1° Grado</label>
                                    <div class="mt-2">
                                        <label for="link_primero" class="block text-xs font-medium text-gray-500 uppercase">Link Formulario 1°</label>
                                        <input id="link_primero" v-model="form.link_primero" type="url" placeholder="https://..."
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-900" />
                                        <div v-if="form.errors.link_primero" class="text-red-500 text-xs mt-1">{{ form.errors.link_primero }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2do Grado -->
                            <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-md border border-gray-200">
                                <div class="flex items-center h-5 mt-2">
                                    <input id="segundo_activo" v-model="form.segundo_activo" type="checkbox"
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                                </div>
                                <div class="flex-1">
                                    <label for="segundo_activo" class="font-medium text-gray-700">Activar para 2° Grado</label>
                                    <div class="mt-2">
                                        <label for="link_segundo" class="block text-xs font-medium text-gray-500 uppercase">Link Formulario 2°</label>
                                        <input id="link_segundo" v-model="form.link_segundo" type="url" placeholder="https://..."
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-900" />
                                        <div v-if="form.errors.link_segundo" class="text-red-500 text-xs mt-1">{{ form.errors.link_segundo }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- 3er Grado -->
                            <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-md border border-gray-200">
                                <div class="flex items-center h-5 mt-2">
                                    <input id="tercero_activo" v-model="form.tercero_activo" type="checkbox"
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                                </div>
                                <div class="flex-1">
                                    <label for="tercero_activo" class="font-medium text-gray-700">Activar para 3° Grado</label>
                                    <div class="mt-2">
                                        <label for="link_tercero" class="block text-xs font-medium text-gray-500 uppercase">Link Formulario 3°</label>
                                        <input id="link_tercero" v-model="form.link_tercero" type="url" placeholder="https://..."
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-900" />
                                        <div v-if="form.errors.link_tercero" class="text-red-500 text-xs mt-1">{{ form.errors.link_tercero }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-200" />

                    <!-- Estado General -->
                    <div class="flex items-center">
                        <input id="activo" v-model="form.activo" type="checkbox"
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                        <label for="activo" class="ml-2 block text-sm text-gray-900 font-bold">
                            Formulario Activo (General)
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 ml-6">Si desactivas esto, el formulario no se mostrará en absoluto, independientemente de las fechas o grados.</p>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <Link :href="route('admin.formularios.index')"
                            class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancelar
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                            {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
