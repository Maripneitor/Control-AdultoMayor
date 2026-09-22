<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalSenior - Expediente Clínico de <?= e($paciente['nombre'] ?? 'Paciente') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
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

        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(t => {
                t.classList.remove('border-blue-600', 'text-blue-600', 'bg-blue-50/70', 'font-bold');
                t.classList.add('border-transparent', 'text-slate-500', 'font-semibold');
            });
            
            const targetContent = document.getElementById(tabId);
            const targetBtn = document.getElementById('btn-' + tabId);
            
            if (targetContent && targetBtn) {
                targetContent.classList.remove('hidden');
                targetBtn.classList.remove('border-transparent', 'text-slate-500', 'font-semibold');
                targetBtn.classList.add('border-blue-600', 'text-blue-600', 'bg-blue-50/70', 'font-bold');
                if (history.replaceState) {
                    history.replaceState(null, null, '#' + tabId);
                }
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            const hash = window.location.hash.replace('#', '');
            if (hash && document.getElementById(hash)) {
                switchTab(hash);
            }
        });
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
        /* Ocultar barra de scroll en carrusel móvil */
        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans min-h-full flex flex-col justify-between pb-20 md:pb-0">
    
    <!-- Barra Superior de Navegación -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 px-4 sm:px-8 py-3.5 shadow-xs">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex items-center space-x-3.5">
                <a href="<?= APP_URL ?>/dashboard" class="tap-bounce touch-min w-10 h-10 sm:w-9 sm:h-9 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl flex items-center justify-center font-bold text-base sm:text-sm border border-slate-200 transition">
                    ←
                </a>
                <div>
                    <div class="flex items-center space-x-2">
                        <h1 class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-tight">
                            Expediente Clínico Geriátrico
                        </h1>
                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full <?= $puedeEditar ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' ?>">
                            <?= $puedeEditar ? 'Modo Edición' : 'Solo Lectura (' . e($nivelAcceso ?? 'consulta') . ')' ?>
                        </span>
                    </div>
                    <?php 
                        $nombreCompleto = trim(($paciente['nombre'] ?? '') . ' ' . ($paciente['apellidos'] ?? ''));
                        if (empty($nombreCompleto) || $nombreCompleto === 'Por completar') {
                            $nombreCompleto = 'Paciente sin nombre registrado';
                        }
                        $uuidPublico = $paciente['uuid_publico'] ?? ($paciente['qr_token'] ?? $paciente['id']);
                        
                        $edad = null;
                        if (!empty($paciente['fecha_nacimiento'])) {
                            try {
                                $fn = new DateTime($paciente['fecha_nacimiento']);
                                $hoy = new DateTime();
                                $edad = $hoy->diff($fn)->y;
                            } catch (\Exception $e) {}
                        }
                    ?>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">
                        Paciente: <strong class="text-slate-800"><?= e($nombreCompleto) ?></strong>
                        <?php if ($edad !== null): ?>
                            • <span class="text-blue-700 font-bold"><?= $edad ?> años</span>
                        <?php endif; ?>
                        • <span class="font-mono text-slate-600">ID: <?= e((string)$paciente['id']) ?></span>
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 no-print w-full sm:w-auto justify-end">
                <a href="<?= APP_URL ?>/paciente/pdf?id=<?= e((string)$paciente['id']) ?>" 
                   target="_blank"
                   class="tap-bounce touch-min px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 shadow-xs transition flex items-center space-x-1.5 cursor-pointer">
                    <span>📄</span>
                    <span>Descargar PDF</span>
                </a>

                <button type="button" 
                        id="btn-abrir-qr"
                        onclick="abrirModalQR('<?= e($uuidPublico) ?>', '<?= e($nombreCompleto) ?>')" 
                        class="tap-bounce touch-min px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs shadow-blue-500/20 transition flex items-center space-x-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    <span>Ficha QR SOS</span>
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto p-4 sm:p-6 md:p-8 w-full space-y-6">
        <?php require_once dirname(__DIR__) . '/components/toast.php'; ?>

        <!-- Tarjeta Principal con Pestañas -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
            <!-- Barra de Pestañas Estilo Carrusel Deslizable Táctil (overflow-x-auto whitespace-nowrap) -->
            <div class="overflow-x-auto scrollbar-none snap-x snap-mandatory border-b border-slate-200 bg-slate-50/70 p-2 sm:px-6 flex gap-1.5 sm:gap-2">
                <button id="btn-tab-datos" onclick="switchTab('tab-datos')" class="tab-btn snap-start touch-min py-3 px-4 rounded-xl font-bold text-xs sm:text-sm border-b-2 border-blue-600 text-blue-600 bg-blue-50/80 whitespace-nowrap focus:outline-none transition-all flex items-center space-x-2 cursor-pointer shrink-0">
                    <span class="text-base">👤</span>
                    <span>Ficha General</span>
                </button>
                <button id="btn-tab-padecimientos" onclick="switchTab('tab-padecimientos')" class="tab-btn snap-start touch-min py-3 px-4 rounded-xl font-semibold text-xs sm:text-sm border-b-2 border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-100/80 whitespace-nowrap focus:outline-none transition-all flex items-center space-x-2 cursor-pointer shrink-0">
                    <span class="text-base">🩺</span>
                    <span>Antecedentes & Clínica</span>
                </button>
                <button id="btn-tab-contactos" onclick="switchTab('tab-contactos')" class="tab-btn snap-start touch-min py-3 px-4 rounded-xl font-semibold text-xs sm:text-sm border-b-2 border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-100/80 whitespace-nowrap focus:outline-none transition-all flex items-center space-x-2 cursor-pointer shrink-0">
                    <span class="text-base">📞</span>
                    <span>Contactos SOS (<?= count($contactosEmergencia ?? []) ?>)</span>
                </button>
                <button id="btn-tab-doctores" onclick="switchTab('tab-doctores')" class="tab-btn snap-start touch-min py-3 px-4 rounded-xl font-semibold text-xs sm:text-sm border-b-2 border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-100/80 whitespace-nowrap focus:outline-none transition-all flex items-center space-x-2 cursor-pointer shrink-0">
                    <span class="text-base">👨‍⚕️</span>
                    <span>Médicos (<?= count($doctores ?? []) ?>)</span>
                </button>
                <button id="btn-tab-visitas" onclick="switchTab('tab-visitas')" class="tab-btn snap-start touch-min py-3 px-4 rounded-xl font-semibold text-xs sm:text-sm border-b-2 border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-100/80 whitespace-nowrap focus:outline-none transition-all flex items-center space-x-2 cursor-pointer shrink-0">
                    <span class="text-base">📋</span>
                    <span>Historial de Visitas (<?= count($visitasMedicas ?? []) ?>)</span>
                </button>
            </div>

            <div class="p-4 sm:p-6 md:p-8">
                <!-- ============================================================== -->
                <!-- PESTAÑA 1: FICHA GENERAL & IDENTIFICACIÓN                      -->
                <!-- ============================================================== -->
                <div id="tab-datos" class="tab-content space-y-6">
                    <div class="bg-blue-50/60 p-4 sm:p-5 rounded-2xl border border-blue-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                        <div>
                            <h2 class="font-bold text-blue-950 text-xs sm:text-sm">Identificación y Código de Vinculación Familiar</h2>
                            <p class="text-xs text-blue-700">Comparte este código con familiares o médicos para concederles acceso al expediente.</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-semibold text-slate-500">Código de Enlace:</span>
                            <span class="font-mono text-sm font-black bg-white px-3 py-1.5 rounded-xl border border-blue-200 text-blue-800 tracking-wider shadow-xs">
                                <?= e($paciente['codigo_vinculacion'] ?? 'NO-GEN') ?>
                            </span>
                        </div>
                    </div>

                    <form action="<?= APP_URL ?>/expediente/actualizar" method="POST" class="space-y-6">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="seccion" value="datos">
                        <input type="hidden" name="paciente_id" value="<?= e((string)$paciente['id']) ?>">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nombre(s) <span class="text-rose-500">*</span></label>
                                <input type="text" name="nombre" value="<?= e($paciente['nombre'] ?? '') ?>" required
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-800 font-semibold focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Apellidos <span class="text-rose-500">*</span></label>
                                <input type="text" name="apellidos" value="<?= e(($paciente['apellidos'] ?? '') === 'Por completar' ? '' : ($paciente['apellidos'] ?? '')) ?>" required placeholder="Apellidos del adulto mayor"
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-800 font-semibold focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Fecha de Nacimiento <span class="text-rose-500">*</span></label>
                                <input type="date" name="fecha_nacimiento" value="<?= e($paciente['fecha_nacimiento'] ?? date('Y-m-d')) ?>" required
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-800 font-semibold focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Grupo Sanguíneo y Factor Rh</label>
                                <select name="tipo_sangre" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-800 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
                                    <option value="N/D" <?= ($paciente['tipo_sangre'] ?? 'N/D') === 'N/D' ? 'selected' : '' ?>>No Especificado / Desconocido</option>
                                    <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $ts): ?>
                                        <option value="<?= $ts ?>" <?= ($paciente['tipo_sangre'] ?? '') === $ts ? 'selected' : '' ?>><?= $ts ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <?php if ($puedeEditar): ?>
                            <div class="pt-4 border-t border-slate-100 flex justify-end">
                                <button type="submit" class="tap-bounce px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs shadow-blue-500/20 transition-all cursor-pointer">
                                    Guardar Ficha de Identificación
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- ============================================================== -->
                <!-- PESTAÑA 2: ANTECEDENTES MÉDICOS & CLÍNICA GERIÁTRICA           -->
                <!-- ============================================================== -->
                <div id="tab-padecimientos" class="tab-content hidden space-y-6">
                    <form action="<?= APP_URL ?>/expediente/actualizar" method="POST" class="space-y-6">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="seccion" value="padecimientos">
                        <input type="hidden" name="paciente_id" value="<?= e((string)$paciente['id']) ?>">

                        <div class="bg-rose-50/50 p-4 rounded-2xl border border-rose-100">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-rose-800 uppercase tracking-wider">Alergias Conocidas y Reacciones Adversas (RAM)</label>
                                <span class="text-[11px] font-semibold text-rose-600 bg-rose-100 px-2 py-0.5 rounded-md">Crítico en Ficha QR</span>
                            </div>
                            <textarea name="alergias" rows="2" class="w-full bg-white border border-rose-200 rounded-xl p-3 text-sm text-slate-800 focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all outline-none font-medium" placeholder="Ej. Alergia severa a Penicilina, AINEs, Yodo, Mariscos"><?= e($paciente['alergias'] ?? '') ?></textarea>
                            <p class="text-[11px] text-rose-700 mt-1.5 font-medium">⚠️ Esta información se presenta con alta prioridad a paramédicos al escanear el QR de auxilio.</p>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Diagnósticos Crónicos y Dispositivos Médicos</label>
                                <span class="text-[11px] text-slate-400">Patologías, prótesis o implantes</span>
                            </div>
                            <textarea name="enfermedades_cronicas" rows="3" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm text-slate-800 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none font-medium" placeholder="Ej. Hipertensión Arterial (2015), Diabetes Mellitus Tipo 2, Marcapasos bicameral implantado en 2021, Prótesis de cadera derecha"><?= e($paciente['enfermedades_cronicas'] ?? '') ?></textarea>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-indigo-900 uppercase tracking-wider">Autonomía, Estado Cognitivo y Movilidad</label>
                                <span class="text-[11px] text-slate-400">Escala funcional Geriátrica</span>
                            </div>
                            <textarea name="notas_cognitivas" rows="3" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-medium" placeholder="Ej. Autonomía funcional Katz B, requiere andadera para traslados largos, riesgo moderado de caídas, usa prótesis dental y audífono derecho"><?= e($paciente['notas_cognitivas'] ?? '') ?></textarea>
                        </div>

                        <?php if ($puedeEditar): ?>
                            <div class="pt-4 border-t border-slate-100 flex justify-end">
                                <button type="submit" class="tap-bounce px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs shadow-blue-500/20 transition-all cursor-pointer">
                                    Guardar Antecedentes Clínicos
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- ============================================================== -->
                <!-- PESTAÑA 3: RED DE CONTACTOS DE EMERGENCIA                      -->
                <!-- ============================================================== -->
                <div id="tab-contactos" class="tab-content hidden space-y-6">
                    <div>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4">
                            <div>
                                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Red de Apoyo y Familiares de Auxilio</h2>
                                <p class="text-xs text-slate-500">Contactos alertados prioritariamente ante eventos del Botón SOS.</p>
                            </div>
                        </div>

                        <?php if (!empty($contactosEmergencia)): ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                <?php foreach ($contactosEmergencia as $contacto): ?>
                                    <div class="p-4 bg-slate-50/80 rounded-2xl border <?= !empty($contacto['es_prioritario']) ? 'border-amber-300 bg-amber-50/30' : 'border-slate-200' ?> relative shadow-xs hover:border-slate-300 transition-colors">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="flex items-center space-x-2">
                                                    <h3 class="font-bold text-slate-900 text-sm"><?= e($contacto['nombre']) ?></h3>
                                                    <?php if (!empty($contacto['es_prioritario'])): ?>
                                                        <span class="px-2 py-0.5 text-[9px] font-black rounded-full bg-amber-500 text-white tracking-wider">PRIORITARIO</span>
                                                    <?php endif; ?>
                                                </div>
                                                <p class="text-xs text-slate-500 font-semibold mt-0.5"><?= e($contacto['parentesco'] ?? 'Familiar') ?></p>
                                            </div>
                                            <a href="tel:<?= e($contacto['telefono']) ?>" class="tap-bounce px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center space-x-1">
                                                <span>📞</span>
                                                <span>Llamar</span>
                                            </a>
                                        </div>

                                        <p class="text-xs font-mono font-bold text-slate-700 mt-2.5">
                                            <?= e($contacto['telefono']) ?>
                                        </p>

                                        <?php if ($puedeEditar): ?>
                                            <form action="<?= APP_URL ?>/expediente/eliminar_contacto" method="POST" class="mt-3 pt-2 border-t border-slate-200/80 flex justify-end" onsubmit="return confirm('¿Seguro que deseas eliminar este contacto de auxilio?');">
                                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                <input type="hidden" name="paciente_id" value="<?= e((string)$paciente['id']) ?>">
                                                <input type="hidden" name="contacto_id" value="<?= e((string)$contacto['id']) ?>">
                                                <button type="submit" class="text-xs text-rose-600 font-bold hover:underline cursor-pointer">Eliminar Contacto</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="p-8 bg-amber-50/60 rounded-3xl border border-amber-200/80 text-center mb-6">
                                <span class="text-3xl">⚠️</span>
                                <h3 class="font-bold text-amber-900 text-sm mt-2">Sin contactos de auxilio registrados</h3>
                                <p class="text-xs text-amber-700 mt-1 max-w-sm mx-auto">
                                    Es indispensable registrar al menos un familiar prioritario para responder ante emergencias médicas.
                                </p>
                            </div>
                        <?php endif; ?>

                        <?php if ($puedeEditar): ?>
                            <div class="bg-slate-50/80 p-5 rounded-3xl border border-slate-200">
                                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider mb-3">Registrar Nuevo Contacto de Auxilio</h3>
                                <form action="<?= APP_URL ?>/expediente/anadir_contacto" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="paciente_id" value="<?= e((string)$paciente['id']) ?>">

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Nombre Completo <span class="text-rose-500">*</span></label>
                                            <input type="text" name="nombre" required placeholder="Ej. Roberto Moguel" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 font-medium focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Parentesco o Rol</label>
                                            <input type="text" name="parentesco" placeholder="Ej. Hijo/a, Cónyuge, Cuidador" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 font-medium focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Teléfono Móvil (WhatsApp) <span class="text-rose-500">*</span></label>
                                            <input type="tel" name="telefono" required placeholder="Ej. +52 999 123 4567" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 font-medium focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                                        </div>
                                    </div>

                                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                        <label class="inline-flex items-center space-x-2 text-xs font-bold text-slate-700 cursor-pointer">
                                            <input type="checkbox" name="es_prioritario" value="1" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                                            <span>Marcar como Contacto Principal Prioritario</span>
                                        </label>

                                        <button type="submit" class="tap-bounce px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                                            + Añadir Contacto
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- PESTAÑA 4: DIRECTORIO DE MÉDICOS TRATANTES                     -->
                <!-- ============================================================== -->
                <div id="tab-doctores" class="tab-content hidden space-y-6">
                    <div>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4">
                            <div>
                                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Equipo Médico Tratante</h2>
                                <p class="text-xs text-slate-500">Directorio de geriatras y especialistas a cargo del cuidado clínico.</p>
                            </div>
                        </div>

                        <?php if (!empty($doctores)): ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                <?php foreach ($doctores as $doc): ?>
                                    <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200 shadow-xs relative hover:border-slate-300 transition-colors">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h3 class="font-bold text-slate-900 text-sm">Dr(a). <?= e($doc['nombre']) ?></h3>
                                                <p class="text-xs text-blue-700 font-bold mt-0.5"><?= e($doc['especialidad'] ?: 'Médico General') ?></p>
                                            </div>
                                            <?php if (!empty($doc['telefono'])): ?>
                                                <a href="tel:<?= e($doc['telefono']) ?>" class="tap-bounce px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold rounded-xl border border-blue-200 transition">
                                                    📞 Llamar
                                                </a>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (!empty($doc['hospital_clinica'])): ?>
                                            <p class="text-xs text-slate-600 mt-2 font-medium">🏥 <?= e($doc['hospital_clinica']) ?></p>
                                        <?php endif; ?>

                                        <?php if (!empty($doc['horario_atencion'])): ?>
                                            <p class="text-[11px] text-slate-500 mt-1">🕒 Horario: <?= e($doc['horario_atencion']) ?></p>
                                        <?php endif; ?>

                                        <?php if ($puedeEditar): ?>
                                            <form action="<?= APP_URL ?>/expediente/eliminar_doctor" method="POST" class="mt-3 pt-2 border-t border-slate-200/80 flex justify-end" onsubmit="return confirm('¿Seguro que deseas eliminar este médico del expediente?');">
                                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                <input type="hidden" name="paciente_id" value="<?= e((string)$paciente['id']) ?>">
                                                <input type="hidden" name="doctor_id" value="<?= e((string)$doc['id']) ?>">
                                                <button type="submit" class="text-xs text-rose-600 font-bold hover:underline cursor-pointer">Eliminar Médico</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="p-8 bg-slate-50 rounded-3xl border border-slate-200 text-center mb-6">
                                <span class="text-3xl">👨‍⚕️</span>
                                <h3 class="font-bold text-slate-800 text-sm mt-2">Sin médicos especialistas registrados</h3>
                                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                    Registra a los médicos de cabecera y geriatras para tener a mano sus números de consultorio.
                                </p>
                            </div>
                        <?php endif; ?>

                        <?php if ($puedeEditar): ?>
                            <div class="bg-blue-50/40 p-5 rounded-3xl border border-blue-100">
                                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider mb-3">Registrar Médico Especialista</h3>
                                <form action="<?= APP_URL ?>/expediente/anadir_doctor" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="paciente_id" value="<?= e((string)$paciente['id']) ?>">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Nombre del Médico <span class="text-rose-500">*</span></label>
                                            <input type="text" name="nombre" required placeholder="Ej. Dra. Laura Sánchez" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 font-medium focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Especialidad</label>
                                            <input type="text" name="especialidad" placeholder="Ej. Geriatría, Cardiología, Medicina Interna" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 font-medium focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Teléfono de Contacto</label>
                                            <input type="tel" name="telefono" placeholder="Ej. +52 999 987 6543" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 font-medium focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Hospital o Consultorio</label>
                                            <input type="text" name="hospital_clinica" placeholder="Ej. Clínica Médica Norte, Consultorio 302" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 font-medium focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none">
                                        </div>
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit" class="tap-bounce px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                                            + Registrar Médico
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- PESTAÑA 5: HISTORIAL DE VISITAS MÉDICAS Y BITÁCORA CLÍNICA      -->
                <!-- ============================================================== -->
                <div id="tab-visitas" class="tab-content hidden space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-100">
                        <div>
                            <h2 class="text-sm sm:text-base font-bold text-slate-800">Bitácora de Visitas y Consultas Médicas</h2>
                            <p class="text-xs text-slate-500">Registro cronológico de diagnósticos, indicaciones y recetas post-consulta.</p>
                        </div>
                        <?php if ($puedeEditar): ?>
                            <a href="<?= APP_URL ?>/visitas/nueva?paciente_id=<?= e((string)$paciente['id']) ?>" 
                               class="tap-bounce touch-min inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-xs shadow-blue-500/20 transition cursor-pointer shrink-0">
                                <span>+ Registrar Visita</span>
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($visitasMedicas)): ?>
                        <div class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            <span class="text-3xl">📋</span>
                            <h3 class="text-sm font-bold text-slate-700 mt-2">No hay visitas médicas registradas</h3>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1 mb-4">
                                Registra los diagnósticos y toma foto a las recetas al salir de cada consulta médica.
                            </p>
                            <?php if ($puedeEditar): ?>
                                <a href="<?= APP_URL ?>/visitas/nueva?paciente_id=<?= e((string)$paciente['id']) ?>" 
                                   class="tap-bounce touch-min inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                                    + Registrar Primera Visita
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="relative pl-4 sm:pl-6 space-y-6 before:absolute before:left-[19px] sm:before:left-[27px] before:top-3 before:bottom-3 before:w-0.5 before:bg-slate-200">
                            <?php foreach ($visitasMedicas as $visita): 
                                $ts = strtotime($visita['fecha_visita']);
                                $fechaFormat = date('d M Y', $ts);
                                $horaFormat = date('H:i', $ts);
                            ?>
                                <div class="relative flex items-start gap-4 sm:gap-6 group">
                                    <!-- Timeline Node -->
                                    <div class="relative z-10 flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 rounded-2xl flex items-center justify-center font-bold text-sm bg-white border-2 border-blue-500 text-blue-600 shadow-xs group-hover:scale-105 transition-transform">
                                        🩺
                                    </div>

                                    <!-- Card de Visita Médica -->
                                    <div class="flex-1 bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-card hover:shadow-md transition-all space-y-3">
                                        
                                        <!-- Top Bar: Fecha/Hora Tabular + Lugar + Doctor -->
                                        <div class="flex flex-wrap items-center justify-between gap-2 pb-2 border-b border-slate-100">
                                            <div class="flex items-center gap-2 font-mono text-xs font-bold text-slate-700 bg-slate-100 px-3 py-1 rounded-xl">
                                                <span>📅 <?= $fechaFormat ?></span>
                                                <span class="text-slate-400">|</span>
                                                <span class="text-blue-600"><?= $horaFormat ?></span>
                                            </div>

                                            <?php if (!empty($visita['lugar_atencion'])): ?>
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200">
                                                    <span>🏥</span>
                                                    <span><?= e($visita['lugar_atencion']) ?></span>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Motivo de Consulta Destacado -->
                                        <div>
                                            <h3 class="text-base font-bold text-slate-900 tracking-tight">
                                                <?= e($visita['motivo_consulta']) ?>
                                            </h3>
                                            <?php if (!empty($visita['doctor_nombre'])): ?>
                                                <p class="text-xs text-slate-500 font-medium mt-0.5">
                                                    Atendido por: <span class="text-slate-800 font-bold">Dr(a). <?= e($visita['doctor_nombre']) ?></span>
                                                </p>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Diagnóstico e Indicaciones Clínicas -->
                                        <?php if (!empty($visita['diagnostico_notas'])): ?>
                                            <div class="p-3.5 bg-blue-50/50 rounded-xl border border-blue-100/80 text-xs sm:text-sm text-slate-700 leading-relaxed">
                                                <p class="font-bold text-blue-950 text-xs uppercase tracking-wider mb-1">Diagnóstico e Indicaciones:</p>
                                                <p class="whitespace-pre-line"><?= e($visita['diagnostico_notas']) ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Badge de Seguimiento / Próxima Cita -->
                                        <?php if (!empty($visita['requiere_seguimiento'])): ?>
                                            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 flex items-start gap-2.5 text-xs">
                                                <span class="text-base leading-none">📅</span>
                                                <div>
                                                    <span class="font-bold text-amber-900">Seguimiento Requerido:</span>
                                                    <span class="font-bold text-amber-800 ml-1"><?= !empty($visita['fecha_seguimiento']) ? date('d/m/Y', strtotime($visita['fecha_seguimiento'])) : 'Fecha pendiente' ?></span>
                                                    <?php if (!empty($visita['instrucciones_seguimiento'])): ?>
                                                        <p class="text-[11px] text-amber-700 mt-0.5"><?= e($visita['instrucciones_seguimiento']) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Miniaturas Expandibles de Recetas / Fotos Adjuntas -->
                                        <?php if (!empty($visita['archivos'])): ?>
                                            <div class="pt-2">
                                                <p class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                                    📸 Recetas y Documentos Adjuntos (<?= count($visita['archivos']) ?>):
                                                </p>
                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                                    <?php foreach ($visita['archivos'] as $archivo): 
                                                        $isImage = strpos($archivo['mime_type'], 'image/') === 0;
                                                        $urlDescarga = APP_URL . '/expediente/archivos/descargar?id=' . $archivo['id'];
                                                    ?>
                                                        <div class="group/thumb relative p-2 bg-slate-50 rounded-xl border border-slate-200 hover:border-blue-300 transition">
                                                            <?php if ($isImage): ?>
                                                                <button type="button" onclick="abrirLightbox('<?= $urlDescarga ?>', '<?= e($archivo['nombre_original']) ?>')" class="w-full text-left cursor-pointer">
                                                                    <img src="<?= $urlDescarga ?>" alt="<?= e($archivo['nombre_original']) ?>" class="w-full h-20 object-cover rounded-lg mb-1 shadow-2xs group-hover/thumb:opacity-90">
                                                                    <span class="block text-[10px] font-semibold text-slate-700 truncate"><?= e($archivo['nombre_original']) ?></span>
                                                                </button>
                                                            <?php else: ?>
                                                                <a href="<?= $urlDescarga ?>" target="_blank" class="block text-center py-2">
                                                                    <div class="w-full h-14 bg-rose-50 text-rose-600 font-black text-xs flex items-center justify-center rounded-lg mb-1">
                                                                        PDF
                                                                    </div>
                                                                    <span class="block text-[10px] font-semibold text-slate-700 truncate"><?= e($archivo['nombre_original']) ?></span>
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Footer Info -->
                                        <?php if (!empty($visita['registrado_por_nombre'])): ?>
                                            <div class="pt-2 text-[11px] text-slate-400">
                                                Registrado por: <?= e($visita['registrado_por_nombre']) ?>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </main>

    <!-- Modal Lightbox para Visualizar Receta / Foto en Grande -->
    <div id="modal-lightbox" class="fixed inset-0 bg-slate-900/80 backdrop-blur-xs z-50 hidden items-center justify-center p-4" onclick="cerrarLightbox()">
        <div class="relative max-w-2xl w-full max-h-[90vh] bg-white rounded-3xl p-4 shadow-2xl flex flex-col items-center" onclick="event.stopPropagation()">
            <div class="w-full flex justify-between items-center pb-2 border-b border-slate-100 mb-3">
                <span id="lightbox-titulo" class="text-xs font-bold text-slate-700 truncate max-w-xs">Receta Adjunta</span>
                <button type="button" onclick="cerrarLightbox()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold flex items-center justify-center hover:bg-slate-200 transition cursor-pointer">
                    &times;
                </button>
            </div>
            <div class="w-full max-h-[70vh] overflow-auto flex items-center justify-center">
                <img id="lightbox-img" src="" alt="Receta" class="max-w-full max-h-[68vh] object-contain rounded-xl">
            </div>
            <div class="w-full pt-3 flex justify-end">
                <a id="lightbox-download" href="#" target="_blank" download class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                    Descargar Original
                </a>
            </div>
        </div>
    </div>

    <script>
    function abrirLightbox(url, titulo) {
        document.getElementById('lightbox-img').src = url;
        document.getElementById('lightbox-titulo').innerText = titulo;
        document.getElementById('lightbox-download').href = url;
        const modal = document.getElementById('modal-lightbox');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function cerrarLightbox() {
        const modal = document.getElementById('modal-lightbox');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    </script>

    <!-- Modal de Código QR de Emergencia (Carnet Digital) -->
    <div id="modal-qr-emergencia" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 border border-slate-100 transform transition-all text-center relative print-area">
            <button type="button" onclick="cerrarModalQR()" class="no-print absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-2 rounded-full hover:bg-slate-100 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <div class="flex items-center justify-center space-x-2 mb-1">
                <span class="w-6 h-6 bg-rose-600 rounded-lg flex items-center justify-center text-white font-black text-xs">✚</span>
                <span class="font-extrabold text-slate-900 tracking-tight text-base">VitalSenior SOS</span>
            </div>
            <p class="text-xs text-slate-500 font-medium">Carnet Médico de Emergencia</p>
            
            <h3 id="qr-paciente-nombre" class="mt-2 text-sm font-bold text-slate-800 leading-tight">
                <?= e($nombreCompleto) ?>
            </h3>

            <div class="my-4 flex justify-center bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <canvas id="qr-canvas" class="mx-auto rounded-lg shadow-xs"></canvas>
            </div>

            <p class="text-[11px] text-slate-500 leading-relaxed">
                Escanea con la cámara del teléfono para ver tipo de sangre, alergias y llamar a contactos de auxilio.
            </p>

            <div class="mt-5 flex flex-col sm:flex-row gap-2 no-print">
                <button type="button" onclick="imprimirCarnetQR()" class="tap-bounce flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center justify-center space-x-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H9v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Imprimir</span>
                </button>
                <button type="button" onclick="cerrarModalQR()" class="tap-bounce px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- Estilos de Impresión Exclusiva para el Carnet QR -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #modal-qr-emergencia, #modal-qr-emergencia .print-area, #modal-qr-emergencia .print-area * {
                visibility: visible;
            }
            #modal-qr-emergencia {
                position: fixed;
                left: 0;
                top: 0;
                width: 100vw;
                height: 100vh;
                display: flex !important;
                align-items: center;
                justify-content: center;
                background: white !important;
                box-shadow: none !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>

    <footer class="text-center py-6 pb-24 md:pb-8 text-xs text-slate-400 no-print">
        VitalSenior • Sistema de Expediente Clínico Geriátrico
    </footer>

    <!-- Barra de Navegación Móvil Fija -->
    <div class="no-print">
        <?php require_once dirname(__DIR__) . '/components/bottom_nav.php'; ?>
    </div>

    <!-- Script del Generador QR -->
    <script src="<?= APP_URL ?>/assets/js/qr-modal.js"></script>
</body>
</html>
