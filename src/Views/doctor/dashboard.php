<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalSenior - Panel Clínico Médico</title>
    
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

    <!-- Top Bar Médica -->
    <header class="bg-white/90 backdrop-blur-md border-b border-slate-200/90 sticky top-0 z-40 shadow-xs px-4 sm:px-6 lg:px-8 py-3.5 transition-all">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
            
            <div class="flex items-center gap-3">
                <a class="tap-bounce flex items-center gap-2.5 group" href="<?= APP_URL ?>/doctor/dashboard">
                    <div class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform text-lg font-black">
                        ✚
                    </div>
                    <div class="leading-none">
                        <span class="text-lg font-black tracking-tight text-slate-900">Vital<span class="text-blue-600">Senior</span></span>
                        <span class="block text-[10px] font-bold text-blue-600 tracking-wider uppercase mt-0.5">Portal Clínico</span>
                    </div>
                </a>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" onclick="abrirModalVincularPaciente()" 
                        class="tap-bounce touch-min px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Vincular Paciente (NIP)</span>
                </button>

                <div class="hidden sm:flex items-center gap-2.5 pl-3 border-l border-slate-200">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center text-xs font-black">
                        Dr
                    </div>
                    <div class="text-left">
                        <p class="text-xs font-bold text-slate-800 leading-tight"><?= e($doctor['nombre'] ?? 'Dr(a). Médico') ?></p>
                        <p class="text-[10px] text-slate-400 font-medium">Céd: <?= e($doctor['cedula_profesional'] ?? 'Sin cédula') ?></p>
                    </div>
                </div>

                <a href="<?= APP_URL ?>/logout" class="tap-bounce touch-min p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Cerrar Sesión">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                </a>
            </div>

        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 w-full flex-1 space-y-6">
        <?php require_once dirname(__DIR__) . '/components/toast.php'; ?>

        <!-- Banner de Bienvenida y Métricas Médicas -->
        <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                <div class="md:col-span-2 space-y-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-500/20 text-blue-200 border border-blue-400/30">
                        Especialidad: <?= e($doctor['especialidad'] ?? 'Geriatría y Medicina Integral') ?>
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        Panel de Seguimiento Clínico
                    </h1>
                    <p class="text-xs sm:text-sm text-blue-100/80 max-w-xl leading-relaxed">
                        Supervisa de forma continua signos vitales, adherencia farmacológica y prescripciones de tus pacientes adultos mayores vinculados.
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-center">
                        <span class="block text-2xl font-black text-white"><?= $totalPacientes ?></span>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-blue-200">Pacientes</span>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-center">
                        <span class="block text-2xl font-black <?= ($promedioAdherencia ?? 100) >= 80 ? 'text-emerald-300' : 'text-amber-300' ?>"><?= $promedioAdherencia ?? 100 ?>%</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-blue-200">Adherencia</span>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-center">
                        <span class="block text-2xl font-black <?= $alertasCriticas > 0 ? 'text-rose-400 font-extrabold' : 'text-emerald-400' ?>"><?= $alertasCriticas ?></span>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-blue-200">Alertas</span>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-center">
                        <span class="block text-2xl font-black text-white"><?= $totalRecetas ?></span>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-blue-200">Recetas</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buscador y Listado de Pacientes -->
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-black text-slate-900">Pacientes en Seguimiento</h2>
                    <p class="text-xs text-slate-500">Expedientes clínicos activos vinculados a tu cédula profesional</p>
                </div>

                <!-- Buscador Dinámico en Vivo -->
                <div class="w-full sm:w-72 relative">
                    <input type="text" id="buscador-pacientes" onkeyup="filtrarPacientes()" placeholder="Buscar por nombre o NIP..." 
                           class="w-full rounded-2xl border-slate-200 bg-white px-3.5 py-2.5 pl-9 text-xs sm:text-sm font-medium focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 outline-none transition shadow-xs">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <?php if (empty($pacientes)): ?>
                <!-- Estado Vacío -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-12 text-center max-w-lg mx-auto space-y-4 shadow-card">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center mx-auto text-2xl font-black">
                        👨‍⚕️
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">No tienes pacientes vinculados aún</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Solicita al familiar o cuidador del adulto mayor su <strong>Código / NIP de Vinculación</strong> (ej. ROB-4821) para incorporarlo a tu panel clínico.
                    </p>
                    <button type="button" onclick="abrirModalVincularPaciente()" 
                            class="tap-bounce touch-min px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-2xl shadow-md shadow-blue-500/20 transition inline-flex items-center gap-2 cursor-pointer">
                        <span>+ Vincular Primer Paciente con NIP</span>
                    </button>
                </div>
            <?php else: ?>
                <!-- Grid de Tarjetas de Pacientes -->
                <div id="grid-pacientes" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <?php foreach ($pacientes as $p): 
                        $nombreCompleto = trim($p['nombre'] . ' ' . ($p['apellidos'] ?? ''));
                        $edad = !empty($p['fecha_nacimiento']) ? (date('Y') - date('Y', strtotime($p['fecha_nacimiento']))) : 'N/D';
                        $tieneAlerta = !empty($p['alerta_emitida']);
                        $adh = (float)($p['adherencia_semanal'] ?? 100);
                    ?>
                        <div class="paciente-card bg-white rounded-3xl border <?= $tieneAlerta ? 'border-rose-300 ring-2 ring-rose-500/20 shadow-md' : 'border-slate-200/80' ?> p-5 shadow-card hover:shadow-lg transition-all space-y-4 flex flex-col justify-between"
                             data-nombre="<?= strtolower(e($nombreCompleto . ' ' . ($p['codigo_vinculacion'] ?? ''))) ?>">
                            
                            <div class="space-y-3">
                                <!-- Cabecera de la tarjeta -->
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-black text-lg shrink-0 shadow-xs">
                                            <?= mb_strtoupper(mb_substr($p['nombre'], 0, 1)) ?>
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="text-sm font-black text-slate-900 truncate" title="<?= e($nombreCompleto) ?>">
                                                <?= e($nombreCompleto) ?>
                                            </h3>
                                            <p class="text-[11px] text-slate-500 font-medium">
                                                <?= $edad !== 'N/D' ? "{$edad} años" : '' ?> • Sangre: <strong class="text-slate-700"><?= e($p['tipo_sangre'] ?? 'N/D') ?></strong>
                                            </p>
                                        </div>
                                    </div>
                                    <span class="font-mono text-[11px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-lg border border-blue-200 shrink-0">
                                        <?= e($p['codigo_vinculacion'] ?? 'N/A') ?>
                                    </span>
                                </div>

                                <!-- Resumen de Signos Vitales y Adherencia -->
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 space-y-2 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Últimos Signos</span>
                                        <?php if ($tieneAlerta): ?>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-rose-100 text-rose-700">
                                                ⚠️ Alerta
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                                ✓ Normal
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!empty($p['presion_sistolica'])): ?>
                                        <div class="grid grid-cols-2 gap-2 font-mono text-[11px]">
                                            <div class="bg-white p-1.5 rounded-xl border border-slate-200/70 text-center">
                                                <span class="text-[9px] text-slate-400 block">P.A.</span>
                                                <strong class="text-slate-800"><?= (int)$p['presion_sistolica'] ?>/<?= (int)$p['presion_diastolica'] ?></strong>
                                            </div>
                                            <div class="bg-white p-1.5 rounded-xl border border-slate-200/70 text-center">
                                                <span class="text-[9px] text-slate-400 block">Glucosa</span>
                                                <strong class="text-slate-800"><?= number_format((float)$p['glucosa'], 0) ?> mg/dL</strong>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-[11px] text-slate-400 italic text-center">Sin registros recientes de signos</p>
                                    <?php endif; ?>

                                    <!-- Indicador de Adherencia Farmacológica -->
                                    <div class="pt-1.5 border-t border-slate-200/60 flex items-center justify-between text-[11px]">
                                        <span class="text-slate-500 font-semibold">Adherencia 7 días:</span>
                                        <span class="font-bold px-2 py-0.5 rounded-lg <?= $adh >= 80 ? 'bg-emerald-100 text-emerald-800' : ($adh >= 50 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') ?>">
                                            💊 <?= $adh ?>%
                                        </span>
                                    </div>
                                </div>

                                <!-- Padecimientos clave -->
                                <?php if (!empty($p['enfermedades_cronicas']) && $p['enfermedades_cronicas'] !== 'Ninguna'): ?>
                                    <div class="text-[11px]">
                                        <span class="font-bold text-slate-500">Padecimientos:</span>
                                        <p class="text-slate-700 line-clamp-1"><?= e($p['enfermedades_cronicas']) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Acciones Clínicas -->
                            <div class="pt-3 border-t border-slate-100 grid grid-cols-3 gap-2">
                                <a href="<?= APP_URL ?>/doctor/paciente/expediente?id=<?= e((string)$p['id']) ?>" 
                                   class="tap-bounce touch-min py-2 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold rounded-xl text-center shadow-xs transition flex items-center justify-center gap-1">
                                    <span>Expediente</span>
                                </a>
                                <a href="<?= APP_URL ?>/doctor/recetas/nueva?paciente_id=<?= e((string)$p['id']) ?>" 
                                   class="tap-bounce touch-min py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold rounded-xl text-center transition flex items-center justify-center gap-1">
                                    <span>+ Receta</span>
                                </a>
                                <a href="<?= APP_URL ?>/doctor/paciente/pdf?id=<?= e((string)$p['id']) ?>" target="_blank"
                                   class="tap-bounce touch-min py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold rounded-xl text-center transition flex items-center justify-center gap-1"
                                   title="Descargar Reporte PDF">
                                    <span>PDF ↓</span>
                                </a>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <!-- Modal para Vincular Paciente por NIP -->
    <div id="modal-vincular-paciente" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative">
            <button type="button" onclick="cerrarModalVincularPaciente()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 text-slate-500 font-bold flex items-center justify-center hover:bg-slate-200 transition cursor-pointer">
                &times;
            </button>

            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>

            <h3 class="text-lg font-bold text-slate-900 text-center mb-1">Vincular Adulto Mayor</h3>
            <p class="text-xs text-slate-500 text-center mb-5">Ingresa el código NIP provisto por la familia para incorporar el expediente a tu panel médico.</p>

            <form action="<?= APP_URL ?>/doctor/paciente/vincular" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Código / NIP del Paciente</label>
                    <input type="text" name="codigo_vinculacion" required placeholder="Ej. ROB-4821 o SEN-9124" maxlength="20"
                           class="w-full text-center uppercase tracking-widest font-mono font-bold text-lg rounded-2xl border-slate-300 px-4 py-3 bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 outline-none transition">
                </div>

                <div class="pt-2 flex gap-2">
                    <button type="button" onclick="cerrarModalVincularPaciente()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 transition cursor-pointer">
                        Vincular a Mi Panel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalVincularPaciente() {
            const modal = document.getElementById('modal-vincular-paciente');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function cerrarModalVincularPaciente() {
            const modal = document.getElementById('modal-vincular-paciente');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function filtrarPacientes() {
            const query = document.getElementById('buscador-pacientes').value.toLowerCase();
            const cards = document.querySelectorAll('.paciente-card');
            cards.forEach(card => {
                const text = card.getAttribute('data-nombre') || '';
                if (text.includes(query)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>

</body>
</html>
