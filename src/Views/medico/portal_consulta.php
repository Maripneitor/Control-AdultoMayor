<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Clínico Médico - VitalSenior | <?= e($paciente['nombre'] . ' ' . $paciente['apellidos']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    boxShadow: {
                        'card': '0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(226, 232, 240, 0.8)',
                    }
                }
            }
        }
    </script>
    <style>
        .tap-bounce {
            transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .tap-bounce:active {
            transform: scale(0.98);
        }
        .touch-min {
            min-height: 48px;
            min-width: 48px;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans min-h-full flex flex-col justify-between antialiased">
    
    <!-- 1. Header Clínico Profesional (Sin barras familiares) -->
    <header class="bg-[#0b1b3d] text-white sticky top-0 z-40 px-4 sm:px-8 py-3.5 shadow-md border-b border-blue-900">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center text-white text-xl font-black shadow-md shadow-blue-500/20">
                    ✚
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-widest text-blue-300">VitalSenior Clinical Portal</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            PIN ACTIVO
                        </span>
                    </div>
                    <h1 class="text-base sm:text-lg font-black tracking-tight leading-tight">
                        Expediente de <?= e($paciente['nombre'] . ' ' . $paciente['apellidos']) ?>
                    </h1>
                </div>
            </div>

            <!-- Acciones Clínicas a la derecha -->
            <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full md:w-auto justify-between md:justify-end">
                <div class="text-[11px] text-slate-300 font-mono bg-white/10 px-3 py-1.5 rounded-xl border border-white/10">
                    Expira: <span class="text-amber-300 font-bold"><?= date('H:i d/m/Y', strtotime($tokenData['expira_at'])) ?></span>
                </div>
                
                <a href="<?= APP_URL ?>/paciente/pdf?id=<?= e((string)$paciente['id']) ?>" target="_blank" class="tap-bounce touch-min px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                    <span>📄</span>
                    <span>Descargar PDF</span>
                </a>
            </div>
        </div>
    </header>

    <!-- 2. Banner de Alertas Críticas (Rojo Carmesí para Alergias Severas) -->
    <?php 
        $edad = null;
        if (!empty($paciente['fecha_nacimiento'])) {
            $fn = new DateTime($paciente['fecha_nacimiento']);
            $hoy = new DateTime();
            $edad = $hoy->diff($fn)->y;
        }
        $tieneAlergias = !empty($paciente['alergias']) && strtoupper(trim($paciente['alergias'])) !== 'NINGUNA' && strtoupper(trim($paciente['alergias'])) !== 'N/D';
    ?>
    <div class="bg-gradient-to-r from-rose-700 via-rose-600 to-rose-800 text-white px-4 sm:px-8 py-3 shadow-inner">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-4 text-xs sm:text-sm">
                <div class="flex items-center gap-1.5">
                    <span class="text-lg">⚠️</span>
                    <span class="font-bold uppercase tracking-wider text-rose-200">Alergias Críticas:</span>
                    <span class="font-black bg-white/20 px-2.5 py-0.5 rounded-lg border border-white/20 text-white">
                        <?= e($paciente['alergias'] ?: 'Ninguna reportada') ?>
                    </span>
                </div>
                
                <div class="flex items-center gap-1.5">
                    <span class="text-rose-200">|</span>
                    <span class="font-bold uppercase tracking-wider text-rose-200">Tipo de Sangre:</span>
                    <span class="font-mono font-black bg-white/20 px-2 py-0.5 rounded-lg text-white"><?= e($paciente['tipo_sangre'] ?: 'N/D') ?></span>
                </div>

                <?php if ($edad !== null): ?>
                    <div class="flex items-center gap-1.5">
                        <span class="text-rose-200">|</span>
                        <span class="font-bold uppercase tracking-wider text-rose-200">Edad:</span>
                        <span class="font-bold text-white"><?= $edad ?> años</span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($paciente['enfermedades_cronicas'])): ?>
                <div class="text-xs text-rose-100 font-medium">
                    <span class="font-bold text-white">Padecimientos:</span> <?= e($paciente['enfermedades_cronicas']) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Flash Notifications -->
    <?php if (!empty($flash)): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-8 mt-4 w-full">
            <div class="p-4 rounded-2xl bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 font-semibold text-sm shadow-xs flex justify-between items-center">
                <span><?= htmlspecialchars($flash['message']) ?></span>
                <button onclick="this.parentElement.remove()" class="text-lg font-bold hover:opacity-75">&times;</button>
            </div>
        </div>
    <?php endif; ?>

    <!-- 3. Contenido Principal Clínico en Cuadrícula -->
    <main class="max-w-7xl mx-auto px-4 sm:px-8 py-6 w-full grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Columna Izquierda: Tratamientos Activos y Signos Vitales (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Tratamientos y Medicación Vigente -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-card">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">💊</span>
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Tratamientos Activos</h2>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800">
                        <?= count($medicamentosActivos) ?>
                    </span>
                </div>

                <?php if (empty($medicamentosActivos)): ?>
                    <p class="text-xs text-slate-400 py-4 text-center">No hay medicamentos activos registrados actualmente.</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($medicamentosActivos as $med): ?>
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                                <div class="flex justify-between items-start mb-1">
                                    <h3 class="font-bold text-slate-800 text-sm"><?= e($med['nombre_comercial']) ?></h3>
                                    <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-200 font-mono">
                                        c/<?= e((string)$med['frecuencia_horas']) ?>h
                                    </span>
                                </div>
                                <p class="text-xs font-semibold text-slate-600">Dosis: <?= e($med['dosis']) ?></p>
                                <?php if (!empty($med['instrucciones'])): ?>
                                    <p class="text-[11px] text-slate-500 mt-1 italic leading-tight">"<?= e($med['instrucciones']) ?>"</p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Últimos Signos Vitales Evaluados -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-card">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100 mb-4">
                    <span class="text-lg">🩺</span>
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Signos Vitales Recientes</h2>
                </div>

                <?php if (empty($ultimoSalud)): ?>
                    <p class="text-xs text-slate-400 py-4 text-center">Sin registros de signos vitales disponibles.</p>
                <?php else: ?>
                    <div class="grid grid-cols-2 gap-3 text-center">
                        <div class="p-3 bg-blue-50/70 rounded-2xl border border-blue-100">
                            <span class="text-[10px] font-bold uppercase text-blue-700">Presión Arterial</span>
                            <p class="font-mono text-base font-black text-slate-800 mt-1">
                                <?= !empty($ultimoSalud['presion_sistolica']) ? "{$ultimoSalud['presion_sistolica']}/{$ultimoSalud['presion_diastolica']}" : 'N/D' ?>
                                <span class="text-[10px] text-slate-500 font-normal">mmHg</span>
                            </p>
                        </div>
                        <div class="p-3 bg-purple-50/70 rounded-2xl border border-purple-100">
                            <span class="text-[10px] font-bold uppercase text-purple-700">Glucosa</span>
                            <p class="font-mono text-base font-black text-slate-800 mt-1">
                                <?= !empty($ultimoSalud['glucosa']) ? $ultimoSalud['glucosa'] : 'N/D' ?>
                                <span class="text-[10px] text-slate-500 font-normal">mg/dL</span>
                            </p>
                        </div>
                        <div class="p-3 bg-emerald-50/70 rounded-2xl border border-emerald-100">
                            <span class="text-[10px] font-bold uppercase text-emerald-700">Ritmo Cardíaco</span>
                            <p class="font-mono text-base font-black text-slate-800 mt-1">
                                <?= !empty($ultimoSalud['ritmo_cardiaco']) ? $ultimoSalud['ritmo_cardiaco'] : 'N/D' ?>
                                <span class="text-[10px] text-slate-500 font-normal">bpm</span>
                            </p>
                        </div>
                        <div class="p-3 bg-amber-50/70 rounded-2xl border border-amber-100">
                            <span class="text-[10px] font-bold uppercase text-amber-700">IMC Estimado</span>
                            <p class="font-mono text-base font-black text-slate-800 mt-1">
                                <?= !empty($ultimoSalud['imc']) ? $ultimoSalud['imc'] : 'N/D' ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Columna Central: Galería de Estudios / Recetas y Bitácora (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-card">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">📋</span>
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Historial Clínico & Estudios</h2>
                    </div>
                    <span class="text-xs text-slate-500"><?= count($visitasMedicas) ?> consultas</span>
                </div>

                <?php if (empty($visitasMedicas)): ?>
                    <p class="text-xs text-slate-400 py-8 text-center">No hay consultas médicas o estudios archivados.</p>
                <?php else: ?>
                    <div class="space-y-4 max-h-[75vh] overflow-y-auto pr-1">
                        <?php foreach ($visitasMedicas as $visita): ?>
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-2.5">
                                <div class="flex justify-between items-start gap-2">
                                    <span class="font-mono text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-lg border border-blue-100">
                                        <?= date('d/m/Y H:i', strtotime($visita['fecha_visita'])) ?>
                                    </span>
                                    <?php if (!empty($visita['lugar_atencion'])): ?>
                                        <span class="text-[11px] font-semibold text-slate-500 truncate max-w-[150px]">
                                            🏥 <?= e($visita['lugar_atencion']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <h3 class="font-bold text-slate-900 text-sm">
                                    <?= e($visita['motivo_consulta']) ?>
                                </h3>

                                <?php if (!empty($visita['doctor_nombre'])): ?>
                                    <p class="text-[11px] text-slate-500 font-medium">
                                        Médico: <span class="text-slate-700 font-bold">Dr(a). <?= e($visita['doctor_nombre']) ?></span>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($visita['diagnostico_notas'])): ?>
                                    <div class="p-3 bg-white rounded-xl border border-slate-200 text-xs text-slate-700 leading-relaxed">
                                        <?= nl2br(e($visita['diagnostico_notas'])) ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Miniaturas de Estudios/Recetas -->
                                <?php if (!empty($visita['archivos'])): ?>
                                    <div class="pt-1">
                                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">
                                            Documentos Adjuntos (<?= count($visita['archivos']) ?>):
                                        </span>
                                        <div class="grid grid-cols-3 gap-2">
                                            <?php foreach ($visita['archivos'] as $arch): 
                                                $isImg = strpos($arch['mime_type'], 'image/') === 0;
                                                $url = APP_URL . '/expediente/archivos/descargar?id=' . $arch['id'];
                                            ?>
                                                <div class="p-1.5 bg-white rounded-xl border border-slate-200 text-center">
                                                    <?php if ($isImg): ?>
                                                        <button type="button" onclick="abrirVisorClínico('<?= $url ?>', '<?= e($arch['nombre_original']) ?>')" class="w-full text-left cursor-pointer">
                                                            <img src="<?= $url ?>" alt="Estudio" class="w-full h-16 object-cover rounded-lg mb-1 shadow-2xs hover:opacity-90 transition">
                                                            <span class="block text-[9px] font-semibold text-slate-700 truncate"><?= e($arch['nombre_original']) ?></span>
                                                        </button>
                                                    <?php else: ?>
                                                        <a href="<?= $url ?>" target="_blank" class="block py-1">
                                                            <div class="w-full h-14 bg-rose-50 text-rose-600 font-black text-xs flex items-center justify-center rounded-lg mb-1">PDF</div>
                                                            <span class="block text-[9px] font-semibold text-slate-700 truncate"><?= e($arch['nombre_original']) ?></span>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Columna Derecha: Formulario de Registro de Nota Clínica Post-Consulta (3 cols) -->
        <div class="lg:col-span-3 space-y-6 sticky top-24">
            
            <div class="bg-white rounded-3xl p-6 border-2 border-blue-500/30 shadow-card">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100 mb-4">
                    <span class="text-lg">✍️</span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Nota de Evolución / Receta</h2>
                        <p class="text-[11px] text-slate-500">Asentar diagnóstico de la consulta</p>
                    </div>
                </div>

                <form action="<?= APP_URL ?>/medico/guardar-nota" method="POST" class="space-y-4">
                    <input type="hidden" name="pin" value="<?= e($pinActual) ?>">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Motivo de la Sesión *
                        </label>
                        <input type="text" name="motivo_consulta" required value="Consulta Médica de Control" 
                               class="touch-min w-full rounded-xl border-slate-300 px-3 py-2 text-xs sm:text-sm focus:border-blue-500 focus:ring-blue-500 bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Médico / Especialidad
                        </label>
                        <input type="text" name="doctor_nombre" placeholder="Dr(a). Nombre y Especialidad" value="<?= e($tokenData['medico_nombre'] ?? '') ?>"
                               class="touch-min w-full rounded-xl border-slate-300 px-3 py-2 text-xs sm:text-sm focus:border-blue-500 focus:ring-blue-500 bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Diagnóstico, Ajuste de Dosis e Indicaciones *
                        </label>
                        <textarea name="diagnostico_notas" rows="4" required placeholder="Escribe el diagnóstico, ajustes a medicamentos o recomendaciones para la familia..." 
                                  class="w-full rounded-xl border-slate-300 px-3 py-2 text-xs sm:text-sm focus:border-blue-500 focus:ring-blue-500 bg-slate-50 leading-relaxed"></textarea>
                    </div>

                    <!-- Seguimiento opcional -->
                    <div class="p-3 bg-blue-50/60 rounded-xl border border-blue-100 space-y-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="requiere_seguimiento" value="1" onchange="document.getElementById('fecha-seg-med').classList.toggle('hidden', !this.checked)" class="rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-xs font-bold text-blue-900">Programar Cita de Seguimiento</span>
                        </label>
                        <div id="fecha-seg-med" class="hidden">
                            <input type="date" name="fecha_seguimiento" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" 
                                   class="w-full rounded-lg border-slate-300 px-2.5 py-1.5 text-xs bg-white font-mono">
                        </div>
                    </div>

                    <button type="submit" class="tap-bounce touch-min w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-blue-500/20 transition cursor-pointer flex items-center justify-center gap-2">
                        <span>💾</span>
                        <span>Guardar en Expediente</span>
                    </button>
                </form>
            </div>

        </div>

    </main>

    <!-- Modal Lightbox Clínico con Zoom para Estudios -->
    <div id="modal-visor-clinico" class="fixed inset-0 bg-slate-950/85 backdrop-blur-xs z-50 hidden items-center justify-center p-4" onclick="cerrarVisorClínico()">
        <div class="relative max-w-3xl w-full max-h-[92vh] bg-white rounded-3xl p-5 shadow-2xl flex flex-col items-center" onclick="event.stopPropagation()">
            <div class="w-full flex justify-between items-center pb-2 border-b border-slate-100 mb-3">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🔬</span>
                    <span id="visor-titulo" class="text-xs sm:text-sm font-bold text-slate-800 truncate max-w-sm">Estudio Clínico</span>
                </div>
                <button type="button" onclick="cerrarVisorClínico()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold flex items-center justify-center hover:bg-slate-200 transition cursor-pointer">
                    &times;
                </button>
            </div>
            
            <div class="w-full max-h-[72vh] overflow-auto flex items-center justify-center bg-slate-900 rounded-2xl p-2">
                <img id="visor-img" src="" alt="Estudio Clínico" class="max-w-full max-h-[70vh] object-contain rounded-lg">
            </div>

            <div class="w-full pt-3 flex justify-between items-center text-xs">
                <span class="text-slate-400">VitalSenior High-Resolution Medical Viewer</span>
                <a id="visor-download" href="#" target="_blank" download class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-xs transition">
                    Descargar en Alta Resolución
                </a>
            </div>
        </div>
    </div>

    <script>
    function abrirVisorClínico(url, titulo) {
        document.getElementById('visor-img').src = url;
        document.getElementById('visor-titulo').innerText = titulo;
        document.getElementById('visor-download').href = url;
        const modal = document.getElementById('modal-visor-clinico');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function cerrarVisorClínico() {
        const modal = document.getElementById('modal-visor-clinico');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    </script>

    <!-- Footer Clínico -->
    <footer class="bg-slate-900 text-slate-400 text-center py-4 text-xs border-t border-slate-800">
        VitalSenior • Portal de Consulta Clínica Hospitalaria Segura
    </footer>
</body>
</html>
