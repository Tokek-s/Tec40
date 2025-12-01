<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const form = useForm({ correo: '', contrasena: '' });
const submit = () => form.post(route('admin.login.submit'), { onFinish: () => form.reset('contrasena') });
</script>

<template>
    <Head title="Acceso Administrativo - Técnica 40" />
    <div class="min-h-screen" style="background: linear-gradient(135deg, #F5F5F5 0%, #E0E0E0 25%, #F5F5F5 50%, #E0E0E0 75%, #F5F5F5 100%);">
        <header class="fixed top-0 left-0 right-0 backdrop-blur-md shadow-xl z-50 border-b" style="background-color: rgba(198, 40, 40, 0.95); border-bottom-color: rgba(158, 158, 158, 0.3);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-3">
                    <div class="flex items-center space-x-3">
                        <img src="/images/logo-escuela.png" alt="Logo Escuela Técnica 40" class="h-10 w-10 object-contain" />
                        <div>
                            <h1 class="text-white font-medium text-sm">Escuela Secundaria</h1>
                            <p class="text-lg font-bold" style="color: #FDD835;">Técnica 40</p>
                        </div>
                    </div>
                    <Link href="/" class="font-medium hover:shadow-lg text-sm transition-all duration-300 px-4 py-2 rounded-full flex items-center space-x-2 info-button" style="background-color: #FFFFFF; color: #424242;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        <span>Regresar al Menú</span>
                    </Link>
                </div>
            </div>
        </header>

        <div class="flex items-center justify-center min-h-screen px-4 py-12 pt-24">
            <div class="w-full max-w-lg">
                <div class="rounded-2xl shadow-2xl p-8 backdrop-blur-sm" style="background-color: rgba(255, 255, 255, 0.95);">
                    <div class="text-center mb-8">
                        <div class="mb-6">
                            <img src="/images/logo-escuela.png" alt="Logo Escuela Técnica 40" class="h-16 w-16 object-contain mx-auto" />
                        </div>
                        <h1 class="text-3xl font-bold mb-3" style="color: #212121;">Acceso Administrativo</h1>
                        <p class="text-lg" style="color: #424242;">Portal exclusivo para personal autorizado</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label for="email" class="block text-sm font-semibold mb-2" style="color: #424242;">Correo</label>
                            <input id="email" v-model="form.correo" type="email" required autocomplete="username" class="w-full px-4 py-3 border rounded-lg transition-all duration-300 focus:outline-none focus:ring-2" style="border-color: #9E9E9E; color: #212121;" placeholder="usuario@tecnica40.edu.mx" />
                            <div v-if="form.errors.correo" class="mt-2 text-sm" style="color: #C62828;">{{ form.errors.correo }}</div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold mb-2" style="color: #424242;">Contraseña</label>
                            <input id="password" v-model="form.contrasena" type="password" required autocomplete="current-password" class="w-full px-4 py-3 border rounded-lg transition-all duration-300 focus:outline-none focus:ring-2" style="border-color: #9E9E9E; color: #212121;" placeholder="Contraseña segura" />
                            <div v-if="form.errors.contrasena" class="mt-2 text-sm" style="color: #C62828;">{{ form.errors.contrasena }}</div>
                        </div>

                        <button type="submit" :disabled="form.processing" class="w-full py-4 px-6 rounded-lg font-semibold text-white transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed transform hover:scale-105" style="background-color: #C62828;">
                            <div v-if="form.processing" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                </svg>
                                Verificando credenciales...
                            </div>
                            <span v-else>Iniciar Sesión</span>
                        </button>
                    </form>

                    <div class="mt-6 pt-6" style="border-top: 1px solid rgba(158, 158, 158, 0.2);">
                        <div class="text-center">
                            <p class="text-sm font-medium mb-2" style="color: #424242;">¿Problemas de acceso?</p>
                            <p class="text-xs" style="color: #9E9E9E;">Contacta al administrador del sistema para soporte técnico</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.info-button:hover { background-color: #E0E0E0 !important; color: #212121 !important; transform: translateY(-1px); }
html { scroll-behavior: smooth; }
</style>
