<template>
  <div class="min-h-screen bg-gradient-to-br from-red-50 to-gray-100 py-12 px-4">
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="text-center mb-6">
        <h1 class="text-xl sm:text-3xl font-bold text-red-700 mb-2">
          {{ tituloFormulario }}
        </h1>
        <p class="text-black font-semibold text-xs sm:text-sm">{{ cuestionario.descripcion }}</p>
      </div>

      <!-- Progreso -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-2">
          <span class="text-sm font-semibold text-black">Paso {{ pasoActual }} de 5</span>
          <span class="text-sm text-black">{{ Math.round((pasoActual / 5) * 100) }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div
            class="bg-red-600 h-2 rounded-full transition-all duration-300"
            :style="{ width: `${(pasoActual / 5) * 100}%` }"
          ></div>
        </div>
      </div>

      <!-- Formulario -->
      <div class="bg-white rounded-lg shadow-md p-6 sm:p-8 text-xs sm:text-sm">
        <form @submit.prevent="siguientePaso">
          <!-- Paso 1: Datos del Alumno -->
          <div v-if="pasoActual === 1">
            <h2 class="text-lg sm:text-xl font-bold text-black mb-4">Datos del Alumno</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 min-w-0">
              <div class="col-span-2">
                  <label class="block text-black font-bold text-xs sm:text-sm mb-2 min-w-0">CURP *</label>
                <input
                  v-model="formulario.curp"
                  type="text"
                  required
                  minlength="18"
                  maxlength="18"
                  pattern="[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[A-Z0-9]{2}"
                  class="w-full min-w-0 border-2 border-gray-400 rounded-lg px-3 py-2 uppercase bg-gray-50 text-black font-semibold text-xs sm:text-sm"
                  placeholder="AAAA000000HDFRRLGG"
                  @input="formulario.curp = formulario.curp.toUpperCase()"
                />
              </div>

              <div class="min-w-0">
                <label class="block text-black font-bold text-xs sm:text-sm mb-2">Nombre(s) *</label>
                <input
                  v-model="formulario.nombre"
                  type="text"
                  required
                   class="w-full min-w-0 border-2 border-gray-400 rounded-lg px-3 py-2 bg-gray-50 text-black font-semibold text-xs sm:text-sm"
                />
              </div>

              <div class="min-w-0">
                <label class="block text-black font-bold text-xs sm:text-sm mb-2">Apellido Paterno *</label>
                <input
                  v-model="formulario.apellido_paterno"
                  type="text"
                  required
                   class="w-full min-w-0 border-2 border-gray-400 rounded-lg px-3 py-2 bg-gray-50 text-black font-semibold text-xs sm:text-sm"
                />
              </div>

              <div class="min-w-0">
                <label class="block text-black font-bold text-xs sm:text-sm mb-2">Apellido Materno *</label>
                <input
                  v-model="formulario.apellido_materno"
                  type="text"
                  required
                   class="w-full min-w-0 border-2 border-gray-400 rounded-lg px-3 py-2 bg-gray-50 text-black font-semibold text-xs sm:text-sm"
                />
              </div>

              <div class="min-w-0">
                <label class="block text-black font-bold text-xs sm:text-sm mb-2">Fecha de Nacimiento *</label>
                <input
                  v-model="formulario.fecha_nacimiento"
                  type="date"
                  required
                   class="w-full min-w-0 border-2 border-gray-400 rounded-lg px-3 py-2 bg-gray-50 text-black font-semibold text-xs sm:text-sm"
                />
              </div>

              <div class="col-span-2 min-w-0">
                <label class="block text-black font-bold text-xs sm:text-sm mb-2">Sexo *</label>
                <div class="flex gap-4">
                  <label class="flex items-center gap-2">
                    <input
                      v-model="formulario.sexo"
                      type="radio"
                      value="M"
                      required
                      class="w-5 h-5"
                    />
                    <span class="text-black font-semibold text-xs sm:text-sm">Masculino</span>
                  </label>
                  <label class="flex items-center gap-2">
                    <input
                      v-model="formulario.sexo"
                      type="radio"
                      value="F"
                      required
                      class="w-5 h-5"
                    />
                    <span class="text-black font-semibold text-xs sm:text-sm">Femenino</span>
                  </label>
                  <label class="flex items-center gap-2">
                    <input
                      v-model="formulario.sexo"
                      type="radio"
                      value="O"
                      required
                      class="w-5 h-5"
                    />
                    <span class="text-black font-semibold text-xs sm:text-sm">Otro</span>
                  </label>
                </div>
              </div>

              <!-- Fotografía del Alumno -->
              <div class="col-span-2 min-w-0">
                <label class="block text-black font-bold mb-2 text-xs sm:text-sm">Fotografía del Alumno *</label>
                <div class="bg-blue-50 border-2 border-blue-300 rounded-lg p-4">
                  <div class="flex flex-col sm:flex-row gap-3 mb-3 min-w-0">
                    <button
                      type="button"
                      @click="abrirCamaraAlumno"
                      class="w-full sm:flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 sm:py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2 text-sm sm:text-base"
                    >
                      📷 Tomar Foto
                    </button>
                    <button
                      type="button"
                      @click="$refs.fotoAlumnoArchivo.click()"
                      class="w-full sm:flex-1 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-3 sm:py-3 sm:px-4 rounded-lg transition-colors flex items-center justify-center gap-2 text-sm sm:text-base"
                    >
                      📁 Subir Archivo
                    </button>
                  </div>
                  <input
                    ref="fotoAlumnoArchivo"
                    type="file"
                    @change="manejarFotoAlumno"
                    accept="image/jpeg,image/png,image/jpg"
                    class="hidden"
                  />
                  <p class="text-xs text-gray-600 mt-2">📸 Puedes tomar una foto con la cámara o subir una existente. Formato: JPG, PNG. Tamaño máximo: 2MB</p>
                  <div v-if="formulario.foto_alumno" class="mt-3">
                    <div class="flex items-center gap-3">
                      <img :src="obtenerVistaPrevia(formulario.foto_alumno)" class="w-24 h-24 object-cover rounded-lg border-2 border-blue-400" />
                      <div class="flex-1">
                        <span class="text-green-600 font-bold text-sm block">✓ Foto seleccionada</span>
                        <span class="text-gray-600 text-xs">{{ formulario.foto_alumno.name }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Paso 2: Datos de Salud -->
          <div v-if="pasoActual === 2">
            <h2 class="text-lg sm:text-xl font-bold text-black mb-4">Datos de Salud</h2>
            
            <div class="space-y-4">
              <div>
                <label class="block text-black font-bold text-xs sm:text-sm mb-2">Tipo de Sangre *</label>
                <select
                  v-model="formulario.tipo_sangre"
                  required
                  class="w-full border-2 border-gray-400 rounded-lg px-3 py-2 bg-gray-50 text-black font-semibold text-xs sm:text-sm"
                >
                  <option value="">Seleccione...</option>
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

              <div>
                <label class="block text-black font-bold text-xs sm:text-sm mb-2">Alergias</label>
                <textarea
                  v-model="formulario.alergias"
                  rows="3"
                  class="w-full border-2 border-gray-400 rounded-lg px-3 py-2 bg-gray-50 text-black font-semibold text-xs sm:text-sm"
                  placeholder="Describa si tiene alguna alergia..."
                ></textarea>
              </div>

              <div>
                <label class="block text-black font-bold mb-2">Enfermedades</label>
                <textarea
                  v-model="formulario.enfermedades"
                  rows="3"
                  class="w-full border-2 border-gray-400 rounded-lg px-4 py-2 bg-gray-50 text-black font-semibold"
                  placeholder="Describa si padece alguna enfermedad..."
                ></textarea>
              </div>

              <div>
                <label class="block text-black font-bold mb-2">Medicamentos</label>
                <textarea
                  v-model="formulario.medicamentos"
                  rows="3"
                  class="w-full border-2 border-gray-400 rounded-lg px-4 py-2 bg-gray-50 text-black font-semibold"
                  placeholder="Liste los medicamentos que toma regularmente..."
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Paso 3: Contactos de Emergencia -->
          <div v-if="pasoActual === 3">
            <h2 class="text-lg sm:text-xl font-bold text-black mb-4">Contactos de Emergencia</h2>
            
            <div v-for="(contacto, index) in formulario.contactos" :key="index" class="mb-6 p-4 border-2 border-blue-300 rounded-lg bg-blue-50 min-w-0">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm sm:text-base font-semibold text-black">Contacto {{ index + 1 }}</h3>
                <button
                  v-if="formulario.contactos.length > 1"
                  type="button"
                  @click="eliminarContacto(index)"
                  class="text-red-600 hover:text-red-800 font-bold"
                >
                  ✗ Eliminar
                </button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 min-w-0">
                <div>
                  <label class="block text-black font-bold text-xs sm:text-sm mb-2">Nombre(s) *</label>
                  <input
                    v-model="contacto.nombre"
                    type="text"
                    required
                    class="w-full min-w-0 border-2 border-gray-400 rounded-lg px-3 py-2 bg-white text-black font-semibold text-xs sm:text-sm"
                  />
                </div>

                <div>
                  <label class="block text-black font-bold text-xs sm:text-sm mb-2">Apellido Paterno *</label>
                  <input
                    v-model="contacto.apellido_paterno"
                    type="text"
                    required
                    class="w-full border-2 border-gray-400 rounded-lg px-3 py-2 bg-white text-black font-semibold text-xs sm:text-sm"
                  />
                </div>

                <div>
                  <label class="block text-black font-bold text-xs sm:text-sm mb-2">Apellido Materno *</label>
                  <input
                    v-model="contacto.apellido_materno"
                    type="text"
                    required
                    class="w-full border-2 border-gray-400 rounded-lg px-3 py-2 bg-white text-black font-semibold text-xs sm:text-sm"
                  />
                </div>

                <div class="min-w-0">
                  <label class="block text-black font-bold mb-2">Teléfono *</label>
                  <input
                    v-model="contacto.telefono"
                    type="tel"
                    required
                    pattern="[0-9]{10}"
                    maxlength="10"
                    class="w-full min-w-0 border-2 border-gray-400 rounded-lg px-4 py-2 bg-white text-black font-semibold"
                    placeholder="10 dígitos"
                  />
                </div>

                <div>
                  <label class="block text-black font-bold mb-2">Email</label>
                  <input
                    v-model="contacto.email"
                    type="email"
                    class="w-full border-2 border-gray-400 rounded-lg px-4 py-2 bg-white text-black font-semibold"
                  />
                </div>

                <div>
                  <label class="block text-black font-bold mb-2">Parentesco *</label>
                  <select
                    v-model="contacto.parentesco"
                    required
                    @change="manejarCambioParentesco(index)"
                    class="w-full border-2 border-gray-400 rounded-lg px-4 py-2 bg-white text-black font-semibold"
                  >
                    <option value="">Seleccione...</option>
                    <option value="Padre">Padre</option>
                    <option value="Madre">Madre</option>
                    <option value="Tutor Legal">Tutor Legal</option>
                    <option value="otro">Otro</option>
                  </select>
                </div>

                <div v-if="contacto.parentesco === 'otro'" class="col-span-2">
                  <label class="block text-black font-bold mb-2">Especifique Parentesco *</label>
                  <input
                    v-model="contacto.parentesco_otro"
                    type="text"
                    required
                    class="w-full border-2 border-gray-400 rounded-lg px-4 py-2 bg-white text-black font-semibold"
                    placeholder="Ej: Tío, Hermano, Abuelo, etc."
                  />
                </div>

                <div class="col-span-2 min-w-0">
                  <div class="flex items-center gap-3 p-4 bg-green-50 rounded-lg border-2 border-green-300">
                    <input
                      v-model="contacto.autorizado_recoger"
                      type="checkbox"
                      :id="`autorizado-${index}`"
                      class="w-6 h-6 text-green-600 rounded"
                    />
                    <label :for="`autorizado-${index}`" class="text-black font-bold cursor-pointer flex-1">
                      ✓ Autorizado para recoger al alumno
                    </label>
                  </div>
                  <p class="text-sm text-black mt-1">Marque esta opción si este contacto está autorizado para recoger al alumno de la escuela.</p>
                </div>

                <!-- Fotografía del Contacto -->
                <div class="col-span-2 min-w-0">
                  <label class="block text-black font-bold mb-2">Fotografía del Contacto {{ index + 1 }} *</label>
                  <div class="bg-purple-50 border-2 border-purple-300 rounded-lg p-4">
                    <div class="flex flex-col sm:flex-row gap-3 mb-3 min-w-0">
                      <button
                        type="button"
                        @click="abrirCamaraContacto(index)"
                        class="w-full sm:flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-3 sm:py-3 sm:px-4 rounded-lg transition-colors flex items-center justify-center gap-2 text-sm sm:text-base"
                      >
                        📷 Tomar Foto
                      </button>
                      <button
                        type="button"
                        @click="$refs[`fotoContactoArchivo${index}`][0].click()"
                        class="w-full sm:flex-1 bg-purple-500 hover:bg-purple-600 text-white font-semibold py-2 px-3 sm:py-3 sm:px-4 rounded-lg transition-colors flex items-center justify-center gap-2 text-sm sm:text-base"
                      >
                        📁 Subir Archivo
                      </button>
                    </div>
                    <input
                      :ref="`fotoContactoArchivo${index}`"
                      type="file"
                      @change="(e) => manejarFotoContacto(index, e)"
                      accept="image/jpeg,image/png,image/jpg"
                      class="hidden"
                    />
                    <p class="text-xs text-gray-600 mt-2">📸 Puedes tomar una foto con la cámara o subir una existente. Formato: JPG, PNG. Tamaño máximo: 2MB</p>
                    <div v-if="contacto.foto_contacto" class="mt-3">
                      <div class="flex items-center gap-3">
                        <img :src="obtenerVistaPrevia(contacto.foto_contacto)" class="w-24 h-24 object-cover rounded-lg border-2 border-purple-400" />
                        <div class="flex-1">
                          <span class="text-green-600 font-bold text-sm block">✓ Foto seleccionada</span>
                          <span class="text-gray-600 text-xs">{{ contacto.foto_contacto.name }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <button
              v-if="formulario.contactos.length < 3"
              type="button"
              @click="agregarContacto"
              class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-3 sm:py-3 sm:px-4 rounded-lg transition-colors text-sm sm:text-base"
            >
              + Agregar Otro Contacto ({{ formulario.contactos.length }}/3)
            </button>
            <div v-else class="w-full p-4 bg-yellow-100 border-2 border-yellow-400 rounded-lg text-center">
              <p class="text-black font-semibold">✓ Máximo de 3 contactos alcanzado</p>
            </div>
          </div>

          <!-- Paso 4: Foto y Campos Extra -->
          <div v-if="pasoActual === 4">
            <h2 class="text-lg sm:text-xl font-bold text-black mb-6">Información Adicional</h2>
            
            <div class="space-y-6 min-w-0">
              <!-- Campos Extra Dinámicos -->
              <div v-if="camposExtra.length > 0" class="space-y-4">
                <div v-for="campo in camposExtra" :key="campo.nombre" class="space-y-2">
                  <label class="block text-black font-bold mb-2">
                    {{ campo.nombre }} *
                  </label>
                  <input
                      v-model="formulario.campos_extra[campo.nombre]"
                      type="text"
                      required
                      class="w-full min-w-0 border-2 border-gray-400 rounded-lg px-4 py-2 bg-gray-50 text-black font-semibold"
                    :placeholder="`Ingrese ${campo.nombre.toLowerCase()}`"
                  />
                </div>
              </div>
              <div v-else class="text-center py-12">
                <p class="text-gray-600 text-lg font-semibold">No hay información adicional requerida</p>
                <p class="text-gray-500 text-sm mt-2">Puede continuar al siguiente paso</p>
              </div>
            </div>
          </div>

          <!-- Paso 5: Confirmación -->
          <div v-if="pasoActual === 5">
            <h2 class="text-lg sm:text-xl font-bold text-black mb-6">Confirmar Información</h2>
            
            <div class="space-y-6 bg-gray-50 p-6 rounded-lg">
              <!-- Datos del Alumno -->
              <div>
                <h3 class="text-lg sm:text-xl font-bold text-red-700 mb-3">Datos del Alumno</h3>
                <div v-if="formulario.foto_alumno" class="mb-4 flex items-center gap-4 p-4 bg-blue-50 border-2 border-blue-300 rounded-lg">
                  <img :src="obtenerVistaPrevia(formulario.foto_alumno)" class="w-32 h-32 object-cover rounded-lg border-2 border-blue-400" alt="Foto del alumno" />
                  <div>
                    <p class="text-black font-bold">📸 Fotografía del Alumno</p>
                    <p class="text-sm text-gray-600">{{ formulario.foto_alumno.name }}</p>
                  </div>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs sm:text-sm">
                  <dt class="font-semibold text-black">Nombre Completo:</dt>
                  <dd class="text-black">{{ formulario.nombre }} {{ formulario.apellido_paterno }} {{ formulario.apellido_materno }}</dd>
                  
                  <dt class="font-semibold text-black">CURP:</dt>
                  <dd class="text-black">{{ formulario.curp }}</dd>
                  
                  <dt class="font-semibold text-black">Fecha de Nacimiento:</dt>
                  <dd class="text-black">{{ formulario.fecha_nacimiento }}</dd>
                  
                  <dt class="font-semibold text-black">Sexo:</dt>
                  <dd class="text-black">{{ formulario.sexo === 'M' ? 'Masculino' : formulario.sexo === 'F' ? 'Femenino' : 'Otro' }}</dd>
                </dl>
              </div>

              <!-- Datos de Salud -->
              <div>
                <h3 class="text-lg sm:text-xl font-bold text-red-700 mb-3">Datos de Salud</h3>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                  <dt class="font-semibold text-black">Tipo de Sangre:</dt>
                  <dd class="text-black">{{ formulario.tipo_sangre }}</dd>
                  
                  <dt class="font-semibold text-black">Alergias:</dt>
                  <dd class="text-black">{{ formulario.alergias || 'Ninguna' }}</dd>
                  
                  <dt class="font-semibold text-black">Enfermedades:</dt>
                  <dd class="text-black">{{ formulario.enfermedades || 'Ninguna' }}</dd>
                  
                  <dt class="font-semibold text-black">Medicamentos:</dt>
                  <dd class="text-black">{{ formulario.medicamentos || 'Ninguno' }}</dd>
                </dl>
              </div>

              <!-- Contactos -->
              <div>
                <h3 class="text-lg sm:text-xl font-bold text-red-700 mb-3">Contactos de Emergencia</h3>
                <div v-for="(contacto, index) in formulario.contactos" :key="index" class="mb-3 p-4 bg-white rounded border-2 border-gray-300">
                  <div v-if="contacto.foto_contacto" class="mb-3 flex items-center gap-4 p-3 bg-purple-50 border-2 border-purple-300 rounded-lg">
                    <img :src="obtenerVistaPrevia(contacto.foto_contacto)" class="w-24 h-24 object-cover rounded-lg border-2 border-purple-400" :alt="`Foto del contacto ${index + 1}`" />
                    <div>
                      <p class="text-black font-bold">📸 Fotografía del Contacto {{ index + 1 }}</p>
                      <p class="text-sm text-gray-600">{{ contacto.foto_contacto.name }}</p>
                    </div>
                  </div>
                  <p class="font-semibold text-black">Contacto {{ index + 1 }}:</p>
                  <p class="text-black text-sm">{{ contacto.nombre }} {{ contacto.apellido_paterno }} {{ contacto.apellido_materno }}</p>
                  <p class="text-black text-sm">Tel: {{ contacto.telefono }} - {{ contacto.parentesco === 'otro' ? contacto.parentesco_otro : contacto.parentesco }}</p>
                  <p class="text-black text-sm font-semibold" :class="contacto.autorizado_recoger ? 'text-green-600' : 'text-red-600'">
                    {{ contacto.autorizado_recoger ? '✓ Autorizado para recoger' : '✗ No autorizado para recoger' }}
                  </p>
                </div>
              </div>

              <!-- Campos Extra -->
              <div v-if="Object.keys(formulario.campos_extra).length > 0">
                <h3 class="text-lg sm:text-xl font-bold text-red-700 mb-3">Información Adicional</h3>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                  <template v-for="(valor, campo) in formulario.campos_extra" :key="campo">
                    <dt class="font-semibold text-black">{{ campo }}:</dt>
                    <dd class="text-black">{{ valor }}</dd>
                  </template>
                </dl>
              </div>
            </div>

            <div class="mt-6 p-4 bg-yellow-100 border-2 border-yellow-400 rounded-lg">
              <p class="text-black font-semibold">
                ⚠️ Por favor, revise cuidadosamente toda la información antes de enviar. Una vez enviado, no podrá modificar los datos.
              </p>
            </div>
          </div>

          <!-- Botones de Navegación -->
          <div class="flex flex-col sm:flex-row justify-between mt-8 gap-3">
                <button
                  v-if="pasoActual > 1"
                  type="button"
                  @click="pasoAnterior"
                  class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white font-semibold text-sm sm:text-base py-2 sm:py-3 px-4 sm:px-8 rounded-lg transition-colors"
                >
                  ← Anterior
                </button>
                <div v-else class="w-full sm:w-auto"></div>

                <button
                  v-if="pasoActual < 5"
                  type="submit"
                  class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-semibold text-sm sm:text-base py-2 sm:py-3 px-4 sm:px-8 rounded-lg transition-colors"
                >
                  Siguiente →
                </button>
                <button
                  v-else
                  type="button"
                  @click="enviarFormulario"
                  :disabled="enviando"
                  class="w-full sm:w-auto bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-semibold text-sm sm:text-base py-2 sm:py-3 px-4 sm:px-8 rounded-lg transition-colors"
                >
                  {{ enviando ? 'Enviando...' : '✓ Enviar Inscripción' }}
                </button>
              </div>
        </form>
      </div>
    </div>

    <!-- Modal de cámara para foto del alumno -->
    <Teleport to="body">
      <div
        v-if="mostrarCamaraAlumno"
        class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4"
      >
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full p-6">
          <h3 class="text-lg sm:text-xl font-bold text-black mb-4">📷 Tomar Foto del Alumno</h3>
          <video ref="videoAlumno" autoplay playsinline class="w-full rounded-lg mb-4 max-h-[50vh] sm:max-h-[60vh] object-cover"></video>
          <canvas ref="canvasAlumno" class="hidden w-full" style="max-height:60vh;"></canvas>
          <div class="flex gap-3">
            <button
              type="button"
              @click="tomarFotoAlumno"
              class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 sm:py-3 px-4 rounded-lg transition-colors text-sm sm:text-base"
            >
              📸 Capturar
            </button>
            <button
              type="button"
              @click="cerrarCamaraAlumno"
              class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 sm:py-3 px-4 rounded-lg transition-colors text-sm sm:text-base"
            >
              ✕ Cancelar
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Modal de cámara para foto del contacto -->
    <Teleport to="body">
      <div
        v-if="mostrarCamaraContacto"
        class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4"
      >
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full p-6">
          <h3 class="text-lg sm:text-xl font-bold text-black mb-4">📷 Tomar Foto del Contacto {{ contactoActualFoto + 1 }}</h3>
          <video ref="videoContacto" autoplay playsinline class="w-full rounded-lg mb-4 max-h-[50vh] sm:max-h-[60vh] object-cover"></video>
          <canvas ref="canvasContacto" class="hidden w-full" style="max-height:60vh;"></canvas>
          <div class="flex gap-3">
            <button
              type="button"
              @click="tomarFotoContacto"
                      class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-3 sm:py-3 sm:px-4 rounded-lg transition-colors text-sm sm:text-base"
            >
              📸 Capturar
            </button>
            <button
              type="button"
              @click="cerrarCamaraContacto"
                      class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-3 sm:py-3 sm:px-4 rounded-lg transition-colors text-sm sm:text-base"
            >
              ✕ Cancelar
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';

interface CampoExtra {
  nombre: string;
  tipo: string;
}

interface Cuestionario {
  id: number;
  titulo: string;
  descripcion: string;
}

interface Props {
  cuestionario: Cuestionario;
  tipo: string;
  camposExtra: CampoExtra[];
}

const props = defineProps<Props>();

const pasoActual = ref(1);
const enviando = ref(false);

// Variables para cámara del alumno
const mostrarCamaraAlumno = ref(false);
const videoAlumno = ref<HTMLVideoElement | null>(null);
const canvasAlumno = ref<HTMLCanvasElement | null>(null);
let streamAlumno: MediaStream | null = null;

// Variables para cámara del contacto
const mostrarCamaraContacto = ref(false);
const videoContacto = ref<HTMLVideoElement | null>(null);
const canvasContacto = ref<HTMLCanvasElement | null>(null);
const contactoActualFoto = ref(0);
let streamContacto: MediaStream | null = null;

const tituloFormulario = computed(() => {
  if (props.tipo === 'primero') return 'Inscripción - Primer Grado';
  if (props.tipo === 'segundo') return 'Inscripción - Segundo Grado';
  if (props.tipo === 'tercero') return 'Inscripción - Tercer Grado';
  return 'Inscripción';
});

const formulario = ref({
  curp: '',
  nombre: '',
  apellido_paterno: '',
  apellido_materno: '',
  fecha_nacimiento: '',
  sexo: '',
  tipo_sangre: '',
  alergias: '',
  enfermedades: '',
  medicamentos: '',
  foto: null as File | null,
  foto_alumno: null as File | null,
  contactos: [
    {
      nombre: '',
      apellido_paterno: '',
      apellido_materno: '',
      telefono: '',
      email: '',
      parentesco: '',
      parentesco_otro: '',
      autorizado_recoger: false,
      foto_contacto: null as File | null
    }
  ],
  campos_extra: {} as Record<string, string>
});

// Inicializar campos extra
props.camposExtra.forEach(campo => {
  formulario.value.campos_extra[campo.nombre] = '';
});

function siguientePaso() {
  if (pasoActual.value < 5) {
    pasoActual.value++;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
}

function pasoAnterior() {
  if (pasoActual.value > 1) {
    pasoActual.value--;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
}

function agregarContacto() {
  if (formulario.value.contactos.length < 3) {
    formulario.value.contactos.push({
      nombre: '',
      apellido_paterno: '',
      apellido_materno: '',
      telefono: '',
      email: '',
      parentesco: '',
      parentesco_otro: '',
      autorizado_recoger: false,
      foto_contacto: null as File | null
    });
  }
}

function manejarCambioParentesco(index: number) {
  // Limpiar el campo de parentesco_otro si no se seleccionó "otro"
  if (formulario.value.contactos[index].parentesco !== 'otro') {
    formulario.value.contactos[index].parentesco_otro = '';
  }
}

function eliminarContacto(index: number) {
  if (formulario.value.contactos.length > 1) {
    formulario.value.contactos.splice(index, 1);
  }
}

function manejarFoto(event: Event) {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    formulario.value.foto = target.files[0];
  }
}

function manejarFotoAlumno(event: Event) {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    const file = target.files[0];
    if (file.size <= 2097152) { // 2MB
      formulario.value.foto_alumno = file;
    } else {
      alert('La foto no debe superar los 2MB');
      target.value = '';
    }
  }
}

function manejarFotoContacto(index: number, event: Event) {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    const file = target.files[0];
    if (file.size <= 2097152) { // 2MB
      formulario.value.contactos[index].foto_contacto = file;
    } else {
      alert('La foto no debe superar los 2MB');
      target.value = '';
    }
  }
}

function obtenerVistaPrevia(file: File | null): string {
  if (file) {
    return URL.createObjectURL(file);
  }
  return '';
}

// Funciones para cámara del alumno
const abrirCamaraAlumno = async () => {
  try {
    mostrarCamaraAlumno.value = true;
    await nextTick();
    if (videoAlumno.value) {
      streamAlumno = await navigator.mediaDevices.getUserMedia({ 
        video: { facingMode: 'user', width: 1280, height: 720 } 
      });
      videoAlumno.value.srcObject = streamAlumno;
    }
  } catch (error) {
    console.error('Error al acceder a la cámara:', error);
    alert('No se pudo acceder a la cámara. Verifique los permisos.');
    mostrarCamaraAlumno.value = false;
  }
};

const tomarFotoAlumno = () => {
  if (videoAlumno.value && canvasAlumno.value) {
    const video = videoAlumno.value;
    const canvas = canvasAlumno.value;
    
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    const ctx = canvas.getContext('2d');
    if (!ctx) return;
    
    ctx.drawImage(video, 0, 0);
    
    canvas.toBlob((blob) => {
      if (blob) {
        const file = new File([blob], `foto_alumno_${Date.now()}.jpg`, { type: 'image/jpeg' });
        formulario.value.foto_alumno = file;
        cerrarCamaraAlumno();
      }
    }, 'image/jpeg', 0.9);
  }
};

const cerrarCamaraAlumno = () => {
  if (streamAlumno) {
    streamAlumno.getTracks().forEach(track => track.stop());
    streamAlumno = null;
  }
  mostrarCamaraAlumno.value = false;
};

// Funciones para cámara del contacto
const abrirCamaraContacto = async (index: number) => {
  try {
    contactoActualFoto.value = index;
    mostrarCamaraContacto.value = true;
    await nextTick();
    if (videoContacto.value) {
      streamContacto = await navigator.mediaDevices.getUserMedia({ 
        video: { facingMode: 'user', width: 1280, height: 720 } 
      });
      videoContacto.value.srcObject = streamContacto;
    }
  } catch (error) {
    console.error('Error al acceder a la cámara:', error);
    alert('No se pudo acceder a la cámara. Verifique los permisos.');
    mostrarCamaraContacto.value = false;
  }
};

const tomarFotoContacto = () => {
  if (videoContacto.value && canvasContacto.value) {
    const video = videoContacto.value;
    const canvas = canvasContacto.value;
    
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    const ctx = canvas.getContext('2d');
    if (!ctx) return;
    
    ctx.drawImage(video, 0, 0);
    
    canvas.toBlob((blob) => {
      if (blob) {
        const file = new File([blob], `foto_contacto_${contactoActualFoto.value + 1}_${Date.now()}.jpg`, { type: 'image/jpeg' });
        formulario.value.contactos[contactoActualFoto.value].foto_contacto = file;
        cerrarCamaraContacto();
      }
    }, 'image/jpeg', 0.9);
  }
};

const cerrarCamaraContacto = () => {
  if (streamContacto) {
    streamContacto.getTracks().forEach(track => track.stop());
    streamContacto = null;
  }
  mostrarCamaraContacto.value = false;
};

function enviarFormulario() {
  // Validar que los campos requeridos estén completos
  if (!formulario.value.sexo) {
    alert('Por favor seleccione el sexo del alumno en el Paso 1');
    enviando.value = false;
    pasoActual.value = 1;
    return;
  }

  if (!formulario.value.curp || !formulario.value.nombre || !formulario.value.apellido_paterno || 
      !formulario.value.apellido_materno || !formulario.value.fecha_nacimiento) {
    alert('Por favor complete todos los campos obligatorios del alumno');
    enviando.value = false;
    pasoActual.value = 1;
    return;
  }

  enviando.value = true;

  const formData = new FormData();
  
  // Datos del alumno
  formData.append('curp', formulario.value.curp);
  formData.append('nombre', formulario.value.nombre);
  formData.append('apellido_paterno', formulario.value.apellido_paterno);
  formData.append('apellido_materno', formulario.value.apellido_materno);
  formData.append('fecha_nacimiento', formulario.value.fecha_nacimiento);
  formData.append('sexo', formulario.value.sexo);
  formData.append('tipo', props.tipo);
  
  // Datos de salud
  formData.append('tipo_sangre', formulario.value.tipo_sangre);
  formData.append('alergias', formulario.value.alergias || '');
  formData.append('enfermedades', formulario.value.enfermedades || '');
  formData.append('medicamentos', formulario.value.medicamentos || '');
  
  // Foto base64 (requerido por el backend para compatibilidad)
  if (formulario.value.foto_alumno) {
    // Crear un base64 dummy si tenemos foto_alumno
    const canvas = document.createElement('canvas');
    canvas.width = 100;
    canvas.height = 100;
    const ctx = canvas.getContext('2d');
    if (ctx) {
      ctx.fillStyle = '#cccccc';
      ctx.fillRect(0, 0, 100, 100);
    }
    formData.append('foto', canvas.toDataURL('image/jpeg'));
  } else {
    // Si no hay foto, crear un base64 dummy
    const canvas = document.createElement('canvas');
    canvas.width = 100;
    canvas.height = 100;
    const ctx = canvas.getContext('2d');
    if (ctx) {
      ctx.fillStyle = '#cccccc';
      ctx.fillRect(0, 0, 100, 100);
    }
    formData.append('foto', canvas.toDataURL('image/jpeg'));
  }
  
  // Foto del alumno
  if (formulario.value.foto_alumno) {
    formData.append('foto_alumno', formulario.value.foto_alumno);
  }
  
  // Contactos - Enviar cada campo individualmente para que Laravel lo pueda parsear
  formulario.value.contactos.forEach((contacto, index) => {
    formData.append(`contactos[${index}][nombre]`, contacto.nombre);
    formData.append(`contactos[${index}][apellido_paterno]`, contacto.apellido_paterno);
    formData.append(`contactos[${index}][apellido_materno]`, contacto.apellido_materno);
    formData.append(`contactos[${index}][telefono]`, contacto.telefono);
    formData.append(`contactos[${index}][correo]`, contacto.email || '');
    formData.append(`contactos[${index}][parentesco]`, contacto.parentesco === 'otro' ? contacto.parentesco_otro : contacto.parentesco);
    formData.append(`contactos[${index}][autorizado_recoger]`, contacto.autorizado_recoger ? '1' : '0');
    
    // Foto del contacto
    if (contacto.foto_contacto) {
      formData.append(`foto_contacto_${index}`, contacto.foto_contacto);
    }
  });
  
  // Campos extra - Enviar cada campo individualmente
  Object.keys(formulario.value.campos_extra).forEach((key) => {
    formData.append(`campos_extra[${key}]`, formulario.value.campos_extra[key]);
  });

  router.post('/inscripcion/guardar', formData, {
    onSuccess: () => {
      // Inertia redirigirá automáticamente al home
      enviando.value = false;
    },
    onError: (errors) => {
      console.error(errors);
      alert('Error al enviar la inscripción. Por favor revise los datos.');
      enviando.value = false;
    }
  });
}
</script>
