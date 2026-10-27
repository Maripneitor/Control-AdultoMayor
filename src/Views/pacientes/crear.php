<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">
    
    <!-- Botón Volver -->
    <div class="mb-6 flex items-center justify-between">
        <a href="<?= APP_URL ?>/dashboard" class="tap-bounce inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-500 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            <span>Volver al Dashboard</span>
        </a>
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Gestión Familiar</span>
    </div>

    <!-- Tarjeta Principal del Formulario -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 sm:p-10">
        
        <!-- Header del Formulario -->
        <div class="flex items-start gap-4 pb-6 border-b border-slate-100">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-2xl shrink-0 shadow-xs">
                👤
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    Registrar Nuevo Adulto Mayor
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 font-medium leading-relaxed mt-1">
                    Crea un perfil de cuidado integral. Podrás registrar sus medicamentos, monitorear signos vitales y compartir el acceso con otros familiares mediante un NIP único.
                </p>
            </div>
        </div>

        <form action="<?= APP_URL ?>/pacientes/guardar" method="POST" class="mt-8 space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <!-- 1. Datos Personales -->
            <div>
                <h2 class="text-xs font-extrabold text-blue-600 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    1. Datos de Identificación
                </h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="nombre">
                            Nombres <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="nombre" 
                               name="nombre" 
                               placeholder="Ej. Roberto" 
                               required 
                               class="w-full h-12 px-4 text-sm font-medium text-slate-900 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all placeholder:text-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="apellidos">
                            Apellidos
                        </label>
                        <input type="text" 
                               id="apellidos" 
                               name="apellidos" 
                               placeholder="Ej. Gómez Bolaños" 
                               class="w-full h-12 px-4 text-sm font-medium text-slate-900 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all placeholder:text-slate-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="fecha_nacimiento">
                            Fecha de Nacimiento
                        </label>
                        <input type="date" 
                               id="fecha_nacimiento" 
                               name="fecha_nacimiento" 
                               value="1950-01-01" 
                               class="w-full h-12 px-4 text-sm font-medium text-slate-900 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="genero">
                            Género
                        </label>
                        <select id="genero" 
                                name="genero" 
                                class="w-full h-12 px-4 text-sm font-semibold text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all cursor-pointer">
                            <option value="Masculino" selected>Masculino</option>
                            <option value="Femenino">Femenino</option>
                            <option value="Otro">Otro / No especificado</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="tipo_sangre">
                            Tipo de Sangre
                        </label>
                        <select id="tipo_sangre" 
                                name="tipo_sangre" 
                                class="w-full h-12 px-4 text-sm font-semibold text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all cursor-pointer">
                            <option value="N/D">No lo sé (N/D)</option>
                            <option value="O+">O Positivo (O+)</option>
                            <option value="O-">O Negativo (O-)</option>
                            <option value="A+">A Positivo (A+)</option>
                            <option value="A-">A Negativo (A-)</option>
                            <option value="B+">B Positivo (B+)</option>
                            <option value="B-">B Negativo (B-)</option>
                            <option value="AB+">AB Positivo (AB+)</option>
                            <option value="AB-">AB Negativo (AB-)</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="parentesco">
                        Tu Relación / Parentesco con el Adulto Mayor
                    </label>
                    <select id="parentesco" 
                            name="parentesco" 
                            class="w-full h-12 px-4 text-sm font-semibold text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all cursor-pointer">
                        <option value="Hijo/a" selected>Hijo / Hija</option>
                        <option value="Esposo/a">Esposo / Esposa</option>
                        <option value="Nieto/a">Nieto / Nieta</option>
                        <option value="Hermano/a">Hermano / Hermana</option>
                        <option value="Cuidador Principal">Cuidador(a) Principal</option>
                        <option value="Tutor Legal">Tutor Legal</option>
                        <option value="Otro Familiar">Otro Familiar</option>
                    </select>
                </div>
            </div>

            <!-- 2. Antecedentes Clínicos Opcionales -->
            <div class="pt-6 border-t border-slate-100">
                <h2 class="text-xs font-extrabold text-blue-600 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    2. Antecedentes Médicos (Opcional)
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="alergias">
                            Alergias Conocidas (Medicamentos o Alimentos)
                        </label>
                        <input type="text" 
                               id="alergias" 
                               name="alergias" 
                               placeholder="Ej. Penicilina, sulfas, mariscos (o 'Ninguna')" 
                               class="w-full h-12 px-4 text-sm font-medium text-slate-900 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all placeholder:text-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="enfermedades_cronicas">
                            Padecimientos o Diagnósticos Crónicos
                        </label>
                        <input type="text" 
                               id="enfermedades_cronicas" 
                               name="enfermedades_cronicas" 
                               placeholder="Ej. Hipertensión arterial, Diabetes Tipo 2, Artritis" 
                               class="w-full h-12 px-4 text-sm font-medium text-slate-900 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all placeholder:text-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="notas_cognitivas">
                            Notas de Cuidado o Estado Funcional
                        </label>
                        <textarea id="notas_cognitivas" 
                                  name="notas_cognitivas" 
                                  rows="3" 
                                  placeholder="Observaciones de movilidad, dieta, horarios de descanso o recordatorios especiales..." 
                                  class="w-full p-4 text-sm font-medium text-slate-900 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all placeholder:text-slate-400"></textarea>
                    </div>
                </div>
            </div>

            <!-- Botón de Envío -->
            <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-3">
                <a href="<?= APP_URL ?>/dashboard" 
                   class="tap-bounce touch-min w-full sm:w-auto px-6 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm text-center transition-all">
                    Cancelar
                </a>
                <button type="submit" 
                        class="tap-bounce touch-min w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm shadow-md shadow-blue-600/20 hover:shadow-lg transition-all inline-flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Guardar y Activar Perfil</span>
                </button>
            </div>
        </form>

    </div>
</div>
