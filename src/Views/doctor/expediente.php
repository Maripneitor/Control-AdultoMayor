<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente Clínico - <?= e($paciente['nombre'] ?? 'Paciente') ?> | VitalSenior</title>
    
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
        .tab-btn.active {
            color: #2563eb;
            border-bottom-color: #2563eb;
            background-color: #eff6ff;
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
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
            
            <div class="flex items-center gap-3">
                <a class="tap-bounce flex items-center gap-2 text-slate-600 hover:text-slate-900 text-xs font-bold py-1.5 px-3 bg-slate-100 hover:bg-slate-200 rounded-xl transition" href="<?= APP_URL ?>/doctor/dashboard">
                    <span>← Volver al Panel</span>
                </a>
                <span class="text-xs text-slate-400 hidden sm:inline">•</span>
                <span class="text-xs font-black text-slate-800 truncate hidden sm:inline">Expediente: <?= e($pacienteNombreDisplay) ?></span>
            </div>

            <div class="flex items-center gap-2">
                <a href="<?= APP_URL ?>/doctor/recetas/nueva?paciente_id=<?= (int)$paciente['id'] ?>" 
                   class="tap-bounce touch-min px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Emitir Receta</span>
                </a>

                <a href="<?= APP_URL ?>/doctor/paciente/pdf?id=<?= (int)$paciente['id'] ?>" target="_blank"
                   class="tap-bounce touch-min px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    <span>Reporte PDF</span>
                </a>
            </div>

        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 w-full flex-1 space-y-6">
        <?php require_once dirname(__DIR__) . '/components/toast.php'; ?>

        <!-- Tarjeta Encabezado del Paciente -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-card space-y-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-3xl bg-blue-600 text-white flex items-center justify-center font-black text-2xl shadow-md shadow-blue-500/20 shrink-0">
                        <?= mb_strtoupper(mb_substr($paciente['nombre'], 0, 1)) ?>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl sm:text-2xl font-black text-slate-900"><?= e($pacienteNombreDisplay) ?></h1>
                            <span class="font-mono text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-lg border border-blue-200">
                                <?= e($paciente['codigo_vinculacion'] ?? 'N/D') ?>
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            <?= $edad !== 'N/D' ? "{$edad} años" : '' ?> • Nacimiento: <?= !empty($paciente['fecha_nacimiento']) ? date('d/m/Y', strtotime($paciente['fecha_nacimiento'])) : 'N/D' ?>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <a href="<?= APP_URL ?>/emergencia?token=<?= e((string)($paciente['qr_token'] ?? '')) ?>" target="_blank"
                       class="tap-bounce touch-min flex-1 sm:flex-initial px-4 py-2 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
                        <span>📱 Ver Ficha SOS QR</span>
                    </a>
                </div>
            </div>

            <!-- Ficha Médica Rápida -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 block mb-0.5 text-[10px] font-bold uppercase tracking-wider">Tipo de Sangre</span>
                    <span class="font-mono text-base font-black text-slate-900"><?= e($paciente['tipo_sangre'] ?? 'N/D') ?></span>
                </div>
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 block mb-0.5 text-[10px] font-bold uppercase tracking-wider">Adherencia Semanal</span>
                    <span class="font-mono text-base font-black <?= $adherenciaSemanal >= 80 ? 'text-emerald-700' : ($adherenciaSemanal >= 50 ? 'text-amber-700' : 'text-rose-700') ?>">
                        <?= $adherenciaSemanal ?>%
                    </span>
                </div>
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 block mb-0.5 text-[10px] font-bold uppercase tracking-wider">Alergias</span>
                    <span class="font-bold text-slate-800 line-clamp-1"><?= e($paciente['alergias'] ?? 'Ninguna') ?></span>
                </div>
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 block mb-0.5 text-[10px] font-bold uppercase tracking-wider">Enfermedades Crónicas</span>
                    <span class="font-bold text-slate-800 line-clamp-1"><?= e($paciente['enfermedades_cronicas'] ?? 'Ninguna') ?></span>
                </div>
            </div>
        </div>

        <!-- Navegación por Pestañas Clínicas -->
        <div class="border-b border-slate-200 bg-white rounded-2xl px-2 shadow-subtle flex gap-1 overflow-x-auto">
            <button onclick="cambiarTab('signos')" id="btn-tab-signos" class="tab-btn active px-4 py-3 text-xs font-bold border-b-2 border-transparent hover:text-blue-600 transition whitespace-nowrap cursor-pointer">
                📊 Signos Vitales y Tendencias
            </button>
            <button onclick="cambiarTab('medicacion')" id="btn-tab-medicacion" class="tab-btn px-4 py-3 text-xs font-bold border-b-2 border-transparent text-slate-600 hover:text-blue-600 transition whitespace-nowrap cursor-pointer">
                💊 Medicación y Adherencia
            </button>
            <button onclick="cambiarTab('recetas')" id="btn-tab-recetas" class="tab-btn px-4 py-3 text-xs font-bold border-b-2 border-transparent text-slate-600 hover:text-blue-600 transition whitespace-nowrap cursor-pointer">
                📜 Recetas Médicas Emitidas
            </button>
            <button onclick="cambiarTab('contactos')" id="btn-tab-contactos" class="tab-btn px-4 py-3 text-xs font-bold border-b-2 border-transparent text-slate-600 hover:text-blue-600 transition whitespace-nowrap cursor-pointer">
                👥 Contactos y Red Familiar
            </button>
            <button onclick="cambiarTab('notas')" id="btn-tab-notas" class="tab-btn px-4 py-3 text-xs font-bold border-b-2 border-transparent text-slate-600 hover:text-blue-600 transition whitespace-nowrap cursor-pointer">
                ✍️ Notas de Evolución
            </button>
        </div>

        <!-- Pestaña 1: Signos Vitales y Tendencias -->
        <div id="tab-signos" class="tab-content space-y-4">
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Historial de Signos Vitales (Últimos Registros)</h2>
                    <span class="text-xs text-slate-400 font-medium"><?= count($signosHistorial) ?> tomas registradas</span>
                </div>

                <?php if (empty($signosHistorial)): ?>
                    <p class="text-xs text-slate-400 py-6 text-center italic">No hay registros de signos vitales para este paciente.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 text-slate-400 uppercase tracking-wider font-bold">
                                    <th class="py-2.5 px-3">Fecha y Hora</th>
                                    <th class="py-2.5 px-3">Presión Arterial</th>
                                    <th class="py-2.5 px-3">Glucosa</th>
                                    <th class="py-2.5 px-3">Ritmo Cardíaco</th>
                                    <th class="py-2.5 px-3">Sueño</th>
                                    <th class="py-2.5 px-3">Dolor</th>
                                    <th class="py-2.5 px-3">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-mono">
                                <?php foreach ($signosHistorial as $s): ?>
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="py-2.5 px-3 text-slate-600"><?= date('d/m/Y H:i', strtotime($s['created_at'])) ?></td>
                                        <td class="py-2.5 px-3 font-bold text-slate-900"><?= (int)$s['presion_sistolica'] ?>/<?= (int)$s['presion_diastolica'] ?> <span class="text-[10px] text-slate-400 font-normal">mmHg</span></td>
                                        <td class="py-2.5 px-3 font-bold text-slate-900"><?= !empty($s['glucosa']) ? number_format((float)$s['glucosa'], 0) . ' mg/dL (' . e($s['momento_glucosa'] ?? '') . ')' : 'N/D' ?></td>
                                        <td class="py-2.5 px-3 text-slate-800"><?= !empty($s['ritmo_cardiaco']) ? (int)$s['ritmo_cardiaco'] . ' lpm' : 'N/D' ?></td>
                                        <td class="py-2.5 px-3 text-slate-600"><?= !empty($s['horas_sueno']) ? $s['horas_sueno'] . ' hrs' : 'N/D' ?></td>
                                        <td class="py-2.5 px-3 text-slate-600"><?= isset($s['nivel_dolor']) ? $s['nivel_dolor'] . '/10' : 'N/D' ?></td>
                                        <td class="py-2.5 px-3">
                                            <?php if (!empty($s['alerta_emitida'])): ?>
                                                <span class="px-2 py-0.5 bg-rose-100 text-rose-700 font-sans font-bold text-[10px] rounded-full">Alerta</span>
                                            <?php else: ?>
                                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 font-sans font-bold text-[10px] rounded-full">Normal</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pestaña 2: Medicación y Adherencia -->
        <div id="tab-medicacion" class="tab-content hidden space-y-4">
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Fármacos y Esquema Terapéutico Activo</h2>
                    <span class="text-xs font-bold text-blue-600">Adherencia: <?= $adherenciaSemanal ?>%</span>
                </div>

                <?php if (empty($medicamentosActivos)): ?>
                    <p class="text-xs text-slate-400 py-6 text-center italic">No hay medicamentos activos registrados en el expediente.</p>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($medicamentosActivos as $m): ?>
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
                                <div class="flex items-start justify-between">
                                    <h3 class="text-xs font-black text-slate-900"><?= e($m['nombre_comercial']) ?></h3>
                                    <span class="text-[10px] font-bold bg-blue-100 text-blue-800 px-2 py-0.5 rounded-md">Cada <?= (int)$m['frecuencia_horas'] ?> hrs</span>
                                </div>
                                <p class="text-xs text-slate-600">Dosis: <strong class="text-slate-800"><?= e($m['dosis']) ?></strong> (<?= (int)$m['dosis_por_toma'] ?> por toma)</p>
                                <p class="text-[11px] text-slate-500">Stock en botiquín: <strong><?= (int)$m['stock_actual'] ?> unidades</strong></p>
                                <?php if (!empty($m['instrucciones'])): ?>
                                    <p class="text-[11px] text-slate-500 italic bg-white p-2 rounded-xl border border-slate-200/60">"<?= e($m['instrucciones']) ?>"</p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pestaña 3: Recetas Médicas Emitidas -->
        <div id="tab-recetas" class="tab-content hidden space-y-4">
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Historial de Prescripciones y Recetas</h2>
                    <a href="<?= APP_URL ?>/doctor/recetas/nueva?paciente_id=<?= (int)$paciente['id'] ?>" class="tap-bounce touch-min px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                        + Emitir Nueva Receta
                    </a>
                </div>

                <?php if (empty($recetas)): ?>
                    <p class="text-xs text-slate-400 py-6 text-center italic">No se han emitido recetas médicas digitales para este paciente.</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($recetas as $r): ?>
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-black text-slate-900">Fecha: <?= date('d/m/Y', strtotime($r['fecha_emision'])) ?></span>
                                    <span class="text-[11px] font-bold text-blue-700"><?= e($r['doctor_nombre'] ?? 'Médico Tratante') ?></span>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Diagnóstico Clínico:</span>
                                    <p class="text-xs text-slate-800 font-semibold"><?= nl2br(e($r['diagnostico'])) ?></p>
                                </div>
                                <?php if (!empty($r['indicaciones_generales'])): ?>
                                    <div>
                                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Indicaciones Terapéuticas:</span>
                                        <p class="text-xs text-slate-600 bg-white p-2.5 rounded-xl border border-slate-200/70"><?= nl2br(e($r['indicaciones_generales'])) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pestaña 4: Contactos SOS y Familiares -->
        <div id="tab-contactos" class="tab-content hidden space-y-4">
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-4">
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Red de Cuidadores y Contactos Familiares</h2>

                <?php if (empty($contactos)): ?>
                    <p class="text-xs text-slate-400 py-6 text-center italic">No hay contactos familiares registrados.</p>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($contactos as $c): ?>
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between gap-3">
                                <div>
                                    <h3 class="text-xs font-black text-slate-900"><?= e($c['nombre']) ?></h3>
                                    <p class="text-[11px] text-slate-500"><?= e($c['parentesco']) ?></p>
                                </div>
                                <a href="tel:<?= e($c['telefono']) ?>" class="tap-bounce touch-min px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 text-xs font-bold rounded-xl transition">
                                    📞 <?= e($c['telefono']) ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pestaña 5: Notas de Evolución Médica -->
        <div id="tab-notas" class="tab-content hidden space-y-4">
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-card space-y-4">
                <div>
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Notas de Evolución y Criterio Clínico</h2>
                    <p class="text-xs text-slate-500">Estas notas son privadas entre el equipo médico y forman parte del expediente clínico oficial.</p>
                </div>

                <form action="<?= APP_URL ?>/doctor/paciente/notas" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="paciente_id" value="<?= (int)$paciente['id'] ?>">

                    <div>
                        <textarea name="notas_medicas" rows="6" placeholder="Escribe aquí las observaciones clínicas, evolución de signos vitales, ajuste de dosis o recomendaciones..."
                                  class="w-full rounded-2xl border-slate-300 p-4 text-xs sm:text-sm bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 outline-none transition leading-relaxed"><?= e($notasDoctorActual) ?></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="tap-bounce touch-min px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 transition cursor-pointer">
                            Guardar Notas de Evolución
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </main>

    <script>
        function cambiarTab(nombreTab) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));

            const content = document.getElementById('tab-' + nombreTab);
            const btn = document.getElementById('btn-tab-' + nombreTab);

            if (content && btn) {
                content.classList.remove('hidden');
                btn.classList.add('active');
            }
        }

        // Si hay hash en la URL, activar la pestaña correspondiente
        window.addEventListener('DOMContentLoaded', () => {
            const hash = window.location.hash.replace('#tab-', '');
            if (hash && document.getElementById('tab-' + hash)) {
                cambiarTab(hash);
            }
        });
    </script>

</body>
</html>
