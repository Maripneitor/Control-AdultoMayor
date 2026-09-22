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
        
        /* Micro-animación suave */
        .tap-bounce {
            transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .tap-bounce:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body id="dashboard-body" class="bg-slate-50 text-slate-900 font-sans min-h-full flex flex-col justify-between transition-opacity duration-500 opacity-0">
    
    <!-- Encabezado de Alto Contraste -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 px-6 py-4 shadow-xs">
        <div class="max-w-4xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center space-x-3 w-full sm:w-auto justify-between sm:justify-start">
                <div class="w-12 h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center font-extrabold text-2xl shadow-md shadow-blue-500/20">
                    ✚
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight tracking-tight">
                        Hola, <?= e($paciente['nombre'] ?? 'Bienvenido') ?>
                    </h1>
                    <p class="text-xs sm:text-sm font-semibold text-slate-500">
                        Perfil de Salud Geriátrico
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-2 w-full sm:w-auto justify-end">
                <a href="<?= APP_URL ?>/logout" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition-all">
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto p-4 sm:p-6 w-full space-y-6">
        <?php require_once dirname(__DIR__) . '/components/toast.php'; ?>

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
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
                    Horario de Medicación
                </span>
                <span class="text-xs font-semibold text-slate-500 tabular-nums">
                    Hoy, <?= date('d M Y') ?>
                </span>
            </div>

            <?php if ($proximaMedicina): ?>
                <div class="text-center py-4">
                    <p class="text-sm font-bold text-slate-500 uppercase tracking-wide">Tu próxima medicina es</p>
                    <h2 class="text-3xl sm:text-5xl font-black text-slate-900 my-2 tracking-tight">
                        <?= e($proximaMedicina['medicamento_nombre'] ?? $proximaMedicina['nombre_comercial']) ?>
                    </h2>
                    <div class="inline-flex items-center space-x-2 bg-blue-50 text-blue-800 px-4 py-1.5 rounded-2xl border border-blue-200 font-bold text-lg my-2">
                        <span>⏰</span>
                        <span class="tabular-nums">Programada a las <?= date('H:i', strtotime($proximaMedicina['hora_programada'])) ?></span>
                    </div>
                </div>

                <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center items-center">
                    <form action="<?= APP_URL ?>/medicamentos/toma" method="POST" class="w-full sm:w-auto">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="registro_id" value="<?= e((string)$proximaMedicina['registro_id']) ?>">
                        <button type="submit" class="tap-bounce w-full sm:w-auto px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-lg rounded-2xl shadow-lg shadow-emerald-600/20 flex items-center justify-center space-x-2 cursor-pointer">
                            <span>✓</span>
                            <span>Marcar como Tomada</span>
                        </button>
                    </form>

                    <a href="<?= APP_URL ?>/medicamentos/control" class="w-full sm:w-auto px-6 py-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-2xl text-center transition">
                        Ver Lista del Día
                    </a>
                </div>
            <?php else: ?>
                <!-- Estado Vacío Elegante (Sin dosis pendientes) -->
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3 text-3xl font-bold">
                        ✓
                    </div>
                    <h2 class="text-2xl font-black text-slate-800 mb-1">¡Todo al día!</h2>
                    <p class="text-sm text-slate-500 max-w-md mx-auto">
                        No tienes tomas pendientes por el momento. Tus cuidadores pueden revisar tu historial en cualquier momento.
                    </p>
                    <a href="<?= APP_URL ?>/medicamentos" class="inline-block mt-4 text-xs font-bold text-blue-600 hover:underline">
                        Consultar catálogo completo de fármacos →
                    </a>
                </div>
            <?php endif; ?>
        </section>

        <!-- BOTÓN SOS DE EMERGENCIA TÁCTIL -->
        <section>
            <button id="btn-sos" 
                    type="button"
                    data-paciente-id="<?= e((string)$paciente['id']) ?>"
                    class="tap-bounce w-full py-6 sm:py-8 bg-rose-600 text-white rounded-3xl shadow-tactile hover:bg-rose-500 active:shadow-tactile-active active:translate-y-1.5 transition-all flex flex-col items-center justify-center cursor-pointer focus:outline-none focus:ring-4 focus:ring-rose-300">
                <span class="text-5xl sm:text-6xl mb-1">🆘</span>
                <span class="text-2xl sm:text-3xl font-black tracking-wider uppercase">Botón SOS de Auxilio</span>
                <span class="text-xs sm:text-sm font-semibold text-rose-100 mt-1">
                    Avisar a mis familiares y cuidadores inmediatamente
                </span>
            </button>
        </section>

        <!-- ACCIONES RÁPIDAS DEL PACIENTE -->
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="<?= APP_URL ?>/expediente" class="tap-bounce p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-blue-300 hover:shadow-md transition-all text-center flex flex-col items-center justify-center">
                <span class="text-3xl mb-2">📋</span>
                <span class="text-base font-bold text-slate-800">Mi Expediente</span>
                <span class="text-xs text-slate-500 mt-0.5">Alergias y contactos</span>
            </a>

            <a href="<?= APP_URL ?>/medicamentos" class="tap-bounce p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-teal-300 hover:shadow-md transition-all text-center flex flex-col items-center justify-center">
                <span class="text-3xl mb-2">💊</span>
                <span class="text-base font-bold text-slate-800">Medicinas</span>
                <span class="text-xs text-slate-500 mt-0.5">Horarios y tomas</span>
            </a>

            <a href="<?= APP_URL ?>/emergencia?token=<?= e((string)($paciente['qr_token'] ?? $paciente['uuid_publico'])) ?>" target="_blank" class="tap-bounce p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-indigo-300 hover:shadow-md transition-all text-center flex flex-col items-center justify-center">
                <span class="text-3xl mb-2">📱</span>
                <span class="text-base font-bold text-slate-800">Ficha QR SOS</span>
                <span class="text-xs text-slate-500 mt-0.5">Credencial digital</span>
            </a>
        </section>

        <!-- VINCULACIÓN FAMILIAR -->
        <section class="bg-slate-100/80 p-5 rounded-2xl border border-slate-200 text-center">
            <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">
                Código de Vinculación Familiar
            </h3>
            <div class="inline-flex items-center space-x-2 bg-white px-6 py-2.5 rounded-xl border border-slate-300 shadow-xs">
                <span class="font-mono text-xl sm:text-2xl font-black text-slate-800 tracking-widest">
                    <?= e($paciente['codigo_vinculacion'] ?? 'NO-GEN') ?>
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-2">
                Comparte este código con tus familiares para que puedan gestionar tus recordatorios y expedientes.
            </p>
        </section>
    </main>

    <!-- Footer Simple -->
    <footer class="text-center py-6 text-xs text-slate-400">
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