<template>
  <PrefectosLayout>
    <Head title="Pase de Lista (Prefectura)" />

    <div class="max-w-7xl mx-auto space-y-6">
      <div class="bg-white rounded-xl shadow p-4 md:p-6">
        <h2 class="text-xl md:text-2xl font-bold text-slate-800">Pase de Lista</h2>
        <p class="text-slate-600 text-sm">Vista restringida para Prefectura. Sin accesos al menú de administración.</p>
      </div>

      <div class="bg-white rounded-xl shadow p-6">
        <PaseListaCore />
      </div>
    </div>
  </PrefectosLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import PrefectosLayout from '../../../layouts/PrefectosLayout.vue'
import PaseListaCore from '../../../components/PaseListaCore.vue'
</script>
