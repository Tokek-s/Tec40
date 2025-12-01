<script setup lang="ts">
import { Head, router, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AdminLayout from '../../../layouts/AdminLayout.vue';

interface Formulario {
    id: number;
    titulo: string;
    descripcion: string;
    fecha_inicio: string;
    fecha_fin: string;
    activo: boolean;
    primero_activo: boolean;
    segundo_activo: boolean;
    tercero_activo: boolean;
    link_primero?: string;
    link_segundo?: string;
    link_tercero?: string;
}

const props = defineProps<{
    formularios: Formulario[];
}>();

const deleteFormulario = (id: number) => {
    if (confirm('¿Estás seguro de que deseas eliminar este formulario?')) {
        router.delete(route('admin.formularios.destroy', id));
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('es-MX', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
</script>

<template>
    <Head title="Gestión de Formularios" />
    <AdminLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">Gestión de Formularios Externos</h2>
                                <p class="mt-1 text-sm text-gray-600 max-w-3xl">
                                    Estos formularios son para información independiente o de consulta durante el año escolar (ej. encuestas, registros de eventos). 
                                    <strong>No son para el proceso de inscripción/reinscripción.</strong>
                                </p>
                            </div>
                            <Link :href="route('admin.formularios.create')" class="w-full sm:w-auto text-center px-4 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700">
                                Nuevo Formulario
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vigencia</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grados Activos</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="formulario in formularios" :key="formulario.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ formulario.titulo }}</div>
                                            <div class="text-sm text-gray-500 truncate max-w-xs">{{ formulario.descripcion }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">Del {{ formatDate(formulario.fecha_inicio) }}</div>
                                            <div class="text-sm text-gray-500">Al {{ formatDate(formulario.fecha_fin) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                formulario.activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                            ]">
                                                {{ formulario.activo ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex space-x-1">
                                                <span :class="formulario.primero_activo ? 'text-green-600 font-bold' : 'text-gray-300'">1°</span>
                                                <span :class="formulario.segundo_activo ? 'text-green-600 font-bold' : 'text-gray-300'">2°</span>
                                                <span :class="formulario.tercero_activo ? 'text-green-600 font-bold' : 'text-gray-300'">3°</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <Link :href="route('admin.formularios.edit', formulario.id)" class="px-3 py-1.5 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 transition-colors text-sm">Editar</Link>
                                                <button @click="deleteFormulario(formulario.id)" class="px-3 py-1.5 rounded-md bg-red-600 text-white hover:bg-red-700 transition-colors text-sm">Eliminar</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="formularios.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            No hay formularios registrados.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
