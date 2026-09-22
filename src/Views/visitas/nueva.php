<div class="w-full max-w-3xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
    
    <!-- Encabezado de la Sección -->
    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-200">
        <a href="<?= APP_URL ?>/expediente#tab-visitas" class="tap-bounce touch-target w-11 h-11 bg-white border border-slate-200 hover:bg-slate-100 rounded-2xl flex items-center justify-center text-slate-700 font-bold shadow-xs transition" title="Regresar al Expediente">
            ←
        </a>
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">Bitácora de Consulta Médica</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                    Post-Consulta
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Paciente: <span class="font-bold text-blue-700"><?= e($paciente['nombre'] . ' ' . $paciente['apellidos']) ?></span>
            </p>
        </div>
    </div>

    <!-- Flash Notifications -->
    <?php if (!empty($flash)): ?>
        <?php 
            $colors = [
                'success' => 'bg-emerald-50 border-emerald-500 text-emerald-800',
                'warning' => 'bg-amber-50 border-amber-500 text-amber-800',
                'danger'  => 'bg-rose-50 border-rose-500 text-rose-800',
                'info'    => 'bg-blue-50 border-blue-500 text-blue-800'
            ];
            $alertStyle = $colors[$flash['type']] ?? $colors['info'];
        ?>
        <div class="mb-6">
            <div class="border-l-4 p-4 rounded-r-2xl shadow-subtle flex items-center justify-between <?= $alertStyle; ?>" role="alert">
                <span class="text-sm font-semibold"><?= htmlspecialchars($flash['message']); ?></span>
                <button onclick="this.parentElement.remove()" class="text-lg font-bold ml-4 p-1 hover:opacity-75 focus:outline-none">&times;</button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Formulario Principal -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-card">
        <form action="<?= APP_URL ?>/visitas/guardar" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="paciente_id" value="<?= $paciente['id'] ?>">

            <!-- 1. Fecha y Hora de la Consulta -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Fecha y Hora de la Atención *
                    </label>
                    <input type="datetime-local" name="fecha_visita" value="<?= date('Y-m-d\TH:i') ?>" required 
                           class="touch-target w-full rounded-2xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Médico Tratante
                    </label>
                    <div class="space-y-2">
                        <?php if (!empty($doctores)): ?>
                            <select id="select-doctor" name="doctor_id" onchange="actualizarNombreDoctor(this)" class="touch-target w-full rounded-2xl border-slate-300 px-3 py-2.5 text-xs sm:text-sm focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50">
                                <option value="">-- Seleccionar de la lista o escribir abajo --</option>
                                <?php foreach ($doctores as $dr): ?>
                                    <option value="<?= $dr['id'] ?>" data-hospital="<?= e($dr['hospital_clinica'] ?? '') ?>">Dr(a). <?= e($dr['nombre']) ?> (<?= e($dr['especialidad'] ?: 'General') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                        <input type="text" id="input-doctor-nombre" name="doctor_nombre" placeholder="O escribe el nombre del médico..." 
                               class="touch-target w-full rounded-2xl border-slate-300 px-4 py-2.5 text-xs sm:text-sm focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50">
                    </div>
                </div>
            </div>

            <!-- 2. Lugar / Clínica con Chips Rápidos -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Lugar / Clínica de Atención
                </label>
                <input type="text" id="input-lugar" name="lugar_atencion" placeholder="Ej. Clínica Familiar No. 4, Consultorio 6" 
                       class="touch-target w-full rounded-2xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50 mb-2">
                <!-- Chips de Lugares Frecuentes -->
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" onclick="setLugar('Centro de Salud')" class="text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl transition">Centro de Salud</button>
                    <button type="button" onclick="setLugar('IMSS / ISSSTE')" class="text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl transition">IMSS / ISSSTE</button>
                    <button type="button" onclick="setLugar('Clínica Familiar')" class="text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl transition">Clínica Familiar</button>
                    <button type="button" onclick="setLugar('Consultorio Privado')" class="text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl transition">Consultorio Privado</button>
                    <button type="button" onclick="setLugar('Hospital General')" class="text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl transition">Hospital General</button>
                    <button type="button" onclick="setLugar('Cruz Roja')" class="text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl transition">Cruz Roja</button>
                </div>
            </div>

            <!-- 3. Motivo de Consulta con Chips Rápidos -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Motivo Principal de la Consulta *
                </label>
                <input type="text" id="input-motivo" name="motivo_consulta" required placeholder="Ej. Control de hipertensión y revisión de glucosa" 
                       class="touch-target w-full rounded-2xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50 mb-2">
                <!-- Chips de Motivos Frecuentes -->
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" onclick="setMotivo('Control Rutinario de Salud')" class="text-xs font-semibold bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-xl transition">Control Rutinario</button>
                    <button type="button" onclick="setMotivo('Revisión de Presión y Glucosa')" class="text-xs font-semibold bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-xl transition">Presión / Glucosa</button>
                    <button type="button" onclick="setMotivo('Dolor o Malestar Físico')" class="text-xs font-semibold bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-xl transition">Dolor / Malestar</button>
                    <button type="button" onclick="setMotivo('Revisión de Resultados de Laboratorio')" class="text-xs font-semibold bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-xl transition">Entrega de Resultados</button>
                    <button type="button" onclick="setMotivo('Ajuste de Dosis o Medicamentos')" class="text-xs font-semibold bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-xl transition">Ajuste de Medicación</button>
                    <button type="button" onclick="setMotivo('Revisión por Caída o Golpe')" class="text-xs font-semibold bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-xl transition">Caída / Golpe</button>
                </div>
            </div>

            <!-- 4. Diagnóstico, Indicaciones y Notas -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Diagnóstico, Indicaciones y Notas del Médico
                </label>
                <textarea name="diagnostico_notas" rows="3" placeholder="Anota lo que indicó el médico: cambios en dieta, nuevos síntomas evaluados, cuidados en casa..." 
                          class="w-full rounded-2xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50 leading-relaxed"></textarea>
            </div>

            <!-- 5. Fotos de Recetas / Órdenes Médicas Adjuntas -->
            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Adjuntar Fotos de Recetas u Órdenes</h3>
                            <p class="text-[11px] text-slate-500">Toma una foto clara o sube imágenes/PDF (máx 5MB c/u)</p>
                        </div>
                    </div>
                </div>

                <label class="tap-bounce flex flex-col items-center justify-center p-5 border-2 border-dashed border-blue-300 hover:border-blue-500 bg-blue-50/40 hover:bg-blue-50/80 rounded-2xl cursor-pointer transition">
                    <div class="flex flex-col items-center justify-center text-center">
                        <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-xs font-bold text-blue-700">Tocar para Tomar Foto o Seleccionar Archivos</span>
                        <span class="text-[11px] text-slate-400 mt-0.5">JPG, PNG, WEBP o PDF</span>
                    </div>
                    <input type="file" id="input-fotos" name="fotos_recetas[]" multiple accept="image/*,.pdf" capture="environment" onchange="previewArchivos(this)" class="hidden">
                </label>

                <!-- Contenedor de miniaturas preview -->
                <div id="preview-container" class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-3 hidden"></div>
            </div>

            <!-- 6. Seguimiento / Próxima Cita -->
            <div class="p-5 bg-gradient-to-r from-blue-50/60 to-indigo-50/60 rounded-2xl border border-blue-100 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-blue-950 uppercase tracking-wider">¿Requiere Consulta de Seguimiento?</h3>
                            <p class="text-[11px] text-blue-700">Crea automáticamente la cita en la agenda de VitalSenior</p>
                        </div>
                    </div>
                    <!-- Switch Toggle -->
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="toggle-seguimiento" name="requiere_seguimiento" value="1" onchange="toggleSeccionSeguimiento(this.checked)" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div id="seccion-seguimiento" class="space-y-3 hidden pt-2 border-t border-blue-100">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Fecha Sugerida de Seguimiento</label>
                            <input type="date" name="fecha_seguimiento" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" 
                                   class="touch-target w-full rounded-xl border-slate-300 px-3 py-2 text-xs sm:text-sm focus:border-blue-500 focus:ring-blue-500 bg-white font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Instrucciones para la cita</label>
                            <input type="text" name="instrucciones_seguimiento" placeholder="Ej. Llevar bitácora de presión y estudios..." 
                                   class="touch-target w-full rounded-xl border-slate-300 px-3 py-2 text-xs sm:text-sm focus:border-blue-500 focus:ring-blue-500 bg-white">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-end gap-3">
                <a href="<?= APP_URL ?>/expediente#tab-visitas" class="tap-bounce touch-target w-full sm:w-auto px-6 py-3 rounded-2xl text-xs sm:text-sm font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 text-center transition">
                    Cancelar
                </a>
                <button type="submit" class="tap-bounce touch-target w-full sm:w-auto px-8 py-3 rounded-2xl text-xs sm:text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/20 transition cursor-pointer flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/></svg>
                    <span>Guardar Bitácora Médica</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function setLugar(lugar) {
    document.getElementById('input-lugar').value = lugar;
}

function setMotivo(motivo) {
    document.getElementById('input-motivo').value = motivo;
}

function actualizarNombreDoctor(select) {
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption.value) {
        document.getElementById('input-doctor-nombre').value = selectedOption.text.split('(')[0].trim();
        const hosp = selectedOption.getAttribute('data-hospital');
        if (hosp && !document.getElementById('input-lugar').value) {
            document.getElementById('input-lugar').value = hosp;
        }
    }
}

function toggleSeccionSeguimiento(activo) {
    const el = document.getElementById('seccion-seguimiento');
    if (activo) {
        el.classList.remove('hidden');
    } else {
        el.classList.add('hidden');
    }
}

function previewArchivos(input) {
    const container = document.getElementById('preview-container');
    container.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        container.classList.remove('hidden');
        Array.from(input.files).forEach((file, index) => {
            const card = document.createElement('div');
            card.className = 'p-2 bg-white rounded-xl border border-slate-200 flex flex-col items-center text-center shadow-xs';
            
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.className = 'w-full h-20 object-cover rounded-lg mb-1';
                img.src = URL.createObjectURL(file);
                card.appendChild(img);
            } else {
                const icon = document.createElement('div');
                icon.className = 'w-full h-20 bg-rose-50 text-rose-600 font-bold text-xs flex items-center justify-center rounded-lg mb-1';
                icon.innerText = 'PDF';
                card.appendChild(icon);
            }
            
            const name = document.createElement('span');
            name.className = 'text-[10px] font-semibold text-slate-700 truncate w-full';
            name.innerText = file.name;
            card.appendChild(name);
            
            container.appendChild(card);
        });
    } else {
        container.classList.add('hidden');
    }
}
</script>
