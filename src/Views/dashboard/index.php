<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>VitalSenior - Mi Perfil de Salud</title>
    <link rel="manifest" href="<?= APP_URL ?>/manifest.json">
    <meta name="theme-color" content="#1e40af">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
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
                        'tactile': '0 8px 0 0 rgb(185 28 28)',
                        'tactile-active': '0 2px 0 0 rgb(185 28 28)',
                        'card-glow': '0 10px 30px -5px rgba(37, 99, 235, 0.08), 0 0 0 1px rgba(226, 232, 240, 0.8)',
                    }
                }
            }
        }
    </script>
    <style>
        /* Tipografía adaptada para accesibilidad geriátrica */
        html { font-size: 17px; }
        @media (min-width: 768px) { html { font-size: 18px; } }
        
        .tap-bounce {
            transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.15s cubic-bezier(0.4, 0, 0.2, 1);
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
<body id="dashboard-body" class="bg-slate-50 text-slate-900 font-sans min-h-full flex flex-col justify-between transition-opacity duration-500 opacity-0 pb-20 md:pb-0">
    
    <!-- Encabezado de Alto Contraste -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 px-4 sm:px-6 py-3.5 shadow-xs">
        <div class="max-w-5xl mx-auto flex justify-between items-center gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-11 h-11 sm:w-12 sm:h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center shadow-md shadow-blue-500/20">
                    <svg class="w-6 h-6 text-white" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.25">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg sm:text-2xl font-black text-slate-900 leading-tight tracking-tight">
                        Hola, <?= e($paciente['nombre'] ?? 'Bienvenido') ?>
                    </h1>
                    <p class="text-xs sm:text-sm font-semibold text-slate-500">
                        Perfil de Salud Geriátrico
                    </p>
                </div>
            </div>

            <!-- Navegación de Escritorio (md+) -->
            <div class="hidden md:flex items-center space-x-3">
                <a href="<?= APP_URL ?>/medicamentos" class="tap-bounce px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a4.5 4.5 0 119 9l-9 9a4.5 4.5 0 01-9-9l9-9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 13.5l9-9"/>
                    </svg>
                    <span>Medicamentos</span>
                </a>
                <a href="<?= APP_URL ?>/expediente" class="tap-bounce px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                    </svg>
                    <span>Expediente</span>
                </a>
                <a href="<?= APP_URL ?>/logout" class="tap-bounce touch-min px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 hover:text-rose-600 text-xs font-bold rounded-xl border border-slate-200 transition-all inline-flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-rose-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                    </svg>
                    <span>Cerrar Sesión</span>
                </a>
            </div>

            <!-- Botón Cerrar Sesión Móvil -->
            <div class="md:hidden flex items-center">
                <a href="<?= APP_URL ?>/logout" class="tap-bounce touch-min px-3 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 inline-flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                    </svg>
                    <span>Salir</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto p-4 sm:p-6 md:p-8 w-full space-y-6">
        <?php require_once dirname(__DIR__) . '/components/toast.php'; ?>

        <!-- BANNER DE ALERTAS DINÁMICAS (Anti-slop & Modern UI) -->
        <?php if (!empty($alertasDosis) || !empty($alertasInventario)): ?>
            <div class="space-y-3 no-print">
                <!-- Alertas Críticas: Dosis Omitidas -->
                <?php foreach ($alertasDosis as $ad): ?>
                    <div class="bg-rose-50/90 hover:bg-rose-50 border border-rose-200/90 rounded-2xl sm:rounded-3xl p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3.5 shadow-sm transition-all">
                        <div class="flex items-start sm:items-center gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 border border-rose-200/80 shadow-xs">
                                <svg class="w-6 h-6 text-rose-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="bg-rose-100 text-rose-700 text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full border border-rose-200 tracking-wider">
                                        Crítica
                                    </span>
                                    <h3 class="text-rose-950 font-extrabold text-sm sm:text-base tracking-tight">
                                        Dosis Omitida no Registrada
                                    </h3>
                                </div>
                                <p class="text-slate-700 text-xs sm:text-sm font-medium leading-relaxed">
                                    Se omitió la toma de <strong class="text-slate-900 font-black"><?= e($ad['nombre_comercial']) ?></strong> programada para las <span class="font-mono font-bold text-rose-800 bg-rose-100/60 px-1.5 py-0.5 rounded"><?= date('H:i', strtotime($ad['hora_programada'])) ?></span>.
                                </p>
                            </div>
                        </div>
                        <form action="<?= APP_URL ?>/medicamentos/toma" method="POST" class="inline m-0 self-stretch sm:self-center">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="toma_id" value="<?= (int)$ad['id'] ?>">
                            <input type="hidden" name="accion" value="justificar_retraso">
                            <button type="submit" class="tap-bounce touch-min w-full sm:w-auto px-5 py-2.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-rose-600/20 transition-all flex items-center justify-center space-x-1.5 whitespace-nowrap cursor-pointer">
                                <span>Gestionar Toma</span>
                                <span>→</span>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>

                <!-- Alertas de Inventario: Stock Bajo -->
                <?php foreach ($alertasInventario as $ai): ?>
                    <div class="bg-amber-50/90 hover:bg-amber-50 border border-amber-200/90 rounded-2xl sm:rounded-3xl p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3.5 shadow-sm transition-all">
                        <div class="flex items-start sm:items-center gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 border border-amber-200/80 shadow-xs">
                                <svg class="w-6 h-6 text-amber-700" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="bg-amber-100 text-amber-800 text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full border border-amber-200 tracking-wider">
                                        Inventario
                                    </span>
                                    <h3 class="text-amber-950 font-extrabold text-sm sm:text-base tracking-tight">
                                        Suministro Próximo a Agotarse
                                    </h3>
                                </div>
                                <p class="text-slate-700 text-xs sm:text-sm font-medium leading-relaxed">
                                    Quedan aprox. <strong class="text-amber-900 font-black font-mono"><?= e((string)$ai['dias_restantes']) ?> días</strong> de <strong class="text-slate-900"><?= e($ai['nombre_comercial']) ?></strong> (Stock: <span class="font-bold text-slate-800"><?= e((string)$ai['stock_actual']) ?> uds</span>).
                                </p>
                            </div>
                        </div>
                        <a href="<?= APP_URL ?>/medicamentos" 
                           class="tap-bounce touch-min w-full sm:w-auto px-5 py-2.5 bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-amber-600/20 transition-all flex items-center justify-center space-x-1.5 whitespace-nowrap self-stretch sm:self-center">
                            <span>Resurtir Stock</span>
                            <span>→</span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php
            // Lógica de cálculo de próxima medicina
            $proximaMedicina = null;
            if (!empty($tomasDelDia)) {
                $ahora = date('H:i');
                foreach ($tomasDelDia as $toma) {
                    if ($toma['estado'] === 'pendiente' && $toma['hora_programada'] >= $ahora) {
                        $proximaMedicina = $toma;
                        break;
                    }
                }
                if (!$proximaMedicina) {
                    foreach ($tomasDelDia as $toma) {
                        if ($toma['estado'] === 'pendiente') {
                            $proximaMedicina = $toma;
                            break;
                        }
                    }
                }
            }
        ?>

        <!-- HERO: Tarjeta de Próximo Medicamento -->
        <section class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-card-glow relative overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-3.5 py-1.5 rounded-full border border-blue-100">
                    Horario de Medicación
                </span>
                <span class="text-xs font-semibold text-slate-500 tabular-nums">
                    Hoy, <?= date('d M Y') ?>
                </span>
            </div>

            <?php if ($proximaMedicina): ?>
                <div class="text-center py-4">
                    <p class="text-xs sm:text-sm font-bold text-slate-500 uppercase tracking-wide">Tu próxima medicina es</p>
                    <h2 class="text-3xl sm:text-5xl font-black text-slate-900 my-2.5 tracking-tight leading-tight">
                        <?= e($proximaMedicina['medicamento_nombre'] ?? $proximaMedicina['nombre_comercial']) ?>
                    </h2>
                    <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-800 px-5 py-2 rounded-2xl border border-blue-200 font-bold text-base sm:text-lg my-2">
                        <svg class="w-5 h-5 text-blue-600 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="tabular-nums">Programada a las <?= date('H:i', strtotime($proximaMedicina['hora_programada'])) ?></span>
                    </div>
                </div>

                <div class="mt-6 flex flex-col sm:flex-row gap-3.5 justify-center items-center">
                    <form action="<?= APP_URL ?>/medicamentos/toma" method="POST" class="w-full sm:w-auto">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="registro_id" value="<?= e((string)$proximaMedicina['registro_id']) ?>">
                        <button type="submit" class="tap-bounce touch-min w-full sm:w-auto px-8 py-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-black text-base sm:text-lg rounded-2xl shadow-lg shadow-emerald-600/25 flex items-center justify-center space-x-2 cursor-pointer">
                            <svg class="w-6 h-6 text-white" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.25">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                            <span>Marcar como Tomada</span>
                        </button>
                    </form>

                    <a href="<?= APP_URL ?>/medicamentos/control" class="tap-bounce touch-min w-full sm:w-auto px-6 py-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-2xl text-center transition flex items-center justify-center border border-slate-200">
                        Ver Lista del Día
                    </a>
                </div>
            <?php else: ?>
                <!-- Estado Vacío Elegante (Sin dosis pendientes) -->
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-emerald-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.25">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-black text-slate-800 mb-1">¡Todo al día!</h2>
                    <p class="text-sm text-slate-500 max-w-md mx-auto">
                        No tienes tomas pendientes por el momento. Tus cuidadores pueden revisar tu historial en cualquier momento.
                    </p>
                    <a href="<?= APP_URL ?>/medicamentos" class="tap-bounce inline-block mt-4 text-xs font-bold text-blue-600 hover:underline py-2">
                        Consultar catálogo completo de fármacos →
                    </a>
                </div>
            <?php endif; ?>
        </section>

        <!-- BOTÓN SOS DE EMERGENCIA TÁCTIL (Touch target gigante) -->
        <section>
            <button id="btn-sos" 
                    type="button"
                    data-paciente-id="<?= e((string)$paciente['id']) ?>"
                    class="tap-bounce touch-min w-full py-6 sm:py-8 px-4 bg-rose-600 text-white rounded-3xl shadow-tactile hover:bg-rose-500 active:shadow-tactile-active active:translate-y-1.5 transition-all flex flex-col items-center justify-center cursor-pointer focus:outline-none focus:ring-4 focus:ring-rose-300">
                <div class="w-16 h-16 rounded-full bg-rose-700/60 flex items-center justify-center mb-2 shadow-inner">
                    <svg class="w-9 h-9 text-white animate-pulse" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <span class="text-2xl sm:text-3xl font-black tracking-wider uppercase">Botón SOS de Auxilio</span>
                <span class="text-xs sm:text-sm font-semibold text-rose-100 mt-1 text-center">
                    Avisar a mis familiares y cuidadores inmediatamente
                </span>
            </button>
        </section>

        <!-- ACCIONES RÁPIDAS DEL PACIENTE (Grid adaptativo: 1 col móvil, 3 cols md+) -->
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="<?= APP_URL ?>/expediente" class="tap-bounce touch-min p-5 sm:p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-blue-300 hover:shadow-md transition-all text-center flex flex-col items-center justify-center group">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                    </svg>
                </div>
                <span class="text-base font-bold text-slate-800">Mi Expediente</span>
                <span class="text-xs text-slate-500 mt-0.5">Alergias y contactos</span>
            </a>

            <a href="<?= APP_URL ?>/medicamentos" class="tap-bounce touch-min p-5 sm:p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-teal-300 hover:shadow-md transition-all text-center flex flex-col items-center justify-center group">
                <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a4.5 4.5 0 119 9l-9 9a4.5 4.5 0 01-9-9l9-9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 13.5l9-9"/>
                    </svg>
                </div>
                <span class="text-base font-bold text-slate-800">Medicinas</span>
                <span class="text-xs text-slate-500 mt-0.5">Horarios y tomas</span>
            </a>

            <a href="<?= APP_URL ?>/emergencia?token=<?= e((string)($paciente['qr_token'] ?? $paciente['uuid_publico'])) ?>" target="_blank" class="tap-bounce touch-min p-5 sm:p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-indigo-300 hover:shadow-md transition-all text-center flex flex-col items-center justify-center group">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h3v3h-3v-3zM16.5 19.5h3v-3h-3v3z"/>
                    </svg>
                </div>
                <span class="text-base font-bold text-slate-800">Ficha QR SOS</span>
                <span class="text-xs text-slate-500 mt-0.5">Credencial digital</span>
            </a>
        </section>

        <!-- VINCULACIÓN FAMILIAR -->
        <section class="bg-slate-100/80 p-5 sm:p-6 rounded-3xl border border-slate-200 text-center">
            <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">
                Código de Vinculación Familiar
            </h3>
            <div class="inline-flex items-center space-x-2 bg-white px-6 py-2.5 rounded-xl border border-slate-300 shadow-xs">
                <span class="font-mono text-xl sm:text-2xl font-black text-slate-800 tracking-widest">
                    <?= e($paciente['codigo_vinculacion'] ?? 'NO-GEN') ?>
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-2 max-w-md mx-auto">
                Comparte este código con tus familiares para que puedan gestionar tus recordatorios y expedientes.
            </p>
        </section>
    </main>

    <!-- Barra de Navegación Móvil Fija -->
    <?php require_once dirname(__DIR__) . '/components/bottom_nav.php'; ?>

    <!-- Footer Simple -->
    <footer class="text-center py-6 pb-24 md:pb-6 text-xs text-slate-400">
        VitalSenior • Asistencia Médica Geriátrica Segura
    </footer>

    <!-- Scripts de SOS y PWA -->
    <script src="<?= APP_URL ?>/assets/js/sos-handler.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            requestAnimationFrame(() => {
                const body = document.getElementById('dashboard-body');
                if (body) {
                    body.classList.remove('opacity-0');
                    body.classList.add('opacity-100');
                }
            });

            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('<?= APP_URL ?>/sw.js').catch(() => {});
            }
        });
    </script>
</body>
</html>