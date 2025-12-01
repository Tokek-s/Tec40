<template>
  <AdminLayout>
    <Head title="Pase de Lista" />

    <div class="max-w-7xl mx-auto space-y-6">
      <div class="bg-white rounded-xl shadow p-4 md:p-6">
        <h2 class="text-xl md:text-2xl font-bold text-slate-800">Pase de Lista</h2>
        <p class="text-slate-600 text-sm">Unificado para todos los roles, con faltas por día.</p>
      </div>

      <div class="bg-white rounded-xl shadow p-6">
        <PaseListaCore />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AdminLayout from '../../../layouts/AdminLayout.vue'
import PaseListaCore from '../../../components/PaseListaCore.vue'
</script>
