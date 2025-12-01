<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-200 flex items-center justify-center px-4">
    <div class="bg-white rounded-lg shadow-xl p-12 text-center max-w-2xl">
      <div class="text-6xl mb-6">⚠️</div>
      <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ mensaje }}</h1>
      <p class="text-gray-600 mb-8">
        Por favor, intente más tarde o contacte a la escuela para más información.
      </p>
      <a
        href="/"
        class="inline-block bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors"
      >
        Volver al Inicio
      </a>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  mensaje: string;
}

defineProps<Props>();
</script>