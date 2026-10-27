<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emitir Receta Médica - <?= e($paciente['nombre'] ?? 'Paciente') ?> | VitalSenior</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS desde CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    boxShadow: {
                        'subtle': '0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px 0 rgba(0, 0, 0, 0.02)',
                        'card': '0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(226, 232, 240, 0.8)',
                    }
                }
            }
        }
    </script>
    <style>
        .tap-bounce {
            transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .tap-bounce:active {
            transform: scale(0.98);
        }
        .touch-min {
            min-height: 44px;
            min-width: 44px;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans min-h-screen flex flex-col justify-between selection:bg-blue-600/20 selection:text-blue-700">

    <?php 
    $pacienteNombreDisplay = trim(($paciente['nombre'] ?? '') . ' ' . ($paciente['apellidos'] ?? ''));
    $edad = !empty($paciente['fecha_nacimiento']) ? (date('Y') - date('Y', strtotime($paciente['fecha_nacimiento']))) : 'N/D';
    ?>

    <!-- Top Bar Médica -->
    <header class="bg-white/90 backdrop-blur-md border-b border-slate-200/90 sticky top-0 z-40 shadow-xs px-4 sm:px-6 lg:px-8 py-3.5 transition-all">
        <div class="max-w-4xl mx-auto flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a class="tap-bounce flex items-center gap-2 text-slate-600 hover:text-slate-900 text-xs font-bold py-1.5 px-3 bg-slate-100 hover:bg-slate-200 rounded-xl transition" 
                   href="<?= APP_URL ?>/doctor/paciente/expediente?id=<?= (int)$paciente['id'] ?>">
                    <span>← Volver al Expediente</span>
                </a>
            </div>
            <span class="text-xs font-bold text-slate-500">Emisión de Prescripción Médica</span>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8 w-full flex-1 space-y-6">
        <?php require_once dirname(__DIR__) . '/components/toast.php'; ?>

        <!-- Tarjeta Formulario de Receta Estilo Membretado Oficial -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-6 sm:p-10 space-y-6">
            
            <!-- Encabezado Membretado Médico -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b-2 border-slate-100">
                <div class="space-y-1">
                    <span class="text-xs font-extrabold text-blue-600 uppercase tracking-widest block">VitalSenior • Prescripción Clínica</span>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900"><?= e($doctor['nombre'] ?? 'Dr(a). Médico') ?></h1>
                    <p class="text-xs text-slate-600 font-medium">
                        <?= e($doctor['especialidad'] ?? 'Geriatría') ?> • Cédula Profesional: <strong class="text-slate-800 font-mono"><?= e($doctor['cedula_profesional'] ?? 'Sin Cédula') ?></strong>
                    </p>
                    <p class="text-[11px] text-slate-400">
                        <?= e($doctor['hospital_clinica'] ?? 'Consultorio Particular') ?> • Tel: <?= e($doctor['telefono_consultorio'] ?? ($doctor['telefono_contacto'] ?? 'N/D')) ?>
                    </p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-3 text-center sm:text-right w-full sm:w-auto">
                    <span class="text-[10px] text-blue-600 font-bold uppercase tracking-wider block">Fecha de Emisión</span>
                    <span class="font-mono text-sm font-black text-blue-950"><?= date('d/m/Y') ?></span>
                </div>
            </div>

            <!-- Datos del Paciente Receptor -->
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Paciente</span>
                    <strong class="text-slate-900 text-sm"><?= e($pacienteNombreDisplay) ?></strong>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Edad & Sangre</span>
                    <span class="text-slate-700 font-medium"><?= $edad !== 'N/D' ? "{$edad} años" : '' ?> • Tipo <?= e($paciente['tipo_sangre'] ?? 'N/D') ?></span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Alergias Conocidas</span>
                    <span class="text-rose-700 font-bold"><?= e($paciente['alergias'] ?? 'Ninguna') ?></span>
                </div>
            </div>

            <!-- Formulario -->
            <form action="<?= APP_URL ?>/doctor/recetas/guardar" method="POST" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="paciente_id" value="<?= (int)$paciente['id'] ?>">
                <input type="hidden" name="fecha_emision" value="<?= date('Y-m-d') ?>">

                <!-- Diagnóstico -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        1. Diagnóstico Clínico y Motivo de Prescripción <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="diagnostico" required rows="2" placeholder="Ej. Hipertensión arterial sistémica estadio 1, control de glucemia en ayuno..." 
                              class="w-full rounded-2xl border-slate-300 p-3.5 text-xs sm:text-sm bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 outline-none transition leading-relaxed"></textarea>
                </div>

                <!-- 2. Fármacos Prescritos (Se insertan automáticamente al esquema de tomas) -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                2. Fármacos y Posología (Esquema de Tomas Automático)
                            </label>
                            <p class="text-[11px] text-slate-500">Los medicamentos indicados se programarán automáticamente en el calendario del paciente.</p>
                        </div>
                        <button type="button" onclick="agregarRenglonMedicamento()" class="tap-bounce touch-min px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs rounded-xl border border-blue-200 transition inline-flex items-center gap-1.5 cursor-pointer">
                            <span class="text-sm">+</span>
                            <span>Agregar Fármaco</span>
                        </button>
                    </div>

                    <div id="contenedor-medicamentos" class="space-y-3">
                        <!-- Fila 0 de Medicamento por Defecto -->
                        <div class="renglon-medicamento bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80 space-y-3 relative">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Nombre Comercial / Genérico <span class="text-rose-500">*</span></label>
                                    <input type="text" name="medicamentos[0][nombre]" required placeholder="Ej. Losartán" 
                                           class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs sm:text-sm font-semibold text-slate-800 focus:border-blue-600 outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Dosis / Presentación <span class="text-rose-500">*</span></label>
                                    <input type="text" name="medicamentos[0][dosis]" required placeholder="Ej. 50 mg (1 tableta)" 
                                           class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs sm:text-sm font-semibold text-slate-800 focus:border-blue-600 outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Frecuencia de Toma <span class="text-rose-500">*</span></label>
                                    <select name="medicamentos[0][frecuencia_horas]" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs sm:text-sm font-semibold text-slate-800 focus:border-blue-600 outline-none">
                                        <option value="24">Cada 24 horas (1 vez al día - Mañana)</option>
                                        <option value="12">Cada 12 horas (2 veces al día - Mañana/Noche)</option>
                                        <option value="8" selected>Cada 8 horas (3 veces al día - Mañana/Tarde/Noche)</option>
                                        <option value="6">Cada 6 horas (4 veces al día)</option>
                                        <option value="48">Cada 48 horas (Día por medio)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                <div class="sm:col-span-3">
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Indicación de Ingesta</label>
                                    <input type="text" name="medicamentos[0][instrucciones]" placeholder="Ej. Tomar con el desayuno, no partir la pastilla" 
                                           class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-medium text-slate-700 focus:border-blue-600 outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Dosis Prescritas (Stock)</label>
                                    <input type="number" name="medicamentos[0][stock_actual]" min="1" value="30" 
                                           class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs sm:text-sm font-semibold text-slate-800 focus:border-blue-600 outline-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Indicaciones Generales y Cuidados -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        3. Indicaciones Generales, Cuidados y Próxima Revisión
                    </label>
                    <textarea name="indicaciones_generales" rows="4" placeholder="Ej. Mantener hidratación adecuada (1.5L de agua al día), dieta baja en sodio, monitoreo de presión arterial 2 veces por semana. Acudir a consulta en 3 meses." 
                              class="w-full rounded-2xl border-slate-300 p-3.5 text-xs sm:text-sm bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 outline-none transition leading-relaxed"></textarea>
                </div>

                <!-- Botones de Acción -->
                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-3">
                    <a href="<?= APP_URL ?>/doctor/paciente/expediente?id=<?= (int)$paciente['id'] ?>" 
                       class="tap-bounce touch-min w-full sm:w-auto px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition text-center">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="tap-bounce touch-min w-full sm:w-auto px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Emitir y Programar Receta</span>
                    </button>
                </div>
            </form>

        </div>
    </main>

    <script>
    let medIndex = 1;

    function agregarRenglonMedicamento() {
        const contenedor = document.getElementById('contenedor-medicamentos');
        const div = document.createElement('div');
        div.className = 'renglon-medicamento bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80 space-y-3 relative';
        div.id = `med-row-${medIndex}`;
        
        div.innerHTML = `
            <button type="button" onclick="eliminarRenglon(this)" class="absolute top-3 right-3 text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition cursor-pointer" title="Eliminar fármaco">
                ✕
            </button>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pr-8">
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Nombre Comercial / Genérico <span class="text-rose-500">*</span></label>
                    <input type="text" name="medicamentos[${medIndex}][nombre]" required placeholder="Ej. Metformina" 
                           class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs sm:text-sm font-semibold text-slate-800 focus:border-blue-600 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Dosis / Presentación <span class="text-rose-500">*</span></label>
                    <input type="text" name="medicamentos[${medIndex}][dosis]" required placeholder="Ej. 850 mg" 
                           class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs sm:text-sm font-semibold text-slate-800 focus:border-blue-600 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Frecuencia de Toma <span class="text-rose-500">*</span></label>
                    <select name="medicamentos[${medIndex}][frecuencia_horas]" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs sm:text-sm font-semibold text-slate-800 focus:border-blue-600 outline-none">
                        <option value="24">Cada 24 horas (1 vez al día - Mañana)</option>
                        <option value="12" selected>Cada 12 horas (2 veces al día - Mañana/Noche)</option>
                        <option value="8">Cada 8 horas (3 veces al día - Mañana/Tarde/Noche)</option>
                        <option value="6">Cada 6 horas (4 veces al día)</option>
                        <option value="48">Cada 48 horas (Día por medio)</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-3">
                    <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Indicación de Ingesta</label>
                    <input type="text" name="medicamentos[${medIndex}][instrucciones]" placeholder="Ej. Tomar después de la comida con agua" 
                           class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-medium text-slate-700 focus:border-blue-600 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Dosis Prescritas (Stock)</label>
                    <input type="number" name="medicamentos[${medIndex}][stock_actual]" min="1" value="30" 
                           class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs sm:text-sm font-semibold text-slate-800 focus:border-blue-600 outline-none">
                </div>
            </div>
        `;
        
        contenedor.appendChild(div);
        medIndex++;
    }

    function eliminarRenglon(btn) {
        const row = btn.closest('.renglon-medicamento');
        if (row) {
            row.remove();
        }
    }
    </script>

</body>
</html>
