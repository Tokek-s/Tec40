<template>
  <AdminLayout>
    <Head :title="`Perfil de ${alumno.nombre_completo}`" />
    
    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Header -->
      <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <button
              @click="router.visit('/admin/alumnos/lista')"
              class="text-gray-600 hover:text-gray-900"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
              </svg>
            </button>
            <div>
              <h1 class="text-2xl font-bold text-gray-900">{{ alumno.nombre_completo }}</h1>
              <p class="text-gray-600 mt-1">Matrícula: {{ alumno.matricula }}</p>
            </div>
          </div>
          
          <button
            @click="guardarCambios"
            :disabled="guardando"
            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed"
          >
            <span v-if="!guardando">Guardar Cambios</span>
            <span v-else>Guardando...</span>
          </button>
        </div>
      </div>

      <!-- Formulario -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna izquierda - Foto y datos básicos -->
        <div class="space-y-6">
          <!-- Foto del alumno -->
          <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Fotografía</h3>
            <div class="flex flex-col items-center">
              <img
                :src="vistaPrevia || alumno.foto_url"
                :alt="alumno.nombre_completo"
                class="w-32 h-32 rounded-full object-cover mb-4"
              />
              <button 
                @click="mostrarModalFoto = true"
                type="button"
                class="text-sm text-blue-600 hover:text-blue-800"
              >
                Cambiar foto
              </button>
            </div>
          </div>

          <!-- Estatus -->
          <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Estatus</h3>
            <select
              v-model="form.estatus"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
            >
              <option value="activo">Activo</option>
              <option value="baja">Baja</option>
              <option value="egresado">Egresado</option>
            </select>
          </div>
        </div>

        <!-- Columna central - Datos personales -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Datos Personales -->
          <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Datos Personales</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-black mb-2">Nombre(s)</label>
                <input
                  v-model="form.nombre_s"
                  type="text"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-black mb-2">Apellido Paterno</label>
                <input
                  v-model="form.apellido_paterno"
                  type="text"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-black mb-2">Apellido Materno</label>
                <input
                  v-model="form.apellido_materno"
                  type="text"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-black mb-2">CURP</label>
                <input
                  :value="alumno.curp"
                  type="text"
                  disabled
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-black mb-2">Fecha de Nacimiento</label>
                <input
                  v-model="form.fecha_nacimiento"
                  type="date"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-black mb-2">Sexo</label>
                <select
                  v-model="form.sexo"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                >
                  <option value="M">Masculino</option>
                  <option value="F">Femenino</option>
                  <option value="X">Otro</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Datos Académicos -->
          <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Datos Académicos</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-black mb-2">Grado/Grupo/Turno</label>
                <select
                  v-model="form.grupo_id"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                >
                  <option :value="null">Sin asignar</option>
                  <option v-for="grupo in grupos" :key="grupo.id" :value="grupo.id">
                    {{ grupo.nombre }}
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-black mb-2">Información Actual</label>
                <div class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-black">
                  <div v-if="alumno.grado && alumno.grupo">
                    <span class="font-medium">{{ alumno.grado }}° {{ alumno.grupo }}</span>
                    <span v-if="alumno.turno" class="ml-2 px-2 py-1 text-xs font-medium rounded-full" :class="{
                      'bg-yellow-100 text-yellow-800': alumno.turno === 'Matutino',
                      'bg-blue-100 text-blue-800': alumno.turno === 'Vespertino'
                    }">
                      {{ alumno.turno }}
                    </span>
                  </div>
                  <span v-else class="text-gray-400 text-sm">Sin grupo asignado</span>
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-black mb-2">Generación</label>
                <input
                  v-model="form.generacion"
                  type="text"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                />
              </div>
            </div>
          </div>

          <!-- Datos de Salud -->
          <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Datos de Salud</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-black mb-2">Tipo de Sangre</label>
                <select
                  v-model="form.salud.tipo_sangre"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                >
                  <option :value="null">Sin especificar</option>
                  <option value="A+">A+</option>
                  <option value="A-">A-</option>
                  <option value="B+">B+</option>
                  <option value="B-">B-</option>
                  <option value="AB+">AB+</option>
                  <option value="AB-">AB-</option>
                  <option value="O+">O+</option>
                  <option value="O-">O-</option>
                </select>
              </div>
              
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-black mb-2">Alergias</label>
                <textarea
                  v-model="form.salud.alergias"
                  rows="2"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                  placeholder="Ninguna o especificar alergias..."
                ></textarea>
              </div>
              
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-black mb-2">Enfermedades Crónicas</label>
                <textarea
                  v-model="form.salud.enfermedades_cronicas"
                  rows="2"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                  placeholder="Ninguna o especificar enfermedades..."
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Contactos -->
          <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-black">Contactos de Emergencia</h3>
              <button
                @click="mostrarModalContacto = true; editandoContacto = null; resetFormContacto()"
                class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition-colors"
              >
                + Agregar Contacto
              </button>
            </div>
            
            <div class="space-y-4">
              <div v-for="contacto in contactos" :key="contacto.id" 
                   class="border rounded-lg p-4"
                   :class="contacto.activo === false ? 'border-gray-300 bg-gray-50' : 'border-gray-200'">
                <div class="flex items-start justify-between mb-2">
                  <div class="flex items-start gap-3 flex-1">
                    <!-- Foto del contacto -->
                    <img
                      v-if="contacto.foto_url"
                      :src="contacto.foto_url"
                      :alt="contacto.nombre_completo"
                      class="w-12 h-12 rounded-full object-cover flex-shrink-0"
                      :class="contacto.activo === false ? 'opacity-50' : ''"
                    />
                    <div v-else class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0"
                         :class="contacto.activo === false ? 'opacity-50' : ''">
                      <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                    </div>
                    
                    <div class="flex-1">
                      <div class="flex items-center gap-2">
                        <h4 class="font-medium" :class="contacto.activo === false ? 'text-gray-500' : 'text-black'">
                          {{ contacto.nombre_completo }}
                        </h4>
                        <span v-if="contacto.es_principal" class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded">
                          Principal
                        </span>
                        <span v-if="contacto.activo === false" class="px-2 py-1 bg-gray-200 text-gray-600 text-xs rounded">
                          Inactivo
                        </span>
                      </div>
                      <p class="text-sm text-gray-600">{{ contacto.parentesco }}</p>
                    </div>
                  </div>
                  
                  <div class="flex items-center gap-2">
                    <button
                      @click="editarContacto(contacto)"
                      class="text-blue-600 hover:text-blue-900 p-1"
                      title="Editar contacto"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>
                    
                    <button
                      v-if="!contacto.es_principal"
                      @click="marcarComoPrincipal(contacto.id)"
                      class="text-yellow-600 hover:text-yellow-900 p-1"
                      title="Marcar como principal"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                      </svg>
                    </button>
                    
                    <button
                      @click="toggleActivoContacto(contacto.id, contacto.activo)"
                      :class="contacto.activo === false ? 'text-green-600 hover:text-green-900' : 'text-orange-600 hover:text-orange-900'"
                      class="p-1"
                      :title="contacto.activo === false ? 'Activar contacto' : 'Desactivar contacto'"
                    >
                      <svg v-if="contacto.activo === false" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                      </svg>
                    </button>
                    
                    <button
                      v-if="!contacto.es_principal"
                      @click="eliminarContacto(contacto.id)"
                      class="text-red-600 hover:text-red-900 p-1"
                      title="Eliminar contacto"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </div>
                
                <div class="grid grid-cols-2 gap-2 text-sm">
                  <div>
                    <span class="text-gray-600">Teléfono:</span>
                    <span :class="contacto.activo === false ? 'text-gray-500' : 'text-black'" class="ml-2">
                      {{ contacto.telefono || 'N/A' }}
                    </span>
                  </div>
                  <div>
                    <span class="text-gray-600">Correo:</span>
                    <span :class="contacto.activo === false ? 'text-gray-500' : 'text-black'" class="ml-2">
                      {{ contacto.correo || 'N/A' }}
                    </span>
                  </div>
                  <div class="col-span-2">
                    <span class="text-gray-600">Autorizado a recoger:</span>
                    <span :class="contacto.activo === false ? 'text-gray-500' : 'text-black'" class="ml-2">
                      {{ contacto.autorizado_recoger ? 'Sí' : 'No' }}
                    </span>
                  </div>
                </div>
              </div>
              
              <div v-if="contactos.length === 0" class="text-center py-8 text-gray-500">
                No hay contactos registrados
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para agregar/editar contacto -->
    <Teleport to="body">
      <div
        v-if="mostrarModalContacto"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        @click.self="mostrarModalContacto = false"
      >
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-xl font-bold text-black">
                {{ editandoContacto ? 'Editar Contacto' : 'Agregar Contacto' }}
              </h3>
              <button
                @click="mostrarModalContacto = false"
                class="text-gray-400 hover:text-gray-600"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <div class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-black mb-2">Nombre(s) *</label>
                  <input
                    v-model="formContacto.nombre_s"
                    type="text"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                    required
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-black mb-2">Apellido Paterno *</label>
                  <input
                    v-model="formContacto.apellido_paterno"
                    type="text"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                    required
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-black mb-2">Apellido Materno *</label>
                  <input
                    v-model="formContacto.apellido_materno"
                    type="text"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                    required
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-black mb-2">Parentesco *</label>
                  <input
                    v-model="formContacto.parentesco"
                    type="text"
                    placeholder="Ej: Madre, Padre, Tutor"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                    required
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-black mb-2">Teléfono</label>
                  <input
                    v-model="formContacto.telefono"
                    type="tel"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-black mb-2">Correo Electrónico</label>
                  <input
                    v-model="formContacto.correo"
                    type="email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-black"
                  />
                </div>
              </div>
              
              <!-- Foto del contacto -->
              <div>
                <label class="block text-sm font-medium text-black mb-2">Foto del Contacto</label>
                <div class="flex items-center gap-4">
                  <!-- Preview de la foto -->
                  <div class="flex-shrink-0">
                    <img
                      v-if="fotoContactoPreview"
                      :src="fotoContactoPreview"
                      alt="Preview"
                      class="w-24 h-24 rounded-full object-cover border-2 border-gray-300"
                    />
                    <div v-else class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-300">
                      <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                    </div>
                  </div>
                  
                  <!-- Botones -->
                  <div class="flex flex-col gap-2">
                    <input
                      ref="inputFotoContacto"
                      type="file"
                      accept="image/*"
                      @change="seleccionarFotoContacto"
                      class="hidden"
                    />
                    <button
                      type="button"
                      @click="inputFotoContacto?.click()"
                      class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors"
                    >
                      📁 Subir Foto
                    </button>
                    <button
                      type="button"
                      @click="abrirCamaraContacto"
                      class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors"
                    >
                      📷 Tomar Foto
                    </button>
                    <button
                      v-if="fotoContactoPreview"
                      type="button"
                      @click="eliminarFotoContacto"
                      class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors"
                    >
                      🗑️ Eliminar
                    </button>
                  </div>
                </div>
              </div>
              
              <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input
                    v-model="formContacto.autorizado_recoger"
                    type="checkbox"
                    class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                  />
                  <span class="text-sm text-black">Autorizado para recoger al alumno</span>
                </label>
                
                <label class="flex items-center gap-2 cursor-pointer">
                  <input
                    v-model="formContacto.es_principal"
                    type="checkbox"
                    class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                  />
                  <span class="text-sm text-black">Contacto principal</span>
                </label>
              </div>
            </div>

            <div class="flex gap-3 mt-6">
              <button
                @click="mostrarModalContacto = false"
                class="flex-1 px-4 py-2 border border-gray-300 text-black rounded-lg hover:bg-gray-50 transition-colors"
              >
                Cancelar
              </button>
              <button
                @click="guardarContacto"
                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
              >
                {{ editandoContacto ? 'Actualizar' : 'Agregar' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Modal para cámara del contacto -->
    <Teleport to="body">
      <div
        v-if="mostrarCamaraContacto"
        class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4"
      >
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-black">Tomar Foto del Contacto</h3>
            <button
              @click="cerrarCamaraContacto"
              class="text-gray-400 hover:text-gray-600"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          
          <div class="space-y-4">
            <div class="relative bg-gray-900 rounded-lg overflow-hidden" style="aspect-ratio: 4/3;">
              <video
                ref="videoContacto"
                autoplay
                playsinline
                class="w-full h-full object-cover"
              ></video>
            </div>
            
            <canvas ref="canvasContacto" class="hidden"></canvas>
            
            <div class="flex gap-3">
              <button
                @click="cerrarCamaraContacto"
                class="flex-1 px-4 py-2 border border-gray-300 text-black rounded-lg hover:bg-gray-50 transition-colors"
              >
                Cancelar
              </button>
              <button
                @click="tomarFotoContacto"
                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
              >
                📷 Capturar Foto
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Modal para cambiar foto -->
    <Teleport to="body">
      <div
        v-if="mostrarModalFoto"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        @click.self="cerrarModalFoto"
      >
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
          <h3 class="text-xl font-bold text-gray-900 mb-6">Cambiar Fotografía</h3>
          
          <div class="space-y-3">
            <button
              @click="seleccionarArchivo"
              class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
              </svg>
              Subir Foto
            </button>

            <button
              @click="activarCamara"
              class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              Tomar Foto
            </button>

            <button
              @click="cerrarModalFoto"
              class="w-full px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
            >
              Cancelar
            </button>
          </div>

          <!-- Input oculto para seleccionar archivo -->
          <input
            ref="inputArchivo"
            type="file"
            accept="image/*"
            class="hidden"
            @change="manejarArchivoSeleccionado"
          />
        </div>
      </div>
    </Teleport>

    <!-- Modal de cámara -->
    <Teleport to="body">
      <div
        v-if="mostrarCamara"
        class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4"
      >
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full p-6">
          <h3 class="text-xl font-bold text-gray-900 mb-4">Tomar Fotografía</h3>
          
          <div class="relative">
            <video
              ref="videoElement"
              autoplay
              playsinline
              class="w-full rounded-lg"
            ></video>
            <canvas ref="canvasElement" class="hidden"></canvas>
          </div>

          <div class="flex gap-3 mt-4">
            <button
              @click="tomarFoto"
              class="flex-1 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
            >
              Capturar
            </button>
            <button
              @click="cerrarCamara"
              class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
            >
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, nextTick } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../layouts/AdminLayout.vue';
import axios from 'axios';

interface Props {
  alumno: any;
  grupos: any[];
}

const props = defineProps<Props>();

// Debug: ver el valor del estatus que llega
console.log('Estatus del alumno:', props.alumno.estatus);

const form = useForm({
  nombre_s: props.alumno.nombre_s,
  apellido_paterno: props.alumno.apellido_paterno,
  apellido_materno: props.alumno.apellido_materno,
  fecha_nacimiento: props.alumno.fecha_nacimiento,
  sexo: props.alumno.sexo,
  generacion: props.alumno.generacion,
  grupo_id: props.alumno.grupo_id,
  estatus: props.alumno.estatus || 'activo',
  salud: {
    tipo_sangre: props.alumno.salud?.tipo_sangre || null,
    alergias: props.alumno.salud?.alergias || '',
    enfermedades_cronicas: props.alumno.salud?.enfermedades_cronicas || '',
  },
});

const guardando = ref(false);
const contactos = ref(props.alumno.contactos);
const mostrarModalContacto = ref(false);
const editandoContacto = ref<any>(null);

// Variables para manejo de foto
const mostrarModalFoto = ref(false);
const mostrarCamara = ref(false);
const vistaPrevia = ref<string | null>(null);
const archivoFoto = ref<File | null>(null);
const inputArchivo = ref<HTMLInputElement | null>(null);
const videoElement = ref<HTMLVideoElement | null>(null);
const canvasElement = ref<HTMLCanvasElement | null>(null);
let streamCamara: MediaStream | null = null;

const formContacto = ref({
  nombre_s: '',
  apellido_paterno: '',
  apellido_materno: '',
  parentesco: '',
  telefono: '',
  correo: '',
  autorizado_recoger: false,
  es_principal: false,
});

// Variables para la foto del contacto
const fotoContactoPreview = ref<string | null>(null);
const fotoContactoFile = ref<File | null>(null);
const inputFotoContacto = ref<HTMLInputElement | null>(null);
const mostrarCamaraContacto = ref(false);
const videoContacto = ref<HTMLVideoElement | null>(null);
const canvasContacto = ref<HTMLCanvasElement | null>(null);
let streamCamaraContacto: MediaStream | null = null;

const resetFormContacto = () => {
  formContacto.value = {
    nombre_s: '',
    apellido_paterno: '',
    apellido_materno: '',
    parentesco: '',
    telefono: '',
    correo: '',
    autorizado_recoger: false,
    es_principal: false,
  };
  fotoContactoPreview.value = null;
  fotoContactoFile.value = null;
  if (inputFotoContacto.value) {
    inputFotoContacto.value.value = '';
  }
};

const editarContacto = (contacto: any) => {
  editandoContacto.value = contacto;
  const nombrePartes = contacto.nombre_completo.split(' ');
  formContacto.value = {
    nombre_s: nombrePartes.slice(2).join(' ') || '',
    apellido_paterno: nombrePartes[0] || '',
    apellido_materno: nombrePartes[1] || '',
    parentesco: contacto.parentesco,
    telefono: contacto.telefono || '',
    correo: contacto.correo || '',
    autorizado_recoger: contacto.autorizado_recoger,
    es_principal: contacto.es_principal,
  };
  // Cargar foto actual si existe
  fotoContactoPreview.value = contacto.foto_url || null;
  fotoContactoFile.value = null;
  mostrarModalContacto.value = true;
};

const guardarContacto = async () => {
  try {
    const formData = new FormData();
    
    // Agregar campos del formulario
    formData.append('nombre_s', formContacto.value.nombre_s);
    formData.append('apellido_paterno', formContacto.value.apellido_paterno);
    formData.append('apellido_materno', formContacto.value.apellido_materno);
    formData.append('parentesco', formContacto.value.parentesco);
    formData.append('telefono', formContacto.value.telefono);
    formData.append('correo', formContacto.value.correo);
    formData.append('autorizado_recoger', formContacto.value.autorizado_recoger ? '1' : '0');
    formData.append('es_principal', formContacto.value.es_principal ? '1' : '0');
    
    // Agregar foto si existe
    if (fotoContactoFile.value) {
      formData.append('foto', fotoContactoFile.value);
    }
    
    if (editandoContacto.value) {
      // Actualizar contacto existente
      formData.append('_method', 'PUT');
      await axios.post(`/admin/contactos/${editandoContacto.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    } else {
      // Crear nuevo contacto
      await axios.post(`/admin/alumnos/${props.alumno.id}/contactos`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    }
    
    // Recargar la página para actualizar los datos
    router.reload();
    mostrarModalContacto.value = false;
    editandoContacto.value = null;
    resetFormContacto();
  } catch (error) {
    console.error('Error al guardar contacto:', error);
    alert('Error al guardar el contacto');
  }
};

const marcarComoPrincipal = async (contactoId: number) => {
  if (confirm('¿Desea marcar este contacto como principal?')) {
    try {
      await axios.patch(`/admin/contactos/${contactoId}/principal`);
      router.reload();
    } catch (error) {
      console.error('Error:', error);
      alert('Error al marcar como principal');
    }
  }
};

const toggleActivoContacto = async (contactoId: number, activo: boolean) => {
  const accion = activo === false ? 'activar' : 'desactivar';
  if (confirm(`¿Desea ${accion} este contacto?`)) {
    try {
      await axios.patch(`/admin/contactos/${contactoId}/toggle-activo`);
      router.reload();
    } catch (error) {
      console.error('Error:', error);
      alert(`Error al ${accion} el contacto`);
    }
  }
};

const eliminarContacto = async (contactoId: number) => {
  if (confirm('¿Está seguro de eliminar este contacto? Esta acción no se puede deshacer.')) {
    try {
      await axios.delete(`/admin/contactos/${contactoId}`);
      router.reload();
    } catch (error) {
      console.error('Error:', error);
      alert('Error al eliminar el contacto');
    }
  }
};

// Funciones para foto del contacto
const seleccionarFotoContacto = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files && input.files[0]) {
    const file = input.files[0];
    fotoContactoFile.value = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      fotoContactoPreview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  }
};

const abrirCamaraContacto = async () => {
  try {
    mostrarCamaraContacto.value = true;
    await nextTick();
    if (videoContacto.value) {
      streamCamaraContacto = await navigator.mediaDevices.getUserMedia({ 
        video: { facingMode: 'user' } 
      });
      videoContacto.value.srcObject = streamCamaraContacto;
    }
  } catch (error) {
    console.error('Error al acceder a la cámara:', error);
    alert('No se pudo acceder a la cámara');
    mostrarCamaraContacto.value = false;
  }
};

const tomarFotoContacto = () => {
  if (videoContacto.value && canvasContacto.value) {
    const context = canvasContacto.value.getContext('2d');
    if (context) {
      canvasContacto.value.width = videoContacto.value.videoWidth;
      canvasContacto.value.height = videoContacto.value.videoHeight;
      context.drawImage(videoContacto.value, 0, 0);
      
      canvasContacto.value.toBlob((blob) => {
        if (blob) {
          fotoContactoFile.value = new File([blob], 'foto-contacto.jpg', { type: 'image/jpeg' });
          fotoContactoPreview.value = canvasContacto.value!.toDataURL('image/jpeg');
          cerrarCamaraContacto();
        }
      }, 'image/jpeg');
    }
  }
};

const cerrarCamaraContacto = () => {
  if (streamCamaraContacto) {
    streamCamaraContacto.getTracks().forEach(track => track.stop());
    streamCamaraContacto = null;
  }
  mostrarCamaraContacto.value = false;
};

const eliminarFotoContacto = () => {
  fotoContactoPreview.value = null;
  fotoContactoFile.value = null;
  if (inputFotoContacto.value) {
    inputFotoContacto.value.value = '';
  }
};

const guardarCambios = () => {
  // Si hay foto, usar router.post con FormData
  if (archivoFoto.value) {
    const formData = new FormData();
    
    // Agregar todos los campos del formulario
    formData.append('_method', 'PUT');
    formData.append('nombre_s', form.nombre_s || '');
    formData.append('apellido_paterno', form.apellido_paterno || '');
    formData.append('apellido_materno', form.apellido_materno || '');
    formData.append('fecha_nacimiento', form.fecha_nacimiento || '');
    formData.append('sexo', form.sexo || '');
    formData.append('generacion', form.generacion || '');
    formData.append('estatus', form.estatus || 'activo');
    
    if (form.grupo_id) {
      formData.append('grupo_id', form.grupo_id.toString());
    }
    
    // Agregar datos de salud (siempre enviar, aunque sea vacío)
    formData.append('salud[tipo_sangre]', form.salud.tipo_sangre || '');
    formData.append('salud[alergias]', form.salud.alergias || '');
    formData.append('salud[enfermedades_cronicas]', form.salud.enfermedades_cronicas || '');
    
    // Agregar foto
    formData.append('foto', archivoFoto.value);
    
    // Log para debug
    console.log('Enviando FormData con estatus:', form.estatus);
    
    router.post(`/admin/alumnos/${props.alumno.id}/actualizar`, formData, {
      preserveScroll: true,
      onSuccess: () => {
        vistaPrevia.value = null;
        archivoFoto.value = null;
        alert('Datos actualizados correctamente');
      },
      onError: (errors) => {
        console.error('Errores:', errors);
        alert('Error al actualizar los datos: ' + JSON.stringify(errors));
      },
    });
  } else {
    // Si no hay foto, usar el método normal de Inertia
    form.put(`/admin/alumnos/${props.alumno.id}/actualizar`, {
      preserveScroll: true,
      onSuccess: () => {
        alert('Datos actualizados correctamente');
      },
      onError: (errors) => {
        console.error('Errores:', errors);
        alert('Error al actualizar los datos');
      },
    });
  }
};

// Funciones para manejo de foto
const cerrarModalFoto = () => {
  mostrarModalFoto.value = false;
};

const seleccionarArchivo = () => {
  inputArchivo.value?.click();
};

const manejarArchivoSeleccionado = async (event: Event) => {
  const target = event.target as HTMLInputElement;
  const archivo = target.files?.[0];
  
  if (!archivo) return;
  
  // Validar tamaño (máximo 5MB)
  const MAX_SIZE = 5 * 1024 * 1024; // 5MB
  if (archivo.size > MAX_SIZE) {
    alert('La imagen es demasiado grande. El tamaño máximo es 5MB.');
    return;
  }
  
  // Validar tipo
  if (!archivo.type.startsWith('image/')) {
    alert('Solo se permiten archivos de imagen.');
    return;
  }
  
  try {
    // Redimensionar imagen
    const imagenRedimensionada = await redimensionarImagen(archivo, 800, 800);
    archivoFoto.value = imagenRedimensionada;
    
    // Crear vista previa
    const reader = new FileReader();
    reader.onload = (e) => {
      vistaPrevia.value = e.target?.result as string;
    };
    reader.readAsDataURL(imagenRedimensionada);
    
    mostrarModalFoto.value = false;
  } catch (error) {
    console.error('Error al procesar imagen:', error);
    alert('Error al procesar la imagen');
  }
};

const activarCamara = async () => {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ 
      video: { facingMode: 'user', width: 1280, height: 720 } 
    });
    
    streamCamara = stream;
    mostrarCamara.value = true;
    mostrarModalFoto.value = false;
    
    // Esperar un tick para que el video element esté en el DOM
    setTimeout(() => {
      if (videoElement.value) {
        videoElement.value.srcObject = stream;
      }
    }, 100);
  } catch (error) {
    console.error('Error al acceder a la cámara:', error);
    alert('No se pudo acceder a la cámara. Verifique los permisos.');
  }
};

const tomarFoto = async () => {
  if (!videoElement.value || !canvasElement.value) return;
  
  const video = videoElement.value;
  const canvas = canvasElement.value;
  
  // Configurar canvas con dimensiones del video
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  
  // Dibujar frame actual del video en el canvas
  const ctx = canvas.getContext('2d');
  if (!ctx) return;
  
  ctx.drawImage(video, 0, 0);
  
  // Convertir canvas a blob
  canvas.toBlob(async (blob) => {
    if (!blob) return;
    
    try {
      // Crear archivo desde blob
      const archivo = new File([blob], `foto_${props.alumno.matricula}.jpg`, { type: 'image/jpeg' });
      
      // Redimensionar imagen
      const imagenRedimensionada = await redimensionarImagen(archivo, 800, 800);
      archivoFoto.value = imagenRedimensionada;
      
      // Crear vista previa
      const reader = new FileReader();
      reader.onload = (e) => {
        vistaPrevia.value = e.target?.result as string;
      };
      reader.readAsDataURL(imagenRedimensionada);
      
      cerrarCamara();
    } catch (error) {
      console.error('Error al procesar foto:', error);
      alert('Error al procesar la foto');
    }
  }, 'image/jpeg', 0.9);
};

const cerrarCamara = () => {
  if (streamCamara) {
    streamCamara.getTracks().forEach(track => track.stop());
    streamCamara = null;
  }
  mostrarCamara.value = false;
};

const redimensionarImagen = (archivo: File, maxWidth: number, maxHeight: number): Promise<File> => {
  return new Promise((resolve, reject) => {
    const img = new Image();
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    
    if (!ctx) {
      reject(new Error('No se pudo crear contexto de canvas'));
      return;
    }
    
    img.onload = () => {
      let width = img.width;
      let height = img.height;
      
      // Calcular nuevas dimensiones manteniendo aspecto
      if (width > height) {
        if (width > maxWidth) {
          height = (height * maxWidth) / width;
          width = maxWidth;
        }
      } else {
        if (height > maxHeight) {
          width = (width * maxHeight) / height;
          height = maxHeight;
        }
      }
      
      canvas.width = width;
      canvas.height = height;
      
      // Dibujar imagen redimensionada
      ctx.drawImage(img, 0, 0, width, height);
      
      // Convertir a blob y luego a File
      canvas.toBlob((blob) => {
        if (!blob) {
          reject(new Error('Error al crear blob'));
          return;
        }
        
        const nuevoArchivo = new File(
          [blob], 
          `${props.alumno.matricula}.jpg`,
          { type: 'image/jpeg' }
        );
        
        resolve(nuevoArchivo);
      }, 'image/jpeg', 0.85);
    };
    
    img.onerror = () => reject(new Error('Error al cargar imagen'));
    img.src = URL.createObjectURL(archivo);
  });
};
</script>

