<template>
  <AdminLayout>
    <Head :title="pageTitle" />

    <div class="max-w-5xl mx-auto">
      <!-- Encabezado -->
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-black">{{ headerTitle }}</h1>
        <Link :href="route('admin.anuncios.index')" class="text-black hover:opacity-80">Volver a la lista</Link>
      </div>

      <!-- Card del formulario (según diseño) -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 text-black" style="color:#000;">
        <form @submit.prevent="submit">
          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm text-black mb-1">Título</label>
              <input v-model="form.titulo" type="text" class="w-full px-3 py-2 rounded-md border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-black placeholder-black" style="color:#000;" required />
            </div>
            <div>
              <label class="block text-sm text-black mb-1">Fecha</label>
              <input v-model="form.fecha" type="date" class="w-full px-3 py-2 rounded-md border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-black placeholder-black" style="color:#000;" />
            </div>
          </div>

          <div class="mt-6">
            <label class="block text-sm text-black mb-1">Descripción</label>
            <textarea v-model="form.contenido" rows="3" class="w-full px-3 py-2 rounded-md border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-black placeholder-black" style="color:#000;" placeholder="Escribe el contenido del anuncio" />
          </div>

          <!-- Dropzone simple -->
          <div class="mt-6">
            <label class="block text-sm text-black mb-2">Subir imagen</label>
            <div
              class="rounded-lg border-2 border-dashed border-slate-300 p-6 text-center cursor-pointer hover:bg-slate-50"
              @dragover.prevent
              @drop.prevent="onDrop"
              @click="triggerFile"
            >
              <input ref="fileRef" type="file" accept="image/*" class="hidden" @change="onFileChange" />
              <div v-if="previewUrl" class="flex items-center justify-center">
                <img :src="previewUrl" class="max-h-40 object-contain rounded" />
              </div>
              <div v-else class="text-black" style="color:#000;">
                <div class="text-2xl text-black" style="color:#000;">⬆</div>
                <p class="mt-2 text-black" style="color:#000;">Arrastra tu imagen o haz clic para seleccionar</p>
                <p class="text-xs mt-1 text-black" style="color:#000;">Las imágenes se verán mejor en formato 3:2 y PNG/JPG</p>
              </div>
            </div>
            <div v-if="mode==='edit' && form.image_url && !fileSelected" class="mt-2 flex items-center gap-3">
              <img :src="form.image_url" class="h-12 w-12 object-cover rounded border" />
              <label class="inline-flex items-center gap-2 text-sm text-black">
                <input type="checkbox" v-model="form.remove_imagen" /> Quitar imagen actual
              </label>
            </div>
          </div>

          <div class="mt-6 flex items-center justify-between">
            <label class="inline-flex items-center gap-2 text-black">
              <input type="checkbox" v-model="form.activo" :true-value="1" :false-value="0" />
              Publicar anuncio
            </label>
            <div class="space-x-2">
              <Link :href="route('admin.anuncios.index')" class="px-4 py-2 rounded-md bg-slate-100 hover:bg-slate-200 text-black">Cancelar</Link>
              <button type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">{{ submitLabel }}</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, reactive, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import AdminLayout from '../../../layouts/AdminLayout.vue';

interface AnuncioDto {
  id?: number;
  titulo: string;
  contenido?: string|null;
  fecha?: string|null;
  activo: number|boolean;
  image_url?: string|null;
  ruta_imagen?: string|null;
  remove_imagen?: boolean;
}

const props = defineProps<{ mode: 'create'|'edit', anuncio: AnuncioDto|null }>();

const form = reactive<AnuncioDto>({
  id: props.anuncio?.id,
  titulo: props.anuncio?.titulo ?? '',
  contenido: props.anuncio?.contenido ?? '',
  fecha: props.anuncio?.fecha ?? '',
  activo: props.anuncio?.activo ?? 1,
  image_url: props.anuncio?.image_url ?? null,
  ruta_imagen: props.anuncio?.ruta_imagen ?? null,
  remove_imagen: false,
});

const pageTitle = computed(() => props.mode === 'edit' ? 'Editar anuncio' : 'Agregar anuncio');
const headerTitle = pageTitle;
const submitLabel = computed(() => props.mode === 'edit' ? 'Actualizar' : 'Publicar anuncio');

const fileRef = ref<HTMLInputElement|null>(null);
const fileSelected = ref(false);
const previewUrl = ref<string|undefined>();

const triggerFile = () => fileRef.value?.click();
const onFileChange = (e: Event) => {
  const input = e.target as HTMLInputElement;
  const f = input.files?.[0];
  if (f) {
    fileSelected.value = true;
    previewUrl.value = URL.createObjectURL(f);
  } else {
    fileSelected.value = false;
    previewUrl.value = undefined;
  }
};
const onDrop = (e: DragEvent) => {
  const f = e.dataTransfer?.files?.[0];
  if (f && fileRef.value) {
    const dt = new DataTransfer();
    dt.items.add(f);
    fileRef.value.files = dt.files;
    fileSelected.value = true;
    previewUrl.value = URL.createObjectURL(f);
  }
};

const submit = () => {
  const data = new FormData();
  data.append('titulo', String(form.titulo));
  data.append('contenido', String(form.contenido ?? ''));
  if (form.fecha) data.append('fecha', String(form.fecha));
  data.append('activo', String(form.activo ? 1 : 0));
  if (fileRef.value?.files?.[0]) data.append('imagen', fileRef.value.files[0]);
  if (props.mode === 'edit' && form.remove_imagen) data.append('remove_imagen', '1');

  if (props.mode === 'edit' && form.id) {
    data.append('_method', 'PUT');
    router.post(route('admin.anuncios.update', { anuncio: form.id }), data, { forceFormData: true });
  } else {
    router.post(route('admin.anuncios.store'), data, { forceFormData: true });
  }
};
</script>
